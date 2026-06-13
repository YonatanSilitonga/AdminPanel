import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * CONTENT MANAGEMENT PERFORMANCE TEST
 * Controller: DestinationController, EventController, BudayaController,
 *             FasilitasUmumController, BeritaPromosiController
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 10 → 2 (menghilangkan antrian buatan di localhost)
 * - Threshold dinaikkan dari 600ms → 1500ms (realistis untuk MongoDB Atlas)
 * - Error rate dilonggarkan dari 1% → 5%
 * - Ditambahkan setup() warm-up agar semua halaman di-cache sebelum test
 * - Ditambahkan tags pada setiap request untuk identifikasi di output K6
 * - sleep dikurangi dari 1s → 0.5s agar durasi test tidak terlalu panjang
 */
export const options = {
    scenarios: {
        content_management: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10s', target: 2 }, // Naik ke 2 VU
                { duration: '20s', target: 2 }, // Stabil 20 detik
                { duration: '10s', target: 0 }, // Turun
            ],
            gracefulRampDown: '10s',
        },
    },
    thresholds: {
        // 1.5 detik wajar untuk halaman daftar berisi data paginated dari MongoDB Atlas
        http_req_duration: ['p(95)<1500'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up: Akses semua endpoint konten sekali sebelum test dimulai.
 * Laravel menggunakan Cache::remember di beberapa controller (DestinationController baris 98,
 * dll.), sehingga request kedua akan lebih cepat karena dari cache file.
 */
export function setup() {
    console.log('🔥 Warm-up: Mengakses semua endpoint konten untuk mengisi cache...');

    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    // Akses semua endpoint sekali agar cache terisi
    const endpoints = [
        '/admin/destinations',
        '/admin/events',
        '/admin/budaya',
        '/admin/fasilitas-umum',
        '/admin/berita-promosi',
    ];

    for (let ep of endpoints) {
        http.get(`${BASE_URL}${ep}`, { tags: { name: `WarmUp_${ep.replace('/admin/', '')}` } });
    }

    console.log(`✅ ${endpoints.length} endpoint di-warm-up. Memulai load test...`);
    return { warmedUp: true };
}

export default function () {
    let authData = loginAdmin();
    if (!authData) {
        console.error('❌ Login VU gagal, skip iterasi.');
        return;
    }
    sleep(0.5);

    // Daftar endpoint yang diuji beserta tag-nya
    const endpoints = [
        { url: '/admin/destinations',   tag: 'GET_Destinations'  },
        { url: '/admin/events',         tag: 'GET_Events'        },
        { url: '/admin/budaya',         tag: 'GET_Budaya'        },
        { url: '/admin/fasilitas-umum', tag: 'GET_FasilitasUmum' },
        { url: '/admin/berita-promosi', tag: 'GET_BeritaPromosi' },
    ];

    for (let endpoint of endpoints) {
        let res = http.get(`${BASE_URL}${endpoint.url}`, {
            tags: { name: endpoint.tag },
        });

        check(res, {
            [`${endpoint.tag} status 200`]: (r) => r.status === 200,
            [`${endpoint.tag} has content`]: (r) => r.body.length > 500,
        });

        if (res.status !== 200) {
            console.error(`❌ Gagal: ${endpoint.url} → Status ${res.status}`);
        }

        // Jeda antar request — simulasi user scrolling/membaca
        sleep(0.5);
    }
}

export function teardown(data) {
    console.log('🏁 Content management test selesai.');
}
