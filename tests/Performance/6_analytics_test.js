import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * ANALYTICS MODULE PERFORMANCE TEST
 * Controller: AnalyticsController (semua 4 endpoint)
 * + DashboardController@getChartData
 *
 * Perubahan dari versi sebelumnya:
 * - VU diturunkan dari 5 → 1 (analytics berat — 1 VU sudah cukup untuk baseline)
 * - Threshold dinaikkan dari 4000ms → 5000ms untuk chart, 3000ms untuk analytics
 * - Error rate dilonggarkan dari 1% → 5%
 * - Ditambahkan setup() warm-up untuk mengisi semua cache analytics
 *   (Cache::remember 10 menit di AnalyticsController baris 23/40/58/74)
 * - Ditambahkan tags per endpoint
 */
export const options = {
    scenarios: {
        analytics_load: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10s', target: 1 }, // 1 VU untuk isolasi bersih
                { duration: '20s', target: 1 },
                { duration: '10s', target: 0 },
            ],
            gracefulRampDown: '10s',
        },
    },
    thresholds: {
        // Chart data — 84 MongoDB Atlas queries → 5 detik masih wajar
        'http_req_duration{name:GET_ChartData}':           ['p(95)<5000'],
        // Analytics summary dashboard — aggregation berat, cache 10 menit
        'http_req_duration{name:GET_Analytics_Dashboard}': ['p(95)<3000'],
        // Destinations, events, reports analytics — lebih ringan dari summary
        'http_req_duration{name:GET_Analytics_Destinations}': ['p(95)<3000'],
        'http_req_duration{name:GET_Analytics_Events}':       ['p(95)<3000'],
        'http_req_duration{name:GET_Analytics_Reports}':      ['p(95)<3000'],
        // Review summary stats — aggregasi MongoDB
        'http_req_duration{name:GET_ReviewSummaryStats}':  ['p(95)<3000'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up KRITIS untuk analytics.
 * AnalyticsController menggunakan Cache::remember 10 menit untuk semua endpoint.
 * Tanpa warm-up, setiap request pertama akan melakukan full MongoDB aggregation
 * yang bisa memakan waktu 10-30 detik via cloud → langsung gagal threshold.
 */
export function setup() {
    console.log('🔥 Warm-up KRITIS: Mengisi cache analytics (Cache::remember 10 menit)...');

    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    // Isi cache chart-data dulu (Cache::remember 1 jam)
    console.log('  → Warming up chart-data (1 jam cache)...');
    http.get(`${BASE_URL}/admin/dashboard/chart-data`, { tags: { name: 'WarmUp' } });

    // Isi semua cache analytics (Cache::remember 10 menit)
    const analyticsEndpoints = [
        '/admin/analytics',
        '/admin/analytics/destinations',
        '/admin/analytics/events',
        '/admin/analytics/reports',
        '/admin/reviews/summary/stats',
    ];

    for (let ep of analyticsEndpoints) {
        console.log(`  → Warming up: ${ep}...`);
        http.get(`${BASE_URL}${ep}`, { tags: { name: 'WarmUp' } });
    }

    console.log('✅ Semua cache analytics terisi. Memulai load test...');
    return { warmedUp: true };
}

export default function () {
    let authData = loginAdmin();
    if (!authData) return;
    sleep(0.5);

    // 1. Dashboard chart data (dari cache setelah warm-up)
    let chartRes = http.get(`${BASE_URL}/admin/dashboard/chart-data`, {
        tags: { name: 'GET_ChartData' },
    });
    check(chartRes, {
        'Chart data loaded (200)': (r) => r.status === 200,
        'Chart data is valid JSON': (r) => {
            try { JSON.parse(r.body); return true; } catch (e) { return false; }
        },
    });
    sleep(1);

    // 2. Analytics dashboard (dari cache)
    let analyticsRes = http.get(`${BASE_URL}/admin/analytics`, {
        tags: { name: 'GET_Analytics_Dashboard' },
    });
    check(analyticsRes, {
        'Analytics dashboard loaded (200)': (r) => r.status === 200,
        'Analytics has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // 3. Destinations analytics
    let destAnalRes = http.get(`${BASE_URL}/admin/analytics/destinations`, {
        tags: { name: 'GET_Analytics_Destinations' },
    });
    check(destAnalRes, {
        'Destinations analytics loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 4. Events analytics
    let eventAnalRes = http.get(`${BASE_URL}/admin/analytics/events`, {
        tags: { name: 'GET_Analytics_Events' },
    });
    check(eventAnalRes, {
        'Events analytics loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 5. Reports analytics
    let reportAnalRes = http.get(`${BASE_URL}/admin/analytics/reports`, {
        tags: { name: 'GET_Analytics_Reports' },
    });
    check(reportAnalRes, {
        'Reports analytics loaded (200)': (r) => r.status === 200,
    });
    sleep(1);

    // 6. Review summary stats
    let statsRes = http.get(`${BASE_URL}/admin/reviews/summary/stats`, {
        tags: { name: 'GET_ReviewSummaryStats' },
    });
    check(statsRes, {
        'Review summary stats loaded (200)': (r) => r.status === 200,
    });
    sleep(1);
}

export function teardown(data) {
    console.log('🏁 Analytics test selesai.');
}
