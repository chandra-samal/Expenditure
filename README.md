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

Here are the step-by-step instructions for installing and setting up the Expenditure project, including the XAMPP setup:

### 1. Clone the Repository
* Open your terminal or command prompt.
* Navigate to the directory where you want to store the project.
* Run the following command to clone the repository:
    ```bash
    git clone https://github.com/chandra-samal/Expenditure.git
    ```

### 2. Set Up XAMPP
* **Download & Install:** If you haven't already, download and install [XAMPP](https://www.apachefriends.org/index.html) for your operating system.
* **Start Services:** Open the XAMPP Control Panel and start the **Apache** and **MySQL** modules.

### 3. Copy to `htdocs`
* Locate your XAMPP installation directory (usually `C:\xampp` on Windows or `/Applications/XAMPP` on macOS).
* Inside the XAMPP directory, find the `htdocs` folder.
* Copy the entire cloned `Expenditure` project folder and paste it into the `htdocs` directory.
    * *Path example (Windows):* `C:\xampp\htdocs\Expenditure`

### 4. Database Setup (`expenditure.sql`)
* Open your web browser and go to `http://localhost/phpmyadmin/`.
* Click on **"New"** in the left sidebar to create a new database.
* Name the database **`expenditure`** [cite: 526] and click **"Create"**.
* Select the newly created `expenditure` database from the left sidebar.
* Click on the **"Import"** tab at the top.
* Click **"Choose File"** and select the `expenditure.sql` file [cite: 733] located inside your project folder (`htdocs/Expenditure/expenditure.sql`).
* Scroll down and click the **"Import"** (or "Go") button to execute the SQL script.

### 5. Environment Configuration
* Navigate to your project folder inside `htdocs` (`htdocs/Expenditure`).
* Find the `.env.example` file and rename it to `.env` [cite: 694] (or create a new `.env` file if it doesn't exist).
* Open the `.env` file and configure your database and JWT settings[cite: 726, 727, 728, 729, 730]:
    ```env
    DB_HOST=127.0.0.1
    DB_USER=root
    DB_PASSWORD=
    DB_NAME=expenditure
    JWT_SECRET=your_secret_key_here
    ```
    *(Note: The default MySQL user in XAMPP is usually `root` with no password.)*

### 6. Test the Application
* Open your web browser.
* Navigate to `http://localhost/Expenditure/`.
* The application should load. You can try logging in with demo credentials or creating a new account[cite: 737].

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
