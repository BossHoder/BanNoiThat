<?php
include "perm.php";
$currentPage = basename($_SERVER['PHP_SELF']); // Lấy tên trang hiện tại

if (!isAllowedPage($currentPage)) {
  header("Location: index.php");  // Chuyển hướng nếu trang không được phép truy cập
  exit();
}

$product = null;
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

if ($product_id) {
  $stmt = $conn->prepare("SELECT p_id, p_name, p_current_price FROM tbl_product WHERE p_id = ?");
  $stmt->bind_param("i", $product_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    if (!isset($_SESSION['cart'][$product_id])) {
      $_SESSION['cart'][$product_id] = 1; // Thêm sản phẩm vào giỏ hàng nếu chưa có
    }
  }
}

// Xử lý khi form được submit
if (isset($_POST['submit_info'])) {
  if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $cust_email = $_SESSION['cust_email'];
    $stmt = $conn->prepare("SELECT cust_id FROM tbl_customer WHERE cust_email = ?");
    $stmt->bind_param("s", $cust_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      // Người dùng có cust_id, tiếp tục xử lý đơn hàng
      $errors = [];
      foreach ($_SESSION['cart'] as $item_id => $quantity) {
        $stmt = $conn->prepare("SELECT p_qty FROM tbl_product WHERE p_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $current_stock = $row['p_qty'];

          if ($current_stock >= $quantity) {
            $new_stock = $current_stock - $quantity;
            $update_stmt = $conn->prepare("UPDATE tbl_product SET p_qty = ? WHERE p_id = ?");
            $update_stmt->bind_param("ii", $new_stock, $item_id);
            if (!$update_stmt->execute()) {
              $errors[] = "Lỗi cập nhật sản phẩm $item_id: " . $conn->error;
            }
          } else {
            $errors[] = "Sản phẩm $item_id không đủ hàng.";
          }
        } else {
          $errors[] = "Sản phẩm $item_id không tồn tại.";
        }
      }

      // Hiển thị lỗi nếu có
      if (!empty($errors)) {
        foreach ($errors as $error) {
          echo "<p style='color:red;'>$error</p>";
        }
        exit;
      }

      unset($_SESSION['cart']); // Xoá giỏ hàng
      echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'index.php';</script>";
      exit;
    }
  }

  // Nếu người dùng chưa đăng nhập hoặc không có cust_id
  $hoTenDem = isset($_POST['first-name']) ? $_POST['first-name'] : '';
  $ten = isset($_POST['last-name']) ? $_POST['last-name'] : '';
  $tenkhach = $hoTenDem . ' ' . $ten;
  $dienthoai = isset($_POST['phone']) ? $_POST['phone'] : '';
  $diachi = isset($_POST['address']) ? $_POST['address'] : '';
  $email = isset($_POST['email']) ? $_POST['email'] : '';

  // Thêm thông tin khách hàng mới
  $stmt = $conn->prepare("INSERT INTO tbl_customer (cust_name, cust_phone, cust_address, cust_email) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $tenkhach, $dienthoai, $diachi, $email);

  if ($stmt->execute()) {
    $last_id = $conn->insert_id; // Lấy ID của khách hàng vừa tạo

    if ($product) {
      $p_id = $product['p_id'];
      $product_name = $product['p_name'];
      $unit_price = $product['p_current_price'];
      $quantity = $_SESSION['cart'][$p_id];
      $total = $unit_price * $quantity;

      // Thêm đơn hàng vào bảng tbl_order
      $order_stmt = $conn->prepare("INSERT INTO tbl_order (product_id, product_name, size, color, quantity, unit_price, payment_id, Total) VALUES (?,?,?,?,?,?,?,?)");
      $order_stmt->bind_param("isssiisd", $p_id, $product_name, $size, $color, $quantity, $unit_price, $payment_id, $total);
      $order_stmt->execute();

      // Cập nhật thông tin khách hàng nếu người dùng đã đăng nhập
      if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        $stmt = $conn->prepare("UPDATE tbl_customer SET cust_id = ? WHERE cust_email = ?");
        $stmt->bind_param("is", $last_id, $cust_email);
        $stmt->execute();
      }

      // Xử lý giỏ hàng
      unset($_SESSION['cart']); // Xóa giỏ hàng sau khi đặt hàng thành công

      echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'index.php';</script>";
      exit;
    }
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
            <input type="text" name="first-name" class="input-field" />
          </div>
          <div class="input-item input-field-mgl-30px">
            <label for="last-name">Tên</label>
            <input type="text" name="last-name" class="input-field" />
          </div>
        </div>
        <div class="input-item">
          <label for="address">Địa chỉ</label>
          <input type="text" name="address" class="input-field wide-field " />
        </div>
        <div class="input-item">
          <label for="phone">Số điện thoại</label>
          <input type="text" name="phone" class="input-field wide-field" />
        </div>
        <div class="input-item">
          <label for="email">Địa chỉ email</label>
          <input type="text" name="email" class="input-field wide-field" />
        </div>
      </div>
      <div class="order-summary">
        <h2 class="heading">Thông tin đơn hàng</h2>

        <?php if ($product): ?>
          <div class="summary-item">
            <span>Product</span>
            <span><?php echo htmlspecialchars($product['p_name']); ?></span>
          </div>
          <!-- Quantity Control for each item -->
          <div class="quantity-control">
            <button class="decrement-btn" onclick="updateQuantity(<?php echo $product_id; ?>, -1)">-</button>
            <input type="text" value="<?php echo $_SESSION['cart'][$product_id]; ?>" id="quantity-<?php echo $product_id; ?>" readonly>
            <button class="increment-btn" onclick="updateQuantity(<?php echo $product_id; ?>, 1)">+</button>
          </div>

          <div class="summary-item">
            <span>Total</span>
            <span id="total-price"><?php
                                    $initialTotalPrice = 0;
                                    if (isset($_SESSION['cart']) && $product != null) {
                                      $quantity =  $_SESSION['cart'][$product_id];
                                      $unit_price = $product['p_current_price'];
                                      $initialTotalPrice = $unit_price * $quantity;
                                    }
                                    echo number_format($initialTotalPrice, 0, '.', '.');
                                    ?> VND</span>
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

          function updateQuantity(productId, change) {
            var quantityInput = document.getElementById("quantity-" + productId);
            var currentQuantity = parseInt(quantityInput.value);
            var newQuantity = currentQuantity + change;

            if (newQuantity < 1) {
              newQuantity = 1; // Giới hạn số lượng tối thiểu là 1
            }

            // Sử dụng fetch API để gửi yêu cầu AJAX
            fetch('checkout_process.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&new_quantity=' + newQuantity,
              })
              .then(response => response.text()) // Parse response as text
              .then(data => {
                console.log(data); // Log the response data for debugging.
                quantityInput.value = newQuantity;
                location.reload(); // Reload trang sau khi cập nhật
              })
              .catch(err => {
                console.error('Error:', err);
              });
          }
        </script>

        </form>
      </div>
    </div>
  </div>
</body>

</html>