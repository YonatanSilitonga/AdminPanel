# 📊 Diagrams Documentation

Folder ini berisi semua diagram visual untuk memahami struktur dan alur sistem Admin Panel.

---

## 📁 File Diagram

### 1. **Sitemap Monitoring & Settings**

#### a. `sitemap-monitoring-settings.puml` ⭐ RECOMMENDED
**Diagram Simple & Mudah Dipahami**

- ✅ Versi paling simple dan clean
- ✅ Fokus pada struktur navigasi
- ✅ Cocok untuk presentasi dan overview
- ✅ Mudah dipahami oleh non-technical team

**Isi:**
- Hierarki menu
- Modul Monitoring (Analytics, Chatbot, Recommendations, Reports)
- Modul Settings (General, API, AI, Audit)
- Flow navigasi

**Target:** Project Manager, Client, UI/UX Designer

---

#### b. `sitemap-monitoring-settings-detailed.puml`
**Diagram Detail dengan Spesifikasi**

- 📋 Detail setiap halaman
- 📋 Filter dan pencarian
- 📋 Permission & role access
- 📋 Validasi & business rules

**Isi:**
- Filter options per halaman
- Field yang ditampilkan
- Status workflow
- Export functions
- Access control (🔓 All Admin, 🛡️ Super Admin)

**Target:** Developer, QA Tester, Technical Writer

---

#### c. `sitemap-monitoring-settings-dataflow.puml`
**Diagram Arsitektur & Data Flow**

- 🏗️ Layer architecture
- 🏗️ Controller & Model relationships
- 🏗️ Database strategy (MongoDB, MySQL)
- 🏗️ Middleware stack

**Isi:**
- Presentation Layer (Views)
- Application Layer (Controllers)
- Domain Layer (Models)
- Data Layer (Database, Cache, Storage)
- External Services
- Data flow antar komponen

**Target:** Backend Developer, System Architect, DevOps

---

### 2. **Flow Diagrams** (Upcoming)

#### `FLOW_DIAGRAMS.md`
Dokumentasi tentang flow diagram untuk berbagai proses di sistem.

---

## 🚀 Cara Menggunakan PlantUML

### Opsi 1: Online Viewer (Tercepat) ⭐

1. Copy isi file `.puml`
2. Buka: https://www.planttext.com/
3. Paste dan klik "Refresh"

### Opsi 2: VSCode Extension

```bash
# Install extension
Name: PlantUML
ID: jebbs.plantuml
```

**Shortcut:**
- Preview: `Alt + D` (Windows/Linux) atau `Option + D` (Mac)
- Export: Klik kanan → Export Current Diagram

### Opsi 3: Command Line

```bash
# Download PlantUML JAR
# https://plantuml.com/download

java -jar plantuml.jar sitemap-monitoring-settings.puml
```

---

## 📖 Membaca Diagram

### Symbol Legend:

| Symbol | Meaning |
|--------|---------|
| 🔓 | All Admin Roles |
| 🛡️ | Super Admin Only |
| 📊 | Analytics/Statistics |
| 💬 | Chat/Communication |
| 🎯 | Recommendations |
| 🚨 | Reports/Alerts |
| ⚙️ | Settings/Configuration |

### Color Code:

| Color | Meaning |
|-------|---------|
| Blue | Monitoring Module |
| Yellow | Settings Module |
| Light Blue | Individual Pages |
| White | Actions/Operations |

---

## 🎯 Kapan Menggunakan Diagram Mana?

### Presentasi ke Client/Stakeholder
→ Gunakan: `sitemap-monitoring-settings.puml` (simple)

### Development & Implementation
→ Gunakan: `sitemap-monitoring-settings-detailed.puml` (detail)

### System Design & Architecture Review
→ Gunakan: `sitemap-monitoring-settings-dataflow.puml` (data flow)

### Dokumentasi Lengkap
→ Gunakan semua 3 diagram untuk perspektif berbeda

---

## 🔄 Export Format

PlantUML dapat di-export ke berbagai format:

- **PNG** - Untuk dokumentasi dan presentation
- **SVG** - Untuk website (scalable)
- **PDF** - Untuk laporan formal
- **LaTeX** - Untuk paper/thesis

---

## 📝 Update Diagram

Jika ada perubahan fitur atau struktur:

1. Edit file `.puml` yang sesuai
2. Preview untuk melihat hasil
3. Export ke format yang dibutuhkan
4. Update dokumentasi terkait

---

## 💡 Tips

1. **Mulai dengan diagram simple** untuk memahami overview
2. **Gunakan diagram detail** saat development
3. **Referensi diagram data flow** untuk debugging
4. **Export ke PNG** untuk share via email/chat
5. **Gunakan online viewer** jika tidak ada tools

---

## 🔗 Resources

- **PlantUML Official**: https://plantuml.com/
- **Online Editor**: https://www.planttext.com/
- **Syntax Guide**: https://plantuml.com/guide
- **VSCode Extension**: https://marketplace.visualstudio.com/items?itemName=jebbs.plantuml

---

**Maintained by**: Development Team  
**Last Updated**: 2026
