<?php
class ProductController extends Controller {
    public function index() {
        $productModel = $this->loadModel('Product');
        $categoryModel = $this->loadModel('Category');
        
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        
        if ($search) {
            $products = $productModel->search($search);
        } else {
            $products = $productModel->getWithCategory();
        }
        
        $categories = $categoryModel->getAll();
        
        $data = [
            'title' => 'Danh sách sản phẩm',
            'products' => $products,
            'categories' => $categories,
            'search' => $search
        ];
        
        $this->loadView('products/index', $data);
    }

     public function detail() {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        
        if (!$id) {
            $this->redirect('/mystore/products');
            return;
        }
        
        $productModel = $this->loadModel('Product');
        $product = $productModel->getByIdWithCategory($id);
        
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
        $categoryId = isset($_GET['id']) ? $_GET['id'] : 0;
        
        if (!$categoryId) {
            $this->redirect('/mystore/products');
            return;
        }
        
        $productModel = $this->loadModel('Product');
        $categoryModel = $this->loadModel('Category');
        
        $category = $categoryModel->getById($categoryId);
        if (!$category) {
            $this->redirect('/mystore/products');
            return;
        }
        
        $products = $productModel->getByCategory($categoryId);
        $categories = $categoryModel->getAll();
        
        $data = [
            'title' => 'Danh mục: ' . $category['name'],
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category
        ];
        
        $this->loadView('products/category', $data);
    }
    
}
