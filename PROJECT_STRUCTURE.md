# 📁 Project Structure Overview

> **Toba Tourism Admin Panel - Complete Project Organization**

---

## 🏗️ Root Directory Structure

```
AdminPanel/
│
├── 📁 Folders/
│   ├── app/                # Application code (Models, Controllers, Middleware)
│   ├── bootstrap/          # Framework bootstrap files
│   ├── config/             # Configuration files
│   ├── database/           # Migrations, seeders, factories
│   ├── docs/               # 📚 Complete documentation (65 files)
│   ├── public/             # Public assets (index.php, images, uploads)
│   ├── resources/          # Views (Blade), CSS, JS
│   ├── routes/             # Route definitions (web.php, admin.php)
│   ├── storage/            # Logs, cache, uploaded files
│   ├── tests/              # PHPUnit tests
│   ├── vendor/             # Composer dependencies (gitignored)
│   ├── node_modules/       # NPM dependencies (gitignored)
│   ├── scripts/            # Custom scripts
│   ├── scratch/            # Temporary/scratch files
│   └── temp/               # Temporary files
│
├── 📄 Configuration Files/
│   ├── .editorconfig       # Editor configuration
│   ├── .env                # Environment variables (gitignored)
│   ├── .env.example        # Environment template
│   ├── .gitattributes      # Git attributes
│   ├── .gitignore          # Git ignore rules
│   ├── composer.json       # PHP dependencies
│   ├── composer.lock       # Locked PHP dependencies
│   ├── package.json        # Node dependencies
│   ├── package-lock.json   # Locked Node dependencies
│   ├── phpunit.xml         # PHPUnit configuration
│   └── vite.config.js      # Vite bundler configuration
│
├── 📄 Entry Points/
│   ├── artisan             # Laravel CLI tool
│   └── public/index.php    # Application entry point
│
├── 📄 Documentation/
│   ├── README.md                      # Project README
│   ├── DOCUMENTATION_READY.md         # Documentation ready announcement
│   ├── REORGANIZATION_COMPLETE.md     # Reorganization summary (ID)
│   └── PROJECT_STRUCTURE.md           # This file
│
└── 📄 Scripts/
    ├── verify-middleware.bat          # Middleware verification (Windows)
    └── verify-middleware.sh           # Middleware verification (Unix)
```

---

## 📚 Documentation Folder (`docs/`)

**Location**: `./docs/`  
**Total Files**: 65+ documentation files  
**Organization**: 6 main categories

### Structure:

```
docs/
├── 📄 Root Files (16 files)
│   ├── README.md                      ⭐ START HERE
│   ├── INDEX.md                       📑 Master index
│   ├── NAVIGATION_GUIDE.md            🧭 Navigate by role
│   ├── FOLDER_STRUCTURE.md            📁 Structure docs
│   ├── REORGANIZATION_SUMMARY.md      📦 Reorganization summary
│   ├── WHAT_CHANGED.md                📝 Detail changes
│   ├── 00_START_HERE.md               🎯 Quick start
│   └── ... (other docs)
│
├── 📁 architecture/ (6 files)         🏗️ System architecture
│   ├── FILE_STRUCTURE.md              # Project file structure
│   ├── MIDDLEWARE_DOCUMENTATION.md    # Middleware system
│   ├── VIEW_STRUCTURE.md              # Blade views
│   └── ...
│
├── 📁 diagrams/ (4 files)             📊 Visual diagrams
│   ├── README.md                      # Diagram guide
│   ├── sitemap-monitoring-settings.puml           ⭐ SIMPLE
│   ├── sitemap-monitoring-settings-detailed.puml  📋 DETAILED
│   └── sitemap-monitoring-settings-dataflow.puml  🏗️ ARCHITECTURE
│
├── 📁 guides/ (7 files)               📖 Practical guides
│   ├── QUICK_REFERENCE.md             ⭐ Most used
│   ├── QUICK_COMMANDS.md              ⭐ Most used
│   ├── ERROR_HANDLING_GUIDE.md        🔍 Troubleshooting
│   └── ...
│
├── 📁 implementation/ (10 files)      🛠️ Implementation docs
│   ├── IMPLEMENTATION_GUIDE.md        # Main guide
│   ├── AI_SMART_FEATURES_IMPLEMENTATION.md  # AI features
│   └── ...
│
├── 📁 reports/ (17 files)             📋 Bug fixes & reports
│   ├── COMPLETION_REPORT.md           # Project completion
│   ├── PHASE_1_SUMMARY.md             # Phase 1 summary
│   └── ...
│
└── 📁 testing/ (5 files)              🧪 Testing & QA
    ├── VERIFICATION_CHECKLIST.md      # Verification
    ├── Laporan_Test_Case_Lengkap.md   # Test cases
    └── ...
```

