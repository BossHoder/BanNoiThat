<?php
session_start();
$servername = "localhost";  // Tên máy chủ MySQL (thường là localhost)
$username = "root";         // Tên người dùng MySQL
$password = "azz123123";             // Mật khẩu của người dùng MySQL
$dbname = "QLBH";   // Tên database bạn muốn kết nối

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error . "");
}
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cập nhật số lượng sản phẩm
    if (isset($_POST['update_quantity'])) {
        $product_id = $_POST['product_id'];
        $new_quantity = $_POST['quantity'];

        // Kiểm tra số lượng không nhỏ hơn 1
        if ($new_quantity > 0) {
            $_SESSION['cart'][$product_id] = $new_quantity;
        } else {
            unset($_SESSION['cart'][$product_id]); // Xóa sản phẩm nếu số lượng bằng 0
        }
    }

    // Xóa sản phẩm khỏi giỏ hàng
    if (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
    }
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    // Xử lý mua hàng
    if (isset($_POST['buy_all'])) {
        $paymentMethod = isset($_POST['payment']) ? $_POST['payment'] : null; // Lấy phương thức thanh toán

        if (!isset($_SESSION['cust_id'])) {
            header("Location: inputKhach.php"); // Chuyển hướng đến trang nhập thông tin khách hàng nếu chưa đăng nhập
            exit;
        }

        $cust_id = $_SESSION['cust_id']; // Lấy ID khách hàng từ session
        $cust_name = $_SESSION['cust_name']; // Lấy tên khách hàng từ session
        $cust_email = $_SESSION['cust_email']; // Lấy email khách hàng từ session

        $total_price = 0; // Tổng tiền của giỏ hàng
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

        // Start transaction
        $conn->begin_transaction();

        try {
            $payment_id = time(); // Hoặc tạo một payment_id duy nhất khác
            $buy_date = date('Y-m-d'); // Lấy ngày hiện tại
            $tongtien = 0; // Khởi tạo tổng tiền

            // Xử lý đơn hàng cho từng sản phẩm trong giỏ hàng
            foreach ($_SESSION['cart'] as $p_id => $quantity) {
                $product_stmt = $conn->prepare("SELECT p_current_price, p_qty, p_name FROM tbl_product WHERE p_id = ?");
                $product_stmt->bind_param("i", $p_id);
                $product_stmt->execute();
                $product_result = $product_stmt->get_result();
                $product = $product_result->fetch_assoc();

                if (!$product) {
                    throw new Exception("Không tìm thấy sản phẩm.");
                }

                if ($product['p_qty'] < $quantity) {
                    throw new Exception("Hết Hàng " . $product['p_name']);
                }

                $unit_price = $product['p_current_price'];
                $product_name = $product['p_name'];

                $size = ""; // Logic xử lý size nếu cần
                $color = ""; // Logic xử lý màu sắc nếu cần

                // Tính tổng tiền cho mỗi sản phẩm
                $tongtien += $unit_price * $quantity;

                // Câu lệnh INSERT vào bảng tbl_order với buy_date
                $order_stmt = $conn->prepare("INSERT INTO tbl_order (product_id, product_name, size, color, quantity, unit_price, payment_id, total, buy_date) 
                                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $order_stmt->bind_param("isssiisds", $p_id, $product_name, $size, $color, $quantity, $unit_price, $payment_id, $tongtien, $buy_date);
                $order_stmt->execute();

                // Cập nhật số lượng sản phẩm trong kho
                $new_qty = $product['p_qty'] - $quantity;
                $update_qty_stmt = $conn->prepare("UPDATE tbl_product SET p_qty = ? WHERE p_id = ?");
                $update_qty_stmt->bind_param("ii", $new_qty, $p_id);
                $update_qty_stmt->execute();
            }

            // Xử lý thông tin thanh toán
            $payment_method = $paymentMethod;
            $payment_status = $payment_method === 'bank-transfer' ? 'pending' : 'completed'; // Tùy theo phương thức thanh toán
            $payment_date = date('Y-m-d H:i:s');
            $paid_amount = $tongtien; // Tổng số tiền đã thanh toán
            $txnid = uniqid(); // Mã giao dịch duy nhất
            $payment_id = time(); // Mã thanh toán duy nhất (hoặc tạo mã khác)

            // Chèn vào bảng tbl_payment
            $payment_stmt = $conn->prepare("INSERT INTO tbl_payment (customer_id, customer_name, customer_email, payment_date, txnid, paid_amount, payment_method, payment_status, payment_id) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $payment_stmt->bind_param("issssiiss", $cust_id, $cust_name, $cust_email, $payment_date, $txnid, $paid_amount, $payment_method, $payment_status, $payment_id);
            $payment_stmt->execute();

            // Hoàn tất giao dịch
            $conn->commit();
            unset($_SESSION['cart']); // Xóa giỏ hàng sau khi hoàn tất mua hàng
            echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'index.php';</script>";
        } catch (Exception $e) {
            $conn->rollback(); // Hủy giao dịch nếu có lỗi xảy ra
            echo "<script>alert('" . htmlspecialchars($e->getMessage()) . "');</script>";
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
                                <input type='hidden' name='product_id' value='" . $item_id . "'>
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
    echo "<form method='POST'>";
    //  "Buy All" button handling (check for cust_id):

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


    echo "</div>";
    ?>

</body>

</html>