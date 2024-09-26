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
    <link rel="stylesheet" href="style.css">
</head>

<body style="height: 5000px;">
    <!-- header -->
    <header class="header fixed">
        <div class="main-content">
            <div class="body-header">
                <!-- logo -->
                <a href="index.php">
                    <img src="img/logo.jpg" alt="logo" class="logo" />
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
    <!-- main -->
    <main>
        <?php include "menu.php" ?>
        <div class="product">
            <p class="title">Sản phẩm của chúng tôi</p>
            <div class="main-content">
                <div class="product-list">
                    <!-- Ghế phonk cack -->
                    <div class="product-item">
                        <div class="product-image">
                            <img src="img/syltherine.svg" alt="" class="thumb">
                        </div>
                        <div class="info">
                            <div class="head">
                                <p class="product-title">Syltherine</p>
                            </div>
                            <p class="decs">Ghế ngồi coffee</p>
                            <div class="price">750.000đ</div>
                            <div class="product-buttons">
                                <button class="add-to-cart">Thêm vào giỏ</button>
                                <button class="buy-now">Mua ngay</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-list">
                    <!-- Ghế phonk cack -->
                    <div class="product-item">
                        <img src="img/leviosa.svg" alt="" class="thumb">
                        <div class="info">
                            <div class="head">
                                <p class="product-title">Ghế đẩu horae o4</p>
                            </div>
                            <p class="decs">Ghế đẩu</p>
                            <div class="price">750.000đ</div>
                            <button class="add-to-cart">Thêm vào giỏ</button>
                            <button class="buy-now">Mua ngay</button>
                        </div>
                    </div>
                </div>
                <div class="product-list">
                    <!-- Ghế phonk cack -->
                    <div class="product-item">
                        <img src="img/potty.svg" alt="" class="thumb">
                        <div class="info">
                            <div class="head">
                                <p class="product-title">Sofa chân cao pusibich</p>
                            </div>
                            <p class="decs">Sofa dành cho người lùn</p>
                            <div class="price">5.750.000đ</div>
                            <button class="add-to-cart">Thêm vào giỏ</button>
                            <button class="buy-now">Mua ngay</button>
                        </div>
                    </div>
                </div>
                <div class="product-list">
                    <!-- Ghế phonk cack -->
                    <div class="product-item">
                        <img src="img/respira.svg" alt="" class="thumb">
                        <div class="info">
                            <div class="head">
                                <p class="product-title">Combo Sofa+Bàn daking</p>
                            </div>
                            <p class="decs">Bộ bàn bàn ghế tiếp khách</p>
                            <div class="price">15.750.000đ</div>
                            <button class="add-to-cart">Thêm vào giỏ</button>
                            <button class="buy-now">Mua ngay</button>
                        </div>
                    </div>
                </div>
                <div class="product-list">
                    <!-- Ghế phonk cack -->
                    <div class="product-item">
                        <img src="img/grifo.svg" alt="" class="thumb">
                        <div class="info">
                            <div class="head">
                                <p class="product-title">Đèn ngủ huiz</p>
                            </div>
                            <p class="decs">Đèn ngủ sang choảnh</p>
                            <div class="price">450.000đ</div>
                            <button class="add-to-cart">Thêm vào giỏ</button>
                            <button class="buy-now">Mua ngay</button>
                        </div>
                    </div>
                </div>
                <div class="product-list">
                    <!-- Ghế phonk cack -->
                    <div class="product-item">
                        <img src="img/muggo.svg" alt="" class="thumb">
                        <div class="info">
                            <div class="head">
                                <p class="product-title">Sofa unicouch</p>
                            </div>
                            <p class="decs">sofa!!!!</p>
                            <div class="price">169.550.000đ</div>
                            <button class="add-to-cart">Thêm vào giỏ</button>
                            <button class="buy-now">Mua ngay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>