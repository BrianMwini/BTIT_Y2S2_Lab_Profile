# MPVS — Preview Run Doc

PHP 8 + MySQL MVC application (no build step, no package manager, no dependencies
to install). Vendor assets (Bootstrap, Font Awesome, Chart.js) are committed under
`public/assets/vendor/`.

## Reproducing the app state

1. Start MySQL (XAMPP): `C:\xampp\mysql\bin\mysqld.exe` or the XAMPP control panel.
   The app expects `localhost` / root / empty password (see `config/config.php`).
2. Create the database by importing `database/mpesa_db.sql`:
   `C:\xampp\mysql\bin\mysql.exe -u root < database/mpesa_db.sql`
   (The `setup.php` web installer runs the same SQL.)
3. No `.env`/secrets or other uncommitted artifacts are required.

## Running the dev server

From the project root:

```
"C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t public public/router.php
```

- Default port: **8000** (see README; `public/router.php` rewrites to the front
  controller, mirroring the Apache `.htaccess` rules).
- Webroot is `public/`; the app is served at `http://127.0.0.1:8000/`.
- Demo login (not shown on the page): `admin` / `Admin@123`.

## Verifying it works

- `http://127.0.0.1:8000/login` renders the sign-in page.
- Admin flow: login → Add Transaction → auto-redirect to Verify Transaction →
  Verify Payment (or Mark as Failed) → receipt → reports → dashboard.
- Staff registrations start Pending; approve them under User Management.
