<?php
class HomeController extends Controller {
    public function index() {
        $productModel = $this->loadModel('Product');
        $categoryModel = $this->loadModel('Category');
        $reviewModel = $this->loadModel('Review');

        // --- BẮT ĐẦU LOGIC PHÂN TRANG ---
        $productsPerPage = 8; // Số sản phẩm hiển thị trên mỗi trang
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $offset = ($currentPage - 1) * $productsPerPage;

        // Lấy tổng số sản phẩm để tính toán tổng số trang
        $totalProducts = $productModel->countAll();
        $totalPages = ceil($totalProducts / $productsPerPage);
        // --- KẾT THÚC LOGIC PHÂN TRANG ---

        // Lấy danh sách sản phẩm cho trang hiện tại
        $products = $productModel->getWithCategory($productsPerPage, $offset);
        
        // Lấy thông tin đánh giá cho các sản phẩm
        $productIds = array_column($products, 'productId');
        $ratings = $reviewModel->getAverageRatingsForProducts($productIds);

        foreach ($products as &$product) {
            if (isset($ratings[$product['productId']])) {
                $product['rating_info'] = $ratings[$product['productId']];
            } else {
                $product['rating_info'] = ['avg_rating' => 0, 'review_count' => 0];
            }
        }
        unset($product);

        $categories = $categoryModel->getAll();
        
        $data = [
            'title' => 'Trang chủ - Cửa hàng điện thoại',
            'products' => $products,
            'categories' => $categories,
            // Thêm các biến phân trang vào mảng data để view có thể sử dụng
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
        
        $this->loadView('home/index', $data);
    }
}