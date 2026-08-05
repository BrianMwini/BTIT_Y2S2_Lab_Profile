# MPVS — M-Pesa Payment Verification & Transaction Management System

A web-based system (PHP 8 + MySQL + PDO + Bootstrap 5) that records and manually
verifies M-Pesa payments for small businesses, per the official SRS document
(`Software Specificaton Requirement document.docx`).

This is a **manual verification system**: transactions are recorded in the local
database by an administrator and then marked as Verified or Failed. **No external
M-Pesa (Daraja) API is used and no API credentials are required.**

**Features**
- Secure registration & login (bcrypt passwords, sessions, CSRF, role-based access)
- **Staff approval workflow** — new registrations start as *Pending* and must be
  approved by an administrator before they can log in
- **Add Transaction** — record a payment (auto-generated 10-character code if blank),
  stored as *Pending*
- **Verify Transaction** — search the local database, review details, then
  Verify Payment or Mark as Failed
- Centralized transaction records with search, filters (code, phone, sender,
  status, verifier, dates) & pagination
- Digital printable receipts (auto-generated on verification)
- Analytical reports: stat cards, Chart.js charts, **Verified By / Verification Date**
  columns, filters by date/status/verifier, CSV export, report history
- Admin user management — approve, reject, suspend and activate users
- Dashboard notification cards: pending verifications, pending approvals,
  verified today, failed today
- Audit log & activity feed, notifications, responsive professional dashboard
- Sample data + one-click installer

## Requirements
- XAMPP with **PHP 8.1+** and **MySQL 5.7+** (Apache)
- Browser

## Installation (XAMPP)

1. Copy this project folder into `C:\xampp\htdocs\` (e.g. `C:\xampp\htdocs\mpesa-projo`).
2. Start **Apache** and **MySQL** from the XAMPP control panel.
3. Open <http://localhost/mpesa-projo/setup.php> and click **Install Database**.
4. Open <http://localhost/mpesa-projo/public/> and log in.

### Demo accounts

| Role | Username | Password | Status |
|------|----------|----------|--------|
| Administrator | `admin`  | `Admin@123` | Approved |
| Business Staff | `staff1` | `Staff@123` | Approved |
| Business Staff | `staff2` | `Staff@123` | Approved |
| Business Staff | `newstaff` | `Staff@123` | **Pending approval** (demo) |

> ⚠️ Delete `setup.php` after installation.

### Alternative: manual database import
Import `database/mpesa_db.sql` into phpMyAdmin, then visit `public/`.

## How Verification Works

1. **Administrator records a transaction** (Add Transaction) — the payment is
   saved with its code, sender and amount as *Pending*.
2. The system **redirects to the Verify Transaction page**, which searches the
   new record automatically.
3. The administrator **reviews the transaction details** (code, sender, phone,
   amount, date, status).
4. The administrator clicks **Verify Payment** or **Mark as Failed**.
5. The database is updated — a digital receipt is generated for verified payments,
   and reports & dashboard statistics refresh automatically.

## Project structure

```
├── index.php            # redirect to /public
├── setup.php            # one-time installer (delete after use)
├── config/config.php    # DB + app settings
├── database/mpesa_db.sql# full schema + sample data
├── docs/SRS_ANALYSIS.md # Phase 1 & 2 analysis of the SRS
├── app/                 # bootstrap, core (MVC), models, controllers
├── public/              # webroot (front controller, assets, vendor libs)
└── views/               # layouts, partials, page templates
```

The **webroot is `public/`**. Point an Apache virtual host at it, or use the
built-in dev server: `php -S localhost:8000 -t public public/router.php`

## Security
Prepared statements (PDO) · bcrypt password hashing · session auth with id
regeneration · role-based authorization · CSRF tokens on every form · XSS-safe
output escaping · duplicate-email & duplicate-code prevention · audit logging of
failed logins, blocked access and critical actions.
