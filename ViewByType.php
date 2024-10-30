<?php
session_start();

// Database connection code (unchanged)
include("conn.php");


// Cart initialization (unchanged)
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
<?php include_once ("header.php");?>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8 mt-[12%] transform scale-150">
        <h1 class="text-6xl font-bold mb-8 text-center">Sản phẩm của chúng tôi</h1>
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
                        <img src="<?php echo $p_featured_photo; ?>" alt="<?php echo $p_name; ?>" class="w-full h-96 object-cover">
                        <div class="p-4">
                            <h2 class="text-4xl font-semibold text-gray-800 mb-2"><?php echo $p_name; ?></h2>
                            <p class="text-2xl text-gray-600 mb-2"><?php echo $nhom; ?></p>
                            <p class="text-3xl font-bold text-blue-600 mb-2"><?php echo $p_current_price; ?> VNĐ</p>
                            <p class="text-3xl text-gray-700 mb-4"><?php echo $p_description; ?></p>
                            <div class="flex justify-between">
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="product_id" value="<?php echo $row['p_id']; ?>">
                                    <button type="submit" name="add_to_cart" class="px-6 py-4 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors text-3xl <?php echo $p_qty > 0 ? '' : 'opacity-50 cursor-not-allowed'; ?>" <?php echo $p_qty > 0 ? '' : 'disabled'; ?>>
                                        Thêm vào giỏ
                                    </button>
                                </form>
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="product_id" value="<?php echo $row['p_id']; ?>">
                                    <button type="submit" name="buy_now" class="px-12 py-4 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors text-3xl <?php echo $p_qty > 0 ? '' : 'opacity-50 cursor-not-allowed'; ?>" <?php echo $p_qty > 0 ? '' : 'disabled'; ?>>
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
</body>
</html>