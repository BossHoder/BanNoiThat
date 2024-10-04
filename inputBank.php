<?php
session_start();
include 'conn.php';  // Make sure this includes your database connection

if (isset($_POST['bank_submit'])) {
    $tenNganHang = $_POST['bank_name'];
    $soTaiKhoan = $_POST['account_number'];
    $soThe = isset($_POST['card_number']) ? $_POST['card_number'] : null; // Optional card number

    // Get the user's IP address
    $diaChiIP = $_SERVER['REMOTE_ADDR'];

    // Validate inputs (you can add more validation based on your needs)
    if (empty($tenNganHang) || empty($soTaiKhoan)) {
        echo "<script>alert('Please provide both the bank name and account number.');</script>";
    } else {
        // Prepare and execute the SQL query using a prepared statement
        $stmt = $conn->prepare("INSERT INTO ThongTinTaiKhoanNganHang (TenNganHang, SoTaiKhoan, SoThe, DiaChiIP) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $tenNganHang, $soTaiKhoan, $soThe, $diaChiIP); // "ssss" indicates four string parameters

        if ($stmt->execute()) {
            // Success! Store the bank info in the session (optional)
            $_SESSION['bank_info_saved'] = true;  // Save in session if needed

            // Redirect back to cart or a confirmation page.
            echo "<script>alert('Bank details saved!'); window.location.href = 'cart.php';</script>";
            exit();
        } else {
            // Error handling
            echo "Error: " . $stmt->error;
        }

        $stmt->close();  // Important to close the prepared statement
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bank Transfer Details</title>
    <link rel="stylesheet" href="asset/css/checkout.css">
</head>
<body>
    <div class="checkout-container">
        <h2>Bank Transfer Details</h2>
        <form action="" method="POST">
            <label for="bank_name">Bank Name:</label>
            <input type="text" id="bank_name" name="bank_name" required><br><br>

            <label for="account_number">Account Number:</label>
            <input type="text" id="account_number" name="account_number" required><br><br>

            <label for="card_number">Card Number (Optional):</label>
            <input type="text" id="card_number" name="card_number"><br><br>

            <input type="submit" name="bank_submit" value="Submit Transfer Details">
        </form>
    </div>
</body>
</html>
