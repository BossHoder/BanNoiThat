<?php
session_start();
include 'admin/inc/config.php'; // Add your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $new_quantity = intval($_POST['new_quantity']);

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = $new_quantity; // Update quantity in session

        // Update quantity in database (if necessary)
        $stmt = $conn->prepare("UPDATE tbl_order SET quantity = ? WHERE product_id = ?");
        $stmt->bind_param("ii", $new_quantity, $product_id);
        if ($stmt->execute()) {
            echo "Quantity updated successfully!";
        } else {
            echo "Error updating quantity: " . $conn->error;
        }
    }
}
?>
