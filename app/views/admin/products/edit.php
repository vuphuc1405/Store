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
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="categoryId" class="form-label">Danh mục</label>
                        <select class="form-select" id="categoryId" name="categoryId" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['categoryId']; ?>" <?php echo (isset($product['categoryId']) && $category['categoryId'] == $product['categoryId']) ? 'selected' : ''; ?>>
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
                        <input type="text" class="form-control price-input" id="price" name="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required inputmode="numeric">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity'] ?? ''); ?>" required>
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
                    <label class="form-label fw-bold">Thông số kỹ thuật chi tiết</label>
                    <div id="specs-container">
                        </div>
                    <button type="button" id="add-spec-btn" class="btn btn-sm btn-outline-success mt-2">
                        <i class="fas fa-plus me-1"></i> Thêm thông số
                    </button>
                    <input type="hidden" name="description" id="description-json" value='<?php
                        $attributes_for_js = [];
                        if (isset($product['attributes']) && is_array($product['attributes'])) {
                            foreach ($product['attributes'] as $attr) {
                                $attributes_for_js[] = ['key' => $attr['name'], 'value' => $attr['value']];
                            }
                        }
                        echo htmlspecialchars(json_encode($attributes_for_js), ENT_QUOTES, 'UTF-8');
                    ?>'>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-image me-2"></i>Hình ảnh</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($product['image'])): ?>
                <div class="mb-3">
                    <label class="form-label">Ảnh hiện tại:</label>
                    <img src="/mystore/public/images/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100px; height: auto;">
                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="image" class="form-label"><?php echo !empty($product['image']) ? 'Thay đổi ảnh (để trống nếu không muốn đổi)' : 'Chọn ảnh sản phẩm'; ?></label>
                    <input class="form-control" type="file" id="image" name="image" <?php echo empty($product['image']) ? 'required' : ''; ?>>
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