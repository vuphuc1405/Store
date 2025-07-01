<?php
class ReviewController extends Controller {

    public function create() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đánh giá sản phẩm.';
            $this->redirect('/mystore/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $userId = $_SESSION['user_id'];
        $productId = $_POST['productId'] ?? 0;
        $rating = $_POST['rating'] ?? 0;
        $comment = trim($_POST['comment'] ?? '');

        if (empty($productId) || empty($rating) || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Dữ liệu đánh giá không hợp lệ.';
            $this->redirect('/mystore/product?id=' . $productId);
            return;
        }
        
        $reviewModel = $this->loadModel('Review');

        if ($reviewModel->hasUserReviewedProduct($userId, $productId)) {
             $_SESSION['error'] = 'Bạn đã đánh giá sản phẩm này rồi.';
             $this->redirect('/mystore/product?id=' . $productId);
             return;
        }

        $data = [
            'productId' => $productId,
            'userId' => $userId,
            'rating' => $rating,
            'comment' => $comment
        ];

        if ($reviewModel->create($data)) {
            $_SESSION['success'] = 'Cảm ơn bạn đã gửi đánh giá!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, không thể gửi đánh giá.';
        }

        $this->redirect('/mystore/product?id=' . $productId);
    }

    /**
     * PHƯƠNG THỨC MỚI: Xử lý việc cập nhật đánh giá
     */
    public function update() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập.';
            $this->redirect('/mystore/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $reviewId = $_POST['reviewId'] ?? 0;
        $productId = $_POST['productId'] ?? 0;
        $rating = $_POST['rating'] ?? 0;
        $comment = trim($_POST['comment'] ?? '');
        
        $reviewModel = $this->loadModel('Review');
        $review = $reviewModel->getById($reviewId);

        // Bảo mật: Đảm bảo người dùng chỉ có thể sửa đánh giá của chính họ
        if (!$review || $review['userId'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này.';
            $this->redirect('/mystore/product?id=' . $productId);
            return;
        }
        
        // Validation
        if (empty($rating) || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Dữ liệu đánh giá không hợp lệ.';
            $this->redirect('/mystore/product?id=' . $productId);
            return;
        }

        $data = [
            'rating' => $rating,
            'comment' => $comment
        ];
        
        if ($reviewModel->update($reviewId, $data)) {
            $_SESSION['success'] = 'Cập nhật đánh giá thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, không thể cập nhật đánh giá.';
        }

        $this->redirect('/mystore/product?id=' . $productId);
    }
 public function delete() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập.';
            $this->redirect('/mystore/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $reviewId = $_POST['reviewId'] ?? 0;
        $productId = $_POST['productId'] ?? 0; // Cần để chuyển hướng về đúng trang sản phẩm

        $reviewModel = $this->loadModel('Review');
        $review = $reviewModel->getById($reviewId);

        // Bảo mật: Đảm bảo người dùng chỉ có thể xóa đánh giá của chính họ (hoặc admin)
        if (!$review || ($review['userId'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này.';
            $this->redirect('/mystore/product?id=' . $productId);
            return;
        }

        if ($reviewModel->delete($reviewId)) {
            $_SESSION['success'] = 'Xóa đánh giá thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, không thể xóa đánh giá.';
        }

        $this->redirect('/mystore/product?id=' . $productId);
    }
}