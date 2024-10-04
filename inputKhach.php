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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            font-size: 3rem;
            color: #2C3E50;
            margin-bottom: 20px;
        }

        .form {
            display: flex;
            flex-direction: column;
        }

        .inputTH,
        .inputDVD,
        .inputDG {
            margin-bottom: 15px;
        }

        .inputTH span,
        .inputDVD span,
        .inputDG span {
            display: block;
            font-size: 14px;
            color: #34495e;
            margin-bottom: 5px;
            font-size: 2.5rem;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease-in-out;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #2C3E50;
        }

        button {
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            background-color: #2C3E50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
            margin-top: 10px;
        }

        button:hover {
            background-color: #1A242F;
        }

        @media screen and (max-width: 768px) {
            .container {
                padding: 15px;
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <form action="" method="post">
        <div class="container">
            <div class="header">
                <h1 style="text-align: center;">Nhập Thông Tin </h1>
                <div class="form">
                    <div class="inputTH">
                        <span>Nhập tên của bạn</span>
                        <input type="text" name="tenkhach" id="" required>
                    </div>
                    <div class="inputDVD">
                        <span>Nhập số điện thoại</span>
                        <input type="number" name="dienthoai" id="" required>
                    </div>
                    <div class="inputDG">
                        <span>Nhập địa chỉ</span>
                        <input type="text" name="diachi" id="" required>
                    </div>
                    <button type="submit">Xác nhận</button>
                </div>
            </div>
        </div>
    </form>


</body>

</html>