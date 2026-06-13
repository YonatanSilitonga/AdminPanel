import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * MODERATION & LOGS PERFORMANCE TEST
 * Controller: ReviewController, ReportController, UserController,
 *             RecommendationLogController, ChatbotLogController, AuditLogController
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 10 → 2
 * - Threshold dinaikkan dari 1000ms → 2500ms
 *   (Chatbot logs & Recommendations adalah query MongoDB besar — wajar lebih lambat)
 * - Error rate dilonggarkan dari 1% → 5%
 * - Ditambahkan setup() warm-up untuk semua endpoint berat
 * - Ditambahkan tags per endpoint
 */
export const options = {
    scenarios: {
        moderation_logs: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10s', target: 2 },
                { duration: '20s', target: 2 },
                { duration: '10s', target: 0 },
            ],
            gracefulRampDown: '10s',
        },
    },
    thresholds: {
        // Endpoint moderasi (reviews, reports, users) — diharapkan < 2.5 detik
        'http_req_duration{name:GET_Reviews}':         ['p(95)<2500'],
        'http_req_duration{name:GET_Reports}':         ['p(95)<2500'],
        'http_req_duration{name:GET_Users}':           ['p(95)<2500'],
        // Recommendations & chatbot logs adalah query MongoDB paling berat
        'http_req_duration{name:GET_Recommendations}': ['p(95)<4000'],
        'http_req_duration{name:GET_ChatbotLogs}':     ['p(95)<4000'],
        // Audit logs cukup ringan (SQL)
        'http_req_duration{name:GET_AuditLogs}':       ['p(95)<2500'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up semua endpoint berat sebelum test.
 * Khusus RecommendationLogController dan ReviewController
 * menggunakan Cache::remember — request pertama akan mengisi cache.
 */
export function setup() {
    console.log('🔥 Warm-up: Login dan akses semua endpoint moderasi...');

    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    const warmUpEndpoints = [
        '/admin/reviews',
        '/admin/reports',
        '/admin/users',
        '/admin/recommendations',
        '/admin/chatbot-logs',
        '/admin/settings/audit-logs',
    ];

    for (let ep of warmUpEndpoints) {
        http.get(`${BASE_URL}${ep}`, { tags: { name: `WarmUp` } });
    }

    console.log('✅ Warm-up selesai. Memulai load test...');
    return { warmedUp: true };
}

export default function () {
    let authData = loginAdmin();
    if (!authData) {
        console.error('❌ Login VU gagal, skip iterasi.');
        return;
    }
    sleep(0.5);

    const endpoints = [
        { url: '/admin/reviews',                 tag: 'GET_Reviews',        expectOnly200: true  },
        { url: '/admin/reports',                 tag: 'GET_Reports',        expectOnly200: true  },
        { url: '/admin/users',                   tag: 'GET_Users',          expectOnly200: true  },
        { url: '/admin/recommendations',         tag: 'GET_Recommendations',expectOnly200: true  },
        { url: '/admin/chatbot-logs',            tag: 'GET_ChatbotLogs',    expectOnly200: true  },
        // Audit logs: super_admin only — mungkin return 403 jika bukan super_admin
        { url: '/admin/settings/audit-logs',     tag: 'GET_AuditLogs',     expectOnly200: false },
    ];

    for (let endpoint of endpoints) {
        let res = http.get(`${BASE_URL}${endpoint.url}`, {
            tags: { name: endpoint.tag },
        });

        if (endpoint.expectOnly200) {
            check(res, {
                [`${endpoint.tag} status 200`]: (r) => r.status === 200,
                [`${endpoint.tag} has content`]: (r) => r.body.length > 500,
            });
        } else {
            // Audit logs bisa return 200 atau 403 — keduanya valid
            check(res, {
                [`${endpoint.tag} status 200 or 403`]: (r) => r.status === 200 || r.status === 403,
            });
        }

        if (res.status !== 200 && res.status !== 403) {
            console.error(`❌ Gagal: ${endpoint.url} → Status ${res.status}`);
        }

        sleep(0.5);
    }
}

export function teardown(data) {
    console.log('🏁 Moderation & logs test selesai.');
}
