# ID Card Generator System

A web-based ID Card Generator System developed using **Core PHP, MySQL, HTML, CSS, and JavaScript**. The system allows users to register, submit ID card requests, and download their generated ID cards after admin approval.

---

## Features

### User Module
- User Registration
- User Login & Logout
- Complete Profile
- Upload Profile Photo
- Submit Fresher ID Request
- Submit New ID Request
- View Request History
- Download Approved ID Card PDF
- View Rejection Reason (if rejected)

### Admin Module
- Secure Admin Login
- View Dashboard
- Manage ID Card Requests
- Approve or Reject Requests
- Generate Professional PDF ID Cards
- Store Generated PDFs
- View Approved Requests

---

## Technologies Used

- Core PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- WAMP Server

---

## Project Structure

```
ID_Card_Generator_System_WD/
│
├── uploads/
│   └── photos/
│
├── generated_ids/
│
├── index.php
├── register.php
├── profile.php
├── user-dashboard.php
├── fresher-id-request.php
├── new-id-request.php
├── my-id-history.php
│
├── admin-login.php
├── admin-dashboard.php
├── admin_requests.php
├── admin_approved.php
│
├── update_status.php
├── generate_pdf.php
├── pdf_generator.php
├── db_connect.php
├── schema.sql
├── setup_admin.php
└── README.md
```

---

# Installation Guide

## Step 1: Copy Project

Copy the project folder into the WAMP `www` directory.

Example:

```
C:\wamp64\www\ID_Card_Generator_System_WD
```

---

## Step 2: Start WAMP

Start **WAMP Server**.

Ensure the WAMP icon turns **Green**, indicating that Apache and MySQL are running successfully.

---

## Step 3: Create Database

1. Open:

```
http://localhost/phpmyadmin
```

2. Create a new database named:

```
idcard_db
```

3. Click **Import**.

4. Select:

```
schema.sql
```

5. Click **Go**.

The following tables will be created:

- users
- admins
- id_requests

---

## Step 4: Configure Database

Open:

```
db_connect.php
```

Update database credentials if necessary.

Example:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "idcard_db";
```

---

## Step 5: Create Admin Account

Open the following URL:

```
http://localhost/ID_Card_Generator_System_WD/setup_admin.php
```

Default Admin Credentials:

```
Email:
admin@idcard.com

Password:
admin123
```

After creating the admin account, you may delete `setup_admin.php` for security.

---

## Step 6: Folder Permissions

Ensure the following folders are writable:

```
uploads/
uploads/photos/
generated_ids/
```

Windows (WAMP) usually provides the required permissions by default.

---

# Running the Project

### User

Login:

```
http://localhost/ID_Card_Generator_System_WD/index.php
```

Register:

```
http://localhost/ID_Card_Generator_System_WD/register.php
```

---

### Admin

```
http://localhost/ID_Card_Generator_System_WD/admin-login.php
```

---

# System Workflow

1. User registers an account.
2. User logs in.
3. User completes profile details.
4. User submits an ID card request.
5. Request is stored in the database.
6. Admin reviews pending requests.
7. Admin approves or rejects the request.
8. On approval:
   - A professional PDF ID card is generated.
   - The PDF is stored in the `generated_ids` folder.
9. The user can download the PDF from **My ID History**.
10. If rejected, the rejection reason is displayed.

---

# Security Features

- Session-based Authentication
- Separate User and Admin Login
- Prepared SQL Statements
- Protected Admin Pages
- Protected User Pages
- File Upload Validation
- Download Available Only After Approval

---

# Fixed Issues

- Fixed Login System
- Fixed Registration
- Fixed Session Management
- Fixed Profile Update
- Fixed Photo Upload
- Fixed ID Request Submission
- Fixed Admin Authentication
- Fixed Dashboard Protection
- Fixed Approve/Reject Workflow
- Fixed PDF Generation
- Fixed Download Permission Logic
- Fixed SQL Injection Vulnerabilities
- Maintained Existing UI Without Design Changes

---

# Default Admin Credentials

| Email | Password |
|--------|----------|
| admin@idcard.com | admin123 |

---

# Notes

- The system generates PDF ID cards without requiring Composer or external libraries.
- PDF generation is handled using the built-in `pdf_generator.php`.
- Works directly with WAMP Server.

---

## Developed For

**College Mini Project**

Built using Core PHP and MySQL for learning web development and CRUD operations.
