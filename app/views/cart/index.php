<?php include 'app/views/layouts/header.php'; ?>

<div class="container section-padding">
    <h1 class="section-title">Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h2 class="mb-3">Giỏ hàng của bạn đang trống</h2>
            <p class="text-muted mb-4">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm.</p>
            <a href="/mystore/products" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table align-middle cart-table">
                        <thead>
                            <tr>
                                <th scope="col" class="border-0 ps-0">Sản phẩm</th>
                                <th scope="col" class="border-0">Giá</th>
                                <th scope="col" class="border-0">Số lượng</th>
                                <th scope="col" class="border-0">Tổng</th>
                                <th scope="col" class="border-0"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td class="ps-0">
                                        <div class="d-flex align-items-center">
                                            <img src="/mystore/public/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <div class="ms-3">
                                                <a href="/mystore/product?id=<?php echo htmlspecialchars($item['productId']); ?>" class="text-dark text-decoration-none fw-bold"><?php echo htmlspecialchars($item['name']); ?></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <form method="POST" action="/mystore/cart/update" class="d-inline">
                                            <input type="hidden" name="cartId" value="<?php echo htmlspecialchars($item['cartId'] ?? 0); ?>">
                                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" class="form-control" style="width: 80px;" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="fw-bold"><?php echo number_format($item['total'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <form method="POST" action="/mystore/cart/remove" class="d-inline">
                                            <input type="hidden" name="cartId" value="<?php echo htmlspecialchars($item['cartId'] ?? 0); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">&times;</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4 class="mb-4 fw-bold">Tổng kết giỏ hàng</h4>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tạm tính</span>
                        <span><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Phí vận chuyển</span>
                        <span>Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold h5">
                        <span>Tổng cộng</span>
                        <span><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                    </div>
                   <div class="d-grid mt-4">
                        <div class="d-flex gap-3 mt-4 justify-content-center">
                            <a href="/mystore/checkout" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center">
                                Thanh toán
                            </a>
                            <a href="/mystore/home" class="btn btn-dark btn-lg w-100 d-flex align-items-center justify-content-center">
                                Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/layouts/footer.php'; ?>