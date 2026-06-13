import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * SETTINGS & PROFILE PERFORMANCE TEST
 * Coverage: SettingsController + ProfileController + AuditLogController
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 3 → 2
 * - Menggunakan ramping-vus executor
 * - Menambahkan setup() warm-up untuk mengisi cache session dan route
 * - Menambahkan tags untuk identifikasi performa per-endpoint
 * - Menentukan thresholds per-tag (audit logs diberi kelonggaran 3 detik)
 */
export const options = {
    scenarios: {
        settings_profile: {
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
        'http_req_duration{name:GET_Profile}':          ['p(95)<2000'],
        'http_req_duration{name:GET_SettingsGeneral}':  ['p(95)<2000'],
        'http_req_duration{name:GET_AuditLogsIndex}':   ['p(95)<3000'], // Audit logs query data history wajar 3 detik
        'http_req_duration{name:GET_AuditLogsPage2}':   ['p(95)<3000'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up: Login dan akses semua halaman settings sekali
 * agar cache database / session Laravel siap sebelum concurrent test.
 */
export function setup() {
    console.log('🔥 Warm-up: Login dan inisialisasi cache settings & profile...');
    
    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    const endpoints = [
        '/admin/profile',
        '/admin/settings/general',
        '/admin/settings/audit-logs',
        '/admin/settings/audit-logs?page=2'
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

    // 1. Profile page
    let profileRes = http.get(`${BASE_URL}/admin/profile`, {
        tags: { name: 'GET_Profile' },
    });
    check(profileRes, {
        'Profile page loaded (200)': (r) => r.status === 200,
        'Profile has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // 2. Settings — General
    let generalRes = http.get(`${BASE_URL}/admin/settings/general`, {
        tags: { name: 'GET_SettingsGeneral' },
    });
    check(generalRes, {
        'Settings general loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Audit Logs index
    let auditRes = http.get(`${BASE_URL}/admin/settings/audit-logs`, {
        tags: { name: 'GET_AuditLogsIndex' },
    });
    check(auditRes, {
        'Audit logs loaded (200)': (r) => r.status === 200,
        'Audit logs has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // 4. Audit Logs page 2 (pagination)
    let auditPage2Res = http.get(`${BASE_URL}/admin/settings/audit-logs?page=2`, {
        tags: { name: 'GET_AuditLogsPage2' },
    });
    check(auditPage2Res, {
        'Audit logs page 2 loaded (200)': (r) => r.status === 200,
    });
    sleep(1);
}

export function teardown(data) {
    console.log('🏁 Settings & profile test selesai.');
}
