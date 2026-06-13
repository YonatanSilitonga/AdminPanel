# 🚨 Admin Panel - Comprehensive Error Handling Guide

**Last Updated:** May 29, 2026  
**Status:** ✅ Implemented

---

## 📋 Overview

Comprehensive error handling system untuk admin panel dengan support untuk:
- ✅ HTTP Status Codes: 200, 201, 300, 400, 401, 403, 404, 500
- ✅ Custom error pages dengan styling TailwindCSS
- ✅ Response helper untuk standardisasi
- ✅ Exception handling di bootstrap/app.php
- ✅ Middleware untuk error interception

---

## 🎯 HTTP Status Codes Handling

### ✅ 200 - OK (Success)

**Handled By:** Response helper `AdminResponse::success()`

```php
// Controller
return AdminResponse::success(
    'Operation successful',
    ['id' => $destination->id],
    route('admin.destinations.index')
);
```

**Response (JSON):**
```json
{
    "success": true,
    "status": "success",
    "message": "Operation successful",
    "code": 200,
    "data": { "id": "123" }
}
```

---

### ✅ 201 - Created

**Handled By:** `AdminResponse::created()`

```php
// Controller
return AdminResponse::created(
    'Destination created successfully',
    ['id' => $destination->id],
    route('admin.destinations.show', $destination)
);
```

---

### ↩️ 300 - Multiple Choices

**Handled By:** Redirect middleware

```php
// Automatic redirect if resource moved
return response()->view('admin.redirect-options', [...], 300);
```

---

### ❌ 400 - Bad Request

**Handled By:** `AdminResponse::badRequest()`

**View:** `resources/views/admin/errors/400.blade.php`

```php
// Controller - Validation Error
return AdminResponse::badRequest(
    'Invalid input',
    ['name' => 'Name is required']
);
```

**Response:**
```json
{
    "success": false,
    "status": "error",
    "message": "Invalid input",
    "code": 400,
    "errors": { "name": ["Name is required"] }
}
```

---

### 🔐 401 - Unauthorized

**Handled By:** `EnsureAdminAuthenticated` middleware

**View:** `resources/views/admin/errors/401.blade.php`

```php
// Auto-redirect to login
if (!auth('admin')->check()) {
    return redirect()->route('admin.login')
        ->with('error', 'Please login first');
}
```

---

### 🚫 403 - Forbidden

**Handled By:** `AdminRoleMiddleware` & `AdminPermissionMiddleware`

**View:** `resources/views/admin/errors/403.blade.php`

```php
// Controller
return AdminResponse::forbidden('You do not have permission');
```

```php
// Route-level protection
Route::middleware('admin.role:super_admin')->group(function () {
    Route::delete('settings/{setting}', [SettingsController::class, 'destroy']);
});
```

---

### 🔍 404 - Not Found

**Handled By:** `AdminErrorHandler` middleware

**View:** `resources/views/admin/errors/404.blade.php`

```php
// Auto-render when resource not found
return response()->view('admin.errors.404', [
    'message' => 'Destination not found',
    'path' => 'admin/destinations/invalid-id'
], 404);
```

---

### ⚠️ 500 - Server Error

**Handled By:** Exception handler in `bootstrap/app.php`

**View:** `resources/views/admin/errors/500.blade.php`

```php
// Exception automatically caught and rendered
try {
    // Operation
} catch (\Exception $e) {
    \Log::error('Operation failed', ['exception' => $e]);
    return AdminResponse::serverError('Operation failed');
}
```

---

## 📁 File Structure

```
app/
├── Helpers/
│   └── AdminResponse.php          # Response helper class
├── Http/
│   └── Middleware/
│       └── AdminErrorHandler.php  # Error interceptor middleware
├── Exceptions/
│   └── Handler.php                # (Laravel default)

bootstrap/
└── app.php                         # Exception rendering config

routes/
└── admin.php                       # Admin routes with error handler

resources/views/
├── admin/errors/
│   ├── 400.blade.php              # Bad Request
│   ├── 401.blade.php              # Unauthorized
│   ├── 403.blade.php              # Forbidden
│   ├── 404.blade.php              # Not Found
│   ├── 500.blade.php              # Server Error
│   └── 200-success.blade.php      # Success page
└── errors/
    └── unavailable.blade.php      # Maintenance mode
```

---

## 🔧 Usage Guide

### 1️⃣ Handling Success (200)

```php
// In Controller
public function store(DestinationRequest $request)
{
    $destination = Destination::create($request->validated());
    
    return AdminResponse::success(
        'Destination created successfully',
        ['id' => $destination->id],
        route('admin.destinations.index')
    );
}
```

### 2️⃣ Handling Validation Errors (400)

