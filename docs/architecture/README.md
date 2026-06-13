# 📐 Architecture Documentation

Dokumentasi arsitektur sistem Admin Panel Wisata Toba.

## 📄 File List

### [FILE_STRUCTURE.md](./FILE_STRUCTURE.md)
Struktur lengkap folder dan file dalam proyek
- Organisasi folder app/, resources/, routes/
- Penjelasan setiap direktori
- Konvensi penamaan file

### [VIEW_STRUCTURE.md](./VIEW_STRUCTURE.md)
Struktur Blade templates dan views
- Layouts (admin, guest, auth)
- Components dan partials
- View organization per modul

### [MIDDLEWARE_DOCUMENTATION.md](./MIDDLEWARE_DOCUMENTATION.md)
Dokumentasi middleware sistem
- AdminMiddleware
- AdminRoleMiddleware
- AdminActivityLogMiddleware
- AdminErrorHandler
- Custom middleware lainnya

### [SITEMAP.md](./SITEMAP.md)
Struktur navigasi dan routing aplikasi
- Menu hierarchy
- Route patterns
- Access control per module

---

## 🏗️ Architecture Overview

### MVC Pattern
```
User Request
    ↓
Routes (web.php, admin.php)
    ↓
Middleware Stack
    ↓
Controller (app/Http/Controllers/Admin/)
    ↓
Model (app/Models/)
    ↓
View (resources/views/admin/)
    ↓
Response
```

### Database Architecture
- **MySQL**: Admin management, settings, audit logs
- **MongoDB**: Chat sessions, recommendations, reports, destinations

### Layer Structure
1. **Presentation Layer**: Blade views + Alpine.js
2. **Application Layer**: Controllers + Request validation
3. **Domain Layer**: Models + Business logic
4. **Data Layer**: Database + Cache + File storage

---

## 🔗 Related Documentation

- [Guides](../guides/) - Usage guides
- [Diagrams](../diagrams/) - Visual diagrams
- [Implementation](../implementation/) - Implementation details
