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
function isAdmin(){
    return isset($_SESSION['perm']) && $_SESSION['perm'] == 1;
}
function isAllowedPage($pageName) {
    // Array of allowed pages for regular users
    $allowedPages = ['index.php', 'cart.php', 'signin.php', 'signup.php', 'checkout.php', 'logout.php', 'menu.php', 'signupprocess.php', 'signup.php', 'ViewByType.php', 'login_process.php', 'payment/momo_payment.php', 'payment/momo_notify.php', 'payment/momo_return.php' ]; // Add your allowed pages

    // Admins have access to all pages
    if (isAdmin()) {
        return true;
    }

    // Check if the current page is in the allowed list
    return in_array($pageName, $allowedPages);
}

?>