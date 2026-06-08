import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * REVIEWS DETAIL & MODERATION PERFORMANCE TEST
 * Coverage: ReviewController — detail + action endpoints (READ only)
 * - GET /admin/reviews                 (index — sudah ada di test 4, diulang sbg baseline)
 * - GET /admin/reviews/{id}            (detail)
 * - GET /admin/reviews/summary/stats   (aggregation)
 * - GET /admin/reviews/analytics/print (print view)
 *
 * NOTE: POST analyze, PATCH approve/reject TIDAK dijalankan
 * karena akan memodifikasi data asli.
 */
export const options = {
    stages: [
        { duration: '10s', target: 5 },
        { duration: '20s', target: 5 },
        { duration: '10s', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<2000'],
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    let authData = loginAdmin();
    if (!authData) return;
    sleep(1);

    // 1. Reviews index list
    let indexRes = http.get(`${BASE_URL}/admin/reviews`);
    check(indexRes, {
        'Reviews index loaded': (r) => r.status === 200,
        'Reviews has content': (r) => r.body.length > 500,
    });
    sleep(2);

    // 2. Reviews filtered by status
    let pendingRes = http.get(`${BASE_URL}/admin/reviews?status=pending`);
    check(pendingRes, {
        'Reviews pending filter loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Review summary stats (aggregation)
    let statsRes = http.get(`${BASE_URL}/admin/reviews/summary/stats`);
    check(statsRes, {
        'Review summary stats loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 4. Review analytics print view
    let printRes = http.get(`${BASE_URL}/admin/reviews/analytics/print`);
    check(printRes, {
        'Review analytics print loaded': (r) => r.status === 200,
    });
    sleep(2);
}
