# 🏦 Bank Ashkona | Secure Bank Management System

A comprehensive, secure, and fully responsive web-based banking application designed to digitalize core financial operations[cite: 5]. This repository showcases full-stack development, emphasizing robust backend security, ACID-compliant database architecture, and a modern user interface.

## ✨ Key Features

* **Role-Based Access Control (RBAC):** Dedicated routing and secure dashboards tailored for Customers and Administrators[cite: 3, 5].
* **Two-Factor Authentication (2FA):** A secure login pipeline requiring a time-sensitive 6-digit One-Time Password (OTP) alongside standard credentials[cite: 3, 5].
* **ACID-Compliant Transfers:** Atomic, peer-to-peer balance transfer mechanisms utilizing strict database row locks and rollbacks to prevent race conditions and ensure zero data loss[cite: 5].
* **Digital Card Services:** A customer portal for requesting Visa and Mastercard digital cards, paired with an Admin engine for secure 16-digit card generation[cite: 1, 5].
* **Transaction Auditing:** An immutable financial ledger logging all system deposits, withdrawals, and transfers with automatic timestamps[cite: 5].

## 💻 Technology Stack

* **Frontend:** HTML5, CSS3, Tailwind CSS (featuring Glassmorphism UI design), Vanilla JavaScript[cite: 3, 4].
* **Backend:** Core PHP (Upgraded with PDO for highly secure database interactions)[cite: 3].
* **Database:** MySQL (Relational Schema normalized to 3NF)[cite: 1, 5].
* **Local Environment:** XAMPP (Apache Web Server)[cite: 2].
* **Version Control:** Git & GitHub (GitFlow methodology).

## 🛡️ Security Architecture

* **SQL Injection Defense:** All database queries utilize PDO Prepared Statements, ensuring user input is never interpreted as executable SQL logic[cite: 3].
* **Cryptography:** Plaintext passwords are never stored; the system uses modern `PASSWORD_BCRYPT` hashing algorithms[cite: 3, 5].
* **XSS Prevention:** Strict input sanitization and validation on all forms to neutralize Cross-Site Scripting payloads[cite: 2].
* **Session Protection:** Automated session ID regeneration upon login and strict memory clearance during logout to prevent session hijacking and fixation[cite: 2, 3].

## 🚀 Installation & Setup (Local Development)

To run Bank Ashkona on your local machine:

1. Install **XAMPP** and start the **Apache** and **MySQL** modules[cite: 2].
2. Clone the repository into your web root directory (`C:\xampp\htdocs\`):
   ```bash

👨‍💻 Development TeamThis project was developed collaboratively by:
🌟 Meraj Islam - Team Leader  
⭐ Waleed Bin Baki - Core Developer  
