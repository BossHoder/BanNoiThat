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
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Xử lý cập nhật số lượng
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);

    // Lấy số lượng tồn kho hiện tại của sản phẩm
    $query = "SELECT soluongton FROM hang WHERE mahang = '$product_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_stock = intval($row['soluongton']);

        // Kiểm tra nếu số lượng vượt quá số lượng tồn hoặc nhỏ hơn 1
        if ($quantity <= $current_stock && $quantity >= 1) {
            // Cập nhật số lượng trong giỏ hàng nếu hợp lệ
            $_SESSION['cart'][$product_id] = $quantity;
        } elseif ($quantity > $current_stock) {
            echo "<script>alert('Số lượng yêu cầu vượt quá số lượng tồn. Hiện còn $current_stock sản phẩm.');</script>";
        } elseif ($quantity < 1) {
            echo "<script>alert('Số lượng không thể nhỏ hơn 1.');</script>";
        }
    } else {
        echo "<script>alert('Sản phẩm không tồn tại.');</script>";
    }

    header("Location: cart.php");
    exit();
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Xử lý mua hàng
if (isset($_POST['buy_all'])) {
    $paymentMethod = isset($_POST['payment']) ? $_POST['payment'] : null; // Get selected payment method

    if ($paymentMethod == 'bank-transfer') {
        // Thanh toán qua MoMo
        $total_price = 0; // Tính tổng tiền của giỏ hàng
        foreach ($cart_items as $item_id => $quantity) {
            $query = "SELECT dongia FROM hang WHERE mahang = '$item_id'";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $dongia = $row['dongia'];
                    $total_price += $dongia * $quantity;
                }
            }
        }

        // Thực hiện yêu cầu thanh toán MoMo
        require 'momo_payment.php'; // Gọi file xử lý MoMo

        // Kết thúc xử lý và không tiếp tục xử lý các phương thức thanh toán khác
        exit;
    } else {
        if (isset($_SESSION['makhach'])) {
            // Proceed with purchase logic here, e.g., reduce stock, log purchase, etc.
            $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            $errors = [];

            $makhach = $_SESSION['makhach'];
            $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            $errors = [];
            $tongtien = 0; // Initialize total price

            // Start transaction
            $conn->begin_transaction();

            try {
                // Insert into 'donhang' table
                $ngaythang = date("Y-m-d");
                $insert_donhang_query = "INSERT INTO donhang (makhach, ngaythang, tongtien) VALUES ('$makhach', '$ngaythang', 0)"; // Tongtien will be updated later
                if (!$conn->query($insert_donhang_query)) {
                    throw new Exception("Lỗi khi thêm đơn hàng: " . $conn->error);
                }
                $madh = $conn->insert_id; // Get the last inserted madh


                foreach ($cart_items as $mahang => $soluong) {
                    // Get product details (dongia)
                    $product_query = "SELECT dongia, soluongton FROM hang WHERE mahang = '$mahang'";
                    $product_result = $conn->query($product_query);

                    if ($product_result->num_rows > 0) {
                        $product_row = $product_result->fetch_assoc();
                        $dongia = $product_row['dongia'];
                        $current_stock = $product_row['soluongton'];

                        // Check stock
                        if ($current_stock < $soluong) {
                            throw new Exception("Sản phẩm $mahang không đủ hàng.");
                        }

                        $thanhtien = $dongia * $soluong;
                        $tongtien += $thanhtien;


                        // Insert into 'chitiethd' table
                        $insert_chitiethd_query = "INSERT INTO chitiethd (mahd, mahang, soluong, thanhtien) VALUES ('$madh', '$mahang', '$soluong', '$thanhtien')";
                        if (!$conn->query($insert_chitiethd_query)) {
                            throw new Exception("Lỗi khi thêm chi tiết hóa đơn: " . $conn->error);
                        }

                        // Insert into 'chitietdonhang' table (if needed - this seems redundant with chitiethd)
                        $insert_chitietdonhang_query = "INSERT INTO chitietdonhang (madh, mahang, soluong, thanhtien) VALUES ('$madh', '$mahang', '$soluong', '$thanhtien')";
                        if (!$conn->query($insert_chitietdonhang_query)) {
                            throw new Exception("Lỗi khi thêm chi tiết đơn hàng: " . $conn->error);
                        }

                        // Update stock
                        $new_stock = $current_stock - $soluong;
                        $update_stock_query = "UPDATE hang SET soluongton = '$new_stock' WHERE mahang = '$mahang'";
                        if (!$conn->query($update_stock_query)) {
                            throw new Exception("Lỗi khi cập nhật số lượng tồn kho: " . $conn->error);
                        }
                    } else {
                        throw new Exception("Sản phẩm $mahang không tồn tại.");
                    }
                }

                // Update 'donhang' with the correct total price
                $update_tongtien_query = "UPDATE donhang SET tongtien = '$tongtien' WHERE madh = '$madh'";
                if (!$conn->query($update_tongtien_query)) {
                    throw new Exception("Lỗi khi cập nhật tổng tiền đơn hàng: " . $conn->error);
                }



                $conn->commit(); // Commit transaction
                unset($_SESSION['cart']); // Clear cart


                echo "<script>
                        alert('Bạn đã đặt hàng thành công');
                        window.location.href = 'index.php';
                      </script>";
            } catch (Exception $e) {
                $conn->rollback(); // Rollback if any error occurred
                echo "<p>Lỗi: " . $e->getMessage() . "</p>"; // Display error message
            }
        } else {
            // Redirect to inputKhach.php if makhach is not set
            header("Location: inputKhach.php");
            exit;
        }
    }
}
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Fetch all products from the 'hang' table
$sql = "SELECT * FROM hang";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <!-- Nhúng font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- Nhúng style -->
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D5B895;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 200px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .total-price {
            font-size: 4rem;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f4b400;
            color: white;
            font-size: 4rem;
        }

        img {
            width: 300px;
            height: 300px;
            object-fit: cover;
        }

        .product-title {
            color: #d9534f;
            font-weight: bold;
        }

        .price {
            color: #5bc0de;
        }

        .category {
            color: #5cb85c;
        }

        .description {
            text-align: left;
            font-size: 1.4rem;
            color: #666;
            line-height: 1.5;
        }

        .menu-loaihang {
            position: fixed;
            top: 100px;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: yellowgreen;
            padding: 20px;
            border-right: 4px solid red;
        }

        .menu-loaihang a {
            font-size: 2rem;
            color: #d60707;
            text-decoration: none;
            display: block;
            margin-bottom: 15px;
        }

        .menu-loaihang a:hover {
            text-decoration: underline;
        }

        .update-btn {
            background-color: #5cb85c;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;

        }

        .remove-btn {
            background-color: #d9534f;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            width: 200px;
            height: 100px;
            font-size: 4rem;
        }

        .quantity-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .quantity-buttons button {
            width: 30px;
            height: 30px;
            font-size: 1.5rem;
            background-color: #f4b400;
            border: none;
            color: white;
            cursor: pointer;
            width: 50px;
            height: 50px;
        }

        .quantity-display {
            width: 50px;
            text-align: center;
            margin: 0 10px;
            font-size: 4rem;
        }

        .buy-all-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 3.5rem;
        }

        .checkout-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 10px 0;
            border-top: 2px solid #ddd;
        }
    </style>

