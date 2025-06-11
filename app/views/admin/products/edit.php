<?php include 'app/views/admin/layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Chỉnh sửa Sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item"><a href="/mystore/admin/products">Sản phẩm</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>
    
    <form action="/mystore/admin/products/update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-sitemap me-2"></i>Tổ chức</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="categoryId" class="form-label">Danh mục</label>
                        <select class="form-select" id="categoryId" name="categoryId" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['categoryId']; ?>" <?php echo ($category['categoryId'] == $product['categoryId']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="brandId" class="form-label">Thương hiệu</label>
                        <select class="form-select" id="brandId" name="brandId" required>
                            <option value="">-- Chọn thương hiệu --</option>
                            <?php foreach ($brands as $brand): ?>
                                 <option value="<?php echo $brand['brandId']; ?>" <?php echo ($brand['brandId'] == $product['brandId']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-dollar-sign me-2"></i>Giá & Kho hàng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Giá (VNĐ)</label>
                        <input type="number" step="1" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-image me-2"></i>Hình ảnh</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Ảnh hiện tại:</label>
                    <img src="/mystore/public/images/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100px; height: auto;">
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Thay đổi ảnh (để trống nếu không muốn đổi)</label>
                    <input class="form-control" type="file" id="image" name="image">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="/mystore/admin/products" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php include 'app/views/admin/layouts/footer.php'; ?>