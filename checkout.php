<?php
include "perm.php";
$currentPage = basename($_SERVER['PHP_SELF']); // Gets the current page filename

if (!isAllowedPage($currentPage)) {
    header("Location: index.php");  // Redirect to a default page (e.g., index.php)
    exit();
}

// Khởi tạo biến $product để tránh lỗi Undefined variable
$product = null;

// Lấy product_id từ URL khi người dùng chưa đăng nhập
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

if ($product_id) {
    // Truy vấn thông tin sản phẩm từ database
    $sql = "SELECT * FROM tbl_product WHERE p_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id); // Bind the product ID
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Lưu thông tin sản phẩm vào biến $product
        $product = $result->fetch_assoc();
    }
}

// Xử lý khi form được submit
if (isset($_POST['submit_info'])) {
    // Kiểm tra người dùng đã đăng nhập và có cust_id hay chưa
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        $cust_email = $_SESSION['cust_email'];
        $sql = "SELECT cust_id FROM tbl_customer WHERE cust_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cust_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Người dùng có cust_id, tiến hành xử lý đơn hàng
            $errors = [];
            foreach ($_SESSION['cart'] as $item_id => $quantity) {
                $query = "SELECT p_qty FROM tbl_product WHERE p_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $item_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $current_stock = $row['p_qty'];
                    if ($current_stock >= $quantity) {
                        $new_stock = $current_stock - $quantity;
                        $update_query = "UPDATE tbl_product SET p_qty = ? WHERE p_id = ?";
                        $stmt = $conn->prepare($update_query);
                        $stmt->bind_param("ii", $new_stock, $item_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Lỗi cập nhật sản phẩm $item_id: " . $conn->error;
                        }
                    } else {
                        $errors[] = "Sản phẩm $item_id không đủ hàng.";
                    }
                } else {
                    $errors[] = "Sản phẩm $item_id không tồn tại.";
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<p style='color:red;'>$error</p>";
                }
                exit;
            }

            unset($_SESSION['cart']);
            echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'index.php';</script>";
            exit;
        }
    }

    // Xử lý nếu người dùng không có cust_id hoặc chưa đăng nhập
    $hoTenDem = $_POST['first-name'];
    $ten = $_POST['last-name'];
    $tenkhach = $hoTenDem . ' ' . $ten;
    $dienthoai = $_POST['phone'];
    $diachi = $_POST['address'];
    $email = $_POST['email'];

    $sql = "INSERT INTO khach (tenkhach, dienthoai, diachi, email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $tenkhach, $dienthoai, $diachi, $email);

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;

        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            $cust_email = $_SESSION['cust_email'];
            $updatetbl_customer = "UPDATE tbl_customer SET cust_id = ? WHERE cust_email = ?";
            $stmt = $conn->prepare($updatetbl_customer);
            $stmt->bind_param("is", $last_id, $cust_email);
            $stmt->execute();
        }

        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            $errors = [];
            foreach ($_SESSION['cart'] as $item_id => $quantity) {
                $query = "SELECT p_qty FROM tbl_product WHERE p_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $item_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $current_stock = $row['p_qty'];
                    if ($current_stock >= $quantity) {
                        $new_stock = $current_stock - $quantity;
                        $update_query = "UPDATE tbl_product SET p_qty = ? WHERE p_id = ?";
                        $stmt = $conn->prepare($update_query);
                        $stmt->bind_param("ii", $new_stock, $item_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Lỗi cập nhật sản phẩm $item_id: " . $conn->error;
                        }
                    } else {
                        $errors[] = "Sản phẩm $item_id không đủ hàng.";
                    }
                } else {
                    $errors[] = "Sản phẩm $item_id không tồn tại.";
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<p style='color:red;'>$error</p>";
                }
                exit;
            }

            unset($_SESSION['cart']);
        }

        echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'index.php';</script>";
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="asset/css/checkout.css" />
</head>

<body>
  <div class="checkout-container">
    <div class="billing-details">
      <div class="billing-info">
        <h2 class="heading">Chi tiết đơn hàng</h2>
        <div class="input-group">
          <div class="input-item">
            <label for="first-name">Họ và tên đệm</label>
            <input type="text" id="first-name" class="input-field" />
          </div>
          <div class="input-item input-field-mgl-30px">
            <label for="last-name">Tên</label>
            <input type="text" id="last-name" class="input-field" />
          </div>
        </div>
        <div class="input-item">
          <label for="address">Địa chỉ</label>
          <input type="text" id="address" class="input-field wide-field " />
        </div>
        <div class="input-item">
          <label for="phone">Số điện thoại</label>
          <input type="text" id="phone" class="input-field wide-field" />
        </div>
        <div class="input-item">
          <label for="email">Địa chỉ email</label>
          <input type="text" id="email" class="input-field wide-field" />
        </div>
      </div>
      <div class="order-summary">
        <h2 class="heading">Thông tin đơn hàng</h2>

        <?php if ($product): ?>
          <div class="summary-item">
            <span>Product</span>
            <span><?php echo htmlspecialchars($product['tenhang']); ?></span>
          </div>
          <div class="summary-item">
            <span>Total</span>
            <span><?php echo number_format($product['dongia'], 0, '.', '.'); ?> VND</span>
          </div>
        <?php else: ?>
          <div class="summary-item">
            <span>Product</span>
            <span>Không tìm thấy sản phẩm.</span>
          </div>
        <?php endif; ?>

        <p class="privacy-policy">
          Dữ liệu cá nhân của bạn sẽ được sử dụng để hỗ trợ trải nghiệm của bạn trên toàn bộ trang web này, để quản lý quyền truy cập vào tài khoản của bạn và cho các mục đích khác được mô tả trong <a href="#" class="privacy-link">chính sách bảo mật</a> của chúng tôi.
        </p>
        <div class="payment-methods">
          <div class="payment-option">
            <input type="radio" id="bank-transfer" name="Banking" />
            <label for="bank-transfer">Chuyển khoản</label>
          </div>
          <div class="payment-option">
            <input type="radio" id="cash-on-delivery" name="COD" />
            <label for="cash-on-delivery">Trả khi nhận hàng</label>
          </div>
        </div>
        <form action="" method="post" onsubmit="return confirmOrder()">
          <button class="order-button" name="submit_info">Đặt hàng</button>
        </form>

        <script>
          function confirmOrder() {
            if (confirm("Xác nhận đặt hàng?")) {
        alert("Bạn đã đặt hàng thành công");
        return true; // Cho phép form được submit
    } else {
        return false; // Hủy đặt hàng, ngăn submit form
    }
          }
        </script>

        </form>
      </div>
    </div>
  </div>
</body>

</html>