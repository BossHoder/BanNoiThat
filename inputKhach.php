<?php
session_start();
$servername = "localhost";  // Tên máy chủ MySQL (thường là localhost)
$username = "root";         // Tên người dùng MySQL
$password = "azz123123";             // Mật khẩu của người dùng MySQL
$dbname = "QLBH";   // Tên database bạn muốn kết nối

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error . "");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['cust_cname']) && !empty($_POST['cust_phone']) && !empty($_POST['address'])) {
        // Lấy dữ liệu từ form
        $cust_cname = $_POST['cust_cname'];
        $cust_phone = $_POST['cust_phone'];
        $address = $_POST['address'];

        // Lấy email đã đăng ký từ session hoặc từ cơ sở dữ liệu
        $email = $_SESSION['cust_email'];

        // Cập nhật thông tin bổ sung vào bảng khách hàng
        $stmt = $conn->prepare("UPDATE tbl_customer SET cust_cname = ?, cust_phone = ?, cust_address = ? WHERE cust_email = ?");
        $stmt->bind_param("ssss", $cust_cname, $cust_phone, $address, $email); // "ssss" vì tất cả đều là chuỗi
        $stmt->execute();
        // Chuyển hướng sau khi cập nhật thành công
        header("Location: cart.php");
        exit();
    } else {
        echo "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">NHẬP THÔNG TIN</h2>
        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="cust_cname" class="block text-sm font-medium text-gray-700 mb-1">Tên Bí Danh</label>
                <input type="text" name="cust_cname" id="cust_cname" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="cust_phone" class="block text-sm font-medium text-gray-700 mb-1">Số Điện Thoại</label>
                <input type="tel" name="cust_phone" id="cust_phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <textarea name="address" id="address" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-32 resize-none"></textarea>
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                    Xác Nhận
                </button>
            </div>
        </form>
    </div>
</body>

</html>