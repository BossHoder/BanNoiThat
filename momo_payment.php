<?php
// Các thông tin nhận từ MoMo Sandbox
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = "MOMO";
$accessKey = "F8BBA842ECF85";
$secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";
$orderInfo = "Thanh toán đơn hàng Nội Thất Theanhdola";
$amount = strval($total_price); // Tổng tiền giỏ hàng
$orderId = time(); // Tạo mã đơn hàng
$returnUrl = "http://localhost/momo_return.php"; // URL trả kết quả khi giao dịch thành công
$notifyUrl = "http://localhost/momo_notify.php"; // URL nhận thông báo từ MoMo
$extraData = "";

// Tạo yêu cầu thanh toán MoMo
$requestId = time();
$requestType = "captureWallet";
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
$jsonResult = json_decode($result, true);  // Giải mã kết quả trả về từ MoMo
curl_close($ch);

// Chuyển hướng người dùng đến trang thanh toán của MoMo
header('Location: ' . $jsonResult['payUrl']);
exit;
?>
