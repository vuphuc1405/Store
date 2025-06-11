<?php include 'app/views/admin/layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Thêm Thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item"><a href="/mystore/admin/brands">Thương hiệu</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <form action="/mystore/admin/brands/store" method="POST">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin Thương hiệu</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Thương hiệu</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Ví dụ: Apple, Samsung..." required>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Lưu Thương hiệu</button>
            <a href="/mystore/admin/brands" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php include 'app/views/admin/layouts/footer.php'; ?>