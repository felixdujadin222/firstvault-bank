# FirstVault Bank 🏦

A intentionally vulnerable PHP/MySQL banking application built for educational purposes, security training, penetration testing labs, and CTF challenges.

> ⚠️ **Disclaimer**: This application contains intentional security vulnerabilities (such as IDOR, SQL injection, CSRF, broken access controls, and race conditions). **Do not deploy this application on a production public server without proper sandboxing or protective network barriers.**

---

## 📋 Features

- **User Authentication**: Registration and login with session management.
- **Account Dashboard**: View account numbers, current balances, and quick actions.
- **Fund Transfers**: Transfer funds between accounts.
- **Account Statements**: View detailed incoming and outgoing transaction histories.
- **Profile Management**: View and inspect user profile data.
- **REST / JSON API**: Lightweight endpoints (e.g. `api/account.php`) to query account details.
- **Admin Dashboard**: Organizer-only administrative panel.

---

## 🛠️ Requirements

- **PHP**: 7.4 or 8.x
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache, Nginx, or PHP's built-in CLI server (for local testing)

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/felixdujadin222/firstvault-bank.git
cd firstvault-bank
```

### 2. Configure the Database

Import the provided `schema.sql` into your MySQL instance:

```bash
mysql -u root -p < schema.sql
```

The database configuration can be customized via environment variables or directly in [`includes/db.php`](includes/db.php):

| Variable | Default Value | Description |
| :--- | :--- | :--- |
| `DB_HOST` | `localhost` | MySQL host address |
| `DB_NAME` | `firstvault_bank` | Database name |
| `DB_USER` | `root` | Database username |
| `DB_PASS` | *(empty)* | Database password |

### 3. Run the Application

Using PHP's built-in web server:

```bash
php -S localhost:8000
```

Open your browser and navigate to `http://localhost:8000`.

---

## 👥 Default Demo Credentials

| Username | Password | Account Number | Role |
| :--- | :--- | :--- | :--- |
| `alice` | `alice123` | `0000000001` | Customer |
| `bob` | `bob123` | `0000000002` | Customer |
| `admin` | `admin123` | `0000000003` | Administrator |

---

## 📁 Project Structure

```text
firstvault-bank/
├── admin/
│   └── index.php          # Admin / organizer interface
├── api/
│   ├── account.php        # Account info JSON API endpoint
│   └── balance.php        # Real-time balance JSON API endpoint
├── css/
│   └── style.css          # Design system & dark fintech UI styling
├── includes/
│   ├── db.php             # Database connection setup
│   ├── header.php         # Navigation & header layout
│   ├── footer.php         # Footer layout
│   └── session.php        # Session handler & auto-login
├── account.php            # Account summary view
├── dashboard.php          # Main customer dashboard & virtual card
├── index.php              # Modern landing hero page
├── login.php              # Sign-in portal with quick demo helpers
├── logout.php             # Session termination
├── profile.php            # User profile view and editor
├── register.php           # User registration with starter balance
├── statement.php          # Transaction statement history & metrics
├── transfer.php           # Fund transfer functionality & presets
├── schema.sql             # Database schema and seed data
└── README.md              # Project documentation
```

---

## 📄 License

This project is licensed under the [MIT License](LICENSE) and is intended for educational and security training purposes.
