<?php include 'app/views/layouts/header.php'; ?>

<main>
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-3">Thế Giới Smartphone Trong Tầm Tay</h1>
                    <p class="lead">Khám phá những mẫu điện thoại mới nhất với công nghệ đột phá. Trải nghiệm mua sắm tuyệt vời đang chờ bạn.</p>
                    <a href="/mystore/products" class="btn btn-primary btn-lg">Xem tất cả sản phẩm</a>
                    <a href="#" class="btn btn-outline-secondary btn-lg">Ưu đãi hot</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Sản Phẩm Bán Chạy</h2>
            <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
                <?php foreach($products as $product): ?>
                    <div class="col">
                        <div class="product-card d-flex flex-column">
                            <div class="product-card-img">
                                <a href="/mystore/product?id=<?php echo $product['productId']; ?>">
                                    <img src="/mystore/public/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                                </a>
                            </div>
                            <div class="product-card-body d-flex flex-column flex-grow-1">
                                <h3 class="product-card-title">
                                    <a href="/mystore/product?id=<?php echo $product['productId']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h3>
                                
                                <div class="star-rating mb-2 justify-content-center">
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
                                
                                <div class="mt-auto pt-2">
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
                    </div>
                <?php endforeach; ?>
            </div>
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
                            <h3>Dòng Flagship Cao Cấp</h3><p>Trải nghiệm đỉnh cao công nghệ</p>
                            <a href="#" class="btn btn-primary btn-sm">Khám phá</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <img src="https://images.unsplash.com/photo-1610792516307-ea5acd9c3b00?q=80&w=800" alt="Dòng Flagship Cao Cấp" style="height: 100%; object-fit: cover;">
                         <div class="feature-card-content">
                            <h3>Tầm Trung Tốt Nhất</h3><p>Hiệu năng vượt trội trong tầm giá</p>
                            <a href="#" class="btn btn-primary btn-sm">Khám phá</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                     <div class="feature-card">
                        <img src="https://images.unsplash.com/photo-1610792516307-ea5acd9c3b00?q=80&w=800" alt="Dòng Flagship Cao Cấp" style="height: 100%; object-fit: cover;">
                        <div class="feature-card-content">
                            <h3>Phụ Kiện Chính Hãng</h3><p>Bảo vệ và nâng tầm điện thoại</p>
                            <a href="#" class="btn btn-primary btn-sm">Khám phá</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <div class="cta-banner">
                <div class="row align-items-center text-center text-lg-start">
                    <div class="col-lg-7">
                        <h2>Trang Bị Phụ Kiện Hoàn Hảo</h2>
                        <p class="lead">Từ ốp lưng, sạc dự phòng đến tai nghe, tìm tất cả phụ kiện bạn cần để nâng tầm trải nghiệm.</p>
                        <a href="#" class="btn btn-primary">Mua sắm phụ kiện</a>
                    </div>
                    <div class="col-lg-5 mt-4 mt-lg-0">
                        <img src="https://images.unsplash.com/photo-1550009158-94ae76552444?q=80&w=800" alt="Phụ kiện điện thoại" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="subscribe-section section-padding" style="background-color: var(--light-gray-bg);">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <h2 class="section-title">Đăng Ký Nhận Tin & Giảm 20%</h2>
                    <p>Nhận thông tin cập nhật mới nhất về sản phẩm và các chương trình giảm giá đặc biệt.</p>
                    <form class="mt-4">
                        <div class="input-group input-group-lg">
                            <input type="email" class="form-control" placeholder="Nhập địa chỉ email của bạn">
                            <button class="btn btn-primary" type="submit">Đăng ký</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'app/views/layouts/footer.php'; ?>