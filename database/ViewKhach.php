<?php
// Kết nối đến file chứa kết nối MySQL
include 'conn.php';

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
