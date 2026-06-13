# 📋 JENIS-JENIS TESTING NON-FUNGSIONAL

## DOKUMEN INFORMASI

| Item | Detail |
|------|--------|
| **Nama Proyek** | Admin Panel - Smart Tourism Danau Toba |
| **Tipe Dokumen** | Non-Functional Testing Types Guide |
| **Standar Referensi** | ISO/IEC 25010, ISTQB |
| **Tanggal Dibuat** | 10 Juni 2026 |
| **Status** | REFERENCE GUIDE |
| **Versi Dokumen** | 1.0 |

---

## DAFTAR ISI

1. [Pengantar](#1-pengantar)
2. [Performance Testing](#2-performance-testing)
3. [Security Testing](#3-security-testing)
4. [Usability Testing](#4-usability-testing)
5. [Compatibility Testing](#5-compatibility-testing)
6. [Reliability Testing](#6-reliability-testing)
7. [Maintainability Testing](#7-maintainability-testing)
8. [Scalability Testing](#8-scalability-testing)
9. [Availability Testing](#9-availability-testing)
10. [Recovery Testing](#10-recovery-testing)

---

## 1. PENGANTAR

### 1.1 Apa itu Non-Functional Testing?

Non-Functional Testing adalah pengujian aspek-aspek sistem yang **bukan** terkait fitur fungsional,
melainkan **bagaimana sistem berperilaku** dan **kualitas sistem** secara keseluruhan.

### 1.2 Referensi Standar: ISO/IEC 25010

Berdasarkan **ISO/IEC 25010 Product Quality Model**, kualitas perangkat lunak terdiri dari:


```
ISO/IEC 25010 Quality Characteristics:

1. Functional Suitability      → Functional Testing
2. Performance Efficiency      → Performance Testing ✓ (DONE)
3. Compatibility              → Compatibility Testing
4. Usability                  → Usability Testing
5. Reliability                → Reliability Testing
6. Security                   → Security Testing
7. Maintainability            → Maintainability Testing
8. Portability                → Portability Testing
```

### 1.3 Testing Priority Matrix

| Aspek | Priority | Complexity | Duration | Resources |
|-------|----------|------------|----------|-----------|
| **Performance** | 🔴 CRITICAL | High | 4-6 weeks | QA + DevOps |
| **Security** | 🔴 CRITICAL | High | 3-4 weeks | Security Team |
| **Usability** | 🟡 HIGH | Medium | 2-3 weeks | UX + QA |
| **Reliability** | 🟡 HIGH | Medium | 2-3 weeks | QA |
| **Compatibility** | 🟡 MEDIUM | Low | 1-2 weeks | QA |
| **Recovery** | 🟢 MEDIUM | Medium | 1-2 weeks | DevOps + QA |
| **Maintainability** | 🟢 LOW | Low | 1 week | Dev Team |
| **Scalability** | 🟡 HIGH | High | 2-3 weeks | DevOps + QA |

---

## 2. PERFORMANCE TESTING

**Status**: ✅ **DOCUMENTED** (See: NON_FUNCTIONAL_PERFORMANCE_TEST_REPORT.md)

### 2.1 Sub-jenis Performance Testing

| Jenis | Tujuan | Dokumen |
|-------|--------|---------|
| **Load Testing** | Menguji sistem pada beban normal | ✅ Section 8.2 |
| **Stress Testing** | Menemukan breaking point | ✅ Section 8.3 |
| **Spike Testing** | Menguji lonjakan mendadak | ✅ Section 8.4 |
| **Soak/Endurance Testing** | Deteksi memory leaks | ✅ Section 8.5 |
| **Volume Testing** | Menguji dengan data besar | ✅ Section 8.6 |
| **Scalability Testing** | Kemampuan scale up/out | ✅ Section 8.8 |

**Lihat dokumen lengkap**: `NON_FUNCTIONAL_PERFORMANCE_TEST_REPORT.md`

