<?php
session_start(); // Start the session

// Kết nối đến MySQL database
$servername = "localhost";
$db_user = "root"; // Thay bằng thông tin đăng nhập của bạn
$db_password = "azz123123"; // Thay bằng mật khẩu của bạn
$dbname = "qlbh";

// Tạo kết nối
$conn = new mysqli($servername, $db_user, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý form đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $cust_email = $_POST['cust_email'];
    $cust_password = $_POST['cust_password'];

    // Kiểm tra nếu cust_email hoặc cust_password rỗng
    if (empty($cust_email) || empty($cust_password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.location.href='signin.php';</script>";
        exit();
    } else {
        // Truy vấn kiểm tra người dùng trong bảng account
        $sql = "SELECT * FROM tbl_customer WHERE cust_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cust_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra nếu người dùng tồn tại
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($cust_password, $row['cust_password'])) {
                // Lưu thông tin người dùng vào session
                $_SESSION['cust_email'] = $cust_email;
                $_SESSION['loggedin'] = true; // Đánh dấu người dùng đã đăng nhập

                // Điều hướng đến trang chủ
                header("Location: index.php");
                exit();
            } else {
                echo "<script>alert('Sai mật khẩu!'); window.location.href='signin.php';</script>";
            }
        } else {
            echo "<script>alert('Tài khoản không tồn tại!'); window.location.href='signin.php';</script>";
        }

        // Đóng statement
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <!-- Nhúng font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <!-- Nhúng style -->
    <link rel="stylesheet" href="asset/css/style.css">

</head>

<body>
    <div id="popup">
        <div class="loader"></div>
        <p id="popup-message"></p>
    </div>
    <header class="header fixed" style="background-color: #2C3E50;">
        <div class="main-content">
            <div class="body-header">
                <!-- logo -->
                <a href="index.php" class="logo-section">
                    <img src="asset/img/logo.jpg" class="logo">
                    <span class="company-name">Nội Thất Theanhdola</span>
                </a>
                <!-- navbar -->
                <nav class="nav">
                    <ul>
                        <li><a href="index.php">Trang chủ</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </nav>
                <!-- btn action -->
                <div class="action">
                    <a href="signup.php" class="btn btn-sign-up btn-mgl">ĐĂNG KÝ</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Form đăng nhập -->
    <div class="main">
        <div class="signup-form">
            <div class="signup-image" style="background-image: url(asset/img/baloon.svg)">
            </div>
            <form action="login_process.php" method="post">
                <div class="signup-field">
                    <div class="input-bar">
                        <label for="cust_email">Nhập email</label>
                        <br>
                        <input type="email" name="cust_email" id="cust_email">
                    </div>
                    <div class="input-bar">
                        <label for="cust_password">Nhập mật khẩu</label>
                        <br>
                        <input type="password" name="cust_password" id="cust_password">
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" name="agreeTP" id="agreeTP">
                        <label for="agreeTP">Chấp nhận <a href="#">điều khoản</a> cùng <a href="#">chính sách</a> của chúng tôi</label>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" name="submitReciveNews" id="submitReciveNews">
                        <label for="submitReciveNews">Nhận thông tin mới nhất từ chúng tôi</label>
                    </div>
                    <button type="submit" class="btn-submit">Đăng Nhập</button>

            </form>
            <div class="login-by-google">
                <button class="btn-submit">
                    <img src="asset/img/Social media logo.svg" alt="" style="height: 40px; width: 40px">
                    Đăng nhập với google
                </button>
            </div>
        </div>
    </div>
    </div>
    <script type="text/javascript" src="main.js"></script>

</body>

</html>