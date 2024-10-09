<?php
session_start(); // Bắt đầu session

// Kết nối đến MySQL database
$servername = "localhost";
$db_user = "root"; // Thay bằng thông tin đăng nhập của bạn
$db_password = "azz123123"; // Thay bằng mật khẩu của bạn
$dbname = "qlbh";

// Tạo kết nối
$conn = new mysqli($servername, $db_user, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý form đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $cust_email = $_POST['cust_email'];
    $cust_password = $_POST['cust_password'];

    // Kiểm tra nếu cust_email hoặc cust_password rỗng
    if (empty($cust_email) || empty($cust_password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.location.href='signin.php';</script>";
        exit();
    } else {
        // Truy vấn kiểm tra người dùng trong bảng tbl_customer
        $sql = "SELECT * FROM tbl_customer WHERE cust_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cust_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra nếu người dùng tồn tại
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($cust_password, $row['cust_password'])) {
                // Lưu thông tin người dùng vào session
                $_SESSION['cust_email'] = $cust_email;
                $_SESSION['cust_name'] = $row['cust_name']; // Lưu tên người dùng vào session
                $_SESSION['loggedin'] = true; // Đánh dấu người dùng đã đăng nhập

                // Điều hướng đến trang chủ
                header("Location: index.php");
                exit();
            } else {
                echo "<script>alert('Sai mật khẩu!'); window.location.href='signin.php';</script>";
            }
        } else {
            echo "<script>alert('Tài khoản không tồn tại!'); window.location.href='signin.php';</script>";
        }

        // Đóng statement
        $stmt->close();
    }
}

// Đóng kết nối
$conn->close();
?>
