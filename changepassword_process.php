<?php 
session_start();

if (isset($_SESSION['cust_email'])) {

    include "admin/inc/config.php";  // Sử dụng PDO từ config.php

    if (isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['confirm_new_password'])) {

        function validate($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $old_password = validate($_POST['old_password']);
        $new_password = validate($_POST['new_password']);
        $confirm_new_password = validate($_POST['confirm_new_password']);

        if ($new_password !== $confirm_new_password) {
            header("Location: changepassword.php?error=The confirmation password does not match");
            exit();
        } else {
            $id = $_SESSION['cust_email'];

            // Truy vấn mật khẩu đã mã hóa từ cơ sở dữ liệu
            $stmt = $pdo->prepare("SELECT cust_password FROM tbl_customer WHERE cust_email = :id");
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch();

            if ($user) {
                $hashed_password = $user['cust_password'];

                // Kiểm tra mật khẩu cũ có khớp với mật khẩu trong cơ sở dữ liệu không
                if (password_verify($old_password, $hashed_password)) {
                    // Nếu khớp, mã hóa mật khẩu mới và cập nhật
                    $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("UPDATE tbl_customer SET cust_password = :new_password WHERE cust_email = :id");
                    $stmt->execute(['new_password' => $new_password_hashed, 'id' => $id]);

                    header("Location: changepassword.php?success=Your password has been changed successfully");
                    exit();
                } else {
                    header("Location: changepassword.php?error=Incorrect password");
                    exit();
                }
            } else {
                header("Location: changepassword.php?error=Something went wrong");
                exit();
            }
        }
    } else {
        header("Location: changepassword.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
