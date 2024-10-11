<?php
// Các thông tin nhận từ MoMo Sandbox
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = "MOMO";
$accessKey = "F8BBA842ECF85";
$secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";
$orderInfo = "Thanh toán đơn hàng Nội Thất Theanhdola";
$total_price = 600000; // Tổng tiền giỏ hàng, đặt giá trị cố định hoặc lấy từ session
$amount = strval($total_price);
$orderId = time(); // Tạo mã đơn hàng

// Địa chỉ đầy đủ
$returnUrl = "http://localhost:3000/BanNoiThat/payment/momo_return.php"; // URL trả kết quả khi giao dịch thành công
$notifyUrl = "http://localhost:3000/BanNoiThat/payment/momo_notify.php"; // URL nhận thông báo từ MoMo
$extraData = "";

// Tạo yêu cầu thanh toán MoMo
$requestId = time();
$requestType = "payWithATM";
$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $notifyUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $returnUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
$signature = hash_hmac("sha256", $rawHash, $secretKey);

// Tạo dữ liệu gửi đến MoMo
$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "MoMo Sandbox",
    'storeId' => "MoMoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $returnUrl,
    'ipnUrl' => $notifyUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);

// Gửi yêu cầu thanh toán đến MoMo
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$result = curl_exec($ch);
if(curl_errno($ch)) {
    echo 'Lỗi CURL: ' . curl_error($ch);
}
$jsonResult = json_decode($result, true);  // Giải mã kết quả trả về từ MoMo
curl_close($ch);

// Kiểm tra kết quả trả về và chuyển hướng
if (!empty($jsonResult['payUrl'])) {
    header('Location: ' . $jsonResult['payUrl']);
    exit;
} else {
    echo "<p>Lỗi: Không thể lấy URL thanh toán từ MoMo. Vui lòng thử lại.</p>";
    var_dump($jsonResult); // Debug thông tin trả về từ MoMo
}
?>
