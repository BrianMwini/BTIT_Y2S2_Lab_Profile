<div align="center">

# M-Pesa Payment Verification & Transaction Management System

A PHP web application for recording and manually verifying M-Pesa payments — a complete, secure, university-level system with an admin dashboard, receipts, reports and an approval workflow.

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

## Tech Stack

PHP 8 · MySQL · PDO (prepared statements) · Bootstrap 5 · HTML5/CSS3 · JavaScript · Chart.js

## Getting Started

**Requirements:** XAMPP (Apache, PHP 8.1+, MySQL 5.7+)

```bash
# 1. Copy the project into your web root

# 2. Start Apache and MySQL from the XAMPP Control Panel

# 3. Open the one-time installer and create the database
http://localhost/mpesa-verification/setup.php

# 4. Open the application
http://localhost/mpesa-verification/public/
```

> ⚠️ Delete `setup.php` after installation.

Alternatively, import `database/mpesa_db.sql` into phpMyAdmin directly.

## Project Structure

```
mpesa-projo/
├── setup.php              # one-time database installer
├── config/                # application configuration
├── database/
│   └── mpesa_db.sql       # schema + sample data
├── app/
│   ├── core/              # MVC framework core
│   ├── controllers/       # request handling
│   ├── models/            # database access
│   └── services/          # business services
├── public/                # webroot (front controller + assets)
└── views/                 # templates
```


## Workflow

1. Administrator logs in and records a transaction (*Pending*).
2. The system opens the Verify Transaction page with the new record.
3. The administrator reviews the details and clicks **Verify Payment** or **Mark as Failed**.
4. The database updates — receipts, reports and dashboard refresh automatically.

---

<div align="center">

Built with PHP, MySQL and Bootstrap — university project.

</div>
