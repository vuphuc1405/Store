<?php 
include 'app/views/admin/layouts/header.php'; 

// Mảng dịch trạng thái
$statusTranslations = [
    'Pending' => 'Chờ xử lý',
    'Processing' => 'Đang xử lý',
    'Shipped' => 'Đang giao hàng',
    'Delivered' => 'Đã giao',
    'Cancelled' => 'Đã hủy'
];

$currentStatusKey = $order['status'];
$currentStatusDisplay = $statusTranslations[$currentStatusKey] ?? $currentStatusKey;
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Chi tiết Đơn hàng #<?php echo htmlspecialchars($order['orderId']); ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item"><a href="/mystore/admin/orders">Đơn hàng</a></li>
        <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="fas fa-box me-1"></i>
                    Sản phẩm trong đơn hàng
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderDetails as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/mystore/public/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px;">
                                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td class="text-end align-middle"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                        <td class="text-end align-middle fw-bold"><?php echo number_format($item['total'], 0, ',', '.'); ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end border-0"><strong>Tổng cộng:</strong></td>
                                    <td class="text-end border-0 h5 fw-bold"><?php echo number_format($order['totalAmount'], 0, ',', '.'); ?>đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Thông tin khách hàng
                </div>
                <div class="card-body">
                    <p><strong>Tên:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                    <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['orderDate'])); ?></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fas fa-sync-alt me-1"></i>
                    Cập nhật trạng thái
                </div>
                <div class="card-body">
                    <p>
                        <strong>Trạng thái hiện tại:</strong> 
                        <span class="status-badge status-<?php echo strtolower($currentStatusKey); ?>"><?php echo $currentStatusDisplay; ?></span>
                    </p>
                    <form action="/mystore/admin/updateOrderStatus" method="POST">
                        <input type="hidden" name="orderId" value="<?php echo $order['orderId']; ?>">
                        <div class="mb-3">
                            <label for="status" class="form-label">Thay đổi trạng thái:</label>
                            <select name="status" id="status" class="form-select">
                                <?php foreach ($statusTranslations as $key => $value): ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($key == $currentStatusKey) ? 'selected' : ''; ?>>
                                        <?php echo $value; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/admin/layouts/footer.php'; ?>