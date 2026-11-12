# Budgify – Simple PHP Expense Tracker

A beginner-friendly **College Mini-Project** built with **Core PHP**, **MySQL (PDO)**, **HTML5**, **CSS3**, **Bootstrap 5**, and **JavaScript**.

## Features

- **User Authentication**: Registration, Login, Logout, PHP Sessions, `password_hash()` & `password_verify()`.
- **Dashboard**: Total Income, Total Expenses, Remaining Balance, Current Month Summary, Category-wise Expense Bars, and Recent Transactions.
- **Transaction Management**: Add, View, Edit, and Delete income or expense transactions with category, date, and optional description.
- **Simple Categories**: `Food`, `Travel`, `Shopping`, `Education`, `Bills`, `Salary`, and `Others`.
- **Monthly Reports**: Filter transactions by month and year to view total income, expenses, and net savings.
- **Security**: PDO prepared statements, `htmlspecialchars()` XSS protection, CSRF tokens on POST forms, and strict per-user data isolation (`WHERE user_id = ?`).

## Project Folder Structure

```text
budgify/
├── assets/
│   ├── css/style.css
│   └── js/script.js
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── register.php
├── config/
│   └── database.php
├── includes/
│   ├── auth.php
│   ├── footer.php
│   └── header.php
├── transactions/
│   ├── add.php
│   ├── delete.php
│   ├── edit.php
│   └── index.php
├── dashboard.php
├── database.sql
├── generate_presentation.py
├── index.php
├── PROJECT_DOCUMENTATION.md
├── README.md
└── reports.php
```

## Step-by-Step XAMPP Setup Instructions

1. **Install XAMPP** on your computer and open the **XAMPP Control Panel**.
2. Start **Apache** and **MySQL** modules.
3. Copy the `budgify` folder into:
   - Windows: `C:\xampp\htdocs\budgify`
4. Open your browser and visit `http://localhost/phpmyadmin`.
5. Click the **Import** tab, select `C:\xampp\htdocs\budgify\database.sql`, and click **Import** (or **Go**).
   - This creates the `budgify` database with 3 tables (`users`, `categories`, `transactions`) and sample data.
6. Verify database settings in `config/database.php` (default XAMPP user is `root` with an empty password `""`).
7. Open `http://localhost/budgify` in your browser.

## Demo Login Credentials

- **Email:** `student@budgify.com`
- **Password:** `Student@123`

*(Or create a new user account from `/auth/register.php`.)*

## Generating the PowerPoint Presentation

Install `python-pptx` and run the included generator script:

```bash
pip install python-pptx
python generate_presentation.py
```

This creates `Budgify_Presentation.pptx` in the project root folder.

