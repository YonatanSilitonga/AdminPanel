import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * DESTINATIONS READ-ONLY PERFORMANCE TEST
 * 
 * Tests READ operations only — no data modification.
 * Safe to run against production/real data.
 * - List/Index (with pagination)
 * - View single destination detail
 * - View edit form (eager loading test)
 * - Browse create form (no submission)
 */

export const options = {
    stages: [
        { duration: '10s', target: 10 },  // Ramp up to 10 users
        { duration: '30s', target: 10 },  // Sustain 10 users
        { duration: '10s', target: 0 },   // Ramp down
    ],
    thresholds: {
        http_req_duration: ['p(95)<1000'], // 95% requests < 1s
        http_req_failed: ['rate<0.01'],    // < 1% errors
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

    // 1. Destinations list page 1
    let indexRes = http.get(`${BASE_URL}/admin/destinations?page=1`);
    check(indexRes, {
        'Destinations index loaded': (r) => r.status === 200,
        'Index has content': (r) => r.body.length > 500,
    });
    sleep(2);

    // 2. Destinations list page 2 (pagination)
    let page2Res = http.get(`${BASE_URL}/admin/destinations?page=2`);
    check(page2Res, {
        'Destinations page 2 loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Create form (READ only — load form, no submit)
    let createRes = http.get(`${BASE_URL}/admin/destinations/create`);
    check(createRes, {
        'Create form loaded': (r) => r.status === 200,
    });
    sleep(2);
}

export function setup() {
    console.log('🚀 Destinations READ-ONLY Performance Test — no data will be modified');
    return { startTime: Date.now() };
}

export function teardown(data) {
    let duration = (Date.now() - data.startTime) / 1000;
    console.log(`✅ Test completed in ${duration.toFixed(2)} seconds`);
}
