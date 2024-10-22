<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
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
                    <img src="asset/img/logo.jpg" alt="logo" class="logo">
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
            <div class="signup-image" style="background-image: url(asset/img/baloon.svg);">
            </div>
            <div class="signup-field">
                <form action="signupprocess.php" method="post" name="form1">

                    <div class="input-bar">
                        <label for="cust_email">Nhập Tên</label>
                        <br>
                        <input type="text" name="cust_name" id="cust_name">
                    </div>
                    <div class="input-bar">
                        <label for="cust_email">Nhập Email</label>
                        <br>
                        <input type="email" name="cust_email" id="cust_email">
                    </div>
                    <div class="input-bar">
                        <label for="cust_password">Nhập mật khẩu</label>
                        <br>
                        <input type="password" name="cust_password" id="cust_password">
                    </div>
                    <div class="input-bar">
                        <label for="cust_re_password">Nhập lại mật khẩu</label>
                        <br>
                        <input type="password" name="cust_re_password" id="cust_re_password">
                    </div>

                    <div class="checkbox">
                        <input type="checkbox" name="agreeTP" id="agreeTP" required>
                        <label for="agreeTP">Chấp nhận <a href="#">điều khoản</a> cùng <a href="#">chính sách</a> của chúng tôi</label>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" name="submitReciveNews" id="submitReciveNews">
                        <label for="submitReciveNews">Nhận thông tin mới nhất từ chúng tôi</label>
                    </div>
                    <button type="submit" class="btn-submit">Đăng Ký</button>
                </form>
                <div class="login-by-google">
                    <button class="btn-submit">
                        <img src="asset/img/Social media logo.svg" alt="" style="height: 40px; width: 40px">
                        Đăng nhập với google
                    </button>
                </div>
                <div class="already-have-account">
                    <span>Bạn đã có tài khoản?</span>
                    <a href="signin.php">Đăng nhập tại đây</a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="main.js"></script>

</body>

</html>