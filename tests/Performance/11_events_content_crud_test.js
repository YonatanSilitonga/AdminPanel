import http from 'k6/http';
import { check, sleep } from 'k6';
import { getSessionCookies } from './utils/auth_helper.js';

/**
 * EVENTS & CONTENT MODULES PERFORMANCE TEST
 * Coverage: EventController + FasilitasUmumController + BudayaController + BeritaPromosiController
 *
 * Menggunakan setup() pattern — login sekali, session di-share ke semua VU.
 * Ini menghindari session race condition di concurrent requests.
 */
export const options = {
    stages: [
        { duration: '10s', target: 10 },
        { duration: '30s', target: 10 },
        { duration: '10s', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<3000'],
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Login sekali sebelum semua VU mulai
 */
export function setup() {
    const cookies = getSessionCookies();
    if (!cookies) {
        console.error('Setup failed: could not get session cookies');
    }
    return { cookies };
}

export default function (data) {
    // Semua request pakai cookie dari setup — session stabil di semua VU
    const headers = {
        Cookie: data.cookies,
        Accept: 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    };

    // --- EVENTS ---
    let eventsRes = http.get(`${BASE_URL}/admin/events`, { headers });
    check(eventsRes, {
        'Events index loaded': (r) => r.status === 200,
        'Events has content': (r) => r.body.length > 500,
    });
    sleep(1);

    let eventCreateRes = http.get(`${BASE_URL}/admin/events/create`, { headers });
    check(eventCreateRes, {
        'Events create form loaded': (r) => r.status === 200,
    });
    sleep(1);

    // --- FASILITAS UMUM ---
    let fasilitasRes = http.get(`${BASE_URL}/admin/fasilitas-umum`, { headers });
    check(fasilitasRes, {
        'Fasilitas umum index loaded': (r) => r.status === 200,
        'Fasilitas umum has content': (r) => r.body.length > 500,
    });
    if (fasilitasRes.status !== 200) {
        console.error(`Fasilitas umum: ${fasilitasRes.status} → ${fasilitasRes.url}`);
    }
    sleep(1);

    // --- BUDAYA ---
    let budayaRes = http.get(`${BASE_URL}/admin/budaya`, { headers });
    check(budayaRes, {
        'Budaya index loaded': (r) => r.status === 200,
        'Budaya has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // --- BERITA PROMOSI ---
    let beritaRes = http.get(`${BASE_URL}/admin/berita-promosi`, { headers });
    check(beritaRes, {
        'Berita promosi index loaded': (r) => r.status === 200,
        'Berita promosi has content': (r) => r.body.length > 500,
    });
    if (beritaRes.status !== 200) {
        console.error(`Berita promosi: ${beritaRes.status} → ${beritaRes.url}`);
    }
    sleep(1);
}
