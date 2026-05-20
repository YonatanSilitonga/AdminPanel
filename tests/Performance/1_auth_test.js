import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    stages: [
        { duration: '10s', target: 10 }, // Ramp up to 10 users
        { duration: '20s', target: 10 }, // Stay at 10 users for 20 seconds
        { duration: '10s', target: 0 },  // Ramp down to 0 users
    ],
    thresholds: {
        http_req_duration: ['p(95)<500'], // 95% of requests should be below 500ms
        http_req_failed: ['rate<0.01'],   // Error rate should be less than 1%
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    // 1. Test GET /admin/login (Public Page Load)
    let getRes = http.get(`${BASE_URL}/admin/login`);
    
    check(getRes, {
        'GET login status is 200': (r) => r.status === 200,
        'Page contains login form': (r) => r.body.includes('name="_token"'),
    });

    // 2. Extract CSRF token
    let csrfMatch = getRes.body.match(/name="_token" value="(.*?)"/);
    let csrfToken = csrfMatch ? csrfMatch[1] : '';

    // Simulate think time before submitting form
    sleep(1);

    // 3. Test POST /admin/login (Authentication Load)
    // Using a valid dummy user to ensure DB queries and session generation are tested
    let loginData = {
        _token: csrfToken,
        email: 'superadmin@smarttourism.local',
        password: 'SuperAdmin@123',
    };

    let postRes = http.post(`${BASE_URL}/admin/login`, loginData);

    check(postRes, {
        // Because k6 follows redirects, a successful login will redirect to /admin/dashboard (status 200)
        'POST login successful (redirects to dashboard)': (r) => r.status === 200 && r.url.includes('/admin/dashboard'),
    });

    sleep(1);
}
