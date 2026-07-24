# 🎨 Silva Kit

> A modern **Laravel 12** admin panel starter kit built with the [Silva Admin Template](https://zoyothemes.com/silva/html/) by Zoyothemes. Equipped with essential system modules, beautiful UI, and developer-friendly architecture — ready for rapid development.

---

## 📸 Preview

| Dashboard | Profile |
|-----------|---------|
| ![Dashboard](public/images/preview/dashboard.png) | ![Profile](public/images/preview/profile.png) |

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

### Step-by-step

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

# 6. Configure your database in .env
#    For MySQL:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=silva_kit
#    DB_USERNAME=root
#    DB_PASSWORD=
#
#    For SQLite (default):
#    DB_CONNECTION=sqlite

# 7. Run database migrations
php artisan migrate

# 8. Seed default data (admin user, permissions, etc.)
php artisan db:seed

# 9. Create storage symlink
php artisan storage:link

# 10. Build frontend assets
npm run build

# 11. Start the development server
php artisan serve
```

### Development Mode (Hot Reload)

```bash
# Run Vite dev server for hot module replacement
npm run dev

# In a separate terminal, run Laravel server
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

### 💬 Feedback Management (Admin)
- CRUD feedback entries via admin panel

---

## 📁 Project Structure

```
silva-kit-new/
├── app/
│   ├── Helpers/
│   │   └── helpers.php           # Global helper functions (send_notification, audit_log)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/             # Login, Register, Lockscreen
│   │   │   ├── Dashboard/        # Dashboard controller
│   │   │   └── System/           # System modules
│   │   │       ├── AuditLog/     # Audit Trail controller
│   │   │       ├── Feedback/
│   │   │       ├── Language/      # Language & Theme toggle
│   │   │       ├── Maintenance/
│   │   │       ├── Notification/ # Bell & Blast notification controllers
│   │   │       ├── Permission/
│   │   │       ├── Profile/       # Profile controller
│   │   │       ├── Search/        # Global search controller
│   │   │       └── User/
│   │   └── Middleware/
│   │       ├── CheckLockscreen.php
│   │       ├── CheckMaintenanceMode.php
│   │       ├── CheckPermission.php
│   │       └── SetLocale.php
│   └── Models/
│       ├── AuditLog.php
│       ├── Permission.php
│       ├── Role.php
│       ├── SystemNotification.php
│       ├── SystemSetting.php
│       └── User.php
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
│       ├── auth/                 # Auth views
│       └── layouts/              # Layout & partials
├── routes/
│   ├── web.php                   # Main routes
│   ├── auth.php                  # Auth routes
│   └── partials/
│       ├── admin.php             # Admin-only routes
│       └── user.php              # General user routes
└── public/
    └── images/
```

---

## 🔄 Last Update

**Date:** 24 July 2026

### ✨ What's New
- ✅ **Dark Mode** — Full dark/light theme toggle with session persistence
- ✅ **Multi-Language (i18n)** — ID/EN language switcher with click-to-toggle UI
- ✅ **Global Search** — Real-time AJAX search bar with direct navigation
- ✅ **SweetAlert2 Modern Styling** — Themed alerts matching Silva template design
- ✅ **Profile Refactor** — Profile routes moved to `v1.profile.*` (accessible by all users)
- ✅ **Avatar Upload** — Center-cropped circular avatar with `object-fit: cover`
- ✅ **Lock Screen Avatar** — Dynamic avatar on lock screen page
- ✅ **Profile Tabs & Inbox** — Includes Personal Details, Security & Password, Granted Access Matrix, and **All Notifications Inbox**
- ✅ **Global Helpers** — Easy-to-use `send_notification()` and `audit_log()` functions registered globally in Composer
- ✅ **Notification Bell System** — Real-time notification updates for user role changes, profile updates, with unread count badge & AJAX polling
- ✅ **Audit Trail System** — Comprehensive activity logging for login/logout, user creation/updates, role assignments, and maintenance toggles with dedicated DataTables view (`/admin/audit-logs`)
- ✅ **Maintenance Mode** — On/Off configuration settings panel allowing login gate bypass only for **Administrator** role

---

## 👨‍💻 Author

**Ferdy Rahmat**

- GitHub: [@ferdyyrahmat](https://github.com/ferdyyrahmat)

---

## 📄 License

This project is built on top of the [Laravel Framework](https://laravel.com/) which is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).

The Silva Admin Template is a product of [Zoyothemes](https://zoyothemes.com/).
