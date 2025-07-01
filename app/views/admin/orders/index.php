<?php include 'app/views/admin/layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý Đơn hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item active">Đơn hàng</li>
    </ol>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($orders)): ?>
                <div class="text-center p-5">
                    <p class="h4">Chưa có đơn hàng nào.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Mã Đơn Hàng</th>
                                <th>Tên Khách Hàng</th>
                                <th>Ngày Đặt</th>
                                <th>Tổng Tiền</th>
                                <th class="text-center">Trạng Thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo htmlspecialchars($order['orderId']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($order['orderDate'])); ?></td>
                                    <td><?php echo number_format($order['totalAmount'] ?? 0, 0, ',', '.'); ?>đ</td>
                                    <td class="text-center">
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', htmlspecialchars($order['status']))); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/mystore/admin/orders/detail?id=<?php echo $order['orderId']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> Xem
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

<?php include 'app/views/admin/layouts/footer.php'; ?>