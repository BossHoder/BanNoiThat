<?php
$servername = "localhost";  // Tên máy chủ MySQL (thường là localhost)
$username = "root";         // Tên người dùng MySQL
$password = "azz123123";             // Mật khẩu của người dùng MySQL
$dbname = "QLBH";   // Tên database bạn muốn kết nối

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error . "");
    
}

