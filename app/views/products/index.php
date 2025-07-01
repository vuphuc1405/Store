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
                            <div class="product-card d-flex flex-column h-100">
                                <a href="/mystore/product?id=<?php echo $product['productId']; ?>" class="text-decoration-none text-dark d-flex flex-column flex-grow-1">
                                    <div class="product-card-img">
                                        <img src="/mystore/public/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                                    </div>
                                    <div class="product-card-body d-flex flex-column flex-grow-1">
                                        <h3 class="product-card-title">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h3>
                                        
                                        <div class="star-rating mt-auto mb-2 justify-content-center">
                                            <?php 
                                                $review_count = (int)($product['rating_info']['review_count'] ?? 0);
                                                $avg_rating = 5;

                                                if ($review_count > 0) {
                                                    $avg_rating = round($product['rating_info']['avg_rating']);
                                                }

                                                for ($i = 1; $i <= 5; $i++):
                                            ?>
                                                <i class="fa-star <?php echo $i <= $avg_rating ? 'fas' : 'far'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="product-card-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
                                    </div>
                                </a>
                                
                                <div class="mt-auto pt-2 px-3 pb-3">
                                    <div class="d-grid gap-2">
                                        <?php if(isset($_SESSION['user_id'])): ?>
                                            <a href="#" class="btn btn-primary btn-sm btn-buy-now" data-product-id="<?php echo $product['productId']; ?>">
                                                <i class="fas fa-dollar-sign"></i> Mua ngay
                                            </a>
                                            <a href="#" class="btn btn-outline-secondary btn-sm btn-add-to-cart" data-product-id="<?php echo $product['productId']; ?>">
                                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                            </a>
                                        <?php else: ?>
                                             <a href="/mystore/login" class="btn btn-primary btn-sm"><i class="fas fa-dollar-sign"></i> Mua ngay</a>
                                             <a href="/mystore/login" class="btn btn-outline-secondary btn-sm"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
        </div>   
            <?php 
            include 'app/views/partials/pagination.php'; 
            ?>
        </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>