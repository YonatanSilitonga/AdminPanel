import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    stages: [
        { duration: '10s', target: 10 }, // Ramp up to 10 users
        { duration: '20s', target: 10 }, // Stay at 10 users for 20 seconds
        { duration: '10s', target: 0 },  // Ramp down to 0 users
    ],
    thresholds: {
        http_req_duration: ['p(95)<1000'], // Relaxed: 95% of requests should be below 1s
        http_req_failed: ['rate<0.01'],    // Error rate should be less than 1%
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    // 1. Test GET /admin/login (Public Page Load)
    let getRes = http.get(`${BASE_URL}/admin/login`, {
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        }
    });
    
    check(getRes, {
        'GET login status is 200': (r) => r.status === 200,
        'Page contains login form': (r) => r.body.includes('name="_token"'),
    });

    // 2. Extract CSRF token - try multiple patterns
    let csrfToken = '';
    
    // Pattern 1: name="_token" value="..."
    let csrfMatch = getRes.body.match(/name="_token"\s+value="([^"]+)"/);
    
    if (csrfMatch && csrfMatch.length > 1) {
        csrfToken = csrfMatch[1];
    } else {
        // Pattern 2: value="..." name="_token"
        csrfMatch = getRes.body.match(/value="([^"]+)"\s+name="_token"/);
        if (csrfMatch && csrfMatch.length > 1) {
            csrfToken = csrfMatch[1];
        }
    }

    if (!csrfToken) {
        console.error("Could not extract CSRF token!");
        return; // Skip this iteration
    }

    // Simulate think time before submitting form
    sleep(1);

    // 3. Test POST /admin/login (Authentication Load)
    // Using the same credentials as seeder will create
    let loginData = {
        _token: csrfToken,
        email: 'superadmin@smarttourism.local',
        password: 'SuperAdmin@123',
    };

    let postRes = http.post(`${BASE_URL}/admin/login`, loginData, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Referer': `${BASE_URL}/admin/login`,
        },
        redirects: 5, // Explicitly follow redirects
    });

    // Debug failed logins
    if (postRes.status !== 200 || !postRes.url.includes('/admin/dashboard')) {
        console.error(`Login failed: Status ${postRes.status}, URL: ${postRes.url}`);
    }

    check(postRes, {
        // Because k6 follows redirects, a successful login will redirect to /admin/dashboard (status 200)
        'POST login successful (redirects to dashboard)': (r) => r.status === 200 && r.url.includes('/admin/dashboard'),
    });

    sleep(1);
}
