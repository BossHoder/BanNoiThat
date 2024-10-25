<?php 
session_start();
include("conn.php");
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