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
        http_req_duration: ['p(95)<700'], 
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    loginAdmin();
    sleep(1);

    const endpoints = [
        '/admin/reviews',
        '/admin/reports',
        '/admin/users',
        '/admin/recommendations', // Heavy log table
        '/admin/chatbot-logs',    // Heavy log table
        '/admin/settings/audit-logs' // Heavy log table
    ];

    for (let endpoint of endpoints) {
        let res = http.get(`${BASE_URL}${endpoint}`);
        check(res, {
            [`${endpoint} loaded (status 200)`]: (r) => r.status === 200,
        });
        sleep(1); // Simulate time reading the logs/tables before clicking the next menu
    }
}
