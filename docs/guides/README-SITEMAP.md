# 📊 Sitemap Documentation - Monitoring & Settings Modules

## 📁 File Overview

Dokumentasi ini berisi 3 diagram PlantUML yang menjelaskan arsitektur dan struktur **Modul Monitoring** dan **Modul Settings** dalam Admin Panel:

### 1. **sitemap-monitoring-settings.puml**
**Diagram Sitemap Umum**

- 🎯 **Tujuan**: Memberikan overview struktur navigasi dan hierarki halaman
- 📋 **Isi**: 
  - Struktur menu utama
  - Relasi antar halaman
  - Flow navigasi user
  - Aksi-aksi yang tersedia
- 👥 **Target**: Project Manager, UI/UX Designer, Developer

### 2. **sitemap-monitoring-settings-detailed.puml**
**Diagram Sitemap Detail dengan Spesifikasi Lengkap**

- 🎯 **Tujuan**: Dokumentasi lengkap setiap halaman dengan detail fitur
- 📋 **Isi**:
  - Filter dan pencarian di setiap halaman
  - Permission dan role access (🔓 🔐 🛡️)
  - Field-field yang ditampilkan
  - Validasi dan business rules
  - Status workflow (misal: pending → reviewed → resolved)
  - Export dan print functions
- 👥 **Target**: Developer, QA Tester, Technical Writer

### 3. **sitemap-monitoring-settings-dataflow.puml**
**Diagram Data Flow dan Arsitektur Teknologi**

- 🎯 **Tujuan**: Menjelaskan aliran data dan layer arsitektur aplikasi
- 📋 **Isi**:
  - Presentation Layer (Blade Views)
  - Application Layer (Controllers)
  - Domain Layer (Models)
  - Data Layer (MongoDB, MySQL, Cache, File Storage)
  - External Services (Export, Upload, Logging)
  - Middleware Stack
  - Relasi antar komponen
- 👥 **Target**: Backend Developer, System Architect, DevOps

---

## 🚀 Cara Menggunakan

### Opsi 1: View Online dengan PlantUML Server

1. Buka file `.puml` di text editor
2. Copy seluruh isi file
3. Kunjungi: https://www.planttext.com/ atau https://plantuml.com/plantuml/
4. Paste kode PlantUML
5. Klik "Generate" atau "Refresh" untuk melihat diagram

### Opsi 2: VSCode Extension

1. Install extension **PlantUML** di VSCode:
   - Extension ID: `jebbs.plantuml`
   - Install Java JDK (required)
   - Install Graphviz (optional, untuk rendering lebih baik)

2. Cara render:
   ```
   - Buka file .puml
   - Tekan Alt+D (Windows/Linux) atau Option+D (Mac)
   - Atau klik kanan → Preview Current Diagram
   ```

3. Export diagram:
   ```
   - Klik kanan pada preview
   - Pilih "Export Current Diagram"
   - Format: PNG, SVG, PDF
   ```

### Opsi 3: Command Line (PlantUML JAR)

1. Download PlantUML JAR: https://plantuml.com/download

2. Generate diagram:
   ```bash
   java -jar plantuml.jar sitemap-monitoring-settings.puml
   java -jar plantuml.jar sitemap-monitoring-settings-detailed.puml
   java -jar plantuml.jar sitemap-monitoring-settings-dataflow.puml
   ```

3. Output: File PNG akan dibuat di folder yang sama

### Opsi 4: Online IDE

- **PlantText**: https://www.planttext.com/
- **PlantUML Editor**: https://plantuml-editor.kkeisuke.com/
- **LiveUML**: https://liveuml.com/

---

## 📖 Penjelasan Modul

### 🔐 MONITORING MODULE

#### **1. Analytics Dashboard** 
- **Route**: `/admin/analytics`
- **Access**: Admin + Super Admin
- **Fitur**:
  - Overview statistik sistem
  - Destination analytics
  - Event analytics
  - Report analytics

#### **2. Chatbot Logs**
- **Route**: `/admin/chatbot-logs`
- **Access**: All Admins
- **Fitur**:
  - List semua sesi chatbot
  - Filter: User/Guest, Search
  - View detail conversation
  - Export to CSV
  - Flag session (placeholder)
- **Data Source**: MongoDB `chat_sessions` collection

#### **3. Recommendation Logs**
- **Route**: `/admin/recommendations`
- **Access**: All Admins
- **Fitur**:
  - Dashboard dengan statistik:
    - Today/Week/Month logs
    - Average duration
    - Click rate
    - Popular destinations
  - View recommendation detail
  - Export to CSV
