document.addEventListener('DOMContentLoaded', () => {

    // Tự động đóng các thông báo tĩnh
    const staticAlerts = document.querySelectorAll('.alert-dismissible.fade.show');
    staticAlerts.forEach(alertElement => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }, 3000);
    });

    // --- BỘ LẮNG NGHE SỰ KIỆN CHUNG ---
    // Sử dụng event delegation trên body để xử lý các nút được tạo động
    document.body.addEventListener('click', function (e) {
        
        const addToCartBtn = e.target.closest('.btn-add-to-cart');
        const buyNowBtn = e.target.closest('.btn-buy-now');
        const editReviewBtn = e.target.closest('.btn-edit-review');
        const cancelEditBtn = e.target.closest('.btn-cancel-edit');

        // Xử lý nút "Thêm vào giỏ" trên trang chủ
        if (addToCartBtn) {
            e.preventDefault();
            const productId = addToCartBtn.dataset.productId;
            if (!productId) return;
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            sendAddToCartRequest(formData);
        }
        
        // Xử lý nút "Mua ngay" trên trang chủ
        else if (buyNowBtn) {
            e.preventDefault();
            const productId = buyNowBtn.dataset.productId;
            if (!productId) return;
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            sendBuyNowRequest(formData);
        }

        // Xử lý nút "Sửa" đánh giá
        else if (editReviewBtn) {
            e.preventDefault();
            const reviewId = editReviewBtn.dataset.reviewId;
            const reviewContainer = document.getElementById(`review-${reviewId}`);
            if (reviewContainer) {
                reviewContainer.querySelector('.review-content').style.display = 'none';
                reviewContainer.querySelector('.review-edit-form').style.display = 'block';
            }
        }

        // Xử lý nút "Hủy" khi sửa đánh giá
        else if (cancelEditBtn) {
            e.preventDefault();
            const reviewId = cancelEditBtn.dataset.reviewId;
            const reviewContainer = document.getElementById(`review-${reviewId}`);
            if (reviewContainer) {
                reviewContainer.querySelector('.review-content').style.display = 'block';
                reviewContainer.querySelector('.review-edit-form').style.display = 'none';
            }
        }
    });

    // --- BỘ LẮNG NGHE SỰ KIỆN RIÊNG CHO TRANG CHI TIẾT SẢN PHẨM ---
    const addToCartBtnDetail = document.getElementById('addToCartBtn');
    if(addToCartBtnDetail) {
        addToCartBtnDetail.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('addToCartForm');
            const formData = new FormData(form);
            sendAddToCartRequest(formData);
        });
    }
    
    const buyNowBtnDetail = document.getElementById('buyNowBtn');
    if(buyNowBtnDetail) {
        buyNowBtnDetail.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('addToCartForm');
            const formData = new FormData(form);
            sendBuyNowRequest(formData);
        });
    }
});

/**
 * Gửi yêu cầu thêm sản phẩm vào giỏ và chỉ hiển thị thông báo.
 */
function sendAddToCartRequest(formData) {
    fetch('/mystore/cart/add', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        if (!response.ok) throw new Error(`Lỗi HTTP! Trạng thái: ${response.status}`);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showFlashMessage(data.message || 'Thêm vào giỏ hàng thành công!', 'success');
            if (typeof data.cartItemCount !== 'undefined') {
                updateCartBadge(data.cartItemCount);
            }
        } else {
            showFlashMessage(data.message || 'Có lỗi xảy ra.', 'danger');
        }
    })
    .catch(error => {
        console.error('Lỗi khi gửi yêu cầu fetch:', error);
        showFlashMessage('Không thể kết nối hoặc có lỗi từ máy chủ.', 'danger');
    });
}

/**
 * Chuyển hướng đến trang thanh toán nhanh với thông tin sản phẩm.
 */
function sendBuyNowRequest(formData) {
    const productId = formData.get('product_id');
    const quantity = formData.get('quantity');
    
    if (!productId) {
        showFlashMessage('Không tìm thấy sản phẩm.', 'danger');
        return;
    }
    
    window.location.href = `/mystore/buy-now?product_id=${productId}&quantity=${quantity}`;
}

/**
 * Cập nhật số lượng hiển thị trên icon giỏ hàng.
 */
function updateCartBadge(count) {
    const badge = document.getElementById('cart-item-count-badge');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Hiển thị một thông báo flash tạm thời.
 */
function showFlashMessage(message, type = 'success') {
    const container = document.querySelector('div[style*="position: fixed"]');
    if (!container) return;

    const oldAlert = container.querySelector('.alert');
    if(oldAlert) {
        oldAlert.remove();
    }

    const alertId = 'flash-alert-' + Date.now();
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert" style="min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    container.insertAdjacentHTML('beforeend', alertHTML);
    const alertElement = document.getElementById(alertId);
    if(alertElement) {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertElement);
            if (bsAlert) {
               bsAlert.close();
            }
        }, 3000);
    }
}