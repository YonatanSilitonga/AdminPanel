# Smart Tourism Admin Panel - Quick Command Reference

## Setup & Initialization

### Fresh Database Setup (Recommended First Time)
```bash
php artisan migrate:fresh --seed
```
**What it does:**
- Drops all tables
- Runs all migrations
- Runs all seeders
- Creates 3 admin accounts
- Creates 15 test users
- Creates 10 destinations with 20 events
- Creates 20 reviews
- Creates 5 reports

**Output should show:**
```
Dropping all tables ... done
Running migrations:
  - 2024_01_01_000000_create_users_table ... DONE
  - 2024_01_01_000001_create_cache_table ... DONE
  - 2024_02_19_000001_create_admin_authentication_tables ... DONE
  - 2024_02_19_000002_create_content_management_tables ... DONE

Running seeders:
  - Database\Seeders\AdminSeeder ... DONE
  - Database\Seeders\DatabaseSeeder ... DONE
  
Admin users seeded successfully!
```

---

## Development Server

### Start Development Server
```bash
php artisan serve
```
**Access at:** http://localhost:8000

### Access Admin Panel
```
URL: http://localhost:8000/admin/login
Email: superadmin@smarttourism.local
Password: SuperAdmin@123
```

### Start with Port Override
```bash
php artisan serve --port=8001
```

---

## Database Management

### Run Only Migrations (Preserve Data)
```bash
php artisan migrate
```

### Rollback Last Migration
```bash
php artisan migrate:rollback
```

### Fresh Migrations Without Seeds
```bash
php artisan migrate:fresh
```

### Run Only Seeders
```bash
php artisan db:seed
```

### Run Specific Seeder
```bash
php artisan db:seed --class=AdminSeeder
```

---

## Testing & Verification

### Start Interactive Shell
```bash
php artisan tinker
```

### Check Admin Accounts
```bash
# In tinker
> Admin::all()
> Admin::count()  # Should be 3
> Admin::pluck('email')  # See all emails
```

### Check Test Data
```bash
# In tinker
> User::count()          # Should be 15
> Destination::count()   # Should be 10
> Event::count()         # Should be 20
> Review::count()        # Should be 20
> Report::count()         # Should be 5
```

### Check Admin Permissions
```bash
# In tinker
> Admin::first()->role  # Get role
> Admin::first()->getPermissions()  # Get all permissions
```

### Verify Password Hash
```bash
# In tinker
> $admin = Admin::first()
> Hash::check('SuperAdmin@123', $admin->password)  # Should return true
```

---

## Cache Management

### Clear All Caches
```bash
php artisan cache:clear
```

### Clear View Cache
```bash
php artisan view:clear
```

### Clear Config Cache
```bash
php artisan config:clear
```

### Clear Application Cache
```bash
php artisan cache:flush
```

### Clear All (Comprehensive)
```bash
php artisan cache:clear && \
php artisan view:clear && \
php artisan config:clear && \
php artisan route:clear
```

---

## Logging & Debugging

### View Application Logs
```bash
tail -f storage/logs/laravel.log
```

### Clear Logs
```bash
> rm storage/logs/laravel.log
```

### Check Log for Errors
```bash
grep -i error storage/logs/laravel.log
```

### Check Failed Logins
```bash
grep -i "Failed login" storage/logs/laravel.log
```

---

## Database Troubleshooting

### Reset Admin Password in Tinker
```bash
# Start tinker
php artisan tinker

# Find admin
> $admin = Admin::where('email', 'superadmin@smarttourism.local')->first()

# Reset password
> $admin->password = Hash::make('NewPassword123')
> $admin->save()

# Verify
> exit
```

### Check Database Connection
```bash
# In tinker
> DB::connection()->getPdo()  # Should work without errors
```

### Check Table Exists
```bash
# In tinker
> Schema::hasTable('admins')  # Should return true
```

---

## Authentication Testing

### Test Login Manually (Tinker)
```bash
php artisan tinker

> $admin = Admin::authenticate('superadmin@smarttourism.local', 'SuperAdmin@123')
> if ($admin) { echo "Login successful"; } else { echo "Login failed"; }
> exit
```

### Check Authenticated User (After Login)
```bash
# In web route or controller
auth('admin')->user()  # Get current admin
auth('admin')->check()  # Check if authenticated
auth('admin')->id()     # Get admin ID
```

---

## File Uploads

### Check Uploads Directory
```bash
ls -la storage/app/public/destinations/thumbnails/
ls -la storage/app/public/destinations/covers/
```

### Create Symbolic Link for Storage
```bash
php artisan storage:link
```

### Clear Uploads (Be Careful!)
```bash
rm -rf storage/app/public/destinations/*
```

---

## Queue & Jobs

### List Jobs
```bash
php artisan queue:work
```

### Process Failed Jobs
```bash
php artisan queue:retry all
```

### Clear Failed Jobs
```bash
php artisan queue:flush
```

---

## Migration & Seeding

### Create New Migration
```bash
php artisan make:migration create_table_name
```

### Create New Seeder
```bash
php artisan make:seeder SeederName
```

### Create New Factory
```bash
php artisan make:factory FactoryName
```

---

## Testing

### Run All Tests
```bash
php artisan test
```

### Run Feature Tests Only
```bash
php artisan test --filter Feature
```

### Run Specific Test
```bash
php artisan test tests/Feature/AdminAuthTest.php
```

### Run Test with Output
```bash
php artisan test --verbose
```

---

## Code Quality

### Format Code (PHP Formatter)
```bash
./vendor/bin/pint
```

### Check Code Standards
```bash
./vendor/bin/phpstan analyse
```

