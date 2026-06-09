import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

export const options = {
    stages: [
        { duration: '10s', target: 10 },
        { duration: '30s', target: 10 },
        { duration: '10s', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<1000'], // Relaxed for logs (heavy queries)
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    let authData = loginAdmin();
    if (!authData) {
        console.error("Authentication failed, skipping iteration");
        return;
    }
    sleep(1);

    const endpoints = [
        { url: '/admin/reviews', needsRole: 'admin,moderator,super_admin' },
        { url: '/admin/reports', needsRole: 'moderator,admin,super_admin' },
        { url: '/admin/users', needsRole: 'admin,super_admin' },
        { url: '/admin/recommendations', needsRole: 'admin,super_admin' }, // Heavy MongoDB table
        { url: '/admin/chatbot-logs', needsRole: 'admin,moderator,super_admin' }, // Heavy MongoDB table
        // Note: /admin/settings/audit-logs requires super_admin role specifically
        // Only test if using super_admin credentials
        { url: '/admin/settings/audit-logs', needsRole: 'super_admin' },
    ];

    for (let endpoint of endpoints) {
        let res = http.get(`${BASE_URL}${endpoint.url}`);
        
        let expectedStatus = 200;
        // If using non-super_admin and endpoint needs super_admin, expect 403 or redirect
        if (endpoint.needsRole === 'super_admin' && endpoint.url.includes('settings/audit-logs')) {
            // This might return 403 or redirect to permission-denied
            expectedStatus = [200, 403].includes(res.status) ? res.status : 200;
        }
        
        check(res, {
            [`${endpoint.url} loaded (status 200 or 403)`]: (r) => r.status === 200 || r.status === 403,
            [`${endpoint.url} has content`]: (r) => r.body.length > 500,
        });
        
        // Log failures for debugging
        if (res.status !== 200 && res.status !== 403) {
            console.error(`Failed to load ${endpoint.url}: Status ${res.status}`);
        }
        
        sleep(1); // Simulate time reading the logs/tables before clicking the next menu
    }
}
