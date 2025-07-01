<?php include 'app/views/layouts/header.php'; ?>

<main>
<div class="container-fluid py-4">
    <div class="container">
        <div id="optimizedBanner" class="carousel slide optimized-banner" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#optimizedBanner" data-bs-slide-to="0" class="active" aria-current="true"></button>
                <button type="button" data-bs-target="#optimizedBanner" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#optimizedBanner" data-bs-slide-to="2"></button>
            </div>
            
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="banner-slide-item">
                        <img src="/mystore/public/images/banner/iphone_15_pro_max.webp" class="banner-image" alt="iPhone 15 Pro Max">
                        <div class="banner-content">
                            <h1 class="banner-title">iPhone 15 Pro Max<br>Mạnh Mẽ Vượt Trội</h1>
                            <p class="banner-subtitle">Khám phá công nghệ Titanium và chip A17 Pro đỉnh cao.</p>
                            <a href="mystore/product?id=2" class="banner-button">Xem thêm <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                     <div class="banner-slide-item">
                        <img src="/mystore/public/images/banner/samsung-galaxy-s24-ultra.png" class="banner-image" alt="Samsung Galaxy S24 Ultra">
                        <div class="banner-content">
                            <h1 class="banner-title">Galaxy S24 Ultra<br>Camera AI Đột Phá</h1>
                            <p class="banner-subtitle">Màn hình Dynamic AMOLED 2X và camera 200MP siêu nét.</p>
                            <a href="mystore/product?id=3" class="banner-button">Xem thêm<i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <div class="banner-slide-item">
                        <img src="/mystore/public/images/banner/xiaomi-14-pro.png" class="banner-image" alt="Xiaomi 14 Pro">
                        <div class="banner-content">
                            <h1 class="banner-title">Xiaomi 14T Pro<br>Hiệu Năng Dẫn Đầu</h1>
                            <p class="banner-subtitle">Snapdragon 8 Gen 3 và sạc nhanh 120W siêu tốc.</p>
                            <a href="mystore/product?id=8" class="banner-button">Xem thêm <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#optimizedBanner" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#optimizedBanner" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</div>

   <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Sản phẩm nổi bật</h2>
            <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
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
                            $avg_rating = 5; // Mặc định 5 sao

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
                        <a href="/mystore/login" class="btn btn-primary btn-sm">
                            <i class="fas fa-dollar-sign"></i> Mua ngay
                        </a>
                        <a href="/mystore/login" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php 
            include 'app/views/partials/pagination.php'; 
            ?>
        </div>
    </section>
    <section class="section-padding" style="background-color: var(--light-gray-bg);">
        <div class="container">
            <h2 class="section-title">Danh Mục Nổi Bật</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <img src="https://images.unsplash.com/photo-1610792516307-ea5acd9c3b00?q=80&w=800" alt="Dòng Flagship Cao Cấp" style="height: 100%; object-fit: cover;">
                        <div class="feature-card-content">
                            <h3>Dòng Flagship Cao Cấp</h3>
                            <p>Trải nghiệm đỉnh cao công nghệ</p>
                            <a href="/mystore/category?id=1" class="btn btn-primary btn-sm">Khám phá</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <img src="https://images.unsplash.com/photo-1610792516307-ea5acd9c3b00?q=80&w=800" alt="Dòng Flagship Cao Cấp" style="height: 100%; object-fit: cover;">
                        <div class="feature-card-content">
                            <h3>Tầm trung tốt nhất</h3>
                            <p>Hiệu năng vượt trội trong tầm giá</p>
                            <a href="/mystore/category?id=2" class="btn btn-primary btn-sm">Khám phá</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <img src="https://images.unsplash.com/photo-1610792516307-ea5acd9c3b00?q=80&w=800" alt="Dòng Flagship Cao Cấp" style="height: 100%; object-fit: cover;">
                        <div class="feature-card-content">
                            <h3>Phụ kiện</h3>
                            <p>Bảo vệ và nâng tầm điện thoại</p>
                            <a href="/mystore/category?id=3" class="btn btn-primary btn-sm">Khám phá</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'app/views/layouts/footer.php'; ?>