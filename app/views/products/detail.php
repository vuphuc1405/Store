<?php include 'app/views/layouts/header.php'; ?>

<div class="container section-padding">
    <div class="row gy-5">
        <div class="col-lg-6">
            <div class="product-detail-img text-center">
                <img src="/mystore/public/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
            </div>
        </div>

        <div class="col-lg-6">
            <div class="product-detail-info">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>

                <div class="star-rating mb-3">
                    <?php
                    $avg_rating = round($ratingInfo['avg_rating'] ?? 0);
                    if (empty($ratingInfo['review_count'])) {
                        $avg_rating = 5; 
                    }
                    for ($i = 1; $i <= 5; $i++) {
                        echo '<i class="fa-star ' . ($i <= $avg_rating ? 'fas' : 'far') . '"></i>';
                    }
                    ?>
                    <span class="text-muted ms-2">(<?php echo (int)($ratingInfo['review_count'] ?? 0); ?> đánh giá)</span>
                </div>

                <p class="product-price mb-4"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
                
                <form id="addToCartForm" class="mt-4">
                    <input type="hidden" name="product_id" value="<?php echo $product['productId']; ?>">
                    <div class="row align-items-center g-3">
                        <div class="col-auto">
                            <label for="quantity" class="form-label mb-0 me-2">Số lượng:</label>
                            <input type="number" class="form-control d-inline-block" style="width: 90px;" id="quantity" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['stock_quantity']); ?>">
                        </div>
                        <div class="col-auto">
                            <p class="text-muted mb-0 ms-2">Còn lại: <?php echo htmlspecialchars($product['stock_quantity']); ?> sản phẩm</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                         <?php if ($this->isLoggedIn()): ?>
                            <button type="button" id="buyNowBtn" class="btn btn-primary flex-grow-1"><i class="fas fa-dollar-sign me-2"></i>Mua ngay</button>
                            <button type="button" id="addToCartBtn" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ</button>
                         <?php else: ?>
                             <a href="/mystore/login" class="btn btn-primary flex-grow-1"><i class="fas fa-dollar-sign me-2"></i>Mua ngay</a>
                             <a href="/mystore/login" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ</a>
                         <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-5">
    <div class="col-12">
        <div class="product-specs-box">
    <h4 class="mb-3">Thông số kỹ thuật</h4>
    <table class="table table-specs">
        <tbody>
            <?php if (isset($product['attributes']) && !empty($product['attributes'])): ?>
                <?php foreach ($product['attributes'] as $spec): ?>
                <tr>
                    <td class="spec-title"><?php echo htmlspecialchars($spec['name']); ?></td>
                    <td class="spec-content"><?php echo nl2br(htmlspecialchars($spec['value'])); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Chưa có thông số kỹ thuật chi tiết cho sản phẩm này.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
</div>

    <div class="row mt-5 pt-5 border-top">
        <div class="col-lg-7">
            <h3 class="mb-4">Viết đánh giá của bạn</h3>
            <?php if ($this->isLoggedIn() && $canReview): ?>
                 <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form action="/mystore/reviews/create" method="POST" class="review-form">
                            <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">
                            <div class="mb-3">
                                <label class="form-label">Xếp hạng của bạn:</label>
                                <div class="rating-stars">
                                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Bình luận của bạn:</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="Sản phẩm này tuyệt vời..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
            <?php elseif ($this->isLoggedIn() && !$canReview): ?>
                <div class="alert alert-info">Cảm ơn, bạn đã đánh giá sản phẩm này rồi.</div>
            <?php else: ?>
                <div class="alert alert-warning">Vui lòng <a href="/mystore/login">đăng nhập</a> để viết đánh giá.</div>
            <?php endif; ?>
        </div>

        <div class="col-lg-5">
            <h3 class="mb-4">Đánh giá của khách hàng (<?php echo (int)($ratingInfo['review_count'] ?? 0); ?>)</h3>
            <?php if (empty($reviews)): ?>
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="d-flex mb-4" id="review-<?php echo $review['reviewId']; ?>">
                        <div class="flex-shrink-0">
                             <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-user fa-lg"></i>
                            </div>
                        </div>
                        <div class="ms-3 w-100">
                            <div class="review-content">
                                <h5 class="mt-0 mb-1"><?php echo htmlspecialchars($review['customer_name']); ?></h5>
                                <div class="star-rating mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-star <?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="mb-2"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y', strtotime($review['createdAt'])); ?>
                                    <?php if (!empty($review['updatedAt'])): ?>
                                        (đã chỉnh sửa)
                                    <?php endif; ?>
                                </small>

                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['userId']): ?>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-secondary btn-edit-review me-2" data-review-id="<?php echo $review['reviewId']; ?>">
                                            <i class="fas fa-pencil-alt"></i> Sửa
                                        </button>
                                        <form action="/mystore/reviews/delete" method="POST" class="d-inline">
                                            <input type="hidden" name="reviewId" value="<?php echo $review['reviewId']; ?>">
                                            <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?');">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['userId']): ?>
                            <div class="review-edit-form" style="display: none;">
                                <form action="/mystore/reviews/update" method="POST">
                                    <input type="hidden" name="reviewId" value="<?php echo $review['reviewId']; ?>">
                                    <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Chỉnh sửa xếp hạng:</label>
                                        <div class="rating-stars">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <input type="radio" id="star-edit-<?php echo $review['reviewId']; ?>-<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($i == $review['rating']) ? 'checked' : ''; ?> required><label for="star-edit-<?php echo $review['reviewId']; ?>-<?php echo $i; ?>" title="<?php echo $i; ?> sao"><i class="fas fa-star"></i></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comment-edit-<?php echo $review['reviewId']; ?>" class="form-label">Chỉnh sửa bình luận:</label>
                                        <textarea name="comment" id="comment-edit-<?php echo $review['reviewId']; ?>" class="form-control" rows="3"><?php echo htmlspecialchars($review['comment']); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Lưu thay đổi</button>
                                    <button type="button" class="btn btn-secondary btn-sm btn-cancel-edit" data-review-id="<?php echo $review['reviewId']; ?>">Hủy</button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>