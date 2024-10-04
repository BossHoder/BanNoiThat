<?php
// Xử lý kết quả trả về từ MoMo
$orderId = $_GET['orderId'];
$resultCode = $_GET['resultCode']; // 0 là giao dịch thành công
$message = $_GET['message'];

if ($resultCode == 0) {
    echo "Giao dịch thành công. Đơn hàng của bạn đã được xử lý.";
    // Cập nhật đơn hàng trong cơ sở dữ liệu
} else {
    echo "Giao dịch thất bại: " . $message;
}
?>
