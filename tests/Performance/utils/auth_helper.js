import http from 'k6/http';
import { check } from 'k6';

const BASE_URL = 'http://127.0.0.1:8000';

/**
 * Login admin dan return session cookies sebagai string
 * Digunakan untuk per-VU login (default behavior)
 * @returns {object|null}
 */
export function loginAdmin() {
    let jar = http.cookieJar();

    let res = http.get(`${BASE_URL}/admin/login`);

    if (!check(res, {
        'Login page loaded': (r) => r.status === 200,
    })) {
        console.error("Failed to load login page!");
        return null;
    }

    let csrfToken = '';
    let csrfMatch = res.body.match(/name="_token"\s+value="([^"]+)"/);
    if (csrfMatch && csrfMatch.length > 1) {
        csrfToken = csrfMatch[1];
    } else {
        csrfMatch = res.body.match(/value="([^"]+)"\s+name="_token"/);
        if (csrfMatch && csrfMatch.length > 1) {
            csrfToken = csrfMatch[1];
        }
    }

    if (!csrfToken) {
        console.error("Could not find CSRF token on login page!");
        return null;
    }

    let loginRes = http.post(`${BASE_URL}/admin/login`, {
        _token: csrfToken,
        email: 'superadmin@smarttourism.local',
        password: 'SuperAdmin@123',
    }, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Referer': `${BASE_URL}/admin/login`,
        },
        redirects: 5,
    });

    if (loginRes.status !== 200 || !loginRes.url.includes('/admin/dashboard')) {
        console.error(`Login failed! Status: ${loginRes.status}, URL: ${loginRes.url}`);
    }

    check(loginRes, {
        'Login successful (redirected to dashboard)': (r) => r.status === 200 && r.url.includes('/admin/dashboard'),
    });

    return {
        cookies: jar,
        status: loginRes.status,
        url: loginRes.url,
    };
}

/**
 * Login sekali dan return cookie string untuk di-share ke semua VU.
 * Digunakan di export function setup() pada test file.
 *
 * Cara pakai di test file:
 *   import { getSessionCookies } from './utils/auth_helper.js';
 *
 *   export function setup() {
 *       return { cookies: getSessionCookies() };
 *   }
 *
 *   export default function(data) {
 *       const headers = { Cookie: data.cookies };
 *       http.get(`${BASE_URL}/admin/dashboard`, { headers });
 *   }
 *
 * @returns {string} Cookie header string, e.g. "laravel_session=abc123; XSRF-TOKEN=xyz"
 */
export function getSessionCookies() {
    // Step 1: GET login page — dapat session cookie awal + CSRF token
    let loginPageRes = http.get(`${BASE_URL}/admin/login`);

    if (loginPageRes.status !== 200) {
        console.error(`Cannot load login page: ${loginPageRes.status}`);
        return '';
    }

    // Step 2: Extract CSRF token
    let csrfToken = '';
    let match = loginPageRes.body.match(/name="_token"\s+value="([^"]+)"/);
    if (match) {
        csrfToken = match[1];
    } else {
        match = loginPageRes.body.match(/value="([^"]+)"\s+name="_token"/);
        if (match) csrfToken = match[1];
    }

    if (!csrfToken) {
        console.error("CSRF token not found");
        return '';
    }

    // Step 3: POST login
    let loginRes = http.post(`${BASE_URL}/admin/login`, {
        _token: csrfToken,
        email: 'superadmin@smarttourism.local',
        password: 'SuperAdmin@123',
    }, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Referer': `${BASE_URL}/admin/login`,
        },
        redirects: 5,
    });

    if (!loginRes.url.includes('/admin/dashboard')) {
        console.error(`Login failed in setup: ${loginRes.status} ${loginRes.url}`);
        return '';
    }

    // Step 4: Ekstrak cookies dari cookie jar menjadi header string
    let jar = http.cookieJar();
    let cookies = jar.cookiesForURL(BASE_URL);
    let cookieHeader = Object.entries(cookies)
        .map(([name, values]) => `${name}=${values[0]}`)
        .join('; ');

    console.log(`Setup login OK — cookies extracted: ${Object.keys(cookies).length} keys`);
    return cookieHeader;
}
