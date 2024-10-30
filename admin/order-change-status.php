<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id']) || !isset($_REQUEST['task'])) {
    header('location: logout.php');
    exit;
} else {
    // Check if the ID is valid
    $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    if ($total == 0) {
        header('location: logout.php');
        exit;
    }
}

// Update the payment status and set shipping status to 'Pending' if the task is 'Completed'
if ($_REQUEST['task'] == 'Completed') {
    $statement = $pdo->prepare("UPDATE tbl_payment SET payment_status=?, shipping_status='Pending' WHERE id=?");
} else {
    $statement = $pdo->prepare("UPDATE tbl_payment SET payment_status=? WHERE id=?");
}
$statement->execute(array($_REQUEST['task'], $_REQUEST['id']));

header('location: order.php');
?>
