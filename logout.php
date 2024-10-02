<?php
session_start();
session_destroy();
header("Location: signin.php"); // Điều hướng đến trang đăng nhập
exit();
?>
