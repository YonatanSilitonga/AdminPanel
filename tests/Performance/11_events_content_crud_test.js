import http from 'k6/http';
import { check, sleep } from 'k6';
import { getSessionCookies } from './utils/auth_helper.js';

/**
 * EVENTS & CONTENT MODULES PERFORMANCE TEST
 * Coverage: EventController + FasilitasUmumController + BudayaController + BeritaPromosiController
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 10 → 2 (realistis untuk koneksi Atlas cloud)
 * - Menggunakan ramping-vus executor
 * - Menambahkan setup() warm-up yang menggunakan shared cookie untuk mengisi cache Laravel
 * - Menambahkan tags untuk identifikasi performa per-endpoint
 * - Menentukan thresholds per-tag (2.5 detik target p(95))
 */
export const options = {
    scenarios: {
        events_content: {
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
        'http_req_duration{name:GET_EventsIndex}':        ['p(95)<2500'],
        'http_req_duration{name:GET_EventsCreateForm}':   ['p(95)<2500'],
        'http_req_duration{name:GET_FasilitasUmumIndex}': ['p(95)<2500'],
        'http_req_duration{name:GET_BudayaIndex}':        ['p(95)<2500'],
        'http_req_duration{name:GET_BeritaPromosiIndex}': ['p(95)<2500'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Login sekali sebelum semua VU mulai dan warm-up cache untuk setiap endpoint.
 */
export function setup() {
    console.log('🔥 Warm-up: Login sekali dan inisialisasi cache events & content...');
    const cookies = getSessionCookies();
    if (!cookies) {
        console.error('Setup failed: could not get session cookies');
        return { cookies: '' };
    }

    const headers = {
        Cookie: cookies,
        Accept: 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    };

    const endpoints = [
        '/admin/events',
        '/admin/events/create',
        '/admin/fasilitas-umum',
        '/admin/budaya',
        '/admin/berita-promosi'
    ];

    for (let ep of endpoints) {
        console.log(`  → Warming up: ${ep}...`);
        http.get(`${BASE_URL}${ep}`, { headers, tags: { name: 'WarmUp' } });
    }

    console.log('✅ Warm-up selesai. Memulai load test...');
    return { cookies };
}

export default function (data) {
    if (!data.cookies) {
        console.error('❌ Tidak ada cookies, skip iterasi.');
        return;
    }

    // Semua request pakai cookie dari setup — session stabil di semua VU
    const headers = {
        Cookie: data.cookies,
        Accept: 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    };

    // --- EVENTS ---
    let eventsRes = http.get(`${BASE_URL}/admin/events`, {
        headers,
        tags: { name: 'GET_EventsIndex' },
    });
    check(eventsRes, {
        'Events index loaded (200)': (r) => r.status === 200,
        'Events has content': (r) => r.body.length > 500,
    });
    sleep(1);

    let eventCreateRes = http.get(`${BASE_URL}/admin/events/create`, {
        headers,
        tags: { name: 'GET_EventsCreateForm' },
    });
    check(eventCreateRes, {
        'Events create form loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // --- FASILITAS UMUM ---
    let fasilitasRes = http.get(`${BASE_URL}/admin/fasilitas-umum`, {
        headers,
        tags: { name: 'GET_FasilitasUmumIndex' },
    });
    check(fasilitasRes, {
        'Fasilitas umum index loaded (200)': (r) => r.status === 200,
        'Fasilitas umum has content': (r) => r.body.length > 500,
    });
    if (fasilitasRes.status !== 200) {
        console.error(`❌ Fasilitas umum gagal: ${fasilitasRes.status} → ${fasilitasRes.url}`);
    }
    sleep(1);

    // --- BUDAYA ---
    let budayaRes = http.get(`${BASE_URL}/admin/budaya`, {
        headers,
        tags: { name: 'GET_BudayaIndex' },
    });
    check(budayaRes, {
        'Budaya index loaded (200)': (r) => r.status === 200,
        'Budaya has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // --- BERITA PROMOSI ---
    let beritaRes = http.get(`${BASE_URL}/admin/berita-promosi`, {
        headers,
        tags: { name: 'GET_BeritaPromosiIndex' },
    });
    check(beritaRes, {
        'Berita promosi index loaded (200)': (r) => r.status === 200,
        'Berita promosi has content': (r) => r.body.length > 500,
    });
    if (beritaRes.status !== 200) {
        console.error(`❌ Berita promosi gagal: ${beritaRes.status} → ${beritaRes.url}`);
    }
    sleep(1);
}

export function teardown(data) {
    console.log('🏁 Events & content test selesai.');
}