**👉 For complete documentation details, see: `docs/FOLDER_STRUCTURE.md`**

---

## 🎯 Key Directories

### `app/` - Application Code

```
app/
├── Console/           # Artisan commands
├── Helpers/           # Helper classes & functions
├── Http/              # Controllers, Middleware, Requests
│   ├── Controllers/   # Application controllers
│   ├── Middleware/    # Middleware classes
│   └── Requests/      # Form request validation
└── Models/            # Eloquent models (MySQL & MongoDB)
```

### `resources/` - Frontend Assets

```
resources/
├── views/             # Blade templates
│   ├── admin/        # Admin panel views
│   │   ├── layouts/  # Layout templates
│   │   ├── auth/     # Authentication views
│   │   ├── dashboard/
│   │   ├── destinations/
│   │   ├── events/
│   │   ├── reviews/
│   │   ├── settings/
│   │   ├── reports/
│   │   └── ...
│   └── components/   # Reusable components
├── css/              # Stylesheets (Tailwind)
└── js/               # JavaScript files (Alpine.js)
```

### `routes/` - Route Definitions

```
routes/
├── web.php           # Public web routes
└── admin.php         # Admin panel routes
```

### `database/` - Database Files

```
database/
├── migrations/       # Database migrations
├── seeders/          # Database seeders
└── factories/        # Model factories
```

### `public/` - Public Assets

```
public/
├── index.php         # Application entry point
├── storage/          # Symlink to storage/app/public
├── uploads/          # User uploads
├── css/              # Compiled CSS
├── js/               # Compiled JS
└── images/           # Static images
```

### `storage/` - Storage Files

```
storage/
├── app/              # Application storage
│   ├── public/      # Public files (symlinked)
│   └── private/     # Private files
├── framework/        # Framework cache, sessions, views
│   ├── cache/
│   ├── sessions/
│   └── views/
└── logs/             # Application logs
```

---

## 📝 Important Configuration Files

### `.env` (Environment)
Contains sensitive configuration:
- Database credentials
- MongoDB connection
- API keys
- App settings

**⚠️ NEVER commit this file to git!**

### `composer.json` (PHP Dependencies)
Manages PHP packages:
- Laravel framework
- MongoDB driver
- Other PHP libraries

### `package.json` (Node Dependencies)
Manages JavaScript packages:
- Vite (bundler)
- Tailwind CSS
- Alpine.js
- Chart.js

### `.gitignore` (Git Ignore)
Specifies files to ignore in git:
- `/vendor/`
- `/node_modules/`
- `.env`
- `/storage/*.key`
- `*.cache`

---

## 🚀 Entry Points

### For Developers

| Entry Point | Purpose |
|-------------|---------|
| `README.md` | Project overview |
| `docs/README.md` | Documentation entry |
| `docs/INDEX.md` | Complete index |
| `docs/guides/QUICK_REFERENCE.md` | Quick reference |

### For Application

| Entry Point | Purpose |
|-------------|---------|
| `public/index.php` | Web application entry |
| `artisan` | CLI tool entry |
| `/admin` route | Admin panel entry |

---

## 📊 File Statistics

| Category | Count |
|----------|-------|
| **Documentation** | 65+ files |
| **Controllers** | 25+ files |
| **Models** | 30+ files |
| **Views** | 100+ files |
| **Migrations** | 20+ files |
| **Tests** | 15+ files |

---

## 🎨 Naming Conventions

