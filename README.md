<div align="center">

# M-Pesa Payment Verification & Transaction Management System

A PHP web application for recording and manually verifying M-Pesa payments — a complete, secure system with an admin dashboard, receipts, reports and an approval workflow.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![MVC](https://img.shields.io/badge/Architecture-MVC-4fae42?style=flat-square)

</div>

---

## About

This system helps small businesses record M-Pesa payments and manually verify them against their local records. Transactions are entered by an administrator, reviewed, and marked as **Verified** or **Failed**. Verified payments automatically generate printable receipts.

## Features

- **Authentication** — registration, login, password hashing, CSRF protection and role-based access control
- **Approval workflow** — new staff accounts require administrator approval before they can log in
- **Add Transaction** — record a payment with an auto-generated or manual code
- **Verify Transaction** — search, review and verify or reject payments
- **Receipts** — printable digital receipts generated on verification
- **Reports** — statistics, charts, filters and CSV export
- **User management** — approve, reject, suspend and activate users
- **Dashboard** — live statistics, notification cards and activity feed
- **Audit logging** — every login, verification, and admin action is recorded

## Tech Stack

PHP 8 · MySQL · PDO (prepared statements) · Bootstrap 5 · HTML5/CSS3 · JavaScript · Chart.js

## Getting Started

**Requirements:** XAMPP (Apache, PHP 8.1+, MySQL 5.7+)

### Option A — Web Installer

```bash
# 1. Copy the project into your XAMPP htdocs folder
#    C:\xampp\htdocs\mpesa-verification

# 2. Start Apache and MySQL from the XAMPP Control Panel

# 3. Open the one-time installer and create the database
#    http://localhost/mpesa-verification/setup.php

# 4. Open the application
#    http://localhost/mpesa-verification/public/
```

> ⚠️ Delete `setup.php` after installation.

### Option B — Import SQL Directly

```bash
# Import the schema into phpMyAdmin or MySQL CLI:
mysql -u root -p < database/mpesa_db.sql
```

### Option C — PHP Built-in Dev Server

```bash
# From the project root:
php -S 127.0.0.1:8000 -t public public/router.php
# Then open http://127.0.0.1:8000/
```


## Project Structure

```
mpesa-verification/
├── setup.php              # One-time database installer
├── config/
│   └── config.php         # Application configuration (DB, branding, Daraja API)
├── database/
│   └── mpesa_db.sql       # Full schema + sample data
├── app/
│   ├── bootstrap.php      # Autoloader, session, helpers
│   ├── core/              # MVC framework (Router, Auth, CSRF, Database, Flash)
│   ├── controllers/       # Request handling (Auth, Dashboard, Transaction, Report, User)
│   ├── models/            # Database access (User, Transaction, Customer, Receipt, Report, AuditLog)
│   └── services/          # Daraja API client (simulation + live)
├── public/                # Webroot (front controller + assets)
│   ├── index.php          # Front controller & route definitions
│   ├── router.php         # PHP built-in dev server router
│   └── assets/            # CSS, JS, vendor libraries (Bootstrap, Font Awesome, Chart.js)
└── views/                 # Templates
    ├── layouts/           # app.php (sidebar), auth.php (centered card)
    ├── auth/              # Login, registration
    ├── dashboard/         # Dashboard with charts
    ├── transactions/      # Add, list, show, verify
    ├── reports/           # Analytics & CSV export
    ├── users/             # User management & approval workflow
    ├── receipts/          # Printable receipts
    ├── errors/            # 403, 404 pages
    └── partials/          # Flash messages, pagination
```

## Workflow

1. **Administrator logs in** and navigates to **Add Transaction**.
2. The payment is recorded with a code, sender name, phone and amount — status is set to *Pending*.
3. The system redirects to **Verify Transaction**, which searches the new record automatically.
4. The administrator reviews the details and clicks **Verify Payment** or **Mark as Failed**.
5. A digital receipt is generated for verified payments. Reports and dashboard update automatically.

## Security Features

- **Password hashing** — bcrypt with cost factor 10
- **CSRF protection** — per-session tokens on every POST form
- **Session hardening** — HttpOnly, SameSite=Lax, session regeneration on login
- **Prepared statements** — all database queries use PDO parameter binding
- **Role-based access control** — admin-only routes guarded server-side
- **Audit logging** — every login, verification, and admin action is recorded with IP and user agent
- **Last-admin guard** — prevents the only approved admin from demoting/suspending themselves

## Daraja API Integration

The system includes a Safaricom Daraja API client (`app/services/DarajaApi.php`) that can verify transactions against the live M-Pesa API. By default, it runs in **simulation mode** which produces deterministic, realistic results for demo purposes.

To enable live API calls, edit `config/config.php`:

```php
define('MPESA_SIMULATION_MODE', false);
define('MPESA_CONSUMER_KEY',    'your-consumer-key');
define('MPESA_CONSUMER_SECRET', 'your-consumer-secret');
define('MPESA_SHORTCODE',       'your-business-shortcode');
define('MPESA_PASSKEY',         'your-lipa-na-mpesa-passkey');
```

## Documentation

| Document | Description |
|---|---|
| `run.md` | How to run the development server and reproduce the app state |

## License

This is a university project built by Brian Mwini

---

<div align="center">

Built with PHP, MySQL and Bootstrap — **Technical University of Kenya**

</div>
