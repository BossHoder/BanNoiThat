<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "azz123123";
$dbname = "qlbh";

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
echo "<pre>";
print_r($_SESSION['cart']);
echo "</pre>";


// Nhận kết quả trả về từ MoMo
$orderId = $_GET['orderId'];
$resultCode = $_GET['resultCode'];
$message = $_GET['message'];

if ($resultCode == 0) {
    // Giao dịch thành công, tiến hành lưu vào cơ sở dữ liệu
    $makhach = isset($_SESSION['cust_id']) ? $_SESSION['cust_id'] : null;
    $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $tongtien = 0;

    // Kiểm tra giỏ hàng
    if (!empty($cart_items) && $makhach !== null) {
        // Bắt đầu giao dịch SQL
        $conn->begin_transaction();
        try {
            // Lấy ngày hiện tại
            $ngaythang = date("Y-m-d");
            $tongtien = 0;
            foreach ($_SESSION['cart'] as $p_id => $quantity) {
                // Lấy thông tin sản phẩm
                $product_stmt = $conn->prepare("SELECT p_current_price, p_qty, p_name FROM tbl_product WHERE p_id = ?");
                $product_stmt->bind_param("i", $p_id);
                $product_stmt->execute();
                $product_result = $product_stmt->get_result();
                $product = $product_result->fetch_assoc();
            
                if (!$product) {
                    throw new Exception("Không tìm thấy sản phẩm.");
                }
            
                if ($product['p_qty'] < $quantity) {
                    throw new Exception("Hết hàng " . $product['p_name']);
                }
            
                $unit_price = $product['p_current_price'];
                $product_name = $product['p_name'];
                $total_item_price = $unit_price * $quantity; // Tính tổng tiền của từng sản phẩm
                $tongtien += $total_item_price; // Cộng tổng giá trị toàn đơn hàng
            
                // Thêm vào tbl_order
                $order_stmt = $conn->prepare("INSERT INTO tbl_order (product_id, product_name, size, color, quantity, unit_price, payment_id, total) VALUES (?,?,?,?,?,?,?,?)");
                $order_stmt->bind_param("isssiisd", $p_id, $product_name, $size, $color, $quantity, $unit_price, $payment_id, $total_item_price);
                $order_stmt->execute();
            
                // Update Product Quantity
                $new_qty = $product['p_qty'] - $quantity;
                $update_qty_stmt = $conn->prepare("UPDATE tbl_product SET p_qty = ? WHERE p_id = ?");
                $update_qty_stmt->bind_param("ii", $new_qty, $p_id);
                $update_qty_stmt->execute();
            }
            
                        
            // Commit giao dịch
            $conn->commit();

            // Xóa giỏ hàng
            unset($_SESSION['cart']);

            // Hiển thị thông báo thành công
            echo "<script>alert('Giao dịch thành công. Đơn hàng của bạn đã được xử lý.'); window.location.href = '../index.php';</script>";

        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $conn->rollback();
            echo "<p style='color:red;'>Lỗi: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Lỗi: Giỏ hàng trống hoặc khách hàng chưa đăng nhập.</p>";
    }
} else {
    // Giao dịch thất bại
    echo "Giao dịch thất bại: " . $message;
}
?>
