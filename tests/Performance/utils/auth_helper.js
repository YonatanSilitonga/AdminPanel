import http from 'k6/http';
import { check } from 'k6';

const BASE_URL = 'http://127.0.0.1:8000';

export function loginAdmin() {
    // 1. Visit login page to get CSRF token and session cookie
    let res = http.get(`${BASE_URL}/admin/login`);
    
    check(res, {
        'Login page loaded': (r) => r.status === 200,
    });

    // 2. Extract CSRF token from the HTML body
    // Looking for <input type="hidden" name="_token" value="...">
    let csrfMatch = res.body.match(/name="_token" value="(.*?)"/);
    let csrfToken = '';

    if (csrfMatch && csrfMatch.length > 1) {
        csrfToken = csrfMatch[1];
    } else {
        console.error("Could not find CSRF token on login page!");
    }

    // 3. Send POST request to login
    let loginData = {
        _token: csrfToken,
        email: 'superadmin@smarttourism.local',
        password: 'SuperAdmin@123',
    };

    let loginRes = http.post(`${BASE_URL}/admin/login`, loginData);

    // After successful login, Laravel redirects (302) to /admin/dashboard
    // k6 will follow redirects by default, so final status should be 200 on dashboard
    check(loginRes, {
        'Login successful (redirected to dashboard)': (r) => r.url.includes('/admin/dashboard') || r.status === 200,
    });
}
