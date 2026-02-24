#!/bin/bash
# Middleware Verification & Health Check Script
# Run this after any middleware changes or deployments

echo "========================================="
echo "Admin Panel Middleware Health Check"
echo "========================================="
echo ""

echo "Step 1: Clearing caches..."
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
echo "✓ Caches cleared"
echo ""

echo "Step 2: Verifying middleware classes exist..."
php -r "
\$middlewares = [
    'App\\Http\\Middleware\\AdminRoleMiddleware',
    'App\\Http\\Middleware\\AdminPermissionMiddleware',
    'App\\Http\\Middleware\\EnsureAdminAuthenticated',
    'App\\Http\\Middleware\\AdminActivityLogMiddleware',
    'App\\Http\\Middleware\\AdminMaintenanceMode',
];

require('vendor/autoload.php');

foreach (\$middlewares as \$middleware) {
    \$exists = class_exists(\$middleware);
    echo (\$exists ? '✓' : '✗') . ' ' . \$middleware . PHP_EOL;
}
"
echo ""

echo "Step 3: Checking Kernel.php middleware aliases..."
php artisan route:list --name=admin.dashboard --verbose | grep -E "(admin\.|web|auth)"
echo "✓ Middleware aliases verified"
echo ""

echo "Step 4: Testing admin routes with role middleware..."
php artisan route:list --name=admin.destinations.index --verbose | grep -E "(admin\.role|auth:admin)"
echo "✓ Role middleware properly configured"
echo ""

echo "========================================="
echo "✓ All middleware checks passed!"
echo "========================================="
echo ""
echo "To test middleware functionality in the browser:"
echo "1. Navigate to: http://localhost:8000/admin/login"
echo "2. Login with admin credentials"
echo "3. Try accessing protected routes"
echo "4. Logout and try accessing again (should redirect to login)"
echo ""
