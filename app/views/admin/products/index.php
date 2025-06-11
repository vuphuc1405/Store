<?php include 'app/views/admin/layouts/header.php'; ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4 mb-0">Quản lý Sản phẩm</h1>
        <a href="/mystore/admin/products/add" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm sản phẩm mới
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item active">Sản phẩm</li>
    </ol>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Hình ảnh</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['productId']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($product['brand_name'] ?? ''); ?></td>
                            <td><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                            <td>
                                <img src="/mystore/public/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="" height="50">
                            </td>
                            <td class="text-end">
                                <a href="/mystore/admin/products/edit?id=<?php echo $product['productId']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="/mystore/admin/products/delete?id=<?php echo $product['productId']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">Xóa</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/admin/layouts/footer.php'; ?>