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
        <div class="summary-item">
          <span>Product</span>
          <span>Asgaard sofa</span>
        </div>
        <div class="summary-item">
          <span>Total</span>
          <span>Rs. 250,000.00</span>
        </div>
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
        <button class="order-button">Place Order</button>
      </div>
    </div>
  </div>
</body>
</html>