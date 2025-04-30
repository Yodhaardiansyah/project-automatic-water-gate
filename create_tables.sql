-- SQL script to create necessary tables for the irigasi_db database

CREATE TABLE pintu_air (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(50) NOT NULL,
    sumber_perubahan VARCHAR(100) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE sensor_air (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ketinggian FLOAT NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    waktu_buka TIME NOT NULL,
    waktu_tutup TIME NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user'
);

-- Insert dummy data into pintu_air
INSERT INTO pintu_air (status, sumber_perubahan) VALUES
('Terbuka', 'Manual'),
('Tertutup', 'Otomatis');

-- Insert dummy data into sensor_air
INSERT INTO sensor_air (ketinggian, recorded_at) VALUES
(75.5, '2024-06-01 08:00:00'),
(80.2, '2024-06-01 12:00:00');

-- Insert dummy data into jadwal
INSERT INTO jadwal (waktu_buka, waktu_tutup) VALUES
('06:00:00', '07:00:00'),
('18:00:00', '19:00:00');

-- Insert dummy data into users
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$abcdefghijklmnopqrstuv', 'admin'), -- password hash placeholder
('user1', '$2y$10$abcdefghijklmnopqrstuv', 'user'); -- password hash placeholder
