import http from 'k6/http';
import { check, sleep } from 'k6';
import { loginAdmin } from './utils/auth_helper.js';

/**
 * SETTINGS & PROFILE PERFORMANCE TEST
 * Coverage: SettingsController + ProfileController + AuditLogController
 * - GET /admin/settings/general
 * - GET /admin/settings/api-keys
 * - GET /admin/settings/ai-config
 * - GET /admin/settings/audit-logs
 * - GET /admin/settings/audit-logs/{id}  (detail)
 * - GET /admin/profile
 *
 * NOTE: Semua READ only. PUT/PATCH tidak dijalankan.
 * Semua super_admin only — menggunakan credentials superadmin@smarttourism.local
 */
export const options = {
    stages: [
        { duration: '10s', target: 3 },  // Settings jarang diakses, VU lebih sedikit
        { duration: '20s', target: 3 },
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

    // 1. Profile page
    let profileRes = http.get(`${BASE_URL}/admin/profile`);
    check(profileRes, {
        'Profile page loaded': (r) => r.status === 200,
        'Profile has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // 2. Settings — General
    let generalRes = http.get(`${BASE_URL}/admin/settings/general`);
    check(generalRes, {
        'Settings general loaded': (r) => r.status === 200,
    });
    sleep(1);

    // 3. Settings — API Keys (route tidak terdaftar di aplikasi, di-skip)
    // 4. Settings — AI Config (route tidak terdaftar di aplikasi, di-skip)

    // 5. Audit Logs index (heavy query — banyak activity logs)
    let auditRes = http.get(`${BASE_URL}/admin/settings/audit-logs`);
    check(auditRes, {
        'Audit logs loaded': (r) => r.status === 200,
        'Audit logs has content': (r) => r.body.length > 500,
    });
    sleep(1);

    // 6. Audit Logs page 2 (pagination test)
    let auditPage2Res = http.get(`${BASE_URL}/admin/settings/audit-logs?page=2`);
    check(auditPage2Res, {
        'Audit logs page 2 loaded': (r) => r.status === 200,
    });
    sleep(2);
}
