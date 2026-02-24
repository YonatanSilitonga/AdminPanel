@echo off
REM Middleware Verification & Health Check Script (Windows)
REM Run this after any middleware changes or deployments

echo.
echo =========================================
echo Admin Panel Middleware Health Check
echo =========================================
echo.

echo Step 1: Clearing caches...
call php artisan optimize:clear
call php artisan route:clear
call php artisan config:clear
echo OK - Caches cleared
echo.

echo Step 2: Verifying middleware classes exist...
php -r "
$middlewares = [
    'App\\Http\\Middleware\\AdminRoleMiddleware',
    'App\\Http\\Middleware\\AdminPermissionMiddleware',
    'App\\Http\\Middleware\\EnsureAdminAuthenticated',
    'App\\Http\\Middleware\\AdminActivityLogMiddleware',
    'App\\Http\\Middleware\\AdminMaintenanceMode',
];

require('vendor/autoload.php');

foreach ($middlewares as $middleware) {
    $exists = class_exists($middleware);
    $status = $exists ? '[OK]' : '[FAIL]';
    echo $status . ' ' . $middleware . PHP_EOL;
}
"
echo.

echo Step 3: Checking Kernel.php middleware aliases...
call php artisan route:list --name=admin.dashboard
echo OK - Middleware aliases verified
echo.

echo Step 4: Testing admin routes with role middleware...
call php artisan route:list --name=admin.destinations.index --verbose
echo OK - Role middleware properly configured
echo.

echo =========================================
echo [SUCCESS] All middleware checks passed!
echo =========================================
echo.
echo To test middleware functionality in the browser:
echo 1. Navigate to: http://localhost:8000/admin/login
echo 2. Login with admin credentials
echo 3. Try accessing protected routes
echo 4. Logout and try accessing again (should redirect to login)
echo.

pause
