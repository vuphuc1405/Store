<?php include 'app/views/admin/layouts/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4 mb-0">Quản lý Thương hiệu</h1>
        <a href="/mystore/admin/brands/add" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm thương hiệu mới
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item active">Thương hiệu</li>
    </ol>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Thương hiệu</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $brand): ?>
                        <tr>
                            <td><?php echo $brand['brandId']; ?></td>
                            <td><?php echo htmlspecialchars($brand['name']); ?></td>
                            <td class="text-end">
                                <a href="/mystore/admin/brands/edit?id=<?php echo $brand['brandId']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="/mystore/admin/brands/delete?id=<?php echo $brand['brandId']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?');">Xóa</a>
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