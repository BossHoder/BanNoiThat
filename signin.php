<?php
session_start(); // Start the session

// Kết nối đến MySQL database
$servername = "localhost";
$db_username = "root"; // Thay bằng thông tin đăng nhập của bạn
$db_password = "azz123123"; // Thay bằng mật khẩu của bạn
$dbname = "qlbh";

// Tạo kết nối
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý form đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra nếu username hoặc password rỗng
    if (empty($username) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.location.href='signin.php';</script>";
        exit();
    } else {
        // Truy vấn kiểm tra người dùng trong bảng account
        $sql = "SELECT * FROM account WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra nếu người dùng tồn tại
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($password, $row['password'])) {
                // Lưu thông tin người dùng vào session
                $_SESSION['username'] = $username;
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
                        <li><a href="#">Cửa hàng</a></li>
                        <li><a href="#">Thông tin</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </nav>
                <!-- btn action -->
                <div class="action">
                    <a href="signup.php" class="btn btn-sign-up">ĐĂNG KÝ</a>
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
                        <label for="username">Nhập tên đăng nhập</label>
                        <br>
                        <input type="text" name="username" id="username">
                    </div>
                    <div class="input-bar">
                        <label for="password">Nhập mật khẩu</label>
                        <br>
                        <input type="password" name="password" id="password">
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
</body>

</html>