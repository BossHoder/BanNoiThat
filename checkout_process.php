<?php
session_start();
include 'admin/inc/config.php'; // Add your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $new_quantity = isset($_POST['new_quantity']) ? (int)$_POST['new_quantity'] : 1;

    // Check if the product ID is valid and exists in the cart
    if ($product_id && isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = $new_quantity;
        echo "Quantity updated successfully";
    } else {
        echo "Failed to update quantity";
    }
}