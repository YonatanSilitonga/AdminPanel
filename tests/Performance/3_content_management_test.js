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
        http_req_duration: ['p(95)<600'], 
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

    // Test all content management endpoints
    const endpoints = [
        '/admin/destinations',           // ✅ Destinations list
        '/admin/events',                 // ✅ Events list
        '/admin/budaya',                 // ✅ Budaya (Culture) list
        '/admin/fasilitas-umum',         // ✅ FIXED: pakai tanda hubung sesuai route
        '/admin/berita-promosi',         // ✅ FIXED: pakai tanda hubung sesuai route
        // Removed non-existent endpoints:
        // '/admin/carousel-banners',     // ❌ Not in routes
        // '/admin/search?q=pantai'       // ❌ GlobalSearchController not registered
    ];

    for (let endpoint of endpoints) {
        let res = http.get(`${BASE_URL}${endpoint}`);
        check(res, {
            [`${endpoint} loaded (status 200)`]: (r) => r.status === 200,
            [`${endpoint} has content`]: (r) => r.body.length > 500,
        });
        
        // Log failures for debugging
        if (res.status !== 200) {
            console.error(`Failed to load ${endpoint}: Status ${res.status}`);
        }
        
        sleep(1); // Think time between loading different CMS modules
    }
}
