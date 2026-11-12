# Budgify – Personal Expense Tracker
## College Mini-Project Documentation

---

## 1. Project Introduction

**Budgify** is a web-based Personal Expense Tracker built using Core PHP and MySQL. It helps students and individuals record their daily income and expenses in one place instead of using notebooks or scattered spreadsheets. After logging in, each user gets a personal dashboard showing their total income, total expenses, remaining balance, monthly summary, and recent transactions.

---

## 2. Problem Statement

Many college students and working individuals lose track of where their money goes each month. Common problems include:
- Writing expenses on paper or in phone notes makes it hard to calculate monthly totals.
- Existing finance apps can be overly complex or require paid subscriptions.
- Without categorization and monthly reports, it is difficult to control unnecessary spending and plan savings.

Budgify solves this by providing a simple, free, and easy-to-use web application to record, organize, and review personal finances.

---

## 3. Objectives

1. Build a secure user registration and login system using PHP sessions and password hashing.
2. Allow users to add, view, edit, and delete income and expense transactions.
3. Organize transactions using clear categories (`Food`, `Travel`, `Shopping`, `Education`, `Bills`, `Salary`, `Others`).
4. Automatically calculate Total Income, Total Expenses, and Remaining Balance on the dashboard.
5. Provide a Monthly Report page to filter transactions by month and year.
6. Ensure every user can only view and modify their own financial records.

---

## 4. Technologies Used

| Layer | Technology | Purpose |
| :--- | :--- | :--- |
| Frontend | HTML5, CSS3, Bootstrap 5 | Responsive layout, sidebar, summary cards, tables, and forms |
| Client Scripting | Basic JavaScript | Mobile sidebar toggle, form validation, delete confirmation |
| Backend | Core PHP | Server-side logic, authentication, session management, CRUD |
| Database | MySQL (via PHP PDO) | Relational data storage using prepared statements |
| Local Server | XAMPP (Apache + MySQL) | Local web hosting and phpMyAdmin database management |

---

## 5. Features and Modules

### A. Authentication Module (`auth/`)
- **Registration (`register.php`)**: Validates user input, checks for duplicate email, hashes passwords with `password_hash()`, and saves the user in MySQL.
- **Login (`login.php`)**: Verifies email and password using `password_verify()`, regenerates the session ID, and sets session variables.
- **Logout (`logout.php`)**: Safely destroys the PHP session and redirects to the login page.

### B. Dashboard Module (`dashboard.php`)
- Displays **Total Income**, **Total Expenses**, and **Remaining Balance**.
- Shows a **Monthly Summary** for the current month and category-wise expense progress bars.
- Lists the **6 most recent transactions**.

### C. Transaction Management Module (`transactions/`)
- **View All (`index.php`)**: Displays all user transactions with type and category filters.
- **Add (`add.php`)**: Form to record a new income or expense with title, amount, category, date, and optional description.
- **Edit (`edit.php`)**: Updates an existing transaction after verifying user ownership.
- **Delete (`delete.php`)**: Deletes a transaction via POST request after user confirmation.

### D. Monthly Reports Module (`reports.php`)
- Lets the user choose any **Month** and **Year**.
- Calculates total income, total expenses, and net savings for that period and lists all matching transactions.

---

## 6. Database Structure

The database (`budgify`) uses **3 normalized tables**:

### 1. `users`
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique user ID |
| `name` | VARCHAR(100) | NOT NULL | User's full name |
| `email` | VARCHAR(150) | NOT NULL, UNIQUE | Login email |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt password hash |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Registration timestamp |

### 2. `categories`
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique category ID |
| `name` | VARCHAR(50) | NOT NULL, UNIQUE | Category name (Food, Travel, etc.) |
| `type` | ENUM | `'income'`, `'expense'`, `'both'` | Applicable transaction type |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation timestamp |

### 3. `transactions`
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique transaction ID |
| `user_id` | INT | NOT NULL, FOREIGN KEY -> `users(id)` | Owner of the transaction |
| `category_id` | INT | NOT NULL, FOREIGN KEY -> `categories(id)` | Linked category |
| `title` | VARCHAR(150) | NOT NULL | Short transaction title |
| `amount` | DECIMAL(10,2) | NOT NULL | Monetary amount |
| `type` | ENUM | `'income'`, `'expense'` | Income or Expense |
| `transaction_date` | DATE | NOT NULL | Date of transaction |
| `description` | TEXT | NULL | Optional notes |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |

**Relationships:**
- One **User** can have many **Transactions** (`1 : N`).
- One **Category** can be assigned to many **Transactions** (`1 : N`).

---

## 7. Simple System Architecture

```text
+------------------+       HTTP Request        +--------------------+       PDO Prepared SQL       +------------------+
|   Web Browser    | ------------------------> |   Apache + PHP     | --------------------------> |  MySQL Database  |
| (HTML/Bootstrap) | <------------------------ | (Session & Logic)  | <-------------------------- |   (budgify)      |
+------------------+       HTML Response       +--------------------+         Result Rows          +------------------+
```

1. **Presentation Layer**: Bootstrap 5 HTML views (`dashboard.php`, `transactions/*.php`, `reports.php`).
2. **Application Layer**: Core PHP scripts validate forms, check `$_SESSION['user_id']`, and run business calculations.
3. **Data Layer**: `config/database.php` connects to MySQL using PHP PDO prepared statements.

---

## 8. Working Process

1. A new user registers on `auth/register.php` or logs in on `auth/login.php`.
2. PHP verifies credentials and stores `user_id` inside `$_SESSION`.
3. Every protected page calls `requireLogin()` from `includes/auth.php`.
4. When the user adds an income or expense via `transactions/add.php`, PHP validates the fields and inserts the row into `transactions` with the logged-in `user_id`.
5. `dashboard.php` runs `SUM()` queries on `transactions` for that `user_id` to calculate Total Income, Total Expenses, and Remaining Balance.
6. `reports.php` filters rows using `MONTH(transaction_date)` and `YEAR(transaction_date)` to display monthly totals.

---

## 9. Testing

The following test cases should be verified locally in XAMPP:

| Test Case | Action | Expected Result |
| :--- | :--- | :--- |
| TC-01: Registration | Submit valid name, unique email, password | User saved with hashed password; redirected to login |
| TC-02: Login | Enter valid credentials (`student@budgify.com`) | Session started; redirected to `dashboard.php` |
| TC-03: Auth Guard | Visit `dashboard.php` without logging in | Redirected automatically to `auth/login.php` |
| TC-04: Add Transaction | Add an expense of ₹500 under Food | Record saved in MySQL; Dashboard expense increases by ₹500 |
| TC-05: Edit Transaction | Change amount from ₹500 to ₹750 | Updated amount shown in table and Dashboard totals |
| TC-06: Delete Transaction | Click Delete and confirm prompt | Record removed from MySQL and Dashboard recalculated |
| TC-07: Monthly Report | Select a specific month and year | Displays exact income, expense, and records for that month |
| TC-08: User Isolation | Log in as a second user | Second user sees only their own transactions |

---

## 10. Conclusion and Future Scope

### Conclusion
**Budgify** successfully meets the goal of creating a clean, secure, and beginner-friendly expense tracker using Core PHP, MySQL PDO, and Bootstrap 5. Its modular 3-table design makes it easy to maintain and straightforward to present.

### Future Scope
- Export monthly reports to PDF or CSV.
- Add monthly budget limits with overspending alerts.
- Add visual charts (bar/pie charts) for multi-month spending trends.
- Add recurring transaction reminders for monthly bills and rent.

