<link rel="stylesheet" href="asset/css/style.css">
<header class="header fixed" style="background-color: #2C3E50;">
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
                        echo '<a href="cart.php"><img src="asset/img/shopping-cart-114.png" alt="User Icon" class="user-icon"></a>';
                        echo '<a href="#" class="user-icon-wrapper">';
                        echo '<img src="asset/img/user-icon.png" alt="User Icon" class="user-icon">';
                        echo '</a>';
                        echo '<span class="username">' . htmlspecialchars($_SESSION['cust_name']) . '</span>';
                        echo '<div class="dropdown-menu">';
                        echo '<a href="#">Tên: ' . htmlspecialchars($_SESSION['cust_name']) . '</a>';
                        echo '<a href="changepassword.php">Đổi mật khẩu</a>';
                        echo '<a href="logout.php">Đăng xuất</a>';
                        echo '</div>';
                        echo '</div>';
                        }
                        else {
                            // Hiển thị nút Đăng ký nếu chưa đăng nhập
                            echo '<a href="signup.php" class="btn btn-sign-up btn-mgl">ĐĂNG KÝ</a>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </header>
