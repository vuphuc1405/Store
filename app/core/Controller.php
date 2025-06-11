<?php
class Controller {
    public function loadView($view, $data = []) {
        // SỬA ĐỔI: Tự động thêm số lượng sản phẩm trong giỏ hàng vào dữ liệu cho mọi view
        if ($this->isLoggedIn()) {
            // Tải model Cart một lần để tái sử dụng
            $cartModelForCount = $this->loadModel('Cart');
            $data['cartItemCount'] = $cartModelForCount->getCartItemCount($_SESSION['user_id']);
        } else {
            $data['cartItemCount'] = 0;
        }

        extract($data);
        require_once 'app/views/' . $view . '.php';
    }

    public function loadModel($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }

    public function redirect($url) {
        header('Location: ' . $url);
        exit();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
    }
}