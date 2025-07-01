<?php include 'app/views/layouts/header.php'; ?>

<div class="container section-padding">
    <h1 class="section-title">Thông Tin Thanh Toán</h1>

    <?php
    // Xác định action cho form dựa trên luồng mua hàng
    $form_action = isset($isBuyNow) && $isBuyNow ? "/mystore/create-buy-now-order" : "/mystore/checkout";
    ?>

    <form action="<?php echo $form_action; ?>" method="POST">
        <?php
        // Nếu là luồng mua ngay, thêm các trường ẩn để gửi thông tin sản phẩm
        if (isset($isBuyNow) && $isBuyNow) {
            echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($buyNowProduct['id']) . '">';
            echo '<input type="hidden" name="quantity" value="' . htmlspecialchars($buyNowProduct['qty']) . '">';
        }
        ?>
    
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold">Thông tin giao hàng</h4>
                        <p class="text-muted">Vui lòng điền đầy đủ thông tin hoặc sử dụng thông tin mặc định từ tài khoản của bạn.</p>
                        
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Họ và tên người nhận</label>
                            <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($currentUser['customer_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control form-control-lg" id="shipping_phone" name="shipping_phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Địa chỉ chi tiết</label>
                            <textarea class="form-control form-control-lg" id="shipping_address" name="shipping_address" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required><?php echo htmlspecialchars($currentUser['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold">Đơn hàng của bạn</h4>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cartItems as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted">Số lượng: <?php echo htmlspecialchars($item['quantity']); ?></small>
                                    </div>
                                    <span class="text-muted fw-bold"><?php echo number_format($item['total'], 0, ',', '.'); ?>đ</span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold h5 mt-3">
                                <span>Tổng cộng</span>
                                <strong><?php echo number_format($total, 0, ',', '.'); ?>đ</strong>
                            </li>
                        </ul>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary w-100 ">Đặt hàng</button>
                            <a href="/mystore/index" class="btn btn-dark w-100 ">Tiếp tục mua sắm</a>
                        </div>               
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'app/views/layouts/footer.php'; ?>