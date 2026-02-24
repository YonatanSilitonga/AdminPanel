# SMART TOURISM APP - Laravel Admin Panel Documentation

**Tanggal**: 19 Februari 2026  
**Aplikasi**: Smart Tourism Admin Dashboard  
**Framework**: Laravel (Terbaru)  
**Template Engine**: Blade  
**Status**: Production Ready

---

## 📋 TABLE OF CONTENTS

1. [Sitemap Admin Panel](#1-sitemap-admin-panel)
2. [Route Structure](#2-route-structure)
3. [Controller Architecture](#3-controller-architecture)
4. [Middleware Strategy](#4-middleware-strategy)
5. [RBAC Permission Matrix](#5-rbac-permission-matrix)
6. [Database Relationships](#6-database-relationships)
7. [UI Layout Structure](#7-ui-layout-structure)
8. [Authentication Flow](#8-authentication-flow)
9. [CRUD Operation Flow](#9-crud-operation-flow)
10. [Security Measures](#10-security-measures)
11. [Best Practices](#11-best-practices)
12. [Scalability Considerations](#12-scalability-considerations)

---

## 1. SITEMAP ADMIN PANEL

```
/admin
├── /login                          (Public)
├── /forgot-password                (Public)
├── /reset-password/{token}         (Public)
│
└── /dashboard                      (Protected - All Roles)
    ├── /destinations
    │   ├── /                       (List)
    │   ├── /create                 (Create Form)
    │   ├── /{id}/edit              (Edit Form)
    │   ├── /{id}/delete            (Delete)
    │   ├── /{id}/gallery           (Gallery Management)
    │   ├── /{id}/facilities        (Facility Management)
    │   └── /{id}/toggle-featured   (Update Status)
    │
    ├── /events
    │   ├── /                       (List)
    │   ├── /create                 (Create Form)
    │   ├── /{id}/edit              (Edit Form)
    │   ├── /{id}/delete            (Delete)
    │   └── /{id}/toggle-status     (Update Status)
    │
    ├── /reviews
    │   ├── /                       (List + Filter)
    │   ├── /{id}/detail            (Detail View)
    │   ├── /{id}/approve           (Approve)
    │   ├── /{id}/reject            (Reject)
    │   └── /{id}/delete            (Delete)
    │
    ├── /reports
    │   ├── /                       (List + Filter)
    │   ├── /{id}/detail            (Detail View)
    │   ├── /{id}/resolve           (Update Status)
    │   ├── /{id}/action            (Take Action)
    │   └── /{id}/flag-suspicious   (Flag)
    │
    ├── /users
    │   ├── /                       (List)
    │   ├── /{id}/activity          (User Activity)
    │   ├── /{id}/toggle-status     (Disable/Enable)
    │   └── /{id}/delete            (Delete)
    │
    ├── /recommendations
    │   ├── /                       (Log List)
    │   ├── /{id}/detail            (Detail View)
    │   └── /export                 (Export Data)
    │
    ├── /chatbot-logs
    │   ├── /                       (Log List)
    │   ├── /{id}/conversation      (Conversation Detail)
    │   └── /{id}/flag              (Flag Suspicious)
    │
    ├── /analytics
    │   ├── /dashboard              (Overview Analytics)
    │   ├── /destinations           (Destination Analytics)
    │   ├── /events                 (Event Analytics)
    │   └── /reports                (Report Analytics)
    │
    ├── /settings
    │   ├── /general                (General Settings)
    │   ├── /api-keys               (API Configuration)
    │   ├── /ai-config              (AI Configuration)
    │   ├── /maintenance            (Maintenance Mode)
    │   └── /audit-logs             (Audit Log Viewer)
    │
    ├── /profile                    (Admin Profile)
    │   ├── /edit-password          (Change Password)
    │   └── /logout                 (Logout)
    │
    └── /permission-denied          (403 Error)
```

---

## 2. ROUTE STRUCTURE

### Prefix: `/admin`

**Authentication Routes (Public)**
```
POST   /login                    → AdminAuthController@login
GET    /forgot-password          → AdminAuthController@showForgotForm
POST   /forgot-password          → AdminAuthController@sendResetLink
GET    /reset-password/{token}   → AdminAuthController@showResetForm
POST   /reset-password           → AdminAuthController@resetPassword
```

**Protected Routes (Middleware: auth:admin)**

**Dashboard**
```
GET    /dashboard                → DashboardController@index
```

**Destinations (Admin Role)**
```
GET    /destinations             → DestinationController@index
GET    /destinations/create      → DestinationController@create
POST   /destinations             → DestinationController@store
GET    /destinations/{id}/edit   → DestinationController@edit
PUT    /destinations/{id}        → DestinationController@update
DELETE /destinations/{id}        → DestinationController@destroy
POST   /destinations/{id}/gallery → DestinationGalleryController@store
DELETE /destinations/{id}/gallery/{galleryId} → DestinationGalleryController@delete
POST   /destinations/{id}/facility → FacilityController@store
DELETE /destinations/{id}/facility/{facilityId} → FacilityController@delete
PATCH  /destinations/{id}/featured → DestinationController@toggleFeatured
```

**Events (Admin Role)**
```
GET    /events                   → EventController@index
GET    /events/create            → EventController@create
POST   /events                   → EventController@store
GET    /events/{id}/edit         → EventController@edit
PUT    /events/{id}              → EventController@update
DELETE /events/{id}              → EventController@destroy
PATCH  /events/{id}/status       → EventController@toggleStatus
```

**Reviews (Admin, Moderator)**
```
GET    /reviews                  → ReviewController@index
GET    /reviews/{id}             → ReviewController@show
PATCH  /reviews/{id}/approve     → ReviewController@approve
PATCH  /reviews/{id}/reject      → ReviewController@reject
DELETE /reviews/{id}             → ReviewController@destroy
```

**Reports (Moderator, Admin)**
```
GET    /reports                  → ReportController@index
GET    /reports/{id}             → ReportController@show
PATCH  /reports/{id}/resolve     → ReportController@resolve
POST   /reports/{id}/action      → ReportController@takeAction
```

**Users (Admin)**
```
GET    /users                    → UserController@index
GET    /users/{id}/activity      → UserController@showActivity
PATCH  /users/{id}/status        → UserController@toggleStatus
DELETE /users/{id}               → UserController@destroy
```

**Logs**
```
GET    /recommendations          → RecommendationLogController@index
GET    /recommendations/{id}     → RecommendationLogController@show
GET    /chatbot-logs             → ChatbotLogController@index
GET    /chatbot-logs/{id}        → ChatbotLogController@show
PATCH  /chatbot-logs/{id}/flag   → ChatbotLogController@flag
```

**Analytics**
```
GET    /analytics                → AnalyticsController@dashboard
GET    /analytics/destinations   → AnalyticsController@destinations
GET    /analytics/events         → AnalyticsController@events
GET    /analytics/reports        → AnalyticsController@reports
```

**Settings (Super Admin)**
```
GET    /settings/general         → SettingsController@editGeneral
PUT    /settings/general         → SettingsController@updateGeneral
GET    /settings/api-keys        → SettingsController@editApiKeys
PUT    /settings/api-keys        → SettingsController@updateApiKeys
GET    /settings/ai-config       → SettingsController@editAiConfig
PUT    /settings/ai-config       → SettingsController@updateAiConfig
PATCH  /settings/maintenance     → SettingsController@toggleMaintenance
GET    /settings/audit-logs      → AuditLogController@index
```

**Profile**
```
GET    /profile                  → ProfileController@edit
PUT    /profile/password         → ProfileController@updatePassword
POST   /logout                   → AdminAuthController@logout
```

---

## 3. CONTROLLER ARCHITECTURE

### Directory Structure
```
app/Http/Controllers/Admin/
├── AdminAuthController.php
├── DashboardController.php
├── DestinationController.php
├── DestinationGalleryController.php
├── EventController.php
├── ReviewController.php
├── ReportController.php
├── UserController.php
├── RecommendationLogController.php
├── ChatbotLogController.php
├── AnalyticsController.php
├── SettingsController.php
├── AuditLogController.php
├── FacilityController.php
└── ProfileController.php
```

### Controller Responsibilities

| Controller | Methods | Role Required |
|-----------|---------|---------------|
| AdminAuthController | login, register, logout, forgotPassword | Public/Auth:admin |
| DashboardController | index, getChartData | Auth:admin |
| DestinationController | index, create, store, edit, update, destroy, toggleFeatured | Admin |
| DestinationGalleryController | store, delete | Admin |
| EventController | index, create, store, edit, update, destroy, toggleStatus | Admin |
| ReviewController | index, show, approve, reject, destroy | Admin+Moderator |
| ReportController | index, show, resolve, takeAction | Moderator+Admin |
| UserController | index, showActivity, toggleStatus, destroy | Admin |
| RecommendationLogController | index, show, export | Admin |
| ChatbotLogController | index, show, flag | Admin+Moderator |
| AnalyticsController | dashboard, destinations, events, reports | Admin |
| SettingsController | editGeneral, updateGeneral, etc. | Super Admin |
| AuditLogController | index, show, filter | Super Admin |
| ProfileController | edit, updatePassword | Auth:admin |

---

## 4. MIDDLEWARE STRATEGY

### Middleware List

```php
// app/Http/Middleware/

1. EnsureAdminAuthenticated.php
   - Check if user is authenticated via auth:admin
   - Redirect to /admin/login if not
   
2. RoleMiddleware.php
   - Check user role against allowed roles
   - Usage: middleware('role:admin,super_admin')
   - Redirect to /admin/permission-denied if unauthorized
   
3. PermissionMiddleware.php
   - Check user permissions
   - Usage: middleware('permission:manage_destinations')
   - Return 403 if denied
   
4. AdminHistoryLog.php
   - Log all admin actions for audit trail
   - Capture: user_id, route, ip, timestamp, changes
   
5. MaintenanceMode.php
   - Allow only Super Admin when maintenance mode is on
   - Return 503 for other users

6. RateLimitAdmin.php
   - Prevent brute force attacks
   - 10 requests per minute for sensitive actions
```

### Middleware Stack
```php
// In route group
Route::middleware([
    'auth:admin',           // Check authentication
    'role:admin|super_admin', // Check role
    'admin:history_log',    // Log actions
])->group(function () {
    // Routes here
});
```

---

## 5. RBAC PERMISSION MATRIX

### Roles Definition

```
┌─────────────────┬──────────────────┬─────────────┬────────────┐
│ Feature         │ Super Admin      │ Admin       │ Moderator  │
├─────────────────┼──────────────────┼─────────────┼────────────┤
│ Dashboard       │ ✓ (View All)     │ ✓ (Own)     │ ✓ (Own)    │
│ Destinations    │ ✓ (CRUD)         │ ✓ (CRUD)    │ ✗          │
│ Events          │ ✓ (CRUD)         │ ✓ (CRUD)    │ ✗          │
│ Reviews         │ ✓ (CRUD)         │ ✓ (Moderate)│ ✓ (Moderate)│
│ Reports         │ ✓ (View+Action)  │ ✓ (Action)  │ ✓ (View+Act)│
│ Users           │ ✓ (CRUD)         │ ✓ (View)    │ ✗          │
│ Logs            │ ✓ (View+Export)  │ ✓ (View)    │ ✗          │
│ Analytics       │ ✓ (All)          │ ✓ (Own)     │ ✓ (Own)    │
│ Settings        │ ✓ (CRUD)         │ ✗           │ ✗          │
│ Audit Logs      │ ✓ (View)         │ ✗           │ ✗          │
│ Admin Mgmt      │ ✓ (CRUD)         │ ✗           │ ✗          │
└─────────────────┴──────────────────┴─────────────┴────────────┘
```

### Permissions List

```
Destination Management
├── view_destinations
├── create_destination
├── edit_destination
├── delete_destination
├── manage_gallery
├── manage_facilities
├── mark_featured

Event Management
├── view_events
├── create_event
├── edit_event
├── delete_event
├── toggle_event_status

Review Management
├── view_reviews
├── approve_review
├── reject_review
├── delete_review

Report Management
├── view_reports
├── resolve_report
├── take_report_action

User Management
├── view_users
├── edit_user
├── delete_user
├── disable_user_account

Log Viewing
├── view_recommendation_logs
├── view_chatbot_logs
├── export_logs

Analytics
├── view_analytics
├── export_analytics

System
├── access_settings
├── manage_api_keys
├── toggle_maintenance
├── view_audit_logs
```

---

## 6. DATABASE RELATIONSHIPS

### Entity Relationship

```
Admin (admins)
├── 1 ─ N → Role
├── 1 ─ N → AuditLog
└── 1 ─ N → AdminActivity

Role (roles)
├── 1 ─ N → Permission (pivot: role_permission)
└── 1 ─ N → Admin

Permission (permissions)
└── N ─ M → Role

User (users)
├── 1 ─ N → Review
├── 1 ─ N → Report
├── 1 ─ N → Wishlist
├── 1 ─ N → Trip
├── 1 ─ N → ChatHistory
└── 1 ─ N → RecommendationLog

Destination (destinations)
├── 1 ─ N → DestinationGallery
├── 1 ─ N → Facility
├── 1 ─ N → Review
├── 1 ─ N → Wishlist
└── 1 ─ N → Report

Event (events)
├── N ─ 1 → Destination
├── 1 ─ N → Report
└── 1 ─ N → EventAttendance

Review (reviews)
├── N ─ 1 → User
├── N ─ 1 → Destination
├── 1 ─ N → Report
└── status: pending, approved, rejected, deleted

Report (reports)
├── N ─ 1 → User (reporter)
├── N ─ 1 → Admin (assigned_to)
├── Reportable: destination, review, event
├── Evidence: attachment_path
└── status: pending, investigating, resolved, dismissed

Trip (trips)
├── N ─ 1 → User
└── 1 ─ N → TripItem

TripItem (trip_items)
├── N ─ 1 → Destination
└── N ─ 1 → Trip

ChatHistory (chat_histories)
├── N ─ 1 → User
├── role: user, assistant
└── content, timestamp

RecommendationLog (recommendation_logs)
├── N ─ 1 → User
├── recommended_destination, behavior_data
└── timestamp

AuditLog (audit_logs)
├── N ─ 1 → Admin
├── action, entity_type, entity_id
├── old_values, new_values, changes
└── timestamp, ip_address
```

### Table Structure

```sql
-- Admin Authentication
admins
├── id (PK)
├── name
├── email (unique, indexed)
├── password
├── role_id (FK)
├── is_active
├── last_login_at
├── phone, avatar (optional)
├── created_at, updated_at

roles
├── id (PK)
├── name (unique: super_admin, admin, moderator)
├── description
├── created_at, updated_at

permissions
├── id (PK)
├── name (unique)
├── description
├── created_at, updated_at

role_permission (pivot)
├── role_id (PK, FK)
├── permission_id (PK, FK)

-- Destinations
destinations
├── id (PK)
├── name (indexed)
├── slug (unique, indexed)
├── description, long_description
├── latitude, longitude (indexed)
├── category (park, beach, museum, etc.)
├── rating, rating_count
├── is_featured, is_trending
├── thumbnail_url, cover_url
├── is_active
├── deleted_at (soft delete)
├── created_at, updated_at
├── admin_id (FK - who created)

destination_galleries
├── id (PK)
├── destination_id (FK, indexed)
├── image_url
├── caption
├── order, is_primary
├── created_at, updated_at

facilities
├── id (PK)
├── destination_id (FK, indexed)
├── name (parking, toilet, wifi, etc.)
├── icon_url, description
├── created_at, updated_at

-- Events
events
├── id (PK)
├── destination_id (FK, indexed)
├── name (indexed)
├── slug (unique)
├── description, long_description
├── start_date, end_date (indexed)
├── banner_url
├── is_active
├── deleted_at (soft delete)
├── created_at, updated_at
├── admin_id (FK)

-- Reviews
reviews
├── id (PK)
├── user_id (FK, indexed)
├── destination_id (FK, indexed)
├── rating (1-5)
├── title, content
├── status (pending, approved, rejected)
├── reported_count
├── deleted_at (soft delete)
├── created_at, updated_at
├── approved_by_admin (FK)

-- Reports
reports
├── id (PK)
├── user_id (FK, indexed - reporter)
├── reportable_type (destination, review, event)
├── reportable_id (polymorphic)
├── reason (spam, inappropriate, etc.)
├── description
├── attachment_path
├── status (pending, investigating, resolved)
├── assigned_to (FK - admin)
├── action_taken (delete_content, warn_user, ignore)
├── deleted_at (soft delete)
├── created_at, updated_at

-- Users
users
├── id (PK)
├── device_id or firebase_id (indexed)
├── name
├── email, phone (indexed, optional)
├── is_active (indexed)
├── last_activity_at (indexed)
├── device_info (json)
├── deleted_at (soft delete)
├── created_at, updated_at

-- Logs
recommendation_logs
├── id (PK)
├── user_id (FK, indexed)
├── recommended_destination_id (FK)
├── behavior_data (json: viewed, searched, saved)
├── recommendation_score
├── is_clicked (tracked later)
├── created_at

chatbot_logs
├── id (PK)
├── user_id (FK, indexed)
├── conversation_id
├── message (text)
├── role (user, assistant)
├── is_flagged
├── flag_reason
├── created_at

-- Audit Logs
audit_logs
├── id (PK)
├── admin_id (FK, indexed)
├── action (create, update, delete, approve, reject, etc.)
├── entity_type (destination, event, review, etc.)
├── entity_id
├── old_values (json)
├── new_values (json)
├── ip_address (indexed)
├── user_agent
├── status (success, failed)
├── reason (if failed)
├── created_at
```

---

## 7. UI LAYOUT STRUCTURE

### Main Layout Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    TOP NAVBAR                           │
│ Logo      Search     Clock    Notifications   User Menu │
└─────────────────────────────────────────────────────────┘
┌─────────────┬───────────────────────────────────────────┐
│             │                                           │
│  SIDEBAR    │        CONTENT AREA                       │
│  (Left)     │                                           │
│             │   ┌─────────────────────────────────────┐│
│ Dashboard   │   │  Page Title - Breadcrumb            ││
│             │   ├─────────────────────────────────────┤│
│ Destinations│   │                                     ││
│             │   │  Cards / Tables / Forms             ││
│ Events      │   │                                     ││
│             │   │  Pagination / Actions               ││
│ Reviews     │   │                                     ││
│             │   └─────────────────────────────────────┘│
│ Reports     │                                           │
│             │                                           │
│ Users       │                                           │
│             │                                           │
│ Logs        │                                           │
│             │                                           │
│ Analytics   │                                           │
│             │                                           │
│ Settings    │                                           │
│             │                                           │
│ Profile     │                                           │
│             │                                           │
│ Logout      │                                           │
└─────────────┴───────────────────────────────────────────┘
```

### Color Palette (Modern Minimal)

```
Primary:     #3B82F6 (Blue)
Secondary:   #10B981 (Green)
Danger:      #EF4444 (Red)
Warning:     #F59E0B (Amber)
Info:        #06B6D4 (Cyan)
Dark:        #1F2937 (Gray-800)
Light:       #F9FAFB (Gray-50)
Border:      #E5E7EB (Gray-200)
```

### Component Structure

**Header Navbar**
- Logo + App Name
- Search bar
- Real-time clock/time
- Notification bell
- User profile dropdown

**Sidebar**
- Logo/Branding
- Main Navigation Menu (Collapsible)
- Role indicator
- Quick Actions
- Logout

**Dashboard Cards**
```
┌─────────────────┐
│ Card Title      │
├─────────────────┤
│ Large Number    │
│ +12% from prev  │
└─────────────────┘
```

**Data Tables**
- Search field
- Filter dropdowns
- Bulk actions
- Pagination controls
- Action buttons (edit, delete, view)
- Responsive horizontal scroll

**Form Elements**
- Clean input fields
- Floating labels (optional)
- Help text below fields
- Multi-step forms (if needed)
- Save draft button

---

## 8. AUTHENTICATION FLOW

### Login Process Flow

```
User Access /admin
       ↓
Is Authenticated?
├→ Yes → Redirect to /admin/dashboard
└→ No → Show Login Page
       ↓
User submits form
       ↓
Validate credentials
├→ Invalid → Show error message
└→ Valid → Check email & password
       ↓
User found & password matches?
├→ No → Log failed attempt (brute force protection)
└→ Yes → Create session
       ↓
is_active = true?
├→ No → Show "Account disabled"
└→ Yes → Update last_login_at
       ↓
Create auth session
       ↓
Set remember me (optional)
       ↓
Redirect to /admin/dashboard
```

### Session Management

```
Session Storage: database (sessions table)
Session Lifetime: 480 minutes (8 hours) - configurable
Remember Me: 30 days
CSRF Protection: Enabled on all POST/PUT/DELETE
```

### Password Reset Flow

```
User clicks "Forgot Password"
       ↓
Enter email
       ↓
Email exists?
├→ No → Show "If email exists, reset link sent"
└→ Yes → Generate reset token
       ↓
Save token in password_resets table (hashed)
       ↓
Send email with reset link
       ↓
User clicks link
       ↓
Verify token (not expired, matches hash)
       ↓
Show reset form
       ↓
User submits new password
       ↓
Validate password strength
       ↓
Update password, delete token
       ↓
Show success, redirect to login
```

---

## 9. CRUD OPERATION FLOW

### Destination Management - CRUD Example

#### CREATE FLOW

```
Admin clicks "Add Destination"
       ↓
GET /admin/destinations/create
       ↓
Show form with fields:
├── Name, Description, Long Description
├── Category (dropdown)
├── Latitude, Longitude (map picker)
├── Thumbnail upload
├── Cover image upload
└── Featured checkbox

User fills form & submits
       ↓
POST /admin/destinations
       ↓
Validate data
├── Name: required, min 3, max 200
├── Description: required, min 10, max 500
├── Category: required, exists in enum
├── Coordinates: required, valid coords
├── Images: required, mimes: jpeg, png, max 5MB
└── Rating: optional, numeric between 1-5

Validation passed?
├→ No → Show validation errors
└→ Yes → Continue

Process images
├── Resize thumbnail to 400x300
├── Resize cover to 1200x600
├── Save to storage/destinations/{id}/
└── Generate URLs

Create destination record
├── Save to DB
├── Set is_active = true
├── Set created_by = auth_admin_id
└── Generate slug

Log action in audit_logs
├── Action: "create"
├── Entity: "destination"
├── New values: {...}
└── IP address, timestamp

Return success response
├── Show toast message
└── Redirect to /admin/destinations/{id}/edit
```

#### READ FLOW

```
GET /admin/destinations
       ↓
Check permission: view_destinations
├→ No → Return 403
└→ Yes → Continue

Load destinations
├── Query destinations where is_active = true
├── Include gallery count, facility count, review count
├── Apply search filters
├── Apply category filter
├── Apply pagination (15 per page)
└── Order by created_at DESC

Load featured destinations separately
├── Where is_featured = true
├── Limit 5

Calculate statistics
├── Total active destinations
├── Total pending reviews
├── Total reports

Load recent activity
├── Last 10 actions on destinations
└── From audit_logs

Render view with data
└── Show table with: name, category, rating, status, actions
```

#### UPDATE FLOW

```
Admin clicks "Edit" on destination
       ↓
GET /admin/destinations/{id}/edit
       ↓
Load destination with relations
├── Gallery items
├── Facilities
└── Stats

Check if destination exists
├→ Not found → Show 404
└→ Found → Continue

Check edit permission
├→ No → Show 403
└→ Yes → Show edit form

Form is pre-populated with current data
User modifies and submits
       ↓
PUT /admin/destinations/{id}
       ↓
Validate data (same as create)
       ↓
Compare old vs new values
├── Get old values from DB
├── Prepare change log
└── Identify modified fields

Update image if changed
├── Delete old image from storage
├── Upload new image
└── Update URL in database

Update destination record
├── Update all fields
└── Update updated_at timestamp

Log audit trail
├── Action: "update"
├── Old values: {...}
├── New values: {...}
├── Changed fields: {...}
└── IP address

Return success
└── Show toast + redirect to list
```

#### DELETE FLOW

```
Admin clicks delete icon
       ↓
Confirmation modal appears
├── Show: "Are you sure? This action cannot be undone"
└── Warning if destination has active reviews/reports

User confirms delete
       ↓
DELETE /admin/destinations/{id}
       ↓
Check permission: delete_destination
├→ No → Return 403
└→ Yes → Continue

Check soft delete feasibility
├── Count active reviews: if > 0, delete those first
├── Count active reports: if > 0, set to dismissed
└── Count in trips: if > 0, show warning

Perform soft delete
├── Set deleted_at = now()
├── Keep data in DB for recovery
└── Data won't appear in public app

Delete associated files
├── Gallery images
├── Cover image
└── Thumbnail

Log audit trail
├── Action: "soft_delete"
├── Entity: "destination"
├── Reason: "Admin deletion"
└── Recoverable until: 30 days

Return success
└── Remove from table, show toast
```

#### GALLERY MANAGEMENT

```
Admin in destination edit page, scrolls to gallery section
       ↓
Shows current gallery images with order handles
       ↓
Admin clicks "Add Image"
       ↓
Modal opens with drag-drop upload
User selects multiple images (max 10)
       ↓
POST /admin/destinations/{id}/gallery
       ↓
Validate images
├── Max 5MB each
├── Mimes: jpeg, png, webp
├── Dimensions: min 800x600
└── Max 10 images per destination

Process each image
├── Resize to 1200x800 (main)
├── Generate thumbnail 400x300
├── Optimize with compression
└── Save both versions

Store metadata
├── filename, size, dimensions
├── upload_date, uploaded_by
└── order (auto-incremented)

Return success with image list
└── Show in gallery with delete & reorder options
```

---

## 10. SECURITY MEASURES

### Authentication & Authorization

✅ **Password Security**
- Bcrypt hashing with 10+ rounds
- Minimum 8 characters, 1 uppercase, 1 number, 1 special char
- Password reset tokens expire in 1 hour
- Old passwords not reusable (last 5 stored)

✅ **Rate Limiting**
- Login attempts: 5 per minute per IP
- API calls: 100 per minute per admin
- File uploads: 50 per hour
- Failed login: exponential backoff (1s, 2s, 4s...)

✅ **Session Security**
- Sessions stored in database (not file)
- HTTPS only (Secure flag on cookies)
- HttpOnly flag on all auth cookies
- SameSite=Strict on CSRF tokens
- Session rotation on privilege change

✅ **CSRF Protection**
- All POST/PUT/DELETE require CSRF token
- Tokens unique per session
- Token lifetime: session duration
- Verify token origin

### Authorization

✅ **Role-Based Access Control (RBAC)**
- 3-tier permission system
- Middleware checks on every route
- Entity-level access control
- Permission inheritance from roles

✅ **Data Access Control**
```php
// Example: Moderator can only view assigned reports
$reports = Report::where('assigned_to', auth('admin')->id())
                  ->orWhere('assigned_to', null) // unassigned
                  ->get();
```

✅ **API Security**
- API keys for external integration
- API key rotation every 90 days
- API logging with request/response
- IP whitelist for API access (optional)

### Data Protection

✅ **Encryption**
- Sensitive fields encrypted at rest
  - API keys in settings
  - User emails in reports
  - File paths in logs

✅ **Audit Logging**
- All admin actions logged
- Data: admin, action, entity, changes, IP, time
- Audit logs immutable (insert only)
- 1-year retention (archival after 6 months)

✅ **File Upload Security**
- Scan files for malware
- Store outside web root
- Serve via controller (no direct access)
- Filename sanitization
- Type validation (extension + mime check)
- Size limits: 5MB single, 50MB total per destination

✅ **Input Validation**
- All user input validated server-side
- Prepared statements/parameter binding
- No direct SQL queries
- HTML escaping in views
- XSS protection

✅ **Output Encoding**
- All dynamic content escaped
- JSON responses have Content-Type header
- HTML entities in error messages
- File download with proper headers

### Infrastructure Security

✅ **Deployment**
- Environment variables for secrets
- No hardcoded credentials
- .env files not in git
- Separate DB user for admin panel (least privilege)

✅ **Database**
- SQL injection prevention (Eloquent ORM)
- Prepared statements
- Limited DB user permissions
- Backups encrypted
- Separate admin database credentials

✅ **HTTP Headers**
```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000
Content-Security-Policy: default-src 'self'
```

### Monitoring & Detection

✅ **Suspicious Activity Detection**
- Multiple failed logins → temporary lock
- Access from unusual IP → email notification
- Bulk data access → flag for review
- Permission escalation attempts → immediate alert
- Rapid create/delete actions → rate limit

✅ **Logging**
- All errors logged with context
- Failed actions logged
- Performance metrics
- External API calls logged
- Log retention: 90 days (searchable)

---

## 11. BEST PRACTICES

### Code Organization

✅ **Service Layer Pattern**
```
    Controller (Routes request)
         ↓
    Service Class (Business Logic)
         ↓
    Repository (Database Access)
         ↓
    Model (Data Representation)
```

✅ **Repository Pattern**
```php
interface DestinationRepositoryInterface {
    public function getAll();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
```

✅ **Dependency Injection**
```php
public function __construct(
    DestinationRepositoryInterface $repository,
    AuditLogService $auditService
) {
    $this->repository = $repository;
    $this->auditService = $auditService;
}
```

### Database Best Practices

✅ **Indexes**
- Foreign keys indexed
- Frequently searched columns indexed
- Composite indexes for multiple-field searches
- Avoid over-indexing

✅ **Query Optimization**
- Use eager loading (with, load)
- Limit select columns
- Cache query results (5-60 minutes)
- Avoid N+1 queries

✅ **Data Integrity**
- Foreign key constraints
- Check constraints for status enums
- Unique constraints on identifiers
- Default values for timestamps

### File Management

✅ **Storage Organization**
```
storage/app/
├── destinations/
│   ├── {id}/
│   │   ├── gallery/
│   │   │   ├── main_{number}.jpg
│   │   │   └── thumb_{number}.jpg
│   │   └── cover.jpg
├── events/
│   └── {id}/
│       └── banner.jpg
└── reports/
    └── {id}/
        └── evidence_{timestamp}.pdf
```

✅ **Image Processing**
- Resize images immediately on upload
- Store multiple sizes (thumbnail, medium, full)
- Use WebP format with PNG fallback
- Compress with quality 80%
- Generate responsive images (srcset)

✅ **File Cleanup**
- Scheduled job daily to remove orphaned files
- Delete old files during soft delete operations
- Archive old audit logs

### View & Template Best Practices

✅ **Blade Template Structure**
```
resources/views/admin/
├── layouts/
│   ├── app.blade.php (main layout)
│   ├── sidebar.blade.php
│   ├── navbar.blade.php
│   └── footer.blade.php
├── destinations/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── components/
│   ├── card.blade.php
│   ├── table.blade.php
│   ├── form.blade.php
│   └── modal.blade.php
└── errors/
    ├── 403.blade.php
    └── 404.blade.php
```

✅ **Component Reusability**
```blade
<!-- card.blade.php -->
<div class="card @if($highlighted) ring-2 ring-blue-500 @endif">
    @if($title)
        <h3>{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>

<!-- Usage -->
<x-card title="Dashboard Stats" highlighted="true">
    <p>Content here</p>
</x-card>
```

### Testing Strategy

✅ **Test Coverage**
- Unit tests for services (70%+ coverage)
- Feature tests for API endpoints
- Integration tests for data flows
- Test auth middleware
- Test permission checks
- Test audit logging

✅ **Test Database**
- Use in-memory SQLite for tests
- Seed factories for test data
- Rollback after each test
- Parallel test execution

### Performance

✅ **Caching Strategy**
- Cache navigation menu (1 hour)
- Cache role permissions (until invalidated)
- Cache dashboard statistics (5 minutes)
- Cache destination categories (12 hours)

✅ **Pagination & Limits**
- Default 15 items per page
- User can choose: 15, 25, 50, 100
- Search results max 1000
- Api export max 10000 records

✅ **Database Optimization**
- Indexed lookups for main queries
- Lazy loading for large datasets
- Database connection pooling
- Query result caching

---

## 12. SCALABILITY CONSIDERATIONS

### Horizontal Scaling

✅ **Stateless Design**
- No file storage on app server
- Use cloud storage (S3-compatible)
- Session stored in database (Redis)
- Queue jobs for background processing

✅ **Load Balancing**
```
     Users
       ↓
  Load Balancer
  ↙       ↙       ↘
App1    App2    App3  (Identical instances)
  ╲       ╱       ╱
Shared Database
Shared Cache (Redis)
Shared Storage
```

### Database Scaling

✅ **Read Replicas**
- Master: Write operations
- Replicas: Read-heavy operations (reports, logs)
- Connection pooling

✅ **Partitioning Strategy**
- Audit logs: partition by date (monthly)
- Chatbot logs: partition by user_id (shard by 1000)
- Tables < 10GB: no immediate need

### Caching Strategy

✅ **Cache Layers**
```
1. Query result cache (Redis) - 5 min
   └─ Dashboard cards, statistics

2. View fragment cache (Redis) - 1 hour
   └─ Sidebar menu, user info

3. Browser cache - vary by endpoint
   └─ Static assets, public files

4. CDN cache for images
   └─ All destination/event images
```

### Async Processing

✅ **Queue Jobs**
- Image processing → Queue
- Email sending → Queue
- Report PDF generation → Queue
- Bulk exports → Queue
- Analytics computation → Queue

```php
// Example
Queue::dispatch(new ProcessDestinationImages($destination));
Queue::dispatch(new GenerateAnalyticsReport())->delay(now()->addHours(1));
```

### Monitoring & Performance

✅ **Metrics to Monitor**
- API response time (target: < 200ms)
- Database query time (target: < 50ms)
- Page load time (target: < 2s)
- Memory usage (alert > 80%)
- CPU usage (alert > 75%)
- Error rate (target: < 0.1%)

✅ **Monitoring Tools**
- Application: Laravel Telescope, Sentry
- Database: Query logs, slow query log
- Infrastructure: New Relic, Datadog
- Synthetic monitoring: Uptime Robot

### Future Expansions

✅ **Feature Expansion Ready**
- Multi-language support (i18n middleware ready)
- White-label support (branding settings)
- Marketing email integration (queue jobs)
- Advanced analytics (separate service)
- AI moderation integration hooks
- Third-party integrations API

✅ **Infrastructure Ready For**
- Microservices (messaging queue pattern)
- Mobile app backend (API endpoints)
- Webhook system (queue + retry logic)
- Real-time updates (WebSocket integration)
- Machine learning model serving

---

## IMPLEMENTATION CHECKLIST

### Phase 1: Foundation (Week 1-2)
- [ ] Database migrations & seeders
- [ ] Authentication system
- [ ] Middleware setup
- [ ] Base layout & views
- [ ] Dashboard skeleton

### Phase 2: Core Features (Week 3-4)
- [ ] Destination CRUD
- [ ] Event CRUD
- [ ] Review moderation
- [ ] Report management
- [ ] Audit logging

### Phase 3: Advanced Features (Week 5-6)
- [ ] User management
- [ ] Log viewers
- [ ] Analytics
- [ ] Settings panel
- [ ] Permission testing

### Phase 4: Polish & Deploy (Week 7)
- [ ] Permission testing
- [ ] Security hardening
- [ ] Performance optimization
- [ ] Documentation
- [ ] Staging deployment
- [ ] Production deployment

---

**End of Admin Panel Documentation v1.0**
