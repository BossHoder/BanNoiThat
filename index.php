<?php
session_start(); // Always start session at the beginning of the file

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

// Fetch all products from the 'hang' table
$sql = "SELECT * FROM hang";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trang chủ</title>
    <!-- Nhúng font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- Nhúng style -->
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body style="height: 5000px; background-color: yellowgreen;">
    <!-- header -->
    <header class="header fixed" style="background-color: yellowgreen;">
        <div class="main-content">
            <div class="body-header">
                <!-- logo -->
                <a href="index.php">
                    <img src="asset/img/logo.jpg" alt="logo" class="logo" />
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
                    <?php
                    // Kiểm tra nếu người dùng đã đăng nhập
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                        // Hiển thị tên người dùng và icon
                        echo '<div class="user-info">';
                        echo '<img src="asset/img/user-icon.png" alt="User Icon" class="user-icon" style="width: 40px; height: 40px; border-radius: 50%;">';
                        echo '<span class="username">' . htmlspecialchars($_SESSION['username']) . '</span>';
                        echo '<a href="logout.php" class="btn">Đăng xuất</a>'; // Nút đăng xuất
                        echo '</div>';
                    } else {
                        // Hiển thị nút Đăng ký nếu chưa đăng nhập
                        echo '<a href="signup.php" class="btn btn-sign-up">ĐĂNG KÝ</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </header>
    <!-- main -->
    <main>
        <?php include "menu.php" ?>
        <div class="product">
            <p class="title">Sản phẩm của chúng tôi</p>
            <div class="main-content">
                <div class="product-list">
                    <?php
                    // Check if there are any products and display them
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="product-item">';
                            echo '<div class="product-image">';
                            // Nếu cột hình trống, sử dụng ảnh mặc định
                            $imagePath = !empty($row["hinh"]) ? $row["hinh"] : 'asset/img/placeholder.png';

                            echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($row["tenhang"]) . '" class="thumb">';
                            echo '</div>';
                            echo '<div class="info">';
                            echo '<div class="head">';
                            echo '<p class="product-title">' . htmlspecialchars($row["tenhang"]) . '</p>';
                            echo '</div>';
                            echo '<p class="decs">' . htmlspecialchars($row["mota"]) . '</p>';
                            echo '<div class="price">Giá: ' . number_format($row["dongia"], 0, '.', '.') . ' VND</div>';
                            echo '<div class="remain">còn lại: ' . number_format($row["soluongton"], 0, '.', '.').' ' . htmlspecialchars($row["donvido"]) .'.</div>';
                            echo '<div class="product-buttons">';
                            echo '<button class="add-to-cart">Thêm vào giỏ</button>';
                            echo '<button class="buy-now">Mua ngay</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>Không có sản phẩm nào trong danh mục.</p>';
                    }
                    ?>
                </div>
            </div>
    </main>
</body>

</html>