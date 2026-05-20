import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * SIMPLIFIED DASHBOARD TEST
 * 
 * Tests only the dashboard index (without heavy chart-data AJAX)
 * to identify performance issues more clearly
 */
export const options = {
    stages: [
        { duration: '10s', target: 5 },   // Ramp up to 5 users
        { duration: '20s', target: 5 },   // Stay at 5 users for 20 seconds
        { duration: '10s', target: 0 },   // Ramp down to 0 users
    ],
    thresholds: {
        http_req_duration: ['p(95)<2000'], // Target: dashboard loads in < 2 seconds
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    // 1. Authenticate user
    loginAdmin();
    sleep(0.5);

    // 2. Load Main Dashboard (without heavy chart data)
    let dashboardRes = http.get(`${BASE_URL}/admin/dashboard`);
    check(dashboardRes, {
        'Dashboard loaded (status 200)': (r) => r.status === 200,
        'Dashboard has content': (r) => r.body.length > 500,
    });
    sleep(0.5);

    // NOTE: Chart data AJAX disabled for now due to performance issues (84 DB calls)
    // Will be re-enabled after optimization
}
