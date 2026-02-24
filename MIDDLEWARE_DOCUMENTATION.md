# Admin Panel Middleware Documentation

## Overview
This document details all middleware configured for the Admin Panel authentication and authorization system.

---

## ✅ Middleware Registration & Validation

### Kernel.php Configuration
**File:** `app/Http/Kernel.php`

All middleware aliases are registered in the `$middlewareAliases` property (Laravel 10+):

```php
protected $middlewareAliases = [
    // Standard Laravel middleware
    'auth' => \App\Http\Middleware\Authenticate::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    // ... other standard middleware ...
    
    // ============================================
    // ADMIN CUSTOM MIDDLEWARE
    // ============================================
    'admin.auth' => \App\Http\Middleware\EnsureAdminAuthenticated::class,
    'admin.role' => \App\Http\Middleware\AdminRoleMiddleware::class,
    'admin.permission' => \App\Http\Middleware\AdminPermissionMiddleware::class,
    'admin.activity-log' => \App\Http\Middleware\AdminActivityLogMiddleware::class,
    'admin.maintenance' => \App\Http\Middleware\AdminMaintenanceMode::class,
];
```

**Backward Compatibility:** The Kernel constructor ensures `$routeMiddleware` property maps to `$middlewareAliases` for compatibility across Laravel versions.

---

## 🔐 Custom Admin Middleware

### 1. AdminRoleMiddleware
**File:** `app/Http/Middleware/AdminRoleMiddleware.php`

**Purpose:** Validates admin has required role(s)

**Usage in Routes:**
```php
Route::middleware('admin.role:admin,super_admin')->group(function () {
    // Only admins and super admins can access
});

Route::middleware('admin.role:super_admin')->group(function () {
    // Only super admins can access
});
```

**Implementation Details:**
- ✅ Checks authentication via `auth('admin')->check()`
- ✅ Validates admin exists and is active
- ✅ Uses null-safe access (`$admin->role?->name`) to prevent errors
- ✅ Auto-loads role relationship if not already loaded
- ✅ Returns 403 Forbidden for insufficient privileges
- ✅ Returns 500 error if role is misconfigured

**Available Roles:**
- `super_admin` - Full system access
- `admin` - Admin panel management
- `moderator` - Review/report moderation

---

### 2. AdminPermissionMiddleware
**File:** `app/Http/Middleware/AdminPermissionMiddleware.php`

**Purpose:** Validates admin has specific permission

**Usage in Routes:**
```php
Route::middleware('admin.permission:delete.users')->group(function () {
    // Only admins with delete.users permission can access
});
```

**Implementation Details:**
- ✅ Checks authentication via `auth('admin')->check()`
- ✅ Calls `$admin->hasPermission($permission)` method
- ✅ Uses defensive null-checks
- ✅ Returns 403 Forbidden for insufficient permissions
- ✅ Clear error messages

---

### 3. EnsureAdminAuthenticated
**File:** `app/Http/Middleware/EnsureAdminAuthenticated.php`

**Purpose:** Ensures admin is logged in and active

**Usage in Routes:**
```php
Route::middleware('admin.auth')->group(function () {
    // Protected routes
});
```

**Features:**
- ✅ Redirects to login if not authenticated
- ✅ Checks if admin account is active (`is_active` flag)
- ✅ Logs out inactive admins automatically

---

### 4. AdminActivityLogMiddleware
**File:** `app/Http/Middleware/AdminActivityLogMiddleware.php`

**Purpose:** Logs all admin actions for audit trail

**Logged Data:**
- Admin ID
- Action (route)
- Method (GET, POST, etc.)
- IP Address
- User Agent
- Request data

---

### 5. AdminMaintenanceMode
**File:** `app/Http/Middleware/AdminMaintenanceMode.php`

**Purpose:** Blocks non-super-admin access during maintenance

**Usage:**
```php
Route::middleware('admin.maintenance')->group(function () {
    // Super admins only during maintenance mode
});
```

---

## 📋 Route Configuration

### Web Routes
**File:** `routes/web.php`

Protected routes with role-based access control:

```php
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    // DESTINATIONS (Admin + Super Admin)
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::resource('destinations', AdminControllers\DestinationController::class);
    });
    
    // REVIEWS (Admin + Moderator + Super Admin)
    Route::middleware('admin.role:admin,moderator,super_admin')->group(function () {
        Route::resource('reviews', AdminControllers\ReviewController::class);
    });
    
    // SETTINGS (Super Admin Only)
    Route::middleware('admin.role:super_admin')->group(function () {
        Route::get('settings/general', [SettingsController::class, 'editGeneral']);
    });
});
```

### Admin Routes
**File:** `routes/admin.php`

Similar role-based grouping with identical middleware structure.

---

## 🚨 Error Handling

### Error Responses

#### 403 Forbidden (Insufficient Privileges)
- **Middleware:** `AdminRoleMiddleware`, `AdminPermissionMiddleware`
- **Response:** HTTP 403 with error message
- **View:** `resources/views/admin/errors/permission-denied.blade.php`

