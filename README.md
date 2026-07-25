# 🎨 Silva Kit

> A modern **Laravel 12** admin panel starter kit built with the [Silva Admin Template](https://zoyothemes.com/silva/html/) by Zoyothemes. Equipped with essential system modules, beautiful UI, and developer-friendly architecture — ready for rapid development.

---

## 🚀 Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | Bootstrap 5.3, SCSS, Vite |
| **Database** | MySQL / SQLite |
| **DataTables** | Yajra DataTables (Server-side) |
| **UI Template** | Silva Admin by Zoyothemes |

---

## 📦 Installation

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18 & npm
- MySQL

### 🐳 Docker Quickstart (Recommended)

#### 1. Development Mode (with Live Reload, MySQL & MinIO)

```bash
# Copy Docker environment template
cp .env.docker.example .env

# Start containers (App, Nginx, MySQL, MinIO, Auto-Bucket Init)
docker compose up -d --build

# Run database migrations & seeders inside container
docker compose exec app php artisan migrate --seed
```

- 🌐 **Web Application**: [http://localhost:8080](http://localhost:8080)
- 🗄️ **MySQL Database**: `localhost:3306` (`DB_DATABASE=silva_kit`)
- 🪣 **MinIO Console (Object Storage Dashboard)**: [http://localhost:9001](http://localhost:9001) (`user: minioadmin / pass: minioadmin`)
- 📦 **MinIO S3 API Endpoint**: `http://localhost:9000` (Default bucket: `silva-bucket`)

#### 2. Production Deployment Mode

```bash
# Start Production Containers (OPcache Optimized, Assets Prebuilt)
docker compose -f docker-compose.prod.yml up -d --build
```

---

### Local Bare-Metal Setup

```bash
# 1. Clone the repository
git clone https://github.com/ferdyyrahmat/silva-kit-new.git
cd silva-kit-new

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Run database migrations & seed default data
php artisan migrate --seed

# 7. Build frontend assets & start server
npm run build
php artisan serve
```

---

## 🌟 Global Features

### 🔐 Authentication System
- Login, Register, Logout
- Lock Screen with session-based PIN
- Password hashing with Bcrypt

### 👤 User Profile Management
- View & edit profile information (name, email, phone, location, avatar)
- Change password with current password validation
- Avatar upload with auto center-crop (1:1 circle)
- Profile accessible to all authenticated users via `/v1/profile`

### 👥 User Management (Admin)
- CRUD users with server-side DataTables
- Assign roles & permissions to users

### 🔑 Role & Permission System
- Dynamic permission-based access control
- Permission middleware (`check_permission`) on all admin routes
- CRUD permissions via admin panel

### 🌙 Dark Mode
- Toggle between Light and Dark themes
- Theme preference saved in session (persists across pages)
- Fully styled dark mode across all components

### 🌐 Multi-Language (i18n)
- Switch between **Bahasa Indonesia (ID)** and **English (EN)**
- Language toggle in topbar (click to switch, no dropdown)
- Language preference saved in session
- Translation files located in `lang/id/` and `lang/en/`

### 🔍 Global Search
- Search bar in topbar with real-time AJAX suggestions
- Searches across menu items and application pages
- Click on a result to navigate directly to the page

### 📊 DataTables Integration
- Server-side rendering with Yajra DataTables
- Supports sorting, pagination, and search
- Responsive design for mobile devices

### 🎨 SweetAlert2 Integration
- Modern, themed alert dialogs matching the Silva template style
- Used for confirmations, success messages, and error handling
- Custom dark mode support

### 🔔 Notification Bell & Inbox System
- Real-time bell notification updates with unread count badge & AJAX polling
- Automatic notification trigger when administrator assigns/updates user roles
- Interactive dropdown menu with single item removal (`x`) and **Clear All** action
- Integrated **All Notifications Inbox** tab inside user profile page (`/v1/profile#tab-notifications`)
- Global helper function `send_notification($title, $message, $url)` for effortless notification sending anywhere in the app

### 📜 Audit Trail System
- Comprehensive activity logging for login/logout, user creation/updates, role assignments, password changes, and maintenance toggles
- Dedicated DataTables view (`/admin/audit-logs`) for system administrators
- Global helper function `audit_log($description)` with auto event-detection and actor mapping

### 🛠 Maintenance Mode
- ON/OFF toggle switch for maintenance mode in settings panel
- Customizable maintenance title and message information
- Exclude/bypass **Administrator** role automatically
- Non-authorized users will be presented with a beautiful, themed `pages-maintenance` view
- Persisted dynamically in database utilizing `system_settings` table

### 🛡️ Two-Factor Authentication (2FA) & Social Login
- Two-Factor Authentication (2FA) via TOTP Authenticator apps (Google Authenticator / Authy) with SVG QR code rendering and emergency recovery codes
- Socialite OAuth Login integration for Google and GitHub accounts

### 🔑 API Engine & Swagger OpenAPI Documentation
- Laravel Sanctum Personal Access Token Manager
- Interactive OpenAPI / Swagger REST API documentation auto-generated at `/api/documentation`

### 📊 System Health Monitor Widget
- Live server metrics card container on Admin Dashboard: Database latency check, MinIO Object Storage connection, RAM memory usage, Disk space capacity

### 💾 Automated Backup Engine
- Database & asset backup management with MinIO S3 object storage synchronization via `spatie/laravel-backup`
- Manual backup trigger & zip archive download from Admin Panel (`/admin/backups`)

---

## 📁 Project Structure

```
silva-kit-new/
├── app/
│   ├── Helpers/
│   │   └── helpers.php           # Global helper functions (send_notification, audit_log)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # REST API & Swagger Controllers
│   │   │   ├── Auth/             # Login, Register, Lockscreen, 2FA, Socialite OAuth
│   │   │   ├── Dashboard/        # Dashboard controller
│   │   │   └── System/           # System modules
│   │   │       ├── AuditLog/     # Audit Trail controller
│   │   │       ├── Backup/       # Backup Manager controller
│   │   │       ├── Feedback/
│   │   │       ├── Language/      # Language & Theme toggle
│   │   │       ├── Maintenance/
│   │   │       ├── Notification/ # Bell & Blast notification controllers
│   │   │       ├── Permission/
│   │   │       ├── Profile/       # Profile & Sanctum token controller
│   │   │       ├── Search/        # Global search controller
│   │   │       └── User/
│   │   └── Middleware/
│   │       ├── CheckLockscreen.php
│   │       ├── CheckMaintenanceMode.php
│   │       ├── CheckPermission.php
│   │       └── SetLocale.php
│   ├── Models/
│   │   ├── AuditLog.php
│   │   ├── Permission.php
│   │   ├── Role.php
│   │   ├── SystemNotification.php
│   │   ├── SystemSetting.php
│   │   └── User.php
│   └── Services/
│       ├── SystemHealthService.php
│       └── TwoFactorService.php
├── docker/
│   ├── nginx/                    # Development & Production Nginx configurations
│   ├── php/                      # Production OPcache configuration
│   ├── Dockerfile.dev
│   ├── Dockerfile.prod
│   ├── entrypoint.sh
│   └── entrypoint.prod.sh
├── database/
│   ├── migrations/
│   └── seeders/
├── lang/
│   ├── en/                       # English translations
│   └── id/                       # Indonesian translations
├── resources/
│   ├── scss/                     # Custom SCSS styles
│   └── views/
│       ├── admin/                # Admin module views
│       ├── auth/                 # Auth & 2FA challenge views
│       └── layouts/              # Layout & partials
├── routes/
│   ├── web.php                   # Main routes
│   ├── api.php                   # Sanctum API routes
│   ├── auth.php                  # Auth & Socialite OAuth routes
│   └── partials/
│       ├── admin.php             # Admin-only routes
│       └── user.php              # General user routes
├── docker-compose.yml            # Development Docker setup (MySQL 8.0, MinIO, App, Web)
├── docker-compose.prod.yml       # Production Docker setup
└── .env.docker.example           # Docker environment template
```

---

## 🔄 Last Update

**Date:** 24 July 2026

### ✨ What's New
- ✅ **Docker Ready (Dev & Prod)** — Complete containerization with PHP 8.2-FPM, Nginx, MySQL 8.0, and MinIO Object Storage
- ✅ **Two-Factor Authentication (2FA)** — TOTP QR code setup with Google Authenticator & recovery code verification challenge
- ✅ **Socialite OAuth Login** — Sign in with Google & GitHub out-of-the-box
- ✅ **Sanctum API Engine** — Personal Access Token generation & management
- ✅ **Interactive OpenAPI / Swagger Docs** — Auto-generated REST API documentation at `/api/documentation`
- ✅ **System Health Monitoring** — Live server metrics (DB latency, MinIO status, RAM, Disk usage) on Admin Dashboard
- ✅ **Automated Backup Engine** — System backup manager with MinIO S3 sync & zip file downloads (`/admin/backups`)
- ✅ **Dark Mode** — Full dark/light theme toggle with session persistence
- ✅ **Multi-Language (i18n)** — ID/EN language switcher with click-to-toggle UI
- ✅ **Notification Bell System** — Real-time notification updates for user role changes, profile updates, with unread count badge & inbox tab
- ✅ **Audit Trail System** — Comprehensive activity logging for login/logout, user creation/updates, role assignments, and maintenance toggles with DataTables view (`/admin/audit-logs`)
- ✅ **Maintenance Mode** — On/Off configuration settings panel allowing login gate bypass for **Administrator** role

---

## 👨‍💻 Author

**Ferdy Rahmat**

- GitHub: [@ferdyyrahmat](https://github.com/ferdyyrahmat)

---

## 📄 License

This project is built on top of the [Laravel Framework](https://laravel.com/) which is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).

The Silva Admin Template is a product of [Zoyothemes](https://zoyothemes.com/).
