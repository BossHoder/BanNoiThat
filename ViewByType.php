<?php
session_start(); // Always start session at the beginning of the file

// Kết nối đến MySQL database
$servername = "localhost";
$db_username = "root"; // Thay bằng thông tin đăng nhập của bạn
$db_password = "azz123123"; // Thay bằng mật khẩu của bạn
$dbname = "qlbh";

// Tạo kết nối
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        echo "<script>
            alert('Bạn cần phải đăng nhập để thêm vào giỏ hàng.');
                window.location.href = 'signin.php';
        </script>";
    } else {
        $product_id = $_POST['product_id'];
        // Kiểm tra xem sản phẩm đã có trong giỏ chưa, nếu có thì tăng số lượng
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += 1;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
    }
}

// Xử lý mua ngay
if (isset($_POST['buy_now'])) {
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;

    if ($product_id) {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            // Nếu chưa đăng nhập, chuyển hướng đến checkout.php kèm theo product_id
            header("Location: checkout.php?product_id=" . $product_id); // This is now correctly executed
            exit(); // Important: Stop further execution
        } else {
            // Nếu đã đăng nhập, thêm sản phẩm vào giỏ hàng
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += 1;
            } else {
                $_SESSION['cart'][$product_id] = 1;
            }
            // Chuyển hướng đến trang cart.php hoặc thực hiện hành động khác
            header("Location: cart.php");
            exit();
        }
    } else {
        echo "Lỗi: Không có product_id!";
    }
}


// Fetch all products from the 'hang' table
$sql = "SELECT * FROM hang";
$result = $conn->query($sql);

?>
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
    $query = "SELECT hang.mahang, hang.maloaihang, hang.tenhang, hang.mota, loaihang.tenloaihang, hang.hinh, hang.dongia, hang.soluongton 
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
            $soluongton = $row['soluongton'];
            

            echo "<tr>";
            echo "<td class='product-title'>$tenhang</td>";
            echo "<td class='price'>$dongia VNĐ</td>";
            echo "<td class='category'>$nhom</td>";
            echo "<td class='description'>$mota</td>";
            echo "<td><img src='$hinh' alt='$tenhang'></td>";
            echo "<td>";
            if ($soluongton > 0) {
                // Form cho nút "Thêm vào giỏ"
            echo '<form method="POST" style="display: inline-block;">';
            echo '<input type="hidden" name="product_id" value="' . $row["mahang"] . '">';
            echo '<button type="submit" name="add_to_cart" class="add-to-cart">Thêm vào giỏ</button>';
            echo '</form>';
            // Form cho nút "Mua ngay"
            echo '<form method="POST" style="display: inline-block;">'; // Remove action attribute
            echo '<input type="hidden" name="product_id" value="' . $row["mahang"] . '">';
            echo '<button type="submit" name="buy_now" class="buy-now">Mua ngay</button>';
            echo '</form>';
            }     
            else {
                // Form cho nút "Thêm vào giỏ"
            echo '<form method="POST" style="display: inline-block;">';
            echo '<input type="hidden" name="product_id" value="' . $row["mahang"] . '">';
            echo '<button type="submit" name="add_to_cart" class="add-to-cart" disabled>Thêm vào giỏ</button>';
            echo '</form>';
            // Form cho nút "Mua ngay"
            echo '<form method="POST" style="display: inline-block;">'; // Remove action attribute
            echo '<input type="hidden" name="product_id" value="' . $row["mahang"] . '">';
            echo '<button type="submit" name="buy_now" class="buy-now" disabled>Mua ngay</button>';
            echo '</form>';
            }                          echo "</td>";
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
