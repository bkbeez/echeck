-- Database: echeck
-- สำหรับสร้างตาราง users และ events สำหรับระบบจัดการกิจกรรม

CREATE DATABASE IF NOT EXISTS `echeck` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `echeck`;

CREATE TABLE events (
    events_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    events_name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    participant_type ENUM('ALL', 'LIST') DEFAULT 'ALL',
    status TINYINT DEFAULT 0 COMMENT '0=ร่าง, 1=เปิด, 2=ปิด, 3=ยกเลิก',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE events_shares (
    share_id INT AUTO_INCREMENT PRIMARY KEY,
    events_id INT NOT NULL,
    shared_email VARCHAR(255) NOT NULL,
    shared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (events_id) REFERENCES activities(events_id) ON DELETE CASCADE
);