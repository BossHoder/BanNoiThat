<?php
include "perm.php";
$currentPage = basename($_SERVER['PHP_SELF']); // Gets the current page filename

if (!isAllowedPage($currentPage)) {
    header("Location: index.php");  // Redirect to a default page (e.g., index.php)
    exit();
}

// Thực hiện truy vấn hoặc các thao tác khác
$sql = "SELECT * FROM khach";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Xuất dữ liệu
    while($row = $result->fetch_assoc()) {
        echo "\nID: " . $row["makhach"] . " - Tên: " . $row["tenkhach"] . " - Điện thoại: " .$row["dienthoai"] . " - Diachi: ".$row["diachi"] ."<br>"  ;
    }
} else {
    echo "Không có kết quả";
}

// Đóng kết nối
$conn->close();
?>
