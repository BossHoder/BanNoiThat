<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hàng theo từng loại</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-size: 1.5rem;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f4b400;
            color: white;
        }

        img {
            width: 300px;
            height: 300px;
            object-fit: cover;
        }

        .product-title {
            color: #d9534f;
            font-weight: bold;
        }

        .price {
            color: #5bc0de;
        }

        .category {
            color: #5cb85c;
        }

        .description {
            text-align: left;
            font-size: 1.4rem;
            color: #666;
            line-height: 1.5;
        }

        .menu-loaihang {
            position: fixed;
            top: 100px;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: yellowgreen;
            padding: 20px;
            border-right: 4px solid red;
        }

        .menu-loaihang a {
            font-size: 2rem;
            color: #d60707;
            text-decoration: none;
            display: block;
            margin-bottom: 15px;
        }

        .menu-loaihang a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php
include 'conn.php'; // Đảm bảo file kết nối cơ sở dữ liệu được đúng

// Kiểm tra biến 'maloaihang' có được truyền vào hay không
if (isset($_REQUEST['maloaihang'])) {
    $malh = $_REQUEST['maloaihang'];

    // Kiểm tra kết nối cơ sở dữ liệu
    if (!$conn) {
        die("Kết nối cơ sở dữ liệu không thành công: " . mysqli_connect_error());
    }

    // Thực hiện truy vấn
    $query = "SELECT hang.maloaihang, hang.tenhang, hang.mota, loaihang.tenloaihang, hang.hinh, hang.dongia, hang.soluongton 
              FROM hang 
              JOIN loaihang ON hang.maloaihang = loaihang.maloaihang 
              WHERE hang.maloaihang = '$malh'";
    $result = mysqli_query($conn, $query);

    // Kiểm tra kết quả truy vấn
    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr><th>Tên hàng</th><th>Đơn giá</th><th>Loại hàng</th><th>Mô tả</th><th>Hình ảnh</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            $tenhang = htmlspecialchars($row['tenhang']);
            $dongia = number_format($row['dongia'], 0, '.', '.');
            $nhom = htmlspecialchars($row['tenloaihang']);
            $mota = htmlspecialchars($row['mota']);
            $hinh = htmlspecialchars($row['hinh']);

            echo "<tr>";
            echo "<td class='product-title'>$tenhang</td>";
            echo "<td class='price'>$dongia VNĐ</td>";
            echo "<td class='category'>$nhom</td>";
            echo "<td class='description'>$mota</td>";
            echo "<td><img src='$hinh' alt='$tenhang'></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>Không có hàng nào thuộc loại này.</p>";
    }
} else {
    echo "<p>Không có mã loại hàng được truyền.</p>";
}
?>

</body>
</html>
