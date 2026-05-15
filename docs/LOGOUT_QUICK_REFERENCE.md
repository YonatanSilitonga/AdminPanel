# ⚡ LOGOUT QUICK REFERENCE

## ✅ Correct Usage

### In Middleware
```php
public function handle(Request $request, Closure $next): Response
{
    /** @var \Illuminate\Auth\SessionGuard $guard */
    $guard = auth('admin');
    
    if (!$guard->check()) {
        return redirect()->route('admin.login');
    }
    
    $admin = $guard->user();
    
    if (!$admin || !$admin->is_active) {
        $guard->logout();  // ✅ CORRECT - logout from guard
        return redirect()->route('admin.login');
    }
    
    return $next($request);
}
```

### In Controller
```php
public function logout(Request $request)
{
    /** @var \Illuminate\Auth\SessionGuard $guard */
    $guard = auth('admin');
    
    // Logout from guard
    $guard->logout();
    
    // Clean up session
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect()->route('admin.login');
}
```

### Alternative Syntax
```php
// Option 1: Direct call
auth('admin')->logout();

// Option 2: Using Auth facade
\Auth::guard('admin')->logout();

// Option 3: Using variable (BEST - IDE friendly)
/** @var \Illuminate\Auth\SessionGuard $guard */
$guard = auth('admin');
$guard->logout();
```

---

## ❌ Incorrect Usage

```php
// DON'T DO THIS - User model doesn't have logout()
$admin = auth('admin')->user();
$admin->logout();  // ❌ ERROR: Undefined method

// DON'T DO THIS - Wrong guard
auth()->logout();  // ❌ Uses 'web' guard, not 'admin'

// DON'T DO THIS - Chained call without type hint
auth('admin')->user()->logout();  // ❌ Error
```

---

## 🔍 Type Hints Required

Always add type hint for IDE support:

```php
/** @var \Illuminate\Auth\SessionGuard $guard */
$guard = auth('admin');
```

This ensures:
- ✅ IDE autocomplete works
- ✅ No "Undefined method" warnings
- ✅ Static analysis passes
- ✅ Better code documentation

---

## 🎯 Logout Flow Checklist

```php
public function logout(Request $request)
{
    // 1. Get guard with type hint
    /** @var \Illuminate\Auth\SessionGuard $guard */
    $guard = auth('admin');
    
    // 2. Optional: Log the action
    Log::info('Admin logout', ['admin_id' => $guard->user()?->id]);
    
    // 3. Logout from guard (destroys auth)
    $guard->logout();
    
    // 4. Invalidate session (security)
    $request->session()->invalidate();
    
    // 5. Regenerate CSRF token (security)
    $request->session()->regenerateToken();
    
    // 6. Redirect
    return redirect()->route('admin.login');
}
```

---

## 🛡️ Security Best Practices

| Step | Why Important |
|------|---------------|
| `$guard->logout()` | Destroys authentication state |
| `session()->invalidate()` | Clears all session data |
| `session()->regenerateToken()` | Prevents CSRF token reuse |
| Redirect to login | Prevents access to protected pages |

---

## 🧪 Testing

```php
// Feature Test
public function test_admin_can_logout()
{
    $admin = Admin::factory()->create();
    
    $this->actingAs($admin, 'admin')
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));
    
    $this->assertGuest('admin');
}
```

---

## 📋 Common Errors & Solutions

| Error | Solution |
|-------|----------|
| "Undefined method logout" | Add type hint for guard |
| "Call to member function on null" | Check if user exists before logout |
| "Session expired" | Ensure using correct guard ('admin') |
| IDE warning on logout() | Add `/** @var \Illuminate\Auth\SessionGuard */` |

---

## 🚀 Commands After Changes

```bash
php artisan optimize:clear
php artisan config:clear
composer dump-autoload
php artisan route:list --name=logout
```

---

**Quick Tip:** Always logout from **Guard**, never from **Model**!
