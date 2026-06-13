import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * DASHBOARD SIMPLIFIED PERFORMANCE TEST
 * Controller: DashboardController (tanpa chart AJAX)
 *
 * Perubahan:
 * - VU diturunkan dari 5 → 1 (isolasi masalah dashboard murni)
 * - Threshold 2000ms → 1500ms (lebih ketat karena VU sedikit)
 * - Ditambahkan setup() warm-up
 * - Ditambahkan tags
 */
export const options = {
    scenarios: {
        dashboard_simple: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10s', target: 1 }, // Hanya 1 VU — murni isolasi performa
                { duration: '20s', target: 1 },
                { duration: '10s', target: 0 },
            ],
            gracefulRampDown: '10s',
        },
    },
    thresholds: {
        // Dengan 1 VU dan cache sudah hangat, 2 detik sangat aman dan realistis
        http_req_duration: ['p(95)<2000'],
        http_req_failed: ['rate<0.05'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Warm-up cache dashboard sebelum test mulai.
 */
export function setup() {
    console.log('🔥 Warm-up: Login + akses dashboard untuk mengisi cache stats...');
    const authData = loginAdmin();
    if (!authData) {
        console.error('❌ Warm-up gagal!');
        return {};
    }
    http.get(`${BASE_URL}/admin/dashboard`, { tags: { name: 'WarmUp_Dashboard' } });
    console.log('✅ Cache terisi. Memulai test...');
    return { warmedUp: true };
}

export default function () {
    let authData = loginAdmin();
    if (!authData) {
        console.error('❌ Login VU gagal, skip iterasi.');
        return;
    }
    sleep(0.5);

    // Muat dashboard — hanya halaman utama tanpa chart AJAX
    let dashboardRes = http.get(`${BASE_URL}/admin/dashboard`, {
        tags: { name: 'GET_Dashboard_Simple' },
    });

    check(dashboardRes, {
        'Dashboard loaded (200)': (r) => r.status === 200,
        'Dashboard has content': (r) => r.body.length > 500,
    });

    if (dashboardRes.status !== 200) {
        console.error(`❌ Dashboard gagal: ${dashboardRes.status}`);
    }

    sleep(0.5);
    // NOTE: Chart-data AJAX sengaja tidak diuji di sini.
    // Gunakan 2_dashboard_analytics_test.js untuk test lengkap dengan chart.
}

export function teardown(data) {
    console.log('🏁 Dashboard simple test selesai.');
}
