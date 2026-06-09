import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * USERS MANAGEMENT PERFORMANCE TEST
 * Coverage: UserController
 * - GET /admin/users                    (index + filter)
 * - GET /admin/users/{id}/activity      (activity log — heavy query)
 *
 * NOTE: PATCH /users/{id}/status tidak dijalankan (modifikasi data).
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

    // 1. Users index
    let usersRes = http.get(`${BASE_URL}/admin/users`);
    check(usersRes, {
        'Users index loaded': (r) => r.status === 200,
        'Users has content': (r) => r.body.length > 500,
    });
    sleep(2);

    // 2. Users index dengan filter status
    let activeUsersRes = http.get(`${BASE_URL}/admin/users?status=active`);
    check(activeUsersRes, {
        'Users active filter loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Users search
    let searchRes = http.get(`${BASE_URL}/admin/users?search=a`);
    check(searchRes, {
        'Users search loaded': (r) => r.status === 200,
    });
    sleep(2);

    // 4. Users page 2
    let page2Res = http.get(`${BASE_URL}/admin/users?page=2`);
    check(page2Res, {
        'Users page 2 loaded': (r) => r.status === 200,
    });
    sleep(1);
}
