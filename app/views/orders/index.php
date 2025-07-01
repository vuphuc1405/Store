<?php include 'app/views/layouts/header.php'; ?>

<div class="container section-padding">
    <h1 class="section-title">Đơn hàng của tôi</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-muted mb-4"></i>
                    <h4 class="mb-3">Bạn chưa có đơn hàng nào.</h4>
                    <p class="text-muted mb-4">Hãy bắt đầu mua sắm để xem lịch sử đơn hàng tại đây.</p>
                    <a href="/mystore/products" class="btn btn-primary">Bắt đầu mua sắm</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Mã Đơn Hàng</th>
                                <th scope="col">Ngày Đặt</th>
                                <th scope="col">Tổng Tiền</th>
                                <th scope="col">Trạng Thái</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo htmlspecialchars($order['orderId']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($order['orderDate'])); ?></td>
                                    <td><?php echo number_format($order['totalAmount'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <span class="badge status-badge status-<?php echo strtolower(str_replace(' ', '-', htmlspecialchars($order['status']))); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>                                        
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/mystore/orders/detail?id=<?php echo $order['orderId']; ?>" class="btn btn-sm btn-outline-primary">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>