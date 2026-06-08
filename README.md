# 🏝️ Admin Panel - Wisata Toba

Sistem manajemen konten dan monitoring untuk platform pariwisata Danau Toba.

## 📖 Dokumentasi Lengkap

Semua dokumentasi tersedia di folder [`docs/`](./docs/)

### 🚀 Quick Start
- **Mulai di sini**: [`docs/guides/README_START_HERE.md`](./docs/guides/README_START_HERE.md)
- **Panduan Cepat**: [`docs/guides/QUICK_REFERENCE.md`](./docs/guides/QUICK_REFERENCE.md)
- **Perintah CLI**: [`docs/guides/QUICK_COMMANDS.md`](./docs/guides/QUICK_COMMANDS.md)

### 📚 Dokumentasi Utama
- 📐 [Architecture](./docs/architecture/) - Arsitektur sistem
- 📖 [Guides](./docs/guides/) - Panduan penggunaan
- 🔧 [Implementation](./docs/implementation/) - Detail implementasi
- 📊 [Diagrams](./docs/diagrams/) - Diagram visual
- 📋 [Reports](./docs/reports/) - Laporan perbaikan
- 🧪 [Testing](./docs/testing/) - Dokumentasi testing

---

## ✨ Fitur Utama

### Content Management
- ✅ Destinasi Wisata
- ✅ Event/Acara
- ✅ Berita & Promosi
- ✅ Budaya Lokal
- ✅ Fasilitas Umum
- ✅ Panduan Wisata
- ✅ Carousel Banners

### User Management
- ✅ User Management
- ✅ Review Moderation
- ✅ Review Sentiment Analysis

### Monitoring & Analytics
- ✅ Analytics Dashboard
- ✅ Chatbot Logs (MongoDB)
- ✅ Recommendation Logs (MongoDB)
- ✅ Reports/Pengaduan (MongoDB)

### Settings & Configuration
- ✅ General Settings (Logo, Colors, Features)
- ✅ API Keys Management
- ✅ AI Configuration
- ✅ Audit Logs (Activity Tracking)

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP**: 8.1+
- **Database**: MySQL + MongoDB
- **Cache**: Redis (optional)

### Frontend
- **Template Engine**: Blade
- **JavaScript**: Alpine.js
- **CSS**: Tailwind CSS
- **Charts**: Chart.js
- **Icons**: Font Awesome

### External Services
- MongoDB for logs and user-generated content
- File storage for uploads
- Email service for notifications

---

## 🚀 Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js & NPM
- MySQL 8.0+
- MongoDB 5.0+

### Setup Steps

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd AdminPanel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=admin_panel
   DB_USERNAME=root
   DB_PASSWORD=

   MONGODB_DSN=mongodb://localhost:27017
   MONGODB_DATABASE=wisata_toba
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start server**
   ```bash
   php artisan serve
   ```

8. **Access admin panel**
   ```
   URL: http://localhost:8000/admin
   Default Login:
   - Email: admin@example.com
   - Password: password
   ```

For detailed setup instructions, see: [`docs/guides/README_START_HERE.md`](./docs/guides/README_START_HERE.md)

---

## 📱 Admin Panel Access

### Default Roles
- **Super Admin**: Full access to all modules
- **Admin**: Access to content and monitoring (limited settings)
- **Editor**: Content management only

### Login URL
```
/admin/login
```

---

## 🔒 Security Features

- ✅ Role-based access control (RBAC)
- ✅ Activity logging (Audit trail)
- ✅ CSRF protection
- ✅ XSS protection
- ✅ File upload validation
- ✅ Password hashing (bcrypt)
- ✅ Session management

---

## 🧪 Testing

Run tests:
```bash
php artisan test
```

For testing documentation, see: [`docs/testing/`](./docs/testing/)

---

## 📊 Performance

- Optimized database queries with eager loading
- Caching for frequently accessed data (10 min TTL)
- Pagination for large datasets
- Async image loading
- Minified CSS/JS assets

---

## 🤝 Contributing

1. Create feature branch
2. Make changes
3. Write tests
4. Update documentation
5. Submit pull request

See: [`docs/implementation/IMPLEMENTATION_CHECKLIST.md`](./docs/implementation/IMPLEMENTATION_CHECKLIST.md)

---

## 📞 Support

For questions or issues:
- Check documentation in [`docs/`](./docs/)
- Review error handling guide: [`docs/guides/ERROR_HANDLING_GUIDE.md`](./docs/guides/ERROR_HANDLING_GUIDE.md)
- Contact development team

---

## 📝 License

© 2024-2026 Admin Panel Wisata Toba. All rights reserved.

---

## 🗺️ Project Structure

```
AdminPanel/
├── app/                    # Application logic
│   ├── Console/           # CLI commands
│   ├── Helpers/           # Helper classes
│   ├── Http/              # Controllers, Middleware, Requests
│   └── Models/            # Eloquent models
├── bootstrap/             # Framework bootstrap
├── config/                # Configuration files
├── database/              # Migrations, seeds, factories
├── docs/                  # 📚 Documentation (START HERE)
│   ├── architecture/      # System architecture
│   ├── guides/            # Usage guides
│   ├── implementation/    # Implementation docs
│   ├── diagrams/          # Visual diagrams
│   ├── reports/           # Bug reports & fixes
│   └── testing/           # Testing docs
├── public/                # Public assets
├── resources/             # Views, assets
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript
│   └── views/            # Blade templates
├── routes/                # Route definitions
│   ├── web.php           # Public routes
│   └── admin.php         # Admin routes
├── storage/               # File storage
├── tests/                 # Test files
└── vendor/                # Composer dependencies
```

For detailed structure, see: [`docs/architecture/FILE_STRUCTURE.md`](./docs/architecture/FILE_STRUCTURE.md)
