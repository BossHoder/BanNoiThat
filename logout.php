<?php
session_start();
unset($_SESSION['loggedin']);
unset($_SESSION['username']);
header("Location: signin.php"); // Điều hướng đến trang đăng nhập
exit();
?>
