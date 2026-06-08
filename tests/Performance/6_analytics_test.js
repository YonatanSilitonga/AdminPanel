import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * ANALYTICS MODULE PERFORMANCE TEST
 * Coverage: AnalyticsController — semua 4 endpoints
 * - GET /admin/analytics
 * - GET /admin/analytics/destinations
 * - GET /admin/analytics/events
 * - GET /admin/analytics/reports
 * + GET /admin/dashboard/chart-data (AJAX heavy endpoint)
 */
export const options = {
    stages: [
        { duration: '10s', target: 5 },
        { duration: '20s', target: 5 },
        { duration: '10s', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<4000'], // Analytics aggregations berat, toleransi lebih tinggi
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    let authData = loginAdmin();
    if (!authData) return;
    sleep(1);

    // 1. Dashboard chart data (AJAX — heavy 84 DB calls)
    let chartRes = http.get(`${BASE_URL}/admin/dashboard/chart-data`);
    check(chartRes, {
        'Chart data loaded': (r) => r.status === 200,
        'Chart data is valid JSON': (r) => {
            try { JSON.parse(r.body); return true; } catch(e) { return false; }
        },
    });
    sleep(2);

    // 2. Analytics dashboard (aggregations)
    let analyticsRes = http.get(`${BASE_URL}/admin/analytics`);
    check(analyticsRes, {
        'Analytics dashboard loaded': (r) => r.status === 200,
        'Analytics has content': (r) => r.body.length > 500,
    });
    sleep(2);

    // 3. Destinations analytics
    let destAnalyticsRes = http.get(`${BASE_URL}/admin/analytics/destinations`);
    check(destAnalyticsRes, {
        'Destinations analytics loaded': (r) => r.status === 200,
    });
    sleep(2);

    // 4. Events analytics
    let eventAnalyticsRes = http.get(`${BASE_URL}/admin/analytics/events`);
    check(eventAnalyticsRes, {
        'Events analytics loaded': (r) => r.status === 200,
    });
    sleep(2);

    // 5. Reports analytics
    let reportAnalyticsRes = http.get(`${BASE_URL}/admin/analytics/reports`);
    check(reportAnalyticsRes, {
        'Reports analytics loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 6. Review summary stats (aggregation endpoint)
    let statsRes = http.get(`${BASE_URL}/admin/reviews/summary/stats`);
    check(statsRes, {
        'Review summary stats loaded': (r) => r.status === 200,
    });
    sleep(1);
}