#### 500 Internal Server Error (Misconfigured Role)
- **Middleware:** `AdminRoleMiddleware`
- **Occurs:** When admin role relationship not found
- **Message:** "Admin role not configured. Contact administrator."

#### Redirect to Login
- **Middleware:** `EnsureAdminAuthenticated`, `AdminRoleMiddleware`
- **Response:** Redirect to `admin.login` route with error message
- **Occurs:** When session expired or admin inactive

---

## 🔍 Defensive Programming Features

### 1. Null-Safe Middleware Access
```php
// Before (Risky - could throw error if role not loaded)
if (!in_array($admin->role->name, $roles)) { ... }

// After (Safe - handles null relationships)
$adminRole = $admin->role?->name ?? null;
if (!$adminRole) {
    abort(500, 'Admin role not configured');
}
```

### 2. Relationship Auto-Loading
```php
// Auto-load role if not already loaded
if (!$admin->relationLoaded('role') && $admin->role_id) {
    $admin->load('role');
}
```

### 3. Status Verification
```php
// Check if admin account is active
if (!$admin->is_active) {
    auth('admin')->logout();
    return redirect()->route('admin.login');
}
```

### 4. Session Validation
```php
// Double-check admin exists in session
if (!$admin) {
    auth('admin')->logout();
    return redirect()->route('admin.login');
}
```

---

## 🧪 Testing Middleware

### Verify Routes with Middleware
```bash
php artisan route:list --name=admin.destinations.index --verbose
```

Expected output shows middleware chain:
```
⇂ web
⇂ auth:admin
⇂ admin.role:admin,super_admin
⇂ Closure
```

### Test Middleware Classes Load
```bash
php artisan tinker
> class_exists('App\Http\Middleware\AdminRoleMiddleware')
> true
```

---

## 🔧 Troubleshooting

### Error: "Target class [admin.role] does not exist"

**Causes:**
1. Cache not cleared after changes
2. Middleware class not found
3. Kernel.php misconfigured
4. Autoloader not regenerated

**Solutions:**
```bash
# 1. Clear all caches
php artisan optimize:clear
php artisan route:clear
php artisan config:clear

# 2. Regenerate autoloader
composer dump-autoload

# 3. Verify middleware classes exist
php artisan route:list --verbose

# 4. Check Kernel.php middleware aliases are correct
```

### Error: "Call to a member function on null"

**Cause:** Middleware accessing null relationship

**Solutions:**
- Ensure role relationship is eager-loaded in query
- Use null-safe operator: `$admin->role?->name`
- Check `is_active` flag is set correctly

### Admin Routes Not Protected

**Verify:**
1. Routes use correct auth guard: `'auth:admin'`
2. Middleware aliases registered in Kernel.php
3. Model relationships defined correctly
4. Database has required role records

---

## 📊 Middleware Execution Order

When accessing `/admin/destinations`:

```
1. web (Middleware Group)
   └─ Session handling
   └─ CSRF protection
   └─ Cookie encryption

2. auth:admin (Guard-specific)
   └─ Verify session has admin logged in
   └─ Check admin exists in database

3. admin.role:admin,super_admin (Custom)
   └─ Verify admin role matches allowed roles
   └─ Check is_active flag
   └─ Load role relationship if needed

4. Closure (Route group)
   └─ Route handler executed
```

---

## 📝 Common Route Patterns

### Protected by Authentication Only
```php
Route::middleware('auth:admin')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit']);
});
```

### Protected by Specific Roles
```php
Route::middleware('admin.role:admin,super_admin')->group(function () {
    Route::delete('users/{user}', [UserController::class, 'destroy']);
});
```

### Protected by Specific Permission
```php
Route::middleware('admin.permission:delete.users')->group(function () {
    Route::delete('users/{user}', [UserController::class, 'destroy']);
});
```

### With Activity Logging
```php
Route::middleware('admin.activity-log')->group(function () {
    // All actions logged automatically
});
```

---

## ✅ Configuration Checklist

Before deploying to production:

- [ ] All middleware classes exist in `app/Http/Middleware/`
- [ ] All middleware registered in `app/Http/Kernel.php`
- [ ] No typos in middleware aliases
- [ ] Routes use correct guard: `auth:admin`
- [ ] Admin model has role relationship defined
- [ ] is_active column exists in admins table
- [ ] All error views exist
- [ ] Cache cleared: `php artisan optimize:clear`
- [ ] Autoloader updated: `composer dump-autoload`
- [ ] Routes verified: `php artisan route:list --verbose`

---

## 🔗 Related Files

- **Models:** `app/Models/Admin.php`, `app/Models/Role.php`
- **Controllers:** `app/Http/Controllers/Admin/`
- **Config:** `config/auth.php`, `app/Http/Kernel.php`
- **Routes:** `routes/web.php`, `routes/admin.php`
- **Views:** `resources/views/admin/`

---

**Last Updated:** February 20, 2026  
**Framework:** Laravel 10  
**PHP Version:** 8.1+