</head>

<body>
    <!-- header -->
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
                        echo '<a href="#"><img src="asset/img/shopping-cart-114.png" alt="User Icon" class="user-icon"></a>';
                        echo '<a href="#"><img src="asset/img/user-icon.png" alt="User Icon" class="user-icon"></a>';
                        echo '<span class="username">' . htmlspecialchars($_SESSION['username']) . '</span>';
                        echo '<a href="logout.php" class="btn ">Đăng xuất</a>'; // Nút đăng xuất
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </header>
    <?php
    // Kiểm tra xem có sản phẩm nào trong giỏ hàng không
    if (!empty($cart_items)) {
        echo "<table>";
        echo "<tr><th>Tên hàng</th><th>Đơn giá</th><th>Số lượng</th><th>Tổng giá</th><th>Hình ảnh</th><th>Hành động</th></tr>";

        $total_price = 0; // Tổng tiền của giỏ hàng

        foreach ($cart_items as $item_id => $quantity) {
            $query = "SELECT tenhang, dongia, mota, hinh FROM hang WHERE mahang = '$item_id'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tenhang = htmlspecialchars($row['tenhang']);
                    $dongia = $row['dongia'];
                    $formatted_price = number_format($dongia, 0, '.', '.');
                    $mota = htmlspecialchars($row['mota']);
                    $hinh = htmlspecialchars($row['hinh']);
                    $total_item_price = $dongia * $quantity;
                    $total_price += $total_item_price;

                    echo "<tr>";
                    echo "<td class='product-title'>$tenhang</td>";
                    echo "<td class='price'>$formatted_price VND</td>";
                    echo "<td>
                        <div class='quantity-buttons'>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='product_id' value='$item_id'>
                                <input type='hidden' name='quantity' value='" . ($quantity - 1) . "'>
                                <button type='submit' name='update_quantity'>-</button>
                            </form>
                            <span class='quantity-display'>$quantity</span>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='product_id' value='$item_id'>
                                <input type='hidden' name='quantity' value='" . ($quantity + 1) . "'>
                                <button type='submit' name='update_quantity'>+</button>
                            </form>
                        </div>
                      </td>";
                    echo "<td class='price'>" . number_format($total_item_price, 0, '.', '.') . " VND</td>";
                    echo "<td><img src='$hinh' alt='$tenhang'></td>";
                    echo "<td>
                        <form method='POST'>
                            <input type='hidden' name='product_id' value='$item_id'>
                            <button type='submit' name='remove_item' class='remove-btn'>Xóa</button>
                        </form>
                      </td>";
                    echo "</tr>";
                }
            }
        }

        echo "</table>";
        echo "<div class='checkout-bar'>";
        echo "<span class='total-price'>Tổng cộng: " . number_format($total_price, 0, '.', '.') . " VND</span>";
        echo "<form method='POST' action='cart.php'>";
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];

            // Check if the user has a 'makhach'
            $query = "SELECT makhach FROM account WHERE username = '$username'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $makhach = $row['makhach'];

                // If 'makhach' is found, proceed with purchase
                
                    $_SESSION['makhach'] = $makhach;  // Ensure 'makhach' is in session
                    // Payment methods INSIDE the form
                    echo "<div class='payment-methods'>
        <div class='payment-option'>
            <input type='radio' id='bank-transfer' name='payment' value='bank-transfer' style='width: 40px; height: 40px;' required>
            <label for='bank-transfer' style='font-size:3rem'>Thanh Toán Online</label>
        </div>
        <div class='payment-option'>
            <input type='radio' id='cash-on-delivery' name='payment' value='cash-on-delivery' style='width: 40px; height: 40px;' checked>
            <label for='cash-on-delivery' style='font-size:3rem'>Trả khi nhận hàng</label>
        </div>
    </div>";

                    echo "<button type='submit' name='buy_all' class='buy-all-btn'>Mua hàng</button>";
                } else {
                    // If no 'makhach', set formaction to inputKhach.php
                    echo "<button type='submit' name='buy_all' class='buy-all-btn' formaction='inputKhach.php'>Mua hàng</button>";
                }
            }
        } else {
            echo "<p>Vui lòng đăng nhập trước khi mua hàng.</p>";
        }
        echo "</div>";
    ?>

</body>

</html>