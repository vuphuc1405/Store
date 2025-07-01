<?php
class ProductController extends Controller {
    public function index() {
        $productModel = $this->loadModel('Product');
        $categoryModel = $this->loadModel('Category');
        $reviewModel = $this->loadModel('Review'); // Thêm dòng này

        $productsPerPage = 8;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $offset = ($currentPage - 1) * $productsPerPage;

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        if ($search) {
            $totalProducts = $productModel->countBySearch($search);
            $products = $productModel->search($search, $productsPerPage, $offset);
        } else {
            $totalProducts = $productModel->countAll();
            $products = $productModel->getWithCategory($productsPerPage, $offset);
        }
        
        // === BẮT ĐẦU THÊM LOGIC LẤY ĐÁNH GIÁ ===
        if (!empty($products)) {
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
        }
        // === KẾT THÚC LOGIC LẤY ĐÁNH GIÁ ===
        
        $totalPages = ceil($totalProducts / $productsPerPage);
        $categories = $categoryModel->getAll();
        
        $data = [
            'title' => 'Danh sách sản phẩm',
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
        
        $this->loadView('products/index', $data);
    }

     public function detail() {
        // ... (phần này không đổi) ...
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        
        if (!$id) {
            $this->redirect('/mystore/products');
            return;
        }
        
        $productModel = $this->loadModel('Product');
        $product = $productModel->getByIdWithDetails($id);

        
        if (!$product) {
            $this->redirect('/mystore/products');
            return;
        }

        $reviewModel = $this->loadModel('Review');
        $reviews = $reviewModel->getByProductId($id);
        $ratingInfo = $reviewModel->getAverageRating($id);

        $canReview = false;
        if ($this->isLoggedIn()) {
            $canReview = !$reviewModel->hasUserReviewedProduct($_SESSION['user_id'], $id);
        }
        
        $data = [
            'title' => $product['name'],
            'product' => $product,
            'reviews' => $reviews,
            'ratingInfo' => $ratingInfo,
            'canReview' => $canReview
        ];
        
        $this->loadView('products/detail', $data);
    }

   public function category() {
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$categoryId) {
            $this->redirect('/mystore/products');
            return;
        }
        
        $productModel = $this->loadModel('Product');
        $categoryModel = $this->loadModel('Category');
        $reviewModel = $this->loadModel('Review'); // Thêm dòng này
        
        $category = $categoryModel->getById($categoryId);
        if (!$category) {
            $this->redirect('/mystore/products');
            return;
        }

        $productsPerPage = 8;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $offset = ($currentPage - 1) * $productsPerPage;
        
        $totalProducts = $productModel->countByCategory($categoryId);
        $products = $productModel->getByCategory($categoryId, $productsPerPage, $offset);
        
        // === BẮT ĐẦU THÊM LOGIC LẤY ĐÁNH GIÁ ===
        if (!empty($products)) {
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
        }
        // === KẾT THÚC LOGIC LẤY ĐÁNH GIÁ ===
        
        $totalPages = ceil($totalProducts / $productsPerPage);
        $categories = $categoryModel->getAll();
        
        $data = [
            'title' => 'Danh mục: ' . $category['name'],
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
        
        $this->loadView('products/index', $data);
    }
}