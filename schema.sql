-- ID Card Generator System - Database Schema
-- Import this file in phpMyAdmin (WAMP) before running the project.
-- Creates database `idcard_db` with all required tables.

CREATE DATABASE IF NOT EXISTS idcard_db;
USE idcard_db;

-- ---------------------------------------------------------
-- Table: users  (registered students / staff who log in)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(50)  NOT NULL,
    middle_name VARCHAR(50)  DEFAULT '',
    last_name   VARCHAR(50)  NOT NULL,
    contact     VARCHAR(20)  DEFAULT '',
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    mobile      VARCHAR(20)  DEFAULT '',
    dob         DATE NULL,
    blood_group VARCHAR(5)   DEFAULT '',
    designation VARCHAR(20)  DEFAULT 'Student',   -- Student / Teacher / Staff
    enrollment  VARCHAR(50)  DEFAULT '',          -- enrollment / employee number
    department  VARCHAR(50)  DEFAULT '',
    join_date   DATE NULL,
    photo       VARCHAR(255) DEFAULT '',          -- profile photo path
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: admins  (separate login for admin panel)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(50)  DEFAULT 'Admin',
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- No admin row is inserted here because the password must be hashed by PHP.
-- After importing this file, open setup_admin.php ONCE in your browser
-- to create the default admin account. See README.txt for details.

-- ---------------------------------------------------------
-- Table: id_requests  (ID card requests raised by users)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS id_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id  INT NOT NULL,
    request_type VARCHAR(20) NOT NULL DEFAULT 'New',   -- 'New' or 'Fresher'

    student_name VARCHAR(150) NOT NULL,
    roll_no      VARCHAR(50)  DEFAULT '',   -- enrollment / employee number
    course       VARCHAR(50)  DEFAULT '',   -- department
    year         VARCHAR(20)  DEFAULT '',   -- designation (Student/Teacher/Staff)

    dob          DATE NULL,
    blood_group  VARCHAR(5)   DEFAULT '',
    email        VARCHAR(100) DEFAULT '',
    mobile       VARCHAR(20)  DEFAULT '',
    photo        VARCHAR(255) DEFAULT '',   -- photo used for the ID card
    reason       VARCHAR(255) DEFAULT '',   -- reason for new-id-request
    document     VARCHAR(255) DEFAULT '',   -- supporting document upload

    status           VARCHAR(20)  NOT NULL DEFAULT 'Pending', -- Pending/Approved/Rejected
    rejection_reason VARCHAR(255) DEFAULT '',
    pdf_path         VARCHAR(255) DEFAULT '',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_id_requests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
