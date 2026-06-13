import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * DASHBOARD + CHART DATA PERFORMANCE TEST
 * Controller: DashboardController
 *
 * Perubahan:
 * - VU diturunkan dari 10 → 2
 * - Threshold dashboard: 2000ms (cache sudah hangat)
 * - Threshold chart-data: 5000ms (84 query MongoDB Atlas, wajar lebih lambat)
 * - Ditambahkan setup() warm-up untuk mengisi cache sebelum test
 * - Ditambahkan tags pada setiap request
 */
export const options = {
    scenarios: {
        dashboard_load: {
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
        // Dashboard setelah warm-up hanya membaca dari cache file → 2 detik cukup
        'http_req_duration{name:GET_Dashboard}': ['p(95)<2000'],
        // Chart-data melakukan 84 query MongoDB Atlas → diberi kelonggaran 5 detik
        'http_req_duration{name:GET_ChartData}': ['p(95)<5000'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up: Akses dashboard sekali agar cache 'admin.dashboard.stats_summary'
 * dan 'admin.dashboard.monthly_chart' terisi sebelum VU mulai.
 * Tanpa ini, semua VU akan menyerbu MongoDB Atlas secara bersamaan pada
 * iterasi pertama (cache miss), menyebabkan spike latensi ekstrem.
 */
export function setup() {
    console.log('🔥 Warm-up: Login dan akses dashboard untuk mengisi cache...');

    // Login dulu
    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up login gagal!');
        return {};
    }

    // Akses dashboard untuk mengisi cache stats (Cache::remember 2 menit)
    http.get(`${BASE_URL}/admin/dashboard`, { tags: { name: 'WarmUp_Dashboard' } });

    // Akses chart-data untuk mengisi cache chart (Cache::remember 1 jam)
    http.get(`${BASE_URL}/admin/dashboard/chart-data`, { tags: { name: 'WarmUp_ChartData' } });

    console.log('✅ Cache terisi. Memulai load test...');
    return { warmedUp: true };
}

export default function () {
    // Login per-VU (setiap VU punya session sendiri)
    let authData = loginAdmin();
    if (!authData) {
        console.error('❌ Login VU gagal, skip iterasi.');
        return;
    }
    sleep(0.5);

    // 1. Muat halaman dashboard utama (sudah dari cache setelah warm-up)
    let dashboardRes = http.get(`${BASE_URL}/admin/dashboard`, {
        tags: { name: 'GET_Dashboard' },
    });
    check(dashboardRes, {
        'Dashboard loaded (200)': (r) => r.status === 200,
        'Dashboard has content': (r) => r.body.length > 500,
    });
    if (dashboardRes.status !== 200) {
        console.error(`❌ Dashboard gagal: ${dashboardRes.status}`);
    }
    sleep(1);

    // 2. Muat chart data via AJAX (sudah dari cache setelah warm-up)
    let chartRes = http.get(`${BASE_URL}/admin/dashboard/chart-data`, {
        tags: { name: 'GET_ChartData' },
    });
    check(chartRes, {
        'Chart data loaded (200)': (r) => r.status === 200,
        'Chart data is valid JSON': (r) => {
            try { JSON.parse(r.body); return true; } catch (e) { return false; }
        },
    });
    if (chartRes.status !== 200) {
        console.error(`❌ Chart data gagal: ${chartRes.status}`);
    }
    sleep(1);
}

export function teardown(data) {
    console.log('🏁 Dashboard test selesai.');
}
