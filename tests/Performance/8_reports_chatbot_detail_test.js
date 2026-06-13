import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * REPORTS & CHATBOT DETAIL PERFORMANCE TEST
 * Controller: ReportController, ChatbotLogController, RecommendationController
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 5 → 2 (realistis untuk koneksi Atlas cloud)
 * - Menambahkan setup() warm-up untuk memicu query caching
 * - Menggunakan ramping-vus executor
 * - Menambahkan tags untuk identifikasi performa per-endpoint
 * - Menentukan thresholds per-tag (terutama export CSV yang diberi kelonggaran 4 detik)
 */
export const options = {
    scenarios: {
        reports_chatbot: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10s', target: 2 }, // Naik ke 2 VU
                { duration: '20s', target: 2 }, // Stabil di 2 VU
                { duration: '10s', target: 0 }, // Turun ke 0
            ],
            gracefulRampDown: '10s',
        },
    },
    thresholds: {
        'http_req_duration{name:GET_ReportsIndex}':          ['p(95)<2500'],
        'http_req_duration{name:GET_ChatbotLogsIndex}':      ['p(95)<2500'],
        'http_req_duration{name:GET_ChatbotLogsPage2}':      ['p(95)<2500'],
        'http_req_duration{name:GET_RecommendationsIndex}':  ['p(95)<2500'],
        // Export CSV memproses query data and generate berkas, wajar 4 detik
        'http_req_duration{name:GET_RecommendationsExport}': ['p(95)<4000'],
        http_req_failed: ['rate<0.05'], // Toleran jika data kosong/sebagian ID missing
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up: Login dan akses semua index & export endpoint sekali
 * agar cache database / session Laravel sudah siap.
 */
export function setup() {
    console.log('🔥 Warm-up: Login dan inisialisasi cache reports & chatbot...');
    
    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    const endpoints = [
        '/admin/reports',
        '/admin/chatbot-logs',
        '/admin/chatbot-logs?page=2',
        '/admin/recommendations',
        '/admin/recommendations/export'
    ];

    for (let ep of endpoints) {
        console.log(`  → Warming up: ${ep}...`);
        http.get(`${BASE_URL}${ep}`, { tags: { name: 'WarmUp' } });
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

    // 1. Reports index
    let reportsRes = http.get(`${BASE_URL}/admin/reports`, {
        tags: { name: 'GET_ReportsIndex' },
    });
    check(reportsRes, {
        'Reports index loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 2. Chatbot logs index
    let chatbotRes = http.get(`${BASE_URL}/admin/chatbot-logs`, {
        tags: { name: 'GET_ChatbotLogsIndex' },
    });
    check(chatbotRes, {
        'Chatbot logs loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Chatbot log page 2 (pagination)
    let chatbotPage2Res = http.get(`${BASE_URL}/admin/chatbot-logs?page=2`, {
        tags: { name: 'GET_ChatbotLogsPage2' },
    });
    check(chatbotPage2Res, {
        'Chatbot logs page 2 loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 4. Recommendations index
    let recRes = http.get(`${BASE_URL}/admin/recommendations`, {
        tags: { name: 'GET_RecommendationsIndex' },
    });
    check(recRes, {
        'Recommendations loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 5. Recommendations export (CSV)
    let exportRes = http.get(`${BASE_URL}/admin/recommendations/export`, {
        tags: { name: 'GET_RecommendationsExport' },
    });
    check(exportRes, {
        'Recommendations export successful (200)': (r) => r.status === 200,
    });
    sleep(1);
}

export function teardown(data) {
    console.log('🏁 Reports & chatbot detail test selesai.');
}
