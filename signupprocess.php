<?php
// Kết nối đến MySQL database
$servername = "localhost";
$username = "root"; // Username của MySQL (thay bằng của bạn)
$password = "azz123123"; // Mật khẩu của MySQL (thay bằng của bạn)
$dbname = "qlbh";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra nếu username hoặc password rỗng
    if (empty($username) || empty($password)) {
        echo "Vui lòng nhập đủ thông tin.";
    } else {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Chèn dữ liệu vào bảng account
        $sql = "INSERT INTO account (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            

            // Thêm script JavaScript để chuyển hướng sau 3 giây
            echo '<script>
            alert("Bạn đã đky thành công")
                        window.location.href = "signin.php";
                  </script>';
        } else {
            echo "Lỗi: " . $stmt->error;
        }

        // Đóng statement
        $stmt->close();
    }
}

// Đóng kết nối
$conn->close();
?>
