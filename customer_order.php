<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['cust_id'])) {
    header("Location: signin.php");
    exit();
}
// Database connection
$conn = new mysqli("localhost", "theanhdola", "azz123123", "qlbh");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$cust_id = $_SESSION['cust_id'];
// Query to get customer's orders
$sql = "SELECT o.payment_id, 
               GROUP_CONCAT(o.product_name) AS product_names, 
               GROUP_CONCAT(o.size) AS sizes, 
               GROUP_CONCAT(o.color) AS colors, 
               GROUP_CONCAT(o.quantity) AS quantities, 
               GROUP_CONCAT(o.unit_price) AS unit_prices, 
               GROUP_CONCAT(o.Total) AS totals, 
               GROUP_CONCAT(p.p_featured_photo) AS photos,
               MAX(o.buy_date) AS buy_date
        FROM tbl_order o
        LEFT JOIN tbl_product p ON o.product_id = p.p_id
        WHERE o.payment_id IN (SELECT payment_id FROM tbl_payment WHERE customer_id = '$cust_id')
        GROUP BY o.payment_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #f3f4f6;
            --text-color: #333;
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1700px;
            margin: 6% auto 0;

            padding: 2rem;
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 3rem;
            font-size: 5rem;
            font-weight: 700;
        }

        .order-card {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .order-card h2 {
            color: var(--primary-color);
            font-size: 3rem;
            margin-bottom: 1rem;
            margin-bottom: 0.5rem;
        }

        .order-date {
            font-size: 2rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            justify-items: center;
        }

        .product-card {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
            max-width: 300px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .product-details {
            padding: 1rem;
        }

        .product-details h3 {
            font-size: 2.2rem;
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        .product-details p {
            margin: 0.25rem 0;
            font-size: 1.9rem;
        }

        .label {
            font-weight: 500;
            color: #666;
        }

        .total {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 2.1rem;
            margin-top: 0.5rem;
        }

        @media (min-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
            }
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
                        echo '<a href="cart.php"><img src="asset/img/shopping-cart-114.png" alt="User Icon" class="user-icon"></a>';
                        echo '<a href="#"><img src="asset/img/user-icon.png" alt="User Icon" class="user-icon"></a>';
                        echo '<span class="username">' . htmlspecialchars($_SESSION['cust_name']) . '</span>';

                        echo '<a href="logout.php" class="btn">Đăng xuất</a>'; // Nút đăng xuất
                        echo '</div>';
                    } else {
                        // Hiển thị nút Đăng ký nếu chưa đăng nhập
                        echo '<a href="signup.php" class="btn btn-sign-up btn-mgl">ĐĂNG KÝ</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <h1>Đơn hàng của tôi</h1>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_names = explode(',', $row['product_names']);
                $quantities = explode(',', $row['quantities']);
                $unit_prices = explode(',', $row['unit_prices']);
                $totals = explode(',', $row['totals']);
                $photos = explode(',', $row['photos']);
        ?>
                <div class="order-card">
                    <h2>Mã Đơn hàng: <?php echo htmlspecialchars($row['payment_id']); ?></h2>
                    <p class="order-date">Ngày mua: <?php echo date('d/m/Y', strtotime($row['buy_date'])); ?></p>
                    <div class="product-grid">
                        <?php
                        for ($i = 0; $i < count($product_names); $i++) {
                        ?>
                            <div class="product-card">
                                <?php if (!empty($photos[$i])): ?>
                                    <img src="<?php echo htmlspecialchars($photos[$i]); ?>" alt="<?php echo htmlspecialchars($product_names[$i]); ?>">
                                <?php endif; ?>
                                <div class="product-details">
                                    <h3><?php echo htmlspecialchars($product_names[$i]); ?></h3>
                                    <p><span class="label">Số lượng:</span> <?php echo htmlspecialchars($quantities[$i]); ?></p>
                                    <p><span class="label">Đơn giá:</span> <?php echo number_format($unit_prices[$i], 0, ',', '.'); ?> ₫</p>
                                    <p class="total"><span class="label">Tổng tiền:</span> <?php echo number_format($totals[$i], 0, ',', '.'); ?> ₫</p>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p class='no-orders'>Bạn chưa có đơn hàng nào.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>

</html>