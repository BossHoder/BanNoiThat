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

    // Cập nhật số lượng trong giỏ hàng, nếu số lượng là 0, xóa sản phẩm
    if ($quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
    } else {
        unset($_SESSION['cart'][$product_id]);
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

        h1 {
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
                        <li><a href="#">Cửa hàng</a></li>
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
    echo "<h1>Tổng cộng: " . number_format($total_price, 0, '.', '.') . " VND</h1>";
} else {
    echo "<p>Giỏ hàng của bạn hiện đang trống.</p>";
}
?>

</body>

</html>