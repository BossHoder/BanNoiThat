<?php
session_start(); // Always start session at the beginning of the file

include("conn.php");

// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        echo "<script>
            alert('Bạn cần phải đăng nhập để thêm vào giỏ hàng.');
            window.location.href = 'signin.php';
        </script>";
    } else {
        $product_id = $_POST['product_id'];

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = (int)$_SESSION['cart'][$product_id] + 1; // Ép kiểu về int trước khi cộng
        } else {
            // Nếu chưa có sản phẩm trong giỏ, thêm sản phẩm vào giỏ với số lượng 1
            $_SESSION['cart'][$product_id] = 1;
        }

        // Optional: Chuyển hướng hoặc thông báo sau khi thêm vào giỏ hàng
        echo "<script>
            alert('Sản phẩm đã được thêm vào giỏ hàng.');
            window.location.href = 'index.php'; // Điều hướng đến trang giỏ hàng nếu cần
        </script>";
    }
}

// Xử lý mua ngay
if (isset($_POST['buy_now'])) {
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;

    if ($product_id) {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            // Nếu chưa đăng nhập, chuyển hướng đến checkout.php kèm theo product_id
            header("Location: checkout.php?product_id=" . $product_id); // This is now correctly executed
            exit(); // Important: Stop further execution
        } else {
            // Nếu đã đăng nhập, thêm sản phẩm vào giỏ hàng
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += 1;
            } else {
                $_SESSION['cart'][$product_id] = 1;
            }
            // Chuyển hướng đến trang cart.php hoặc thực hiện hành động khác
            header("Location: cart.php");
            exit();
        }
    } else {
        echo "Lỗi: Không có product_id!";
    }
}


// Fetch products from tbl_product (prepared statement)
$stmt = $conn->prepare("SELECT * FROM tbl_product WHERE p_is_active = 1"); // Assuming p_is_active indicates product visibility
$stmt->execute();
$result = $stmt->get_result();

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
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet" />
    <!-- Nhúng style -->
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body style="height: 5000px; background-color: #D5B895;">
    <!-- header -->
     <?php include_once "header.php" ?>
    <!-- main -->
    <main>
        <?php include "menu.php" ?>
        <div class="product">
            <p class="title">Sản phẩm của chúng tôi</p>
            <div class="main-content">
                <div class="product-list">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="product-item">';
                            echo '<div class="product-image">';
                            $imagePath = !empty($row['p_featured_photo']) ? $row['p_featured_photo'] : 'asset/img/placeholder.png'; // Placeholder image
                            echo '<a href="product-details.php?p_id=' . $row['p_id'] . '">';
                            echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($row['p_name']) . '" class="thumb">';
                            echo '</a>';
                            echo '</div>';
                            echo '<div class="info">';
                            echo '<div class="head">';
                            echo '<p class="product-title">' . htmlspecialchars($row['p_name']) . '</p>';
                            echo '</div>';
                            echo '<p class="decs">' . htmlspecialchars($row['p_description']) . '</p>'; // Using short description
                            echo '<div class="price">Giá: ' . number_format($row['p_current_price'], 0, '.', '.') . ' VND</div>';

                            // Quantity and Stock Handling:  You'll need to decide how to handle sizes and colors
                            echo '<div class="remain">còn lại: ' . $row['p_qty'] . ' </div>'; //  For now, just showing total quantity. You'll need to refine this.

                            echo '<div class="product-buttons">';

                            if ($row['p_qty'] > 0) {
                                echo '<form method="POST" style="display: inline-block;">';
                                echo '<input type="hidden" name="product_id" value="' . $row["p_id"] . '">';
                                echo '<button type="submit" name="add_to_cart" class="add-to-cart">Thêm vào giỏ</button>';
                                echo '</form>';
                                // Form cho nút "Mua ngay"
                                echo '<form method="POST" style="display: inline-block;">';
                                echo '<input type="hidden" name="product_id" value="' . $row["p_id"] . '">';
                                echo '<button type="submit" name="buy_now" class="buy-now">Mua ngay</button>';
                                echo '</form>';
                            } else {
                                echo '<form method="POST" style="display: inline-block;">';
                                echo '<input type="hidden" name="product_id" value="' . $row["p_id"] . '">';
                                echo '<button type="submit" name="add_to_cart" class="add-to-cart" disabled>Thêm vào giỏ</button>';
                                echo '</form>';
                                // Form cho nút "Mua ngay"
                                echo '<form method="POST" style="display: inline-block;">';
                                echo '<input type="hidden" name="product_id" value="' . $row["p_id"] . '">';
                                echo '<button type="submit" name="buy_now" class="buy-now" disabled>Mua ngay</button>';
                                echo '</form>';
                            }
                            echo '</div>'; // product-buttons

                            echo '</div>'; // info
                            echo '</div>'; // product-item
                        }
                    } else {
                        echo '<p>Không có sản phẩm nào.</p>';
                    }
                    $stmt->close(); // Close the statement

                    ?>
                </div>
            </div>
    </main>
</body>

</html>