- **Data Source**: MongoDB `recommendations` collection
- **Performance**: Cache 10 menit untuk stats

#### **4. Reports (Laporan Pengaduan)**
- **Route**: `/admin/reports`
- **Access**: All Admins
- **Fitur**:
  - List laporan dengan filtering
  - Assign report ke admin
  - Update status (pending/reviewed/resolved)
  - Take action (delete_content/warn_user/ignore)
  - Delete report
  - Export CSV/Excel
  - Print official government report
- **Business Rule**: 
  - Status "resolved" = LOCKED (tidak bisa diubah)
  - Action requires reason
- **Data Source**: MongoDB `reports` collection

---

### 🛡️ SETTINGS MODULE

**Access**: Super Admin Only

#### **1. General Settings**
- **Route**: `/admin/settings/general`
- **Fitur**:
  - Upload logo & favicon
  - Set primary & secondary colors
  - Default language (ID/EN)
  - Toggle features (reviews, reports, moderation)
  - Notification preferences
  - Dark mode toggle
- **File Storage**: `public/storage/settings/`

#### **2. API Keys**
- **Route**: `/admin/settings/api-keys`
- **Fitur**: Manage external API keys dan integrations

#### **3. AI Configuration**
- **Route**: `/admin/settings/ai-config`
- **Fitur**: Configure AI model, chatbot behavior, recommendation engine

#### **4. Audit Logs**
- **Route**: `/admin/settings/audit-logs`
- **Fitur**:
  - List semua admin activities
  - Advanced filtering:
    - Action type
    - Module/Entity
    - IP address
    - Date range (today/week/month/custom)
  - View detail log dengan old/new values
  - Track IP, user agent, timestamp
- **Data Source**: MySQL `admin_activity_logs` table
- **Tracking**: ALL admin CRUD operations

---

## 🗂️ Teknologi Stack

### Database
- **MongoDB**: Logs, Reports, Recommendations, ChatSessions
  - Driver: `mongodb/laravel-mongodb`
  - Features: Eloquent ORM, Relations, Aggregation
  
- **MySQL**: Admin management, Settings, Audit logs
  - Driver: Laravel default
  - Features: Relations, Transactions, JSON columns

### Storage
- **File Storage**: Logo, favicon, uploads
- **Cache**: Redis/File untuk performance optimization

### Export
- **CSV**: UTF-8 BOM, semicolon delimiter
- **Excel**: XLS format
- **Print**: Government official format dengan letterhead

### Middleware Stack
1. `AdminMiddleware` - Verify authentication
2. `AdminRoleMiddleware` - Check permissions
3. `AdminActivityLogMiddleware` - Auto-log activities
4. `AdminErrorHandler` - Exception handling

---

## 🔑 Access Control

| Module | Route Pattern | Admin | Super Admin |
|--------|---------------|-------|-------------|
| Analytics | `/admin/analytics/*` | ✅ | ✅ |
| Chatbot Logs | `/admin/chatbot-logs/*` | ✅ | ✅ |
| Recommendations | `/admin/recommendations/*` | ✅ | ✅ |
| Reports | `/admin/reports/*` | ✅ | ✅ |
| Settings | `/admin/settings/*` | ❌ | ✅ |
| Audit Logs | `/admin/settings/audit-logs/*` | ❌ | ✅ |

### Legend:
- 🔓 All Admin Roles
- 🔐 Admin + Super Admin
- 🛡️ Super Admin Only

---

## 📝 Notes

### Performance Optimizations
1. **Cache**: Recommendation stats cached for 10 minutes
2. **Eager Loading**: Relations loaded with `with()` to prevent N+1 queries
3. **Pagination**: All lists paginated (10-100 items per page)
4. **Streaming Export**: Large CSV exports streamed to prevent memory issues

### Security Features
1. **File Upload Validation**: MIME type, size, extension checks
2. **Input Sanitization**: All inputs validated with Laravel validation
3. **Activity Logging**: All CRUD operations tracked in audit log
4. **Role-Based Access**: Middleware enforces permissions
5. **Locked Status**: Resolved reports cannot be modified (data integrity)

### Data Integrity
1. **Audit Trail**: Old values tracked before updates
2. **Soft Deletes**: Can be implemented if needed
3. **Relationship Loading**: Ensures data consistency
4. **MongoDB ObjectId**: Proper handling of MongoDB IDs

---

## 📞 Support

Untuk pertanyaan atau bantuan, hubungi tim development.

---

**Dibuat dengan**: PlantUML  
**Tanggal**: 2026  
**Versi**: 1.0.0
