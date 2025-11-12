-- Database: echeck
-- สำหรับสร้างตาราง users และ events สำหรับระบบจัดการกิจกรรม

CREATE DATABASE IF NOT EXISTS `echeck` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `echeck`;


CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- เจ้าของกิจกรรม
    events_id VARCHAR(50) NOT NULL UNIQUE,
    events_name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    participant_type ENUM('ALL', 'LIST') DEFAULT 'ALL',
    status ENUM('0', '1', '2', '3') DEFAULT '0',  -- 0=ร่าง, 1=เปิด, 2=ปิด, 3=ยกเลิก
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_events_owner (user_id),
    INDEX idx_events_dates (start_date, end_date)
);

CREATE TABLE IF NOT EXISTS event_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    shared_email VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_event_share (event_id, shared_email),
    CONSTRAINT fk_event_shares_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);
