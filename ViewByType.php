<?php
session_start();

// Database connection code (unchanged)
$servername = "localhost";
$db_username = "root";
$db_password = "azz123123";
$dbname = "qlbh";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Cart initialization (unchanged)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart and Buy now logic (unchanged)
// ... (keep your existing PHP logic for add_to_cart and buy_now)

// Fetch products (modified to fetch all products if no category is selected)
$sql = isset($_REQUEST['ecat_id']) 
    ? "SELECT p.*, ec.ecat_name FROM tbl_product p 
       JOIN tbl_end_category ec ON p.ecat_id = ec.ecat_id 
       WHERE p.ecat_id = '" . $_REQUEST['ecat_id'] . "'"
    : "SELECT p.*, ec.ecat_name FROM tbl_product p 
       JOIN tbl_end_category ec ON p.ecat_id = ec.ecat_id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm theo loại</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">Sản phẩm của chúng tôi</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $p_name = htmlspecialchars($row['p_name']);
                    $p_current_price = number_format($row['p_current_price'], 0, '.', '.');
                    $nhom = htmlspecialchars($row['ecat_name']);
                    $p_description = htmlspecialchars($row['p_description']);
                    $p_featured_photo = htmlspecialchars($row['p_featured_photo']);
                    $p_qty = $row['p_qty'];
                    ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?php echo $p_featured_photo; ?>" alt="<?php echo $p_name; ?>" class="w-full h-64 object-cover">
                        <div class="p-4">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2"><?php echo $p_name; ?></h2>
                            <p class="text-sm text-gray-600 mb-2"><?php echo $nhom; ?></p>
                            <p class="text-lg font-bold text-blue-600 mb-2"><?php echo $p_current_price; ?> VNĐ</p>
                            <p class="text-gray-700 mb-4"><?php echo $p_description; ?></p>
                            <div class="flex justify-between">
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="product_id" value="<?php echo $row['p_id']; ?>">
                                    <button type="submit" name="add_to_cart" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors <?php echo $p_qty > 0 ? '' : 'opacity-50 cursor-not-allowed'; ?>" <?php echo $p_qty > 0 ? '' : 'disabled'; ?>>
                                        Thêm vào giỏ
                                    </button>
                                </form>
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="product_id" value="<?php echo $row['p_id']; ?>">
                                    <button type="submit" name="buy_now" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors <?php echo $p_qty > 0 ? '' : 'opacity-50 cursor-not-allowed'; ?>" <?php echo $p_qty > 0 ? '' : 'disabled'; ?>>
                                        Mua ngay
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='col-span-full text-center text-gray-700'>Không có sản phẩm nào.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>