<?php
// Nhận và xử lý dữ liệu POST từ MoMo
$data = json_decode(file_get_contents('php://input'), true);

$orderId = $data['orderId'];
$resultCode = $data['resultCode']; // 0 là thành công
$transId = $data['transId'];

// Cập nhật trạng thái giao dịch trong cơ sở dữ liệu
if ($resultCode == 0) {
    echo "Giao dịch thành công. Mã giao dịch MoMo: " . $transId;
    // Cập nhật đơn hàng thành công trong hệ thống
} else {
    echo "Giao dịch thất bại.";
}

// Trả về phản hồi cho MoMo
http_response_code(200);
?>
