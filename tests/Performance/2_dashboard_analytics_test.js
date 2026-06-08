import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

export const options = {
    stages: [
        { duration: '10s', target: 10 }, // Ramp up to 10 users
        { duration: '30s', target: 10 }, // Stay at 10 users for 30 seconds
        { duration: '10s', target: 0 },  // Ramp down to 0 users
    ],
    thresholds: {
        http_req_duration: ['p(95)<3000'], // Temporary threshold while optimizing (3 seconds)
        http_req_failed: ['rate<0.01'],    // Error rate still strict
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    // 1. Authenticate user (this sets the session cookies for this VU)
    let authData = loginAdmin();
    if (!authData) {
        console.error("Authentication failed, skipping iteration");
        return;
    }
    sleep(1);

    // 2. Load Main Dashboard
    let dashboardRes = http.get(`${BASE_URL}/admin/dashboard`);
    check(dashboardRes, {
        'Dashboard loaded (status 200)': (r) => r.status === 200,
    });
    
    if (dashboardRes.status !== 200) {
        console.error(`Dashboard load failed: Status ${dashboardRes.status}`);
    }
    sleep(1);

    // 3. Load Dashboard Chart Data (AJAX endpoint)
    let chartRes = http.get(`${BASE_URL}/admin/dashboard/chart-data`);
    check(chartRes, {
        'Chart data loaded (status 200)': (r) => r.status === 200,
        'Chart data is JSON': (r) => {
            try {
                JSON.parse(r.body);
                return true;
            } catch (e) {
                return false;
            }
        },
    });
    
    if (chartRes.status !== 200) {
        console.error(`Chart data load failed: Status ${chartRes.status}`);
    }
    sleep(1);

    // TODO: Implement AnalyticsController with these methods:
    // - dashboard()
    // - destinations()
    // - events()
    // - reports()
    // 
    // Currently disabled because endpoints are not implemented yet.
    // Uncomment these when AnalyticsController is ready:
    //
    // // 4. Load Main Analytics Page
    // let analyticsRes = http.get(`${BASE_URL}/admin/analytics`);
    // check(analyticsRes, {
    //     'Analytics page loaded (status 200)': (r) => r.status === 200,
    // });
    // sleep(1);
    //
    // // 5. Load Destinations Analytics Page
    // let destAnalyticsRes = http.get(`${BASE_URL}/admin/analytics/destinations`);
    // check(destAnalyticsRes, {
    //     'Destinations Analytics loaded (status 200)': (r) => r.status === 200,
    // });
    // sleep(1);
    //
    // // 6. Load Events Analytics Page
    // let eventAnalyticsRes = http.get(`${BASE_URL}/admin/analytics/events`);
    // check(eventAnalyticsRes, {
    //     'Events Analytics loaded (status 200)': (r) => r.status === 200,
    // });
    // sleep(1);
}
