<?php include 'app/views/layouts/header.php'; ?>

<div class="container section-padding">
    <div class="row">
        <div class="col-12">
            <h1 class="section-title">
                <?php if(!empty($search)): ?>
                    Kết quả cho "<?php echo htmlspecialchars($search); ?>"
                <?php elseif(!empty($currentCategory)): ?>
                    <?php echo htmlspecialchars($currentCategory['name']); ?>
                <?php else: ?>
                    Tất cả sản phẩm
                <?php endif; ?>
            </h1>
            
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                <?php if(empty($products)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <p class="h4">Không tìm thấy sản phẩm nào.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach($products as $product): ?>
                        <div class="col">
                             <div class="product-card">
                                <div class="product-card-img">
                                    <a href="/mystore/product?id=<?php echo $product['productId']; ?>">
                                        <img src="/mystore/public/images/products/<?php echo $product['image']; ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                                    </a>
                                </div>
                                <div class="product-card-body">
                                    <h3 class="product-card-title">
                                        <a href="/mystore/product?id=<?php echo $product['productId']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </a>
                                    </h3>
                                    <div class="product-card-rating">
                                        <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="far fa-star"></i>
                                    </div>
                                    <p class="product-card-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>