<?php
// Kết nối đến MySQL database
$servername = "localhost";
$username = "root"; // Thay đổi theo thông tin của bạn
$password = "azz123123"; // Thay đổi theo thông tin của bạn
$dbname = "qlbh";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $valid = 1;
    $error_message = '';

    // Kiểm tra tên
    if(empty($_POST['cust_name'])) {
        $valid = 0;
        $error_message .= "Vui lòng nhập tên của bạn.<br>";
    }

    // Kiểm tra email
    if(empty($_POST['cust_email'])) {
        $valid = 0;
        $error_message .= "Vui lòng nhập email của bạn.<br>";
    } else {
        // Kiểm tra định dạng email
        if (!filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL)) {
            $valid = 0;
            $error_message .= "Định dạng email không hợp lệ.<br>";
        } else {
            // Kiểm tra email đã tồn tại chưa
            $stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE cust_email = ?");
            $stmt->bind_param("s", $_POST['cust_email']);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                $valid = 0;
                $error_message .= "Email này đã tồn tại.<br>";
            }
            $stmt->close();
        }
    }

    // Kiểm tra password
    if(empty($_POST['cust_password']) || empty($_POST['cust_re_password'])) {
        $valid = 0;
        $error_message .= "Vui lòng nhập mật khẩu.<br>";
    } elseif ($_POST['cust_password'] != $_POST['cust_re_password']) {
        $valid = 0;
        $error_message .= "Mật khẩu không khớp.<br>";
    }

    // Nếu không có lỗi, thực hiện đăng ký
    if($valid == 1) {
        $token = md5(time());
        $cust_datetime = date('Y-m-d h:i:s');
        $cust_timestamp = time();
        $cust_password = password_hash($_POST['cust_password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu

        // Thực thi câu lệnh SQL để thêm khách hàng
        $stmt = $conn->prepare("INSERT INTO tbl_customer (cust_name, cust_email, cust_password, cust_token, cust_datetime, cust_timestamp, cust_status, cust_country) VALUES (?, ?, ?, ?, ?, ?, ?, 237)");
        $status = 1;
        $stmt->bind_param("ssssssi", $_POST['cust_name'], $_POST['cust_email'], $cust_password, $token, $cust_datetime, $cust_timestamp, $status);
        
        $stmt->execute(); // Gọi hàm thực thi
        $stmt->close();

        echo "<script>
        alert('Đăng ký thành công!');
        window.location.href = 'signin.php';
      </script>";
    } else {
        echo $error_message; // Thông báo lỗi
    }
}

$conn->close();
?>
