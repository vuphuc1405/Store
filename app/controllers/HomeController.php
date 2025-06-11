<?php
class HomeController extends Controller {
    public function index() {
        $productModel = $this->loadModel('Product');
        $categoryModel = $this->loadModel('Category');
        $reviewModel = $this->loadModel('Review'); // Tải Review Model

        $allProducts = $productModel->getWithCategory();
        $products = array_slice($allProducts, 0, 8); // Chỉ hiển thị 8 sản phẩm
        
        // Lấy ID của các sản phẩm sẽ được hiển thị
        $productIds = array_column($products, 'productId');

        // Lấy thông tin đánh giá cho các sản phẩm đó trong 1 lần query
        $ratings = $reviewModel->getAverageRatingsForProducts($productIds);

        // Gắn thông tin đánh giá vào từng sản phẩm
        foreach ($products as &$product) { // Dùng tham chiếu & để cập nhật trực tiếp
            if (isset($ratings[$product['productId']])) {
                $product['rating_info'] = $ratings[$product['productId']];
            } else {
                // Đặt giá trị mặc định nếu sản phẩm chưa có đánh giá
                $product['rating_info'] = ['avg_rating' => 0, 'review_count' => 0];
            }
        }
        unset($product); // Hủy tham chiếu

        $categories = $categoryModel->getAll();
        
        $data = [
            'title' => 'Trang chủ - Cửa hàng điện thoại',
            'products' => $products,
            'categories' => $categories
        ];
        
        $this->loadView('home/index', $data);
    }
}