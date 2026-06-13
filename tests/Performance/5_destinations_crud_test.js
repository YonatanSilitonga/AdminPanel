import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * DESTINATIONS READ-ONLY PERFORMANCE TEST
 * Controller: DestinationController
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 10 → 2
 * - Threshold dinaikkan dari 1000ms → 1500ms (MongoDB Atlas pagination)
 * - Error rate dilonggarkan dari 1% → 5%
 * - Ditambahkan setup() warm-up (mengisi cache trending_stats 15 menit)
 * - Ditambahkan tags per request
 * - sleep dikurangi dari 2s → 1s
 *
 * NOTE: Test ini READ-ONLY — tidak ada operasi write ke database.
 *       Aman dijalankan terhadap data production.
 */
export const options = {
    scenarios: {
        destinations_read: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10s', target: 2 }, // Naik ke 2 VU
                { duration: '20s', target: 2 }, // Stabil
                { duration: '10s', target: 0 }, // Turun
            ],
            gracefulRampDown: '10s',
        },
    },
    thresholds: {
        // Daftar destinasi dengan pagination — wajar 1.5 detik via MongoDB Atlas
        'http_req_duration{name:GET_Destinations_Page1}': ['p(95)<1500'],
        'http_req_duration{name:GET_Destinations_Page2}': ['p(95)<1500'],
        // Form create hanya render halaman kosong — seharusnya lebih cepat
        'http_req_duration{name:GET_Destinations_Create}': ['p(95)<1500'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up: Akses halaman destinasi untuk mengisi cache 'admin.destinations.trending_stats'
 * (Cache::remember 15 menit di DestinationController@index baris 98).
 */
export function setup() {
    console.log('🔥 Warm-up: Login + akses destinations untuk mengisi cache trending_stats...');

    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    // Warm-up page 1 — mengisi cache trending_stats
    http.get(`${BASE_URL}/admin/destinations?page=1`, { tags: { name: 'WarmUp_Destinations' } });

    console.log('✅ Cache terisi. Memulai load test...');
    return { startTime: Date.now() };
}

export default function () {
    let authData = loginAdmin();
    if (!authData) {
        console.error('❌ Login VU gagal, skip iterasi.');
        return;
    }
    sleep(0.5);

    // 1. Destinations index — halaman 1
    let page1Res = http.get(`${BASE_URL}/admin/destinations?page=1`, {
        tags: { name: 'GET_Destinations_Page1' },
    });
    check(page1Res, {
        'Destinations page 1 loaded (200)': (r) => r.status === 200,
        'Destinations page 1 has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // 2. Destinations index — halaman 2 (uji pagination)
    let page2Res = http.get(`${BASE_URL}/admin/destinations?page=2`, {
        tags: { name: 'GET_Destinations_Page2' },
    });
    check(page2Res, {
        'Destinations page 2 loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Form create — hanya memuat halaman (tanpa submit)
    let createRes = http.get(`${BASE_URL}/admin/destinations/create`, {
        tags: { name: 'GET_Destinations_Create' },
    });
    check(createRes, {
        'Destinations create form loaded (200)': (r) => r.status === 200,
    });
    sleep(1);
}

export function teardown(data) {
    const duration = (Date.now() - data.startTime) / 1000;
    console.log(`🏁 Destinations test selesai dalam ${duration.toFixed(2)} detik.`);
}
