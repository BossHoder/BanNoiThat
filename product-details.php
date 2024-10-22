<?php
session_start();
include("conn.php");

// Get product_id from URL
if (isset($_GET['p_id'])) {
    $product_id = $_GET['p_id'];
} else {
    die("Product not found!");
}

// Query product based on p_id
$stmt = $conn->prepare("SELECT * FROM tbl_product WHERE p_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if product exists
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product does not exist!");
}

$stmt->close();

// Function to format price
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' ₫';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['p_name']); ?> - Product Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        body .zoom250{
            margin-top: 50px;
            zoom: 250%;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">    <!-- header -->
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

    <div class="bg-white rounded-lg shadow-xl overflow-hidden max-w-6xl w-full zoom250">
        <div class="md:flex">
            <div class="md:flex-shrink-0">
                <?php
                $imagePath = !empty($product['p_featured_photo']) ? $product['p_featured_photo'] : 'asset/img/placeholder.png';
                ?>
                <img class="h-96 w-full object-cover md:w-96" src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($product['p_name']); ?>">
            </div>
            <div class="p-8">
                <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">
                    Product ID: <?php echo $product['p_id']; ?>
                </div>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900">
                    <?php echo htmlspecialchars($product['p_name']); ?>
                </h1>
                <p class="mt-4 text-gray-500">
                    <?php echo htmlspecialchars($product['p_description']); ?>
                </p>
                <div class="mt-6">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-gray-900"><?php echo formatPrice($product['p_current_price']); ?></span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Số lượng còn lại: <?php echo $product['p_qty']; ?>
                    </p>
                </div>
                <div class="mt-8 space-y-4">
                    <?php if ($product['p_qty'] > 0): ?>
                        <form method="POST" action="cart.php" class="mb-2">
                            <input type="hidden" name="product_id" value="<?php echo $product['p_id']; ?>">
                            <button type="submit" name="add_to_cart" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
                                <i class="fas fa-shopping-cart mr-2"></i> Thêm vào giỏ
                            </button>
                        </form>
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['p_id']; ?>">
                            <button type="submit" name="buy_now" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
                                <i class="fas fa-credit-card mr-2"></i> Mua ngay
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-red-500 font-semibold">Sản phẩm này đã hết hàng.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>