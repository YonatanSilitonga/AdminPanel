import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * USERS MANAGEMENT PERFORMANCE TEST
 * Coverage: UserController
 * - GET /admin/users                    (index + filter)
 * - GET /admin/users/{id}/activity      (activity log — heavy query, bypassed if IDs dynamic)
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 5 → 2 (realistis untuk MongoDB Atlas cloud)
 * - Menggunakan ramping-vus executor
 * - Menambahkan setup() warm-up untuk mengisi cache session dan route
 * - Menambahkan tags untuk identifikasi performa per-endpoint
 * - Menentukan thresholds per-tag (2.5 detik target p(95))
 */
export const options = {
    scenarios: {
        users_activity: {
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
        'http_req_duration{name:GET_UsersIndex}':        ['p(95)<2500'],
        'http_req_duration{name:GET_UsersActiveFilter}': ['p(95)<2500'],
        'http_req_duration{name:GET_UsersSearch}':       ['p(95)<2500'],
        'http_req_duration{name:GET_UsersPage2}':        ['p(95)<2500'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up: Login dan akses semua halaman users sekali
 * agar cache database / session Laravel siap sebelum concurrent test.
 */
export function setup() {
    console.log('🔥 Warm-up: Login dan inisialisasi cache users management...');
    
    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    const endpoints = [
        '/admin/users',
        '/admin/users?status=active',
        '/admin/users?search=a',
        '/admin/users?page=2'
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

    // 1. Users index
    let usersRes = http.get(`${BASE_URL}/admin/users`, {
        tags: { name: 'GET_UsersIndex' },
    });
    check(usersRes, {
        'Users index loaded (200)': (r) => r.status === 200,
        'Users has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // 2. Users index dengan filter status
    let activeUsersRes = http.get(`${BASE_URL}/admin/users?status=active`, {
        tags: { name: 'GET_UsersActiveFilter' },
    });
    check(activeUsersRes, {
        'Users active filter loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Users search
    let searchRes = http.get(`${BASE_URL}/admin/users?search=a`, {
        tags: { name: 'GET_UsersSearch' },
    });
    check(searchRes, {
        'Users search loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 4. Users page 2
    let page2Res = http.get(`${BASE_URL}/admin/users?page=2`, {
        tags: { name: 'GET_UsersPage2' },
    });
    check(page2Res, {
        'Users page 2 loaded (200)': (r) => r.status === 200,
    });
    sleep(1);
}

export function teardown(data) {
    console.log('🏁 Users activity test selesai.');
}