### Files
- **Controllers**: `PascalCase` + `Controller.php` (e.g., `DestinationController.php`)
- **Models**: `PascalCase` + `.php` (e.g., `Destination.php`)
- **Views**: `kebab-case` + `.blade.php` (e.g., `index.blade.php`)
- **Migrations**: `date_snake_case.php` (e.g., `2024_01_01_create_destinations_table.php`)
- **Documentation**: `UPPERCASE_SNAKE_CASE.md` for important files

### Folders
- **Laravel folders**: `lowercase` (app, public, resources)
- **Documentation folders**: `lowercase` (architecture, diagrams, guides)

---

## 🔍 Finding Files

### By Purpose:

| Need | Location |
|------|----------|
| **Documentation** | `docs/` folder |
| **Controllers** | `app/Http/Controllers/` |
| **Models** | `app/Models/` |
| **Views** | `resources/views/` |
| **Routes** | `routes/` |
| **Config** | `config/` |
| **Migrations** | `database/migrations/` |
| **Tests** | `tests/` |

### By Feature:

| Feature | Files |
|---------|-------|
| **Destinations** | `app/Http/Controllers/Admin/DestinationController.php`<br>`app/Models/MongoDB/MongoDestination.php`<br>`resources/views/admin/destinations/` |
| **Authentication** | `app/Http/Controllers/Admin/AdminAuthController.php`<br>`app/Http/Middleware/AdminMiddleware.php`<br>`resources/views/admin/auth/` |
| **Settings** | `app/Http/Controllers/Admin/SettingsController.php`<br>`app/Models/AppSetting.php`<br>`resources/views/admin/settings/` |

---

## 💡 Best Practices

### 1. Keep Root Clean
- Don't create files directly in root
- Use appropriate folders (app, resources, docs, etc.)
- Move temporary files to `temp/` or `scratch/`

### 2. Follow Laravel Structure
- Controllers in `app/Http/Controllers/`
- Models in `app/Models/`
- Views in `resources/views/`
- Routes in `routes/`

### 3. Document Everything
- Add new docs to `docs/` folder
- Update `docs/INDEX.md` when adding docs
- Follow existing structure

### 4. Git Best Practices
- Don't commit `.env`
- Don't commit `vendor/` or `node_modules/`
- Don't commit generated files (cache, logs)
- Use `.gitignore` properly

---

## 📂 Folder Purposes

| Folder | Purpose | Commit to Git? |
|--------|---------|----------------|
| `app/` | Application code | ✅ Yes |
| `bootstrap/` | Framework bootstrap | ✅ Yes |
| `config/` | Configuration | ✅ Yes |
| `database/` | Migrations, seeds | ✅ Yes |
| `docs/` | Documentation | ✅ Yes |
| `public/` | Public assets | ✅ Yes (except uploads) |
| `resources/` | Views, assets | ✅ Yes |
| `routes/` | Route definitions | ✅ Yes |
| `storage/` | Generated files | ❌ No (except .gitkeep) |
| `tests/` | Test files | ✅ Yes |
| `vendor/` | Composer packages | ❌ No (install via composer) |
| `node_modules/` | NPM packages | ❌ No (install via npm) |
| `temp/` | Temporary files | ❌ No |
| `scratch/` | Scratch files | ❌ No |

---

## 🎯 Quick Navigation

### Need Documentation?
→ Start from `docs/README.md`

### Need to Understand Code?
→ Check `app/Http/Controllers/Admin/`

### Need to Modify Views?
→ Go to `resources/views/admin/`

### Need to Add Routes?
→ Edit `routes/admin.php`

### Need to Run Commands?
→ Use `php artisan` (see `docs/guides/QUICK_COMMANDS.md`)

---

## 📞 More Information

For more detailed information:

- **Complete Documentation**: `docs/README.md`
- **Documentation Index**: `docs/INDEX.md`
- **Navigation Guide**: `docs/NAVIGATION_GUIDE.md`
- **Folder Structure**: `docs/FOLDER_STRUCTURE.md`

---

**Last Updated**: June 7, 2026  
**Version**: 1.2.0  
**Maintained by**: Development Team
