# 💰 Expenditure — Expense Tracker

A full-featured, self-hosted personal finance web application built with PHP and MySQL. Track your income and expenses, visualize spending patterns, manage custom categories, and import/export your data — all behind a secure JWT-authenticated interface.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Database Architecture](#database-architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage Guide](#usage-guide)
- [API Overview](#api-overview)
- [Default Credentials](#default-credentials)

---

## Features

- **Dashboard** — Real-time summary of today's, yesterday's, and monthly expenses alongside total income and current balance
- **Expense & Income Tracking** — Add, edit, and delete transactions with date, category, amount, and description
- **Custom Categories** — Create user-specific categories tagged as either `expense` or `income`
- **Analytics & Reports** — Visual charts (bar, pie) for spending breakdowns by category and date range
- **CSV Import / Export** — Bulk-import transactions from a CSV file or download your full history
- **JWT Authentication** — Stateless, 90-day token-based auth with a legacy PHP session fallback
- **User Profiles** — Manage account details and preferences
- **REST API** — Every feature is exposed via a JSON API, making it easy to integrate with mobile apps or other clients
- **CORS-ready** — All API endpoints send the appropriate headers for cross-origin access

---

## Tech Stack

| Layer      | Technology                                  |
|------------|---------------------------------------------|
| Backend    | PHP 8.x                                     |
| Database   | MySQL / MariaDB 10.4+                       |
| Auth       | Custom JWT (HS256) + PHP Sessions (legacy)  |
| Frontend   | Bootstrap 3, jQuery 1.11, Chart.js, Font Awesome |
| Charting   | Chart.js, EasyPieChart                      |
| Server     | Apache / Nginx with PHP-FPM                 |

---

## Project Structure

```
Expenditure/
├── index.php                   # Public landing page
├── expenditure.sql             # Full database schema + seed data
├── .env                        # Local environment variables (not committed)
├── .env.example                # Environment variable template
├── API_DOCS.md                 # Detailed API reference
│
├── includes/
│   ├── index.php               # Login page
│   ├── signup.php              # Registration page
│   ├── home.php                # Main dashboard (authenticated)
│   ├── add-expenses.php        # Add expense form
│   ├── add-income.php          # Add income form
│   ├── add_category.php        # Add category form
│   ├── manage-transaction.php  # Transaction list & management
│   ├── manage-income.php       # Income list & management
│   ├── update-expense.php      # Edit expense form
│   ├── analytics.php           # Charts and analytics view
│   ├── expense-report.php      # Expense report view
│   ├── report.php              # General report view
│   ├── search.php              # Transaction search
│   ├── user_profile.php        # User profile settings
│   ├── logout.php              # Session/token teardown
│   │
│   ├── database.php            # MySQLi connection (reads .env)
│   ├── env_loader.php          # Loads .env into putenv / $_ENV
│   ├── jwt.php                 # JWT encode / decode / validate
│   ├── auth_helper.php         # Unified auth (JWT-first, session fallback)
│   │
│   ├── api/                    # JSON REST API endpoints
│   │   ├── login.php
│   │   ├── signup.php
│   │   ├── dashboard.php
│   │   ├── transactions.php
│   │   ├── add-expense.php
│   │   ├── add-income.php
│   │   ├── update-expense.php
│   │   ├── update-income.php
│   │   ├── delete-expense.php
│   │   ├── delete-income.php
│   │   ├── delete-transaction.php
│   │   ├── add-category.php
│   │   ├── get-categories.php
│   │   ├── report.php
│   │   ├── export-csv.php
│   │   └── import-csv.php
│   │
│   ├── js/
│   │   ├── auth.js             # AuthManager — token storage & AJAX setup
│   │   ├── app.js              # Core app logic
│   │   ├── chart-data.js       # Chart rendering helpers
│   │   └── ...                 # Bootstrap, jQuery, datepicker, etc.
│   │
│   └── css/
│       ├── style.css
│       ├── styles.css
│       └── ...                 # Bootstrap, Font Awesome, datepicker
│
└── fonts/                      # FontAwesome & Glyphicons webfonts
```

---

## Database Architecture

The application uses a single database named **`expenditure`** with four tables.

### Entity-Relationship Overview

![ER DIAGRAM](./images/ER.png)

---

### `users`

Stores registered user accounts.

| Column              | Type           | Notes                          |
|---------------------|----------------|--------------------------------|
| `id`                | INT PK AI      | Auto-incrementing user ID      |
| `name`              | VARCHAR(50)    | Display name (unique)          |
| `email`             | VARCHAR(30)    | Login email                    |
| `phone`             | VARCHAR(15)    | Contact number                 |
| `password`          | VARCHAR(255)   | bcrypt hash                    |
| `verification_code` | VARCHAR(32)    | Registration verification code |
| `created_at`        | DATETIME       | Account creation timestamp     |

---

### `tblcategory`

User-defined categories for tagging expenses and income.

| Column         | Type                     | Notes                                     |
|----------------|--------------------------|-------------------------------------------|
| `CategoryId`   | INT PK AI                | Auto-incrementing category ID             |
| `CategoryName` | VARCHAR(255)             | Display label (supports emoji)            |
| `Mode`         | ENUM(`expense`,`income`) | Determines which form this appears in     |
| `UserId`       | INT FK → `users.id`      | Categories are private per user           |
| `CreatedAt`    | TIMESTAMP                | Auto-set on insert                        |

> **Foreign key:** `UserId` references `users(id)` with `ON DELETE CASCADE` — removing a user also removes all their categories.

---

### `tblexpense`

Individual expense transactions.

| Column        | Type         | Notes                                       |
|---------------|--------------|---------------------------------------------|
| `ID`          | INT PK AI    | Auto-incrementing expense ID                |
| `UserId`      | INT          | Owner (no FK — application-level scoping)   |
| `ExpenseDate` | DATE         | The date the expense occurred               |
| `CategoryId`  | INT          | Refers to `tblcategory.CategoryId`          |
| `category`    | VARCHAR(255) | Denormalized category name at time of entry |
| `ExpenseCost` | VARCHAR(200) | Amount as a string                          |
| `Description` | VARCHAR(300) | Free-text note                              |
| `NoteDate`    | TIMESTAMP    | Record creation timestamp                   |

---

### `tblincome`

Individual income transactions.

| Column         | Type         | Notes                                       |
|----------------|--------------|---------------------------------------------|
| `ID`           | INT PK AI    | Auto-incrementing income ID                 |
| `UserId`       | INT          | Owner (no FK — application-level scoping)   |
| `IncomeDate`   | DATE         | The date the income was received            |
| `CategoryId`   | INT          | Refers to `tblcategory.CategoryId`          |
| `category`     | VARCHAR(255) | Denormalized category name at time of entry |
| `IncomeAmount` | VARCHAR(200) | Amount as a string                          |
| `Description`  | VARCHAR(300) | Free-text note                              |
| `NoteDate`     | TIMESTAMP    | Record creation timestamp                   |

---

## Installation

### Prerequisites

- PHP **8.0** or higher
- MySQL **5.7+** or MariaDB **10.4+**
- A web server: **Apache** (with `mod_rewrite`) or **Nginx**
- (Optional) phpMyAdmin for database management

---

### Step 1 — Clone or extract the project

```bash
# If cloning from a repository
git clone https://github.com/chandra-samal/Expenditure.git
cd Expenditure

# Or simply extract the zip and move it to your web root
cp -r Expenditure/ /var/www/html/
```

---

### Step 2 — Create the database

Log into MySQL and import the provided schema:

```bash
mysql -u root -p < expenditure.sql
```

Or via phpMyAdmin:

1. Open phpMyAdmin and click **Import**
2. Select `expenditure.sql`
3. Click **Go**

This creates the `expenditure` database along with all four tables and sample seed data.

---

### Step 3 — Configure environment variables

Copy the example file and fill in your database credentials:

```bash
cp .env.example .env
```

Edit `.env`:

```ini
DB_HOST=localhost
DB_USER=your_mysql_username
DB_PASS=your_mysql_password
DB_NAME=expenditure
```

For additional security, set a custom JWT secret:

```ini
SESSION_SECRET=your_long_random_secret_here
```

> If `SESSION_SECRET` is not set, the app falls back to a built-in default key. **Always set this in production.**

---

### Step 4 — Web server configuration

**Apache** — Place the project folder inside your document root (e.g. `/var/www/html/`) and ensure `AllowOverride All` is set so `.htaccess` rules work.

```apache
<Directory /var/www/html/Expenditure>
    AllowOverride All
</Directory>
```

**Nginx** — Add a server block pointing to the project root and pass PHP requests to PHP-FPM:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/Expenditure;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

### Step 5 — Set file permissions

```bash
chmod -R 755 /var/www/html/Expenditure
chown -R www-data:www-data /var/www/html/Expenditure
```

---

### Step 6 — Open in your browser

Navigate to:

```
http://localhost/Expenditure/
```

You should see the landing page. Click **Sign In** to log in or **Sign Up** to create a new account.

---

## Configuration

| Variable         | Default                            | Description                          |
|------------------|------------------------------------|--------------------------------------|
| `DB_HOST`        | `localhost`                        | MySQL host                           |
| `DB_PORT`        | `3306`                             | MySQL port                           |
| `DB_USER`        | `root`                             | MySQL username                       |
| `DB_PASS`        | *(empty)*                          | MySQL password                       |
| `DB_NAME`        | `expenditure`                      | Database name                        |
| `DATABASE_URL`   | *(not set)*                        | Full DSN — overrides individual vars |
| `SESSION_SECRET` | `expenditure_jwt_secret_key_2024`  | Secret key for signing JWTs          |

`DATABASE_URL` takes priority over individual variables and follows the format:
```
mysql://user:password@host:port/dbname
```

---

## Usage Guide

### Registering & Logging In

1. Visit the landing page and click **Sign Up** to create an account with your name, email, phone, and password.
2. Click **Sign In** and enter your credentials. A JWT token is issued and stored in `localStorage` for 90 days.

### Dashboard

After login you land on the **Dashboard**, which shows:
- Today's and yesterday's expense totals
- Monthly expense and overall balance
- A bar chart of recent daily spending
- A category breakdown pie chart

### Adding Transactions

- Go to **Add Expense** or **Add Income** from the navigation menu
- Select a date, choose a category from the dropdown, enter the amount, and write an optional description
- Hit **Save** — the entry appears immediately in your transaction list

### Managing Categories

- Navigate to **Categories** and click **Add Category**
- Give it a name (emoji supported!) and choose whether it applies to expenses or income
- Categories are private — each user manages their own list

### Reports & Analytics

- The **Analytics** page shows pie and bar charts filtered by date range and category
- The **Report** page lets you filter by type (`expense`, `income`) and custom date ranges

### CSV Import / Export

- **Export:** Go to **Manage Transactions → Export CSV** and optionally filter by type and date range. A `.csv` file downloads immediately.
- **Import:** Click **Import CSV** and upload a file in the following format:

```
Date,Particulars,Expense,Income,Category
2024-01-15,Monthly salary,0,50000,Salary
2024-01-16,Groceries,1500,0,Food
```

---

## API Overview

All API endpoints live under `/includes/api/` and return JSON. Protected routes require a `Bearer` token in the `Authorization` header.

| Method | Endpoint                    | Description              |
|--------|-----------------------------|--------------------------|
| POST   | `api/login.php`             | Login and receive a JWT  |
| POST   | `api/signup.php`            | Register a new account   |
| GET    | `api/dashboard.php`         | Dashboard summary data   |
| GET    | `api/transactions.php`      | Paginated transaction list |
| POST   | `api/add-expense.php`       | Create an expense        |
| POST   | `api/add-income.php`        | Create an income entry   |
| POST   | `api/update-expense.php`    | Edit an expense          |
| POST   | `api/update-income.php`     | Edit an income entry     |
| POST   | `api/delete-transaction.php`| Delete a transaction     |
| POST   | `api/add-category.php`      | Create a category        |
| GET    | `api/get-categories.php`    | List categories          |
| GET    | `api/report.php`            | Filtered report data     |
| GET    | `api/export-csv.php`        | Download transactions as CSV |
| POST   | `api/import-csv.php`        | Bulk-import from CSV     |

For full request/response schemas, see [`API_DOCS.md`](./API_DOCS.md).

---

## Default Credentials

The seed data in `expenditure.sql` includes one demo account:

| Field    | Value              |
|----------|--------------------|
| Email    | `user@gmail.com`   |
| Password | `password` *(bcrypt-hashed in DB)* |

> **Security note:** Change or remove the demo account before deploying to a production environment. Never commit your `.env` file to version control.
