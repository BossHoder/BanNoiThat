<?php
session_start();

// Kết nối đến database
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

// Lấy product_id từ URL khi người dùng chưa đăng nhập
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

$product = null;
if ($product_id) {
    // Truy vấn thông tin sản phẩm từ database
    $sql = "SELECT * FROM hang WHERE mahang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id); // Bind the product ID
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Lưu thông tin sản phẩm vào biến $product
        $product = $result->fetch_assoc();
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
          <label for="phone">Phone</label>
          <input type="text" id="phone" class="input-field wide-field" />
        </div>
        <div class="input-item">
          <label for="email">Email Address</label>
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
            <input type="radio" id="bank-transfer" name="payment" />
            <label for="bank-transfer">Chuyển khoản</label>
          </div>
          <div class="payment-option">
            <input type="radio" id="cash-on-delivery" name="payment" />
            <label for="cash-on-delivery">Trả khi nhận hàng</label>
          </div>
        </div>
        <form action="" method="post">
          <button class="order-button">Đặt hàng</button>
          <script>
            alert("Bạn đã đặt hàng thành công")
          </script>
        </form>
      </div>
    </div>
  </div>
</body>

</html>