### Run Linter
```bash
./vendor/bin/phpcs
```

---

## Route Management

### List All Routes
```bash
php artisan route:list
```

### List Admin Routes Only
```bash
php artisan route:list --path=admin
```

### Clear Route Cache
```bash
php artisan route:clear
```

---

## Configuration

### Show Config Value
```bash
php artisan config:show auth
```

### Show Specific Config
```bash
# In tinker
> config('admin-panel.pagination.per_page')
> config('auth.guards.admin.driver')
```

---

## Model Introspection

### Inspect Model
```bash
# In tinker
> Admin::first()->attributesToArray()
> Admin::first()->getAttributes()
> Admin::first()->getTable()  # Get table name
```

### Check Model Relationships
```bash
# In tinker
> Admin::first()->role
> Admin::first()->role->permissions
```

---

## Debugging Queries

### Log Queries (Enable in Config)
```php
// In routes or controller
DB::listen(function ($query) {
    echo $query->sql;
});
```

### Profile Queries with QueryBuilder
```bash
# In tinker
> DB::enableQueryLog()
> Admin::all()
> DB::getQueryLog()
```

---

## Common Issues & Quick Fixes

### Issue: "No table called admins"
**Fix:**
```bash
php artisan migrate:fresh --seed
```

### Issue: "Admin not found for email"
**Fix:**
```bash
# Check if seeded
php artisan tinker
> Admin::where('email', 'superadmin@smarttourism.local')->count()

# If 0, re-seed
php artisan db:seed --class=AdminSeeder
```

### Issue: "Undefined method: givePermission"
**Fix:**
```bash
# Check Role model has givePermission method
# Check if role table has proper relationships
```

### Issue: "Password hashing not working"
**Fix:**
```bash
# Verify Hash facade is imported
use Illuminate\Support\Facades\Hash;

# Test hash
Hash::check('password', Hash::make('password'))  # Should be true
```

### Issue: "View composer errors"
**Fix:**
```bash
php artisan cache:clear
php artisan view:clear
```

---

## Production Checklist Commands

### Pre-Deployment
```bash
# 1. Run tests
php artisan test

# 2. Check for errors
grep -i error storage/logs/laravel.log

# 3. Verify database
php artisan migrate:status

# 4. Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Health Check
```bash
# In tinker
> Admin::count() > 0  # Should be true
> DB::connection()->getPdo()  # Should work
> Storage::exists('.')  # Should be true
```

---

## Batch Operations

### Create Multiple Admins
```bash
# In tinker
> Admin::factory(5)->create(['role_id' => 1])
```

### Create Test Data
```bash
# In tinker
> User::factory(100)->create()
> Destination::factory(50)->create()
> Review::factory(200)->create()
```

### Delete Specific Data
```bash
# In tinker
> Review::where('status', 'rejected')->delete()
> Admin::where('is_active', false)->forceDelete()  # Permanent delete
```

---

## Useful Aliases for .bashrc or .zshrc

```bash
# Add to ~/.bashrc or ~/.zshrc

# Laravel aliases
alias pa='php artisan'
alias pamt='php artisan migrate:fresh --seed'
alias paclear='php artisan cache:clear && php artisan view:clear'
alias pserve='php artisan serve'
alias ptinker='php artisan tinker'
alias ptest='php artisan test'
alias proutes='php artisan route:list'
alias pmodels='php artisan tinker'

# Then use:
pamt          # Fresh migration with seeding
paclear       # Clear all caches
pserve        # Start server
ptinker       # Start tinker
```

---

## Frequently Used Tinker Commands

```bash
php artisan tinker

# Admins
> Admin::count()
> Admin::first()
> Admin::find(1)->toArray()
> Admin::pluck('email')
> Admin::where('role_id', 1)->count()

# Check Everything
> Admin::count() . ' admins'
> User::count() . ' users'
> Destination::count() . ' destinations'
> Review::count() . ' reviews'
> Report::count() . ' reports'

# Reset Password
> $admin = Admin::find(1); $admin->password = Hash::make('NewPass123'); $admin->save();

# Check Auth
> auth('admin')->check()
> auth('admin')->user()

# Database Info
> DB::connection()->getName()
> Schema::getTables()
```

---

## Performance Monitoring

### Check Query Count
```bash
# In tinker
> DB::enableQueryLog()
> Admin::with('role')->get()
> count(DB::getQueryLog())  # Should be low (2-3, not 50+)
```

### Check Slow Queries
```bash
# Enable slow query log in MySQL
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;

# Then check
tail -f /var/log/mysql/slow.log
```

---

## Summary of Most Important Commands

```bash
# Setup
php artisan migrate:fresh --seed

# Development
php artisan serve

# Debugging
php artisan tinker

# Testing
php artisan test

# Cache Clear
php artisan cache:clear

# View Routes
php artisan route:list

# Check Status
php artisan status
```

---

## Emergency Commands

### If Database is Broken
```bash
php artisan migrate:reset
php artisan migrate:fresh --seed
```

### If Caches are Broken
```bash
php artisan cache:flush
php artisan view:clear
php artisan config:clear
```

### If Routes are Broken
```bash
php artisan route:clear
php artisan route:list
```

### Full System Reset (Nuclear Option)
```bash
php artisan migrate:reset && \
php artisan migrate:fresh --seed && \
php artisan cache:flush && \
php artisan view:clear
```

---

**Remember**: Always backup data before running destructive commands like `migrate:reset` or `migrate:fresh`!

For detailed explanations, see the documentation files:
- AUTHENTICATION_FIX_SUMMARY.md
- SETUP_AUTHENTICATION.md
- VERIFICATION_CHECKLIST.md
