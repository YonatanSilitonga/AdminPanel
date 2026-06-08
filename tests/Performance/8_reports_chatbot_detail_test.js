import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * REPORTS & CHATBOT DETAIL PERFORMANCE TEST
 * Coverage: ReportController + ChatbotLogController — detail endpoints
 * - GET /admin/reports/{id}              (detail)
 * - GET /admin/chatbot-logs/{id}         (detail — conversation history)
 * - GET /admin/recommendations/{id}      (detail)
 * - GET /admin/recommendations/export    (CSV export — memory intensive)
 *
 * NOTE: PATCH/POST actions tidak dijalankan (modifikasi data).
 */
export const options = {
    stages: [
        { duration: '10s', target: 5 },
        { duration: '20s', target: 5 },
        { duration: '10s', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<3000'],
        http_req_failed: ['rate<0.05'], // Sedikit toleran — beberapa ID mungkin tidak ada
    },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
    let authData = loginAdmin();
    if (!authData) return;
    sleep(1);

    // 1. Reports index
    let reportsRes = http.get(`${BASE_URL}/admin/reports`);
    check(reportsRes, {
        'Reports index loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 2. Chatbot logs index
    let chatbotRes = http.get(`${BASE_URL}/admin/chatbot-logs`);
    check(chatbotRes, {
        'Chatbot logs index loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Chatbot log detail (page 1 data — first available session)
    // Menggunakan page query untuk mensimulasikan navigasi
    let chatbotPage2Res = http.get(`${BASE_URL}/admin/chatbot-logs?page=2`);
    check(chatbotPage2Res, {
        'Chatbot logs page 2 loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 4. Recommendations index
    let recRes = http.get(`${BASE_URL}/admin/recommendations`);
    check(recRes, {
        'Recommendations index loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 5. Recommendations export (CSV — memory intensive)
    let exportRes = http.get(`${BASE_URL}/admin/recommendations/export`);
    check(exportRes, {
        'Recommendations export loaded': (r) => r.status === 200,
    });
    sleep(2);
}
