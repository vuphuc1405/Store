document.addEventListener('DOMContentLoaded', () => {

    initializeFlashMessageAutoClose();
    initializeBannerSwipe();
    initializePriceFormatting();

    const setupValidation = (elementId, message) => {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('invalid', (e) => {
                e.target.setCustomValidity(message);
            });
            const resetValidity = (e) => e.target.setCustomValidity('');
            element.addEventListener('input', resetValidity);
            element.addEventListener('change', resetValidity);
        }
    };
    setupValidation('username', 'Vui lòng nhập tên đăng nhập.');
    setupValidation('password', 'Vui lòng nhập mật khẩu.');

    document.body.addEventListener('click', function (e) {
        const target = e.target;
        const addToCartBtn = target.closest('.btn-add-to-cart');
        const buyNowBtn = target.closest('.btn-buy-now');
        const editReviewBtn = target.closest('.btn-edit-review');
        const cancelEditBtn = target.closest('.btn-cancel-edit');

        if (addToCartBtn) {
            e.preventDefault();
            const productId = addToCartBtn.dataset.productId;
            if (!productId) return;
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            sendAddToCartRequest(formData);
        } else if (buyNowBtn) {
            e.preventDefault();
            const productId = buyNowBtn.dataset.productId;
            if (!productId) return;
            sendBuyNowRequest(productId, 1);
        } else if (editReviewBtn) {
            e.preventDefault();
            toggleReviewEditForm(editReviewBtn.dataset.reviewId, true);
        } else if (cancelEditBtn) {
            e.preventDefault();
            toggleReviewEditForm(cancelEditBtn.dataset.reviewId, false);
        }
    });

    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        const buyNowBtnDetail = document.getElementById('buyNowBtn');
        const addToCartBtnDetail = document.getElementById('addToCartBtn');
        const quantityInput = document.getElementById('quantity');
        const productId = addToCartForm.querySelector('input[name="product_id"]').value;

        if (buyNowBtnDetail) {
            buyNowBtnDetail.addEventListener('click', (e) => {
                e.preventDefault();
                sendBuyNowRequest(productId, quantityInput.value);
            });
        }
        if (addToCartBtnDetail) {
            addToCartBtnDetail.addEventListener('click', (e) => {
                e.preventDefault();
                const formData = new FormData(addToCartForm);
                sendAddToCartRequest(formData);
            });
        }
    }
});


function initializeFlashMessageAutoClose() {
    const staticAlerts = document.querySelectorAll('.alert-dismissible.fade.show');
    staticAlerts.forEach(alertElement => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }, 3000);
    });
}


function initializeBannerSwipe() {
    const bannerEl = document.getElementById('optimizedBanner');
    if (!bannerEl) return;

    const carousel = new bootstrap.Carousel(bannerEl);
    let startX = 0;
    let endX = 0;

    const handleSwipe = () => {
        const threshold = 50; // Ngưỡng vuốt tối thiểu
        if (startX - endX > threshold) {
            carousel.next(); // Vuốt sang trái
        } else if (endX - startX > threshold) {
            carousel.prev(); // Vuốt sang phải
        }
    };

    bannerEl.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; });
    bannerEl.addEventListener('touchmove', (e) => { endX = e.touches[0].clientX; });
    bannerEl.addEventListener('touchend', handleSwipe);

    bannerEl.addEventListener('mousedown', (e) => {
        startX = e.clientX;
        e.preventDefault();
    });
    bannerEl.addEventListener('mouseup', (e) => {
        endX = e.clientX;
        handleSwipe();
    });
}

function toggleReviewEditForm(reviewId, showEdit) {
    const reviewContainer = document.getElementById(`review-${reviewId}`);
    if (reviewContainer) {
        const content = reviewContainer.querySelector('.review-content');
        const form = reviewContainer.querySelector('.review-edit-form');
        content.style.display = showEdit ? 'none' : 'block';
        form.style.display = showEdit ? 'block' : 'none';
    }
}

function sendAddToCartRequest(formData) {
    fetch('/mystore/cart/add', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(response => response.json())
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
            console.error('Lỗi khi thêm vào giỏ:', error);
            showFlashMessage('Không thể kết nối hoặc có lỗi từ máy chủ.', 'danger');
        });
}

function sendBuyNowRequest(productId, quantity) {
    if (!productId) {
        showFlashMessage('Không tìm thấy sản phẩm.', 'danger');
        return;
    }
    window.location.href = `/mystore/buy-now?product_id=${productId}&quantity=${quantity}`;
}


function updateCartBadge(count) {
    const badge = document.getElementById('cart-item-count-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? '' : 'none';
    }
}

function showFlashMessage(message, type = 'success') {
    const container = document.querySelector('div[style*="position: fixed"]');
    if (!container) return;

    const oldAlert = container.querySelector('.alert');
    if (oldAlert) {
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
    if (alertElement) {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertElement);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 3000);
    }
}


function getRawNumber(value) {
    let s = String(value).trim();
    if (/\.\d{1,2}$/.test(s)) {
        s = s.split('.')[0];
    }

    return s.replace(/[^\d]/g, '');
}

function formatNumber(num) {
    const rawNumber = getRawNumber(num);
    if (rawNumber === '') {
        return ''; // Trả về rỗng nếu không có giá trị
    }
    return Number(rawNumber).toLocaleString('vi-VN');
}


function initializePriceFormatting() {
    const priceInputs = document.querySelectorAll('.price-input');

    priceInputs.forEach(input => {
        if (input.value) {
            input.value = formatNumber(input.value);
        }

        input.addEventListener('focus', () => {
            input.value = getRawNumber(input.value);
        });

        input.addEventListener('blur', () => {
            if (input.value) {
                input.value = formatNumber(input.value);
            }
        });

        const form = input.closest('form');
        if (form && !form.hasAttribute('data-price-formatted')) {
            form.setAttribute('data-price-formatted', 'true');

            form.addEventListener('submit', () => {
                const allPriceInputsInForm = form.querySelectorAll('.price-input');
                allPriceInputsInForm.forEach(priceInput => {
                    priceInput.value = getRawNumber(priceInput.value);
                });
            });
        }
    });
}
