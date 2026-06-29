# UIRI Inventory Management System

A web-based Inventory Management System for Uganda Industrial Research Institute (UIRI),
covering both the Nakawa (HQ) and Namanve branches.

Built with **PHP, MySQL, HTML, CSS, JavaScript** — designed to run on **XAMPP**.

---

## 1. Setup Instructions (XAMPP)

1. **Copy the project folder**
   Copy the entire `uiri-ims` folder into your XAMPP `htdocs` directory, e.g.:
   ```
   C:\xampp\htdocs\uiri-ims
   ```

2. **Start Apache and MySQL**
   Open the XAMPP Control Panel and start both **Apache** and **MySQL**.

3. **Create the database**
   - Open `http://localhost/phpmyadmin`
   - Click **Import**
   - Choose the file `database.sql` from the project folder
   - Click **Go**

   This will create the `uiri_ims` database with both branches, sample categories,
   suppliers, inventory items, and 3 user accounts already pre-loaded.

4. **Check the config**
   Open `includes/config.php` and confirm the database credentials match your
   XAMPP MySQL setup (default XAMPP uses `root` with no password — already set).

5. **Visit the site**
   ```
   http://localhost/uiri-ims/
   ```

---

## 2. Default Login Accounts

| Username   | Password   | Role             | Branch   |
|------------|-----------|------------------|----------|
| `admin`     | `password` | Administrator    | Nakawa (HQ) — can switch branches |
| `jssemanda` | `password` | Store Manager    | Nakawa  |
| `gakello`   | `password` | Store Manager    | Namanve |

> ⚠️ Change these passwords before any real deployment.

---

## 3. Features Implemented

- **Authentication** — login, logout, bcrypt password hashing, CSRF protection, session handling
- **Role-based access control** — Administrator, Store Manager, Staff
- **Multi-branch support** — Nakawa (HQ) and Namanve, with branch switcher for Admins
- **Inventory management** — add/edit/deactivate items, categories, images, min-stock thresholds
- **Stock management** — Stock In / Stock Out with live stock updates, transaction history
- **Supplier management** — add/edit suppliers, TIN numbers, contact details
- **Dashboard** — KPI cards, stock movement charts, category breakdown, low-stock alerts, branch comparison
- **Reports** — Inventory Summary, Stock Movement, Stock Valuation, Low Stock Report (print/export to PDF via browser print)
- **Audit trail** — logs logins, branch switches, and all create/edit/delete actions
- **Security** — PDO prepared statements (SQL injection protection), CSRF tokens, input sanitization, session-based auth

---

## 4. Folder Structure

```
uiri-ims/
├── index.php                 # Login page
├── database.sql              # Full DB schema + seed data
├── includes/
│   ├── config.php            # DB connection, auth helpers, CSRF, audit log
│   ├── header.php            # Sidebar + topnav (shared layout)
│   ├── footer.php
│   ├── logout.php
│   └── switch_branch.php
├── pages/
│   ├── dashboard.php
│   ├── items.php
│   ├── categories.php
│   ├── stock_in.php
│   ├── stock_out.php
│   ├── transactions.php
│   ├── suppliers.php
│   ├── users.php
│   ├── reports.php
│   ├── audit.php
│   └── profile.php
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   └── img/uiri-logo.webp
└── uploads/items/             # Item image uploads stored here
```

---

## 5. Notes for Your Project Report

This system addresses all the requirements from the ICT Team's project brief:
user authentication & RBAC, inventory CRUD, stock in/out tracking with automatic
low-stock alerts, supplier management, a reporting dashboard with charts, search
& filtering, a relational database (Users, Inventory Items, Categories, Suppliers,
Stock Transactions, Audit Log), and security features (input validation, PDO
parameterized queries against SQL injection, session management, audit logging).

The two-branch requirement (Nakawa HQ + Namanve) is handled at the database level
(`branches` table, every item/transaction/user tied to a `branch_id`) so it can be
extended to additional UIRI branches in future without restructuring the schema.
