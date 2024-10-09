<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "azz123123";
$dbname = "qlbh";

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận kết quả trả về từ MoMo
$orderId = $_GET['orderId'];
$resultCode = $_GET['resultCode'];
$message = $_GET['message'];

if ($resultCode == 0) {
    // Giao dịch thành công, tiến hành lưu vào cơ sở dữ liệu
    $makhach = isset($_SESSION['makhach']) ? $_SESSION['makhach'] : null;
    $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $tongtien = 0;

    // Kiểm tra giỏ hàng
    if (!empty($cart_items)) {
        // Bắt đầu giao dịch SQL
        $conn->begin_transaction();
        try {
            // Lấy ngày hiện tại
            $ngaythang = date("Y-m-d");

            // Thêm vào bảng donhang
            $sql_donhang = "INSERT INTO donhang (makhach, ngaythang, tongtien) VALUES (?, ?, ?)";
            $stmt_donhang = $conn->prepare($sql_donhang);
            $stmt_donhang->bind_param("isd", $makhach, $ngaythang, $tongtien);
            if (!$stmt_donhang->execute()) {
                throw new Exception("Lỗi khi thêm đơn hàng: " . $stmt_donhang->error);
            }
            $madh = $conn->insert_id; // Lấy mã đơn hàng vừa tạo

            // Lặp qua các sản phẩm trong giỏ hàng và thêm vào chitietdonhang
            foreach ($cart_items as $mahang => $soluong) {
                // Lấy thông tin sản phẩm (đơn giá)
                $sql_product = "SELECT dongia FROM hang WHERE mahang = ?";
                $stmt_product = $conn->prepare($sql_product);
                $stmt_product->bind_param("i", $mahang);
                $stmt_product->execute();
                $result_product = $stmt_product->get_result();

                if ($result_product->num_rows > 0) {
                    $product = $result_product->fetch_assoc();
                    $dongia = $product['dongia'];
                    $thanhtien = $dongia * $soluong;
                    $tongtien += $thanhtien;

                    // Thêm vào bảng chitietdonhang
                    $sql_chitietdonhang = "INSERT INTO chitietdonhang (madh, mahang, soluong, thanhtien) VALUES (?, ?, ?, ?)";
                    $stmt_chitietdonhang = $conn->prepare($sql_chitietdonhang);
                    $stmt_chitietdonhang->bind_param("iiid", $madh, $mahang, $soluong, $thanhtien);
                    if (!$stmt_chitietdonhang->execute()) {
                        throw new Exception("Lỗi khi thêm chi tiết đơn hàng: " . $stmt_chitietdonhang->error);
                    }
                }
            }

            // Cập nhật tổng tiền cho đơn hàng trong bảng donhang
            $sql_update_donhang = "UPDATE donhang SET tongtien = ? WHERE madh = ?";
            $stmt_update_donhang = $conn->prepare($sql_update_donhang);
            $stmt_update_donhang->bind_param("di", $tongtien, $madh);
            if (!$stmt_update_donhang->execute()) {
                throw new Exception("Lỗi khi cập nhật tổng tiền: " . $stmt_update_donhang->error);
            }

            // Thêm vào bảng hoadon
            $ngayxuat = date("Y-m-d");
            $sql_hoadon = "INSERT INTO hoadon (makhach, ngayxuat, tongtien) VALUES (?, ?, ?)";
            $stmt_hoadon = $conn->prepare($sql_hoadon);
            $stmt_hoadon->bind_param("isd", $makhach, $ngayxuat, $tongtien);
            if (!$stmt_hoadon->execute()) {
                throw new Exception("Lỗi khi thêm hóa đơn: " . $stmt_hoadon->error);
            }
            $mahd = $conn->insert_id; // Lấy mã hóa đơn vừa tạo

            // Thêm chi tiết hóa đơn vào bảng chitiethd
            foreach ($cart_items as $mahang => $soluong) {
                $thanhtien = $dongia * $soluong;

                $sql_chitiethd = "INSERT INTO chitiethd (mahd, mahang, soluong, thanhtien) VALUES (?, ?, ?, ?)";
                $stmt_chitiethd = $conn->prepare($sql_chitiethd);
                $stmt_chitiethd->bind_param("iiid", $mahd, $mahang, $soluong, $thanhtien);
                if (!$stmt_chitiethd->execute()) {
                    throw new Exception("Lỗi khi thêm chi tiết hóa đơn: " . $stmt_chitiethd->error);
                }
            }

            // Commit giao dịch
            $conn->commit();

            // Xóa giỏ hàng
            unset($_SESSION['cart']);

            // Hiển thị thông báo thành công
            echo "<script>alert('Giao dịch thành công. Đơn hàng của bạn đã được xử lý.'); window.location.href = '../index.php';</script>";

        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $conn->rollback();
            echo "<p style='color:red;'>Lỗi: " . $e->getMessage() . "</p>";
        }
    }
} else {
    // Giao dịch thất bại
    echo "Giao dịch thất bại: " . $message;
}
?>
