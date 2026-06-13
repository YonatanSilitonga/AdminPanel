# 🧪 K6 Performance Testing Methodology

## 📚 Table of Contents
1. [Kenapa K6?](#kenapa-k6)
2. [Testing Strategy](#testing-strategy)
3. [Test Types](#test-types)
4. [Load Patterns](#load-patterns)
5. [Metrics & Thresholds](#metrics--thresholds)
6. [Best Practices](#best-practices)
7. [Implementation Guide](#implementation-guide)

---

## 🎯 Kenapa K6?

### Keunggulan K6 untuk Project Ini:

1. **JavaScript-based** - Developer friendly (sudah pakai JS/Node)
2. **CLI-first** - Easy integration dengan CI/CD
3. **Realistic load simulation** - VU (Virtual Users) yang mirip user asli
4. **Rich metrics** - Built-in metrics + custom metrics
5. **Cloud-ready** - Bisa scale ke K6 Cloud untuk load lebih besar
6. **Open source** - Gratis & community support bagus

### Alternatif (Kenapa Tidak Dipilih):

| Tool | Pros | Cons | Why Not? |
|------|------|------|----------|
| **JMeter** | Mature, GUI | Heavy, XML config | Too complex for team JS |
| **Gatling** | Scala-based, powerful | Learning curve | Not JS-friendly |
| **Artillery** | JS-based, YAML | Less features | K6 lebih powerful |
| **Locust** | Python-based | Need Python setup | Team prefer JS |

---

## 🎯 Testing Strategy

### 1. **Pyramid Testing Approach**

```
        ┌─────────────────┐
        │  Stress Tests   │  ← Find limits (200+ VUs)
        │   (5% time)     │
        ├─────────────────┤
        │   Load Tests    │  ← Normal traffic (10-50 VUs)
        │   (30% time)    │
        ├─────────────────┤
        │  Smoke Tests    │  ← Quick validation (1 VU)
        │   (65% time)    │
        └─────────────────┘
```

**Rationale:**
- Smoke tests: Run setiap PR/commit (fast feedback)
- Load tests: Run daily/nightly (catch regressions)
- Stress tests: Run weekly/pre-release (capacity planning)

### 2. **Progressive Testing**

```
Week 1: Baseline
  └─ Run smoke tests → Document current performance
  
Week 2: Load Testing
  └─ Run load tests → Identify bottlenecks
  
Week 3: Optimization
  └─ Fix issues → Re-test → Compare improvements
  
Week 4: Stress Testing
  └─ Push limits → Capacity planning
```

### 3. **Risk-Based Testing Priority**

| Priority | Criteria | Examples |
|----------|----------|----------|
| **P0** | User-facing, high traffic, $$$ impact | Login, Dashboard, Destinations |
| **P1** | Critical business logic | Reviews, Reports, Analytics |
| **P2** | Important but lower traffic | Settings, Audit Logs |
| **P3** | Nice to have | Profile, Minor features |

---

## 🧪 Test Types

### 1. **Smoke Test**
**Purpose:** Quick sanity check - "Does it work at all?"

**Configuration:**
```javascript
export const options = {
    vus: 1,              // Only 1 user
    iterations: 1,       // Run once per endpoint
    thresholds: {
        http_req_failed: ['rate<0.01'],  // Must not error
    },
};
```

**When to run:**
- Before every test session (warmup)
- After deployment
- As part of health check
- In CI/CD pipeline

**What to test:**
- All critical endpoints return 200
- Authentication works
- Database connection OK

---

### 2. **Load Test**
**Purpose:** Simulate normal production traffic

**Configuration:**
```javascript
export const options = {
    stages: [
        { duration: '2m', target: 10 },   // Ramp up slowly
        { duration: '5m', target: 10 },   // Normal traffic (10 users)
        { duration: '2m', target: 30 },   // Peak hours (30 users)
        { duration: '5m', target: 30 },   // Sustain peak
        { duration: '2m', target: 0 },    // Ramp down
    ],
    thresholds: {
        http_req_duration: ['p(95)<500'],
        http_req_failed: ['rate<0.01'],
    },
};
```

**When to run:**
- Daily (nightly builds)
- Before major releases
- After performance changes

**What to test:**
- Read operations (index, show)
- Write operations (create, update, delete)
- Mixed workloads (realistic user flows)

---

### 3. **Stress Test**
**Purpose:** Find breaking point - "When does it crash?"

**Configuration:**
```javascript
export const options = {
    stages: [
        { duration: '2m', target: 50 },   // Warm up
        { duration: '5m', target: 100 },  // Push harder
        { duration: '3m', target: 200 },  // Push to limits
        { duration: '5m', target: 200 },  // Can it survive?
        { duration: '3m', target: 300 },  // Break it!
        { duration: '5m', target: 0 },    // Recovery time
    ],
    thresholds: {
        http_req_duration: ['p(95)<5000'],  // Allow degradation
        http_req_failed: ['rate<0.1'],      // Allow more errors
    },
};
```

**When to run:**
- Weekly
- Capacity planning
- Pre-launch testing

**What to measure:**
- Maximum VUs supported
- Error rate at breaking point
- Recovery time
- Degradation curve

---

### 4. **Spike Test**
**Purpose:** Sudden traffic burst - "Can it handle Reddit hug of death?"

**Configuration:**
```javascript
export const options = {
    stages: [
        { duration: '1m', target: 10 },   // Normal
        { duration: '30s', target: 200 }, // SUDDEN SPIKE! 🚀
        { duration: '3m', target: 200 },  // Sustain spike
        { duration: '1m', target: 10 },   // Back to normal
        { duration: '1m', target: 0 },    // Cool down
    ],
};
```

**When to run:**
- Before marketing campaigns
- Pre-viral content testing
- Black Friday prep

**Real-world scenario:**
- Influencer posts about your app
- News article goes viral
- Promo campaign launch

---

### 5. **Soak Test** (Endurance Test)
**Purpose:** Memory leaks & long-term stability - "Does it leak memory?"

**Configuration:**
```javascript
export const options = {
    stages: [
        { duration: '5m', target: 20 },    // Ramp up
        { duration: '60m', target: 20 },   // Soak for 1 hour
        { duration: '5m', target: 0 },     // Ramp down
    ],
};
```

**When to run:**
- Weekly
- After memory-related changes
- Before long-running deployments

**What to monitor:**
- Memory usage over time (should be flat, not climbing)
- Database connections (should not leak)
- Response time degradation
- Error rate increase

---

### 6. **Breakpoint Test**
**Purpose:** Find maximum capacity - "What's the absolute limit?"

**Configuration:**
```javascript
export const options = {
    executor: 'ramping-arrival-rate',
    startRate: 10,          // Start with 10 req/s
    timeUnit: '1s',
    preAllocatedVUs: 500,
    maxVUs: 1000,
    stages: [
        { duration: '5m', target: 50 },    // 50 req/s
        { duration: '5m', target: 100 },   // 100 req/s
        { duration: '5m', target: 200 },   // 200 req/s
        { duration: '5m', target: 400 },   // Until it breaks!
    ],
};
```

**When to run:**
- Capacity planning
- Infrastructure scaling decisions

---

## 📊 Load Patterns

### Pattern 1: **Stepped Load** (Paling Common)
```
Users
  200│              ┌────────┐
     │              │        │
  100│       ┌──────┤        │
     │       │      │        │
   50│  ┌────┤      │        │
     │  │    │      │        │
    0└──┴────┴──────┴────────┴───> Time
      2m   5m    3m    5m   2m
```

**Use for:** Understanding system behavior at different loads

### Pattern 2: **Ramp-Up** (Gradual)
```
Users
  100│                    ┌──────┐
     │                   /        \
   50│                 /            \
     │               /                \
    0└─────────────/──────────────────\──> Time
                 5m      10m         5m
```

**Use for:** Realistic user growth, warmup

### Pattern 3: **Spike** (Sudden)
```
Users
  200│         ┌──┐
     │         │  │
  100│         │  │
     │  ───────┘  └────────
    0└───────────────────────> Time
      1m  30s 3m  1m
```

**Use for:** Marketing campaigns, viral events

### Pattern 4: **Wave** (Periodic)
```
Users
  100│   ┌─┐   ┌─┐   ┌─┐
     │  /   \ /   \ /   \
   50│ /     X     X     \
     │/       \   /       \
    0└─────────────────────> Time
```

**Use for:** Daily patterns (lunch hours, evening peak)

---

## 📏 Metrics & Thresholds

### Core Metrics

| Metric | Description | Good Threshold |
|--------|-------------|----------------|
| **http_req_duration** | Total request time | p(95) < 500ms |
| **http_req_waiting** | Time to first byte (TTFB) | p(95) < 300ms |
| **http_req_connecting** | TCP connection time | p(95) < 100ms |
| **http_req_tls_handshaking** | TLS handshake time | p(95) < 200ms |
| **http_req_sending** | Time sending request | p(95) < 50ms |
| **http_req_receiving** | Time receiving response | p(95) < 100ms |
| **http_req_failed** | Failed requests rate | < 1% |
| **http_reqs** | Total requests | Baseline |
| **iterations** | Test iterations completed | Baseline |
| **iteration_duration** | Full iteration time | Context-dependent |
| **vus** | Active virtual users | Matches stages |
| **data_sent** | Data transmitted | Monitor bandwidth |
| **data_received** | Data received | Monitor payload size |

### Percentile Explanation

```
p(50) = Median    → 50% of requests are faster than this
p(90)             → 90% of requests are faster than this
p(95)             → 95% of requests are faster than this  ← MOST IMPORTANT!
p(99)             → 99% of requests are faster than this
max               → Slowest request
```

**Why p(95)?**
- p(50) hides outliers (too optimistic)
- p(99) too sensitive to spikes
- **p(95) balances: most users (95%) happy, but acknowledges some slow requests**

### Custom Thresholds by Operation Type

#### Read Operations (GET)
```javascript
thresholds: {
    'http_req_duration{operation:read}': ['p(95)<500'],     // 500ms
    'http_req_duration{operation:read}': ['p(99)<1000'],    // 1s
    'http_req_failed{operation:read}': ['rate<0.01'],       // <1% errors
}
```

#### Write Operations (POST/PUT/DELETE)
```javascript
thresholds: {
    'http_req_duration{operation:write}': ['p(95)<1000'],   // 1s
    'http_req_duration{operation:write}': ['p(99)<2000'],   // 2s
    'http_req_failed{operation:write}': ['rate<0.05'],      // <5% errors
}
```

#### Heavy Operations (AI, Export, Upload)
```javascript
thresholds: {
    'http_req_duration{operation:heavy}': ['p(95)<5000'],   // 5s
    'http_req_duration{operation:heavy}': ['p(99)<10000'],  // 10s
    'http_req_failed{operation:heavy}': ['rate<0.1'],       // <10% errors
}
```

### Tagging Requests

```javascript
// Tag by operation type
http.get(`${BASE_URL}/admin/destinations`, {
    tags: { name: 'destinations_index', operation: 'read' }
});

http.post(`${BASE_URL}/admin/destinations`, data, {
    tags: { name: 'destinations_create', operation: 'write' }
});

http.post(`${BASE_URL}/admin/reviews/analyze-batch`, data, {
    tags: { name: 'reviews_ai_batch', operation: 'heavy' }
});
```

---

## ✅ Best Practices

### 1. **Test Data Management**

#### ❌ BAD: Test on empty database
```javascript
// Empty DB = unrealistic fast queries!
http.get(`${BASE_URL}/admin/reviews`); // Returns 0 rows (fast!)
```

#### ✅ GOOD: Test with realistic data volume
```bash
# Seed database with realistic data
php artisan db:seed --class=PerformanceTestSeeder

# Data volumes:
# - 1000+ destinations
# - 10,000+ reviews
# - 50,000+ recommendation logs
# - 100,000+ chatbot logs
```

### 2. **Session Management**

#### ❌ BAD: Login on every iteration
```javascript
export default function () {
    loginAdmin();  // SLOW! Login setiap kali
    http.get(`${BASE_URL}/admin/dashboard`);
}
```

#### ✅ GOOD: Login once per VU
```javascript
export function setup() {
    // Login once for all VUs
    return loginAdmin();
}

export default function (authData) {
    // Reuse session
    http.get(`${BASE_URL}/admin/dashboard`, {
        cookies: authData.cookies
    });
}
```

### 3. **Think Time** (User Pause)

#### ❌ BAD: No sleep (unrealistic)
```javascript
export default function () {
    http.get('/page1');
    http.get('/page2');  // INSTANT click? Not realistic!
    http.get('/page3');
}
```

#### ✅ GOOD: Add realistic think time
```javascript
import { sleep } from 'k6';

export default function () {
    http.get('/page1');
    sleep(2);  // User reads page for 2 seconds
    
    http.get('/page2');
    sleep(3);  // User reads for 3 seconds
    
    http.get('/page3');
}
```

**Realistic think times:**
- Reading index/list: 2-5 seconds
- Reading detail page: 5-10 seconds
- Filling form: 10-30 seconds
- After submit: 1-2 seconds

### 4. **Random Data**

#### ❌ BAD: Same data every time
```javascript
http.get(`${BASE_URL}/admin/destinations/1`); // Always ID 1!
```

#### ✅ GOOD: Random realistic data
```javascript
import { SharedArray } from 'k6/data';

const destinationIds = new SharedArray('ids', function () {
    return [1, 2, 3, 5, 8, 13, 21, 34, 55, 89]; // Example IDs
});

export default function () {
    const randomId = destinationIds[Math.floor(Math.random() * destinationIds.length)];
    http.get(`${BASE_URL}/admin/destinations/${randomId}`);
}
```

### 5. **Error Handling**

#### ❌ BAD: Ignore errors
```javascript
http.get(`${BASE_URL}/admin/dashboard`);
// No check! Silent failure
```

#### ✅ GOOD: Check & log errors
```javascript
import { check } from 'k6';

let res = http.get(`${BASE_URL}/admin/dashboard`);

if (!check(res, {
    'status is 200': (r) => r.status === 200,
    'has content': (r) => r.body.length > 1000,
})) {
    console.error(`Dashboard load failed: ${res.status} - ${res.body.substring(0, 100)}`);
}
```

### 6. **Performance Monitoring During Test**

```bash
# Terminal 1: Run K6 test
k6 run tests/Performance/5_destinations_test.js

# Terminal 2: Monitor Laravel logs
php artisan tail

# Terminal 3: Monitor system resources
# Windows PowerShell:
while ($true) { 
    Get-Counter '\Processor(_Total)\% Processor Time', 
                '\Memory\Available MBytes' 
    Start-Sleep -Seconds 2 
}

# Terminal 4: Monitor MySQL slow queries
mysql> SET GLOBAL slow_query_log = 'ON';
mysql> SET GLOBAL long_query_time = 0.1;  -- Log queries > 100ms
mysql> TAIL -f /path/to/mysql-slow.log
```

---

## 🚀 Implementation Guide

### Step 1: Install K6

```bash
# Windows (Chocolatey)
choco install k6

# Or download from: https://k6.io/docs/getting-started/installation/
```

### Step 2: Prepare Test Environment

```bash
# 1. Start Laravel server
php artisan serve

# 2. Seed test data
php artisan db:seed --class=PerformanceTestSeeder

# 3. Clear cache
php artisan cache:clear

# 4. Enable query log (temporary)
php artisan config:set database.log_queries true
```

### Step 3: Run First Test (Smoke)

```bash
k6 run tests/Performance/1_auth_test.js
```

### Step 4: Analyze Results

```bash
# Export to JSON for detailed analysis
k6 run --out json=results.json tests/Performance/1_auth_test.js

# Or HTML report (requires k6-reporter)
k6 run --out json=results.json tests/Performance/1_auth_test.js
# Then convert to HTML with k6-reporter
```

### Step 5: Compare Results Over Time

```bash
# Baseline (before optimization)
k6 run tests/Performance/5_destinations_test.js > baseline.txt

# After optimization
k6 run tests/Performance/5_destinations_test.js > after_optimization.txt

# Compare
diff baseline.txt after_optimization.txt
```

---

## 📈 Reporting

### What to Report

1. **Executive Summary**
   - Pass/Fail status
   - Key metrics (p95 response time)
   - Capacity (max concurrent users)
   - Bottlenecks identified

2. **Detailed Metrics**
   - Response time percentiles
   - Error rates
   - Throughput
   - Resource utilization

3. **Trends**
   - Performance over time
   - Before/after optimizations
   - Capacity growth

4. **Recommendations**
   - Performance issues
   - Scalability concerns
   - Optimization opportunities

### Sample Report Structure

```markdown
# Performance Test Report - 2026-06-08

## Summary
✅ PASS - System meets performance targets

## Key Metrics
- Max VUs supported: 50 (target: 50)
- p(95) response time: 420ms (target: <500ms)
- Error rate: 0.3% (target: <1%)
- Throughput: 87 req/s

## Bottlenecks Identified
1. Dashboard chart-data endpoint (84 DB queries)
2. Review batch analysis (Gemini API calls)
3. MongoDB pagination (no indexing)

## Recommendations
1. Add eager loading to dashboard query
2. Implement queue for batch AI processing
3. Add MongoDB indexes for logs
```

---

## 🔗 References

- [K6 Documentation](https://k6.io/docs/)
- [K6 Examples](https://k6.io/docs/examples/)
- [Performance Testing Guide](https://k6.io/docs/test-types/introduction/)

---

**Created**: 2026-06-08  
**Status**: Reference Guide - Ready to Use
