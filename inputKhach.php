<?php
// inputKhach.php
session_start(); // Start the session to access user data

include 'conn.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ensure all required fields are filled
    if (!empty($_POST['tenkhach']) && !empty($_POST['dienthoai']) && !empty($_POST['diachi'])) {

        // Lấy dữ liệu từ form
        $tenkhach = $_POST['tenkhach'];
        $dienthoai = $_POST['dienthoai'];
        $diachi = $_POST['diachi'];

        // Lấy username từ session
        $username = $_SESSION['username'];

        // Insert khách hàng information first
        $insertKhachQuery = "INSERT INTO khach (tenkhach, dienthoai, diachi) VALUES ('$tenkhach', '$dienthoai', '$diachi')";
        if ($conn->query($insertKhachQuery) === TRUE) {
            $makhach = $conn->insert_id; // Get the last inserted ID (makhach)

            // Update the account table with makhach
            $updateAccountQuery = "UPDATE account SET makhach = '$makhach' WHERE username = '$username'";
            if ($conn->query($updateAccountQuery) === TRUE) {
                $_SESSION['makhach'] = $makhach; // Set makhach in session
                header("Location: cart.php"); // Redirect back to cart.php
                exit(); // Ensure script stops here
            } else {
                echo "Error updating account: " . $conn->error;
            }
        } else {
            echo "Error inserting customer: " . $conn->error;
        }

    } else {
        echo "Please fill all required fields.";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
    <div class="container">
        <div class="header">
            <h1 style="text-align: center;">Nhập Khách</h1>
            <div class="form">
                <div class="inputTH">
                    <span>Nhập tên khách</span>
                    <input type="text" name="tenkhach" id="">
                </div>
                <div class="inputDVD">
                    <span>Nhập số điện thoại</span>
                    <input type="number" name="dienthoai" id="">
                </div>
                <div class="inputDG">
                    <span>Nhập địa chỉ</span>
                    <input type="text" name="diachi" id="">
                </div>
                <button type="submit">Nhập khách</button>
            </div>
        </div>
    </div>
    </form>


</body>
</html>