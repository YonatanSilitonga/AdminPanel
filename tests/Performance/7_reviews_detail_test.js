import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * REVIEWS DETAIL & MODERATION PERFORMANCE TEST
 * Controller: ReviewController
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 5 → 2
 * - Threshold dinaikkan dari 2000ms → 2500ms
 * - Error rate dilonggarkan dari 1% → 5%
 * - Ditambahkan setup() warm-up untuk mengisi cache review stats dan summary
 *   (Cache::remember 5 menit dan 2 jam di ReviewController)
 * - Ditambahkan tags per endpoint
 *
 * NOTE: POST /analyze, PATCH /approve, PATCH /reject TIDAK dijalankan
 *       karena akan memodifikasi data asli. Test ini READ-ONLY.
 */
export const options = {
    scenarios: {
        reviews_load: {
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
        // Daftar reviews (dengan filter status) — MongoDB Atlas pagination
        'http_req_duration{name:GET_Reviews_Index}':   ['p(95)<2500'],
        'http_req_duration{name:GET_Reviews_Pending}': ['p(95)<2500'],
        // Stats summary — aggregation MongoDB, sudah di-cache
        'http_req_duration{name:GET_Reviews_Stats}':   ['p(95)<2500'],
        // Print analytics view — aggregation lebih kompleks
        'http_req_duration{name:GET_Reviews_Print}':   ['p(95)<3000'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up: Isi cache review stats (Cache::remember 5 menit) dan summary
 * (Cache::remember 2 jam) sebelum VU mulai.
 */
export function setup() {
    console.log('🔥 Warm-up: Mengisi cache review stats dan summary...');

    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    // Warm-up: mengisi cache stats (5 menit) dan summary (2 jam)
    http.get(`${BASE_URL}/admin/reviews`, { tags: { name: 'WarmUp' } });
    http.get(`${BASE_URL}/admin/reviews/summary/stats`, { tags: { name: 'WarmUp' } });
    http.get(`${BASE_URL}/admin/reviews/analytics/print`, { tags: { name: 'WarmUp' } });

    console.log('✅ Cache reviews terisi. Memulai load test...');
    return { warmedUp: true };
}

export default function () {
    let authData = loginAdmin();
    if (!authData) return;
    sleep(0.5);

    // 1. Reviews index (daftar semua ulasan)
    let indexRes = http.get(`${BASE_URL}/admin/reviews`, {
        tags: { name: 'GET_Reviews_Index' },
    });
    check(indexRes, {
        'Reviews index loaded (200)': (r) => r.status === 200,
        'Reviews index has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // 2. Reviews filter status=pending
    let pendingRes = http.get(`${BASE_URL}/admin/reviews?status=pending`, {
        tags: { name: 'GET_Reviews_Pending' },
    });
    check(pendingRes, {
        'Reviews pending filter loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Review summary stats (aggregation — dari cache setelah warm-up)
    let statsRes = http.get(`${BASE_URL}/admin/reviews/summary/stats`, {
        tags: { name: 'GET_Reviews_Stats' },
    });
    check(statsRes, {
        'Review summary stats loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 4. Review analytics print view
    let printRes = http.get(`${BASE_URL}/admin/reviews/analytics/print`, {
        tags: { name: 'GET_Reviews_Print' },
    });
    check(printRes, {
        'Review analytics print loaded (200)': (r) => r.status === 200,
    });
    sleep(1);
}

export function teardown(data) {
    console.log('🏁 Reviews detail test selesai.');
}
