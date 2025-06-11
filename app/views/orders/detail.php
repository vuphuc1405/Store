<?php include 'app/views/layouts/header.php'; ?>

<?php
// Mảng dịch trạng thái
$statusTranslations = [
    'Pending' => 'Chờ xử lý',
    'Processing' => 'Đang xử lý',
    'Shipped' => 'Đang giao hàng',
    'Delivered' => 'Đã giao',
    'Cancelled' => 'Đã hủy'
];
?>

<div class="container section-padding">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1 class="section-title mb-0 me-3" style="text-align: left;"><?php echo $title; ?></h1>
        <a href="/mystore/orders" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Các sản phẩm trong đơn hàng</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <tbody>
                                <?php foreach ($orderDetails as $item): ?>
                                    <tr class="border-bottom">
                                        <td class="p-3" style="width: 90px;">
                                            <img src="/mystore/public/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid rounded border">
                                        </td>
                                        <td class="p-3">
                                            <a href="/mystore/product?id=<?php echo htmlspecialchars($item['productId']); ?>" class="text-dark fw-bold text-decoration-none d-block mb-1"><?php echo htmlspecialchars($item['name']); ?></a>
                                            <div class="text-muted small">
                                                <span>Đơn giá: <?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span><br>
                                                <span>Số lượng: <?php echo htmlspecialchars($item['quantity']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold p-3">
                                            <?php echo number_format($item['total'], 0, ',', '.'); ?>đ
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Mã đơn hàng
                            <span class="fw-bold">#<?php echo htmlspecialchars($order['orderId']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Ngày đặt hàng
                            <span><?php echo date('d/m/Y H:i', strtotime($order['orderDate'])); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Trạng thái
                            <span class="badge status-badge status-<?php echo strtolower(htmlspecialchars($order['status'])); ?>">
                                <?php 
                                    $statusKey = htmlspecialchars($order['status']);
                                    echo $statusTranslations[$statusKey] ?? $statusKey; 
                                ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold h5 mt-3">
                            Tổng cộng
                            <span class="text-danger"><?php echo number_format($order['totalAmount'], 0, ',', '.'); ?>đ</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>