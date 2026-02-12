<?php
$conn = mysqli_connect('localhost', 'root', '', 'bookingsmarts');
if (!$conn) die("Failed to connect");

// 1. Create admins table
$sql_create = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'MANAGE') NOT NULL,
    nama_lengkap VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql_create);

// 2. Insert initial data with hashing
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$manage_pass = password_hash('manage123', PASSWORD_DEFAULT);

mysqli_query($conn, "INSERT IGNORE INTO admins (username, password, role, nama_lengkap) VALUES ('admin', '$admin_pass', 'ADMIN', 'Super Admin')");
mysqli_query($conn, "INSERT IGNORE INTO admins (username, password, role, nama_lengkap) VALUES ('manage', '$manage_pass', 'MANAGE', 'Manager Office')");

echo "Success: Table 'admins' created and initial accounts added.\n";
mysqli_close($conn);
