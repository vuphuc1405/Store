<?php include 'app/views/admin/layouts/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4 mb-0">Quản lý Danh mục</h1>
        <a href="/mystore/admin/categories/add" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm danh mục mới
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item active">Danh mục</li>
    </ol>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Danh mục</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['categoryId']; ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td class="text-end">
                                <a href="/mystore/admin/categories/edit?id=<?php echo $category['categoryId']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="/mystore/admin/categories/delete?id=<?php echo $category['categoryId']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">Xóa</a>
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