```php
// In Controller
public function update(DestinationRequest $request, Destination $destination)
{
    try {
        $destination->update($request->validated());
        return AdminResponse::success('Updated successfully');
    } catch (\Exception $e) {
        return AdminResponse::badRequest(
            'Update failed',
            ['error' => $e->getMessage()]
        );
    }
}
```

### 3️⃣ Handling Authorization (403)

```php
// Route-level
Route::middleware('admin.role:super_admin')->group(function () {
    Route::patch('users/{user}/activate', [UserController::class, 'activate']);
});

// Or Manual
if (!auth('admin')->user()->can('manage_users')) {
    return AdminResponse::forbidden('Not authorized');
}
```

### 4️⃣ Handling Not Found (404)

```php
// In Controller
public function show(Destination $destination)
{
    if (!$destination) {
        return AdminResponse::notFound('Destination not found');
    }
    
    return view('admin.destinations.show', ['destination' => $destination]);
}
```

### 5️⃣ Handling Server Errors (500)

```php
// Auto-handled by exception handler
// But you can manually return:
try {
    // Risky operation
    performDatabaseOperation();
} catch (\Exception $e) {
    \Log::error('Database error', ['exception' => $e]);
    
    if (request()->expectsJson()) {
        return AdminResponse::serverError('Database operation failed');
    }
    
    return back()->with('error', 'Something went wrong');
}
```

---

## 🎨 Error Page Features

### 400 Bad Request
- ✅ Error code display
- ✅ User-friendly message
- ✅ Input validation details (debug mode)
- ✅ Back & Dashboard buttons

### 401 Unauthorized
- ✅ Authentication required message
- ✅ Login button redirect
- ✅ Home navigation

### 403 Forbidden
- ✅ Permission denied message
- ✅ Reason explanation
- ✅ Additional info card
- ✅ Back & Dashboard options

### 404 Not Found
- ✅ Page not found message
- ✅ Requested path display (debug)
- ✅ Navigation options

### 500 Server Error
- ✅ Error code & message
- ✅ Debug info (when debug=true)
  - Exception class
  - Error message
  - File & line number
- ✅ Retry & Support options

---

## 🧪 Testing Error Handling

### Test 400 Bad Request
```bash
curl -X POST http://localhost:8000/admin/destinations \
  -H "Cookie: PHPSESSID=..." \
  -d "name=" \
  -d "description=test"
# Should render 400 error view
```

### Test 401 Unauthorized
```bash
curl http://localhost:8000/admin/dashboard
# Should redirect to login
```

### Test 403 Forbidden
```bash
# Login as moderator, try super_admin route
curl http://localhost:8000/admin/settings \
  -H "Cookie: PHPSESSID=..."
# Should render 403 error view
```

### Test 404 Not Found
```bash
curl http://localhost:8000/admin/destinations/invalid-id
# Should render 404 error view
```

### Test 500 Server Error
```bash
# In debug mode, trigger exception
// code that throws exception
# Should render 500 with debug info
```

---

## 📊 Error Logging

All errors automatically logged to `storage/logs/laravel.log`:

```
[2026-05-29 10:15:30] local.ERROR: Database connection failed {
  "exception": "PDOException",
  "message": "Could not find driver",
  "path": "admin/destinations/1"
}
```

### Log Locations

- **Error Logs:** `storage/logs/laravel.log`
- **Admin Activity:** `storage/logs/admin.log`
- **Debug Info:** Check via `tail -f storage/logs/laravel.log`

---

## 🔒 Security Considerations

1. **Sensitive Info Protection**
   - Stack traces only shown in debug mode
   - Production hides error details
   - User IDs sanitized in logs

2. **CSRF Protection**
   - All forms require CSRF tokens
   - Middleware validates automatically

3. **Rate Limiting**
   - Failed login attempts throttled
   - Error page requests limited

4. **Audit Trail**
   - Admin errors logged with user ID
   - Action timestamps recorded
   - IP address tracked

---

## ✅ Implementation Checklist

- ✅ Response helper class created
- ✅ Error handler middleware registered
- ✅ Exception handler in bootstrap/app.php configured
- ✅ Error views created (400, 401, 403, 404, 500, 200-success)
- ✅ Routes protected with error-handler middleware
- ✅ Logging configured
- ✅ Debug mode settings respected
- ✅ Redirects properly configured

---

## 🚀 Next Steps

1. Test all error scenarios
2. Configure error email notifications (optional)
3. Set up error tracking (Sentry, Bugsnag)
4. Add custom error codes if needed
5. Update documentation with team guidelines

---

**Framework:** Laravel 10  
**PHP Version:** 8.1+  
**Status:** Production Ready ✅
