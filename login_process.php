<?php
session_start(); // Start the session

// Kết nối đến MySQL database
$servername = "localhost";
$db_username = "root"; // Thay bằng thông tin đăng nhập của bạn
$db_password = "azz123123"; // Thay bằng mật khẩu của bạn
$dbname = "qlbh";

// Tạo kết nối
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý form đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra nếu username hoặc password rỗng
    if (empty($username) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.location.href='signin.php';</script>";
        exit();
    } else {
        // Truy vấn kiểm tra người dùng trong bảng account
        $sql = "SELECT * FROM account WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra nếu người dùng tồn tại
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($password, $row['password'])) {
                // Lưu thông tin người dùng vào session
                $_SESSION['username'] = $username;
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
