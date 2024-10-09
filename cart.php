<?php
session_start();
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
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
// Xử lý cập nhật số lượng
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);

    // Lấy số lượng tồn kho hiện tại của sản phẩm
    $query = "SELECT p_qty FROM tbl_product WHERE p_id = '$product_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_stock = intval($row['p_qty']);

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

    // Debug: kiểm tra xem product_id có đúng không và sản phẩm có trong giỏ hàng không
    if (isset($_SESSION['cart'][$product_id])) {
        // Get product name
        $query = "SELECT p_name FROM tbl_product WHERE p_id = '$product_id'";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $product_name = $row['p_name'];

        // Remove product from cart
        unset($_SESSION['cart'][$product_id]);

        // Alert product name and redirect to cart.php
        echo "<script>alert('Sản phẩm $product_name đã được xóa khỏi giỏ hàng.'); window.location.href = 'cart.php';</script>";
    } else {
        echo "<script>alert('Sản phẩm không tồn tại trong giỏ hàng.'); window.location.href = 'cart.php';</script>";
    }
    exit();
}

// Xử lý mua hàng
if (isset($_POST['buy_all'])) {
    $paymentMethod = isset($_POST['payment']) ? $_POST['payment'] : null; // Get selected payment method
    if (!isset($_SESSION['cust_address'])) { // Using cust_id now
        header("Location: inputKhach.php"); // You'll need to adapt inputKhach.php
        exit;
    }
    $cust_id = $_SESSION['cust_id']; // Use cust_id

    if ($paymentMethod == 'bank-transfer') {
        // Thanh toán qua MoMo
        $total_price = 0; // Tính tổng tiền của giỏ hàng
        foreach ($cart_items as $item_id => $quantity) {
            $query = "SELECT p_current_price FROM tbl_product WHERE p_id = '$item_id'";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $p_current_price = $row['p_current_price'];
                    $total_price += $p_current_price * $quantity;
                }
            }
        }

        // Thực hiện yêu cầu thanh toán MoMo
        require 'payment/momo_payment.php'; // Gọi file xử lý MoMo

        // Kết thúc xử lý và không tiếp tục xử lý các phương thức thanh toán khác
        exit;
    } else {
        if (isset($_SESSION['cust_id'])) {
            // Proceed with purchase logic here, e.g., reduce stock, log purchase, etc.
            $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            $errors = [];

            $cust_id = $_SESSION['cust_id'];
            $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            $errors = [];
            $tongtien = 0; // Initialize total price

            // Start transaction
            $conn->begin_transaction();
            try {
                // tbl_order insert (no equivalent to donhang, directly insert into order table)
                $payment_id = time(); // Or generate a unique payment ID
                foreach ($_SESSION['cart'] as $p_id => $quantity) {
                    $product_stmt = $conn->prepare("SELECT p_current_price, p_qty, p_name FROM tbl_product WHERE p_id = ?");
                    $product_stmt->bind_param("i", $p_id);
                    $product_stmt->execute();
                    $product_result = $product_stmt->get_result();
                    $product = $product_result->fetch_assoc();

                    if (!$product) {
                        throw new Exception("Không tìm thấy sp.");
                    }

                    if ($product['p_qty'] < $quantity) {
                        throw new Exception("Hết Hàng " . $product['p_name']);
                    }


                    $unit_price = $product['p_current_price'];
                    $product_name = $product['p_name'];
                    // ... Get size and color if applicable (you'll need to adapt this based on your cart structure and database) ...
                    $size = ""; // Replace with actual size logic if needed
                    $color = ""; // Replace with actual color logic if needed



                    $order_stmt = $conn->prepare("INSERT INTO tbl_order (product_id, product_name, size, color, quantity, unit_price, payment_id) VALUES (?,?,?,?,?,?,?)");
                    $order_stmt->bind_param("isssiis", $p_id, $product_name, $size, $color, $quantity, $unit_price, $payment_id);
                    $order_stmt->execute();


                    // Update Product Quantity
                    $new_qty = $product['p_qty'] - $quantity;
                    $update_qty_stmt = $conn->prepare("UPDATE tbl_product SET p_qty = ? WHERE p_id = ?");
                    $update_qty_stmt->bind_param("ii", $new_qty, $p_id);
                    $update_qty_stmt->execute();
                }


                // You don't need the donhang update as you are inserting directly into tbl_order

                $conn->commit();
                unset($_SESSION['cart']);
                echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'index.php';</script>";
            } catch (Exception $e) {
                $conn->rollback();
                echo "<p>Lỗi: " . $e->getMessage() . "</p>"; // Handle/display error
            }
        }
    }
}
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Fetch all products from the 'hang' table
$sql = "SELECT * FROM tbl_product";
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
                        echo '<span class="username">' . htmlspecialchars($_SESSION['cust_name']) . '</span>';
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
            $query = "SELECT p_name, p_current_price, p_description, p_featured_photo FROM tbl_product WHERE p_id = '$item_id'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $p_name = htmlspecialchars($row['p_name']);
                    $p_current_price = $row['p_current_price'];
                    $formatted_price = number_format($p_current_price, 0, '.', '.');
                    $p_description = htmlspecialchars($row['p_description']);
                    $p_featured_photo = htmlspecialchars($row['p_featured_photo']);
                    $total_item_price = $p_current_price * $quantity;
                    $total_price += $total_item_price;

                    echo "<tr>";
                    echo "<td class='product-title'>$p_name</td>";
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
                    echo "<td><img src='$p_featured_photo' alt='$p_name'></td>";
                    echo "<td>
                            <form method='POST'>
                                <input type='hidden' name='product_id' value='". $item_id. "'> 
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
        //  "Buy All" button handling (check for cust_id):

        if (isset($_SESSION['cust_email'])) {
            $email = $_SESSION['cust_email'];

            // Truy vấn để lấy cust_id từ bảng tbl_customer
            $query = "SELECT cust_id FROM tbl_customer WHERE cust_email = '$email'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $cust_id = $row['cust_id'];

                // Thiết lập cust_id vào session
                $_SESSION['cust_id'] = $cust_id; // Payment methods INSIDE the form
                echo "<div class='payment-methods'>
                        <div class='payment-option'>
                            <input type='radio' id='bank-transfer' name='payment' value='bank-transfer' style='width: 40px; height: 40px;' required>
                            <label for='bank-transfer' style='font-size:3rem'>Thanh Toán Online</label>
                        </div>
                        <div class='payment-option'>
                            <input type='radio' id='cash-on-delivery' name='payment' value='cash-on-delivery' style='width: 40px; height: 40px;' >
                            <label for='cash-on-delivery' style='font-size:3rem'>Trả khi nhận hàng</label>
                        </div>
                        </div>";
                echo "<button type='submit' name='buy_all' class='buy-all-btn'>Mua hàng</button>"; // Correct formaction
            }
        } else {
            echo "<p>Vui lòng đăng nhập trước khi mua hàng.</p>";
        }
    }
    echo "</div>";
    ?>

</body>

</html>