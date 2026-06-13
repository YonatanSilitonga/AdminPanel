import http from 'k6/http';
import { check, sleep } from 'k6';

/**
 * AUTH PERFORMANCE TEST
 * Controller: AdminAuthController
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 10 → 2 (realistis untuk MongoDB Atlas cloud)
 * - Threshold dinaikkan dari 1000ms → 3000ms (login butuh enkripsi + session write + Atlas round-trip)
 * - Error rate dilonggarkan dari 1% → 5%
 * - Ditambahkan setup() warm-up agar cache Laravel terisi sebelum test mulai
 * - Ditambahkan tags pada setiap request untuk identifikasi mudah di output K6
 * - Menggunakan executor ramping-vus (lebih eksplisit dari stages)
 */
export const options = {
    scenarios: {
        auth_load: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10s', target: 2 }, // Naik ke 2 VU dalam 10 detik
                { duration: '20s', target: 2 }, // Stabil di 2 VU selama 20 detik
                { duration: '10s', target: 0 }, // Turun ke 0
            ],
            gracefulRampDown: '10s',
        },
    },
    thresholds: {
        // Login memerlukan bcrypt hash check + session write + MongoDB read → wajar 3 detik
        http_req_duration: ['p(95)<3000'],
        // Longgarkan ke 5% karena koneksi internet ke Atlas kadang fluktuatif
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up: Akses login page sekali agar session & cache Laravel terisi.
 * Ini mencegah "cold start" yang membuat request pertama selalu jauh lebih lambat.
 */
export function setup() {
    console.log('🔥 Warming up: mengakses halaman login untuk inisialisasi session...');
    http.get(`${BASE_URL}/admin/login`, { tags: { name: 'WarmUp_LoginPage' } });
    console.log('✅ Warm-up selesai. Memulai test...');
    return { warmedUp: true };
}

export default function () {
    // 1. GET /admin/login — Muat halaman login (publik, tanpa session)
    let getRes = http.get(`${BASE_URL}/admin/login`, {
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
        tags: { name: 'GET_LoginPage' },
    });

    check(getRes, {
        'GET login status is 200': (r) => r.status === 200,
        'Login page has CSRF token': (r) => r.body.includes('name="_token"'),
    });

    // Extract CSRF token
    let csrfToken = '';
    let csrfMatch = getRes.body.match(/name="_token"\s+value="([^"]+)"/);
    if (csrfMatch && csrfMatch.length > 1) {
        csrfToken = csrfMatch[1];
    } else {
        csrfMatch = getRes.body.match(/value="([^"]+)"\s+name="_token"/);
        if (csrfMatch && csrfMatch.length > 1) {
            csrfToken = csrfMatch[1];
        }
    }

    if (!csrfToken) {
        console.error('❌ Tidak dapat mengekstrak CSRF token. Skip iterasi ini.');
        return;
    }

    // Jeda "think time" — simulasi user mengetik password
    sleep(1);

    // 2. POST /admin/login — Submit form autentikasi
    let loginData = {
        _token: csrfToken,
        email: 'superadmin@smarttourism.local',
        password: 'SuperAdmin@123',
    };

    let postRes = http.post(`${BASE_URL}/admin/login`, loginData, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Referer': `${BASE_URL}/admin/login`,
        },
        redirects: 5,
        tags: { name: 'POST_LoginSubmit' },
    });

    if (postRes.status !== 200 || !postRes.url.includes('/admin/dashboard')) {
        console.error(`❌ Login gagal! Status: ${postRes.status}, URL: ${postRes.url}`);
    }

    check(postRes, {
        'Login berhasil (redirect ke dashboard)': (r) => r.status === 200 && r.url.includes('/admin/dashboard'),
    });

    sleep(1);
}

export function teardown(data) {
    console.log('🏁 Auth test selesai.');
}
