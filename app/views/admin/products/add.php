<?php include 'app/views/admin/layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Thêm Sản phẩm mới</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item"><a href="/mystore/admin/products">Sản phẩm</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>
    
    <form action="/mystore/admin/products/store" method="POST" enctype="multipart/form-data">
        
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="categoryId" class="form-label">Danh mục</label>
                        <select class="form-select" id="categoryId" name="categoryId" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['categoryId']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="brandId" class="form-label">Thương hiệu</label>
                        <select class="form-select" id="brandId" name="brandId" required>
                            <option value="">-- Chọn thương hiệu --</option>
                            <?php foreach ($brands as $brand): ?>
                                 <option value="<?php echo $brand['brandId']; ?>" <?php echo (isset($product['brandId']) && $brand['brandId'] == $product['brandId']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Giá (VNĐ)</label>
                        <input type="text" class="form-control price-input" id="price" name="price" required inputmode="numeric">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-cog me-2"></i>Thông số kỹ thuật</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="paste-specs" class="form-label fw-bold">Thêm nhanh thông số </label>
                    <textarea id="paste-specs" class="form-control" rows="2"></textarea>
                    <button type="button" id="parse-specs-btn" class="btn btn-secondary btn-sm mt-2">
                        <i class="fas fa-magic me-1"></i> Thêm
                    </button>
                </div>
                <hr>

                <div class="mb-3">
                    <label class="form-label">Thông số kỹ thuật chi tiết</label>
                    <div id="specs-container">
                        </div>
                    <button type="button" id="add-spec-btn" class="btn btn-sm btn-outline-success mt-2">
                        <i class="fas fa-plus me-1"></i> Thêm thông số
                    </button>
                    <input type="hidden" name="description" id="description-json" value='<?php echo htmlspecialchars($product['description'] ?? '[]'); ?>'>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-image me-2"></i>Hình ảnh</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh sản phẩm</label>
                    <input class="form-control" type="file" id="image" name="image" required>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
            <a href="/mystore/admin/products" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php include 'app/views/admin/layouts/footer.php'; ?>