# Elite Guard - Security & Attendance Management System

A comprehensive web application and API service built with **Laravel 11** for security guard scheduling, shift attendance tracking, weekly run sheets, NFC tag scanning, and administrative reporting.

---

## 📋 System Requirements

Ensure your system meets the following requirements before installation:

- **PHP**: `^8.2` or higher
- **Composer**: `^2.x`
- **Node.js**: `^18.x` or `^20.x`
- **NPM**: `^9.x` or `^10.x`
- **Database**: MySQL `8.0+` / MariaDB `10.4+` or SQLite
- **Extensions**: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

---

## 🚀 Installation & Setup Guide

Follow these steps sequentially to clone, configure, and run the project locally.

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd elite-guard
```

### Step 2: Install PHP Dependencies
Install required Composer packages:
```bash
composer install
```

### Step 3: Install Frontend Dependencies
Install JavaScript/CSS packages:
```bash
npm install
```

### Step 4: Environment Configuration
Create a copy of the environment configuration file:
```bash
cp .env.example .env
```

Open `.env` in your code editor and update your database credentials and application settings:

```ini
APP_NAME="Elite Guard"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database Configuration Example (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elite_guard
DB_USERNAME=root
DB_PASSWORD=
```

> **Note**: Make sure to create the database in MySQL (`CREATE DATABASE elite_guard;`) if it doesn't already exist.

### Step 5: Generate Application Key
Generate the Laravel `APP_KEY`:
```bash
php artisan key:generate
```

### Step 6: Generate JWT Secret Key
Generate the secret key required for API JWT authentication:
```bash
php artisan jwt:secret
```

### Step 7: Run Database Migrations & Seeders
Execute database migrations and seed default data:
```bash
php artisan migrate --seed
```

### Step 8: Create Storage Symbolic Link
Link the public storage directory to enable uploaded file/image access:
```bash
php artisan storage:link
```

### Step 9: Generate Swagger API Documentation
Generate L5-Swagger API documentation files:
```bash
php artisan l5-swagger:generate
```

---

## 🖥️ Running the Application

### Option A: Standard Execution (Multiple Terminals)

1. **Start Laravel Development Server**:
   ```bash
   php artisan serve
   ```
   *Access Admin Panel at:* `http://127.0.0.1:8000`

2. **Start Vite Asset Bundler** (in a new terminal window):
   ```bash
   npm run dev
   ```

3. **Start Queue Worker** (for background processing, notifications, & emails):
   ```bash
   php artisan queue:work
   ```

---

### Option B: Concurrent Execution (Single Terminal Command)
Run server, queue listener, logs, and vite simultaneously:
```bash
composer run dev
```

---

## 📚 API Documentation (Swagger)

The project includes integrated **L5-Swagger** interactive API documentation for Mobile & Frontend integrations.

- **Swagger UI Path**: `http://127.0.0.1:8000/api/documentation`
- **Re-generate API Docs** (after editing annotations or routes):
  ```bash
  git checkout -- storage/api-docs/api-docs.json
  php artisan l5-swagger:generate
  ```

---

## 🛠️ Useful Maintenance & Clearing Commands

If you make configuration or route changes, clear the application caches:

```bash
# Clear all application caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize application for production
php artisan optimize
```

---

## 📄 License
This repository is private software. All rights reserved.