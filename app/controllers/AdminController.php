<?php
class AdminController extends Controller {
    public function __construct() {
        if (!$this->isLoggedIn() || !$this->isAdmin()) {
            header('Location: /login');
            exit();
        }
    }

    public function index() {
        $productModel = $this->loadModel('Product');
        $orderModel = $this->loadModel('Order');
        $userModel = $this->loadModel('User');
        
        $totalProducts = count($productModel->getAll());
        $totalOrders = count($orderModel->getAll());
        $totalUsers = count($userModel->getAll());
        
        $recentOrders = array_slice($orderModel->getAll(), 0, 10);
        
        $data = [
            'title' => 'Quản trị hệ thống',
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'recentOrders' => $recentOrders
        ];
        
        $this->loadView('admin/dashboard', $data);
    }

    public function products() {
        $productModel = $this->loadModel('Product');
        $products = $productModel->getWithCategory();
        
        $data = [
            'title' => 'Quản lý sản phẩm',
            'products' => $products
        ];
        
        $this->loadView('admin/products/index', $data);
    }

    public function orders() {
        $orderModel = $this->loadModel('Order');
        $orders = $orderModel->getAllForAdmin();
        
        $data = [
            'title' => 'Quản lý đơn hàng',
            'orders' => $orders
        ];
        
        $this->loadView('admin/orders/index', $data);
    }

    public function users() {
        $userModel = $this->loadModel('User');
        $users = $userModel->getAll();
        
        $data = [
            'title' => 'Quản lý người dùng',
            'users' => $users
        ];
        
        $this->loadView('admin/users/index', $data);
    }

    public function updateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/admin/orders');
            return;
        }
        
        $orderId = $_POST['orderId'] ?? 0;
        $status = $_POST['status'] ?? '';
        
        $orderModel = $this->loadModel('Order');
        if ($orderModel->updateStatus($orderId, $status)) {
            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra';
        }
        
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/mystore/admin/orders');
    }

    public function addProduct() {
        $categoryModel = $this->loadModel('Category');
        $brandModel = $this->loadModel('Brand');
        $data = [
            'title' => 'Thêm sản phẩm mới',
            'categories' => $categoryModel->getAll(),
            'brands' => $brandModel->getAll()
        ];
        $this->loadView('admin/products/add', $data);
    }

    // Lưu sản phẩm mới vào DB
    public function storeProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/admin/products');
            return;
        }

        // Xử lý upload ảnh
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "public/images/products/";
            // Tạo tên file duy nhất để tránh trùng lặp
            $imageName = time() . '_' . basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $imageName;
            
            // Di chuyển file đã upload vào thư mục
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $_SESSION['error'] = 'Có lỗi khi tải ảnh lên.';
                $this->redirect('/mystore/admin/products/add');
                return;
            }
        }

        $productData = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'categoryId' => $_POST['categoryId'],
            'brandId' => $_POST['brandId'],
            'stock_quantity' => $_POST['stock_quantity'],
            'image' => $imageName
        ];
        $price = $_POST['price'];

        $productModel = $this->loadModel('Product');
        if ($productModel->createProduct($productData, $price)) {
            $_SESSION['success'] = 'Thêm sản phẩm thành công.';
            $this->redirect('/mystore/admin/products');
        } else {
            $_SESSION['error'] = 'Thêm sản phẩm thất bại.';
            $this->redirect('/mystore/admin/products/add');
        }
    }

    // Hiển thị form sửa sản phẩm
    public function editProduct() {
        $id = $_GET['id'] ?? 0;
        $productModel = $this->loadModel('Product');
        $product = $productModel->getByIdWithCategory($id);

        if (!$product) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm.';
            $this->redirect('/mystore/admin/products');
            return;
        }
        
        $categoryModel = $this->loadModel('Category');
        $brandModel = $this->loadModel('Brand');
        $data = [
            'title' => 'Sửa sản phẩm: ' . $product['name'],
            'product' => $product,
            'categories' => $categoryModel->getAll(),
            'brands' => $brandModel->getAll()
        ];
        $this->loadView('admin/products/edit', $data);
    }
    
    // Cập nhật sản phẩm trong DB
    public function updateProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/admin/products');
            return;
        }
        
        $productId = $_POST['productId'];
        $productModel = $this->loadModel('Product');
        $currentProduct = $productModel->getById($productId);

        // Xử lý upload ảnh mới (nếu có)
        $imageName = $currentProduct['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "public/images/products/";
            $imageName = time() . '_' . basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $imageName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                // Xóa ảnh cũ nếu upload ảnh mới thành công
                if ($currentProduct['image'] && file_exists($targetDir . $currentProduct['image'])) {
                    unlink($targetDir . $currentProduct['image']);
                }
            } else {
                $imageName = $currentProduct['image']; // Giữ lại ảnh cũ nếu upload lỗi
            }
        }

        $productData = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'categoryId' => $_POST['categoryId'],
            'brandId' => $_POST['brandId'],
            'stock_quantity' => $_POST['stock_quantity'],
            'image' => $imageName
        ];

        // Cập nhật giá
        if ($currentProduct['price'] != $_POST['price']) {
             $productModel->updatePrice($productId, $_POST['price']);
        }

        if ($productModel->updateProduct($productId, $productData)) {
            $_SESSION['success'] = 'Cập nhật sản phẩm thành công.';
            $this->redirect('/mystore/admin/products');
        } else {
            $_SESSION['error'] = 'Cập nhật sản phẩm thất bại.';
            $this->redirect('/mystore/admin/products/edit?id=' . $productId);
        }
    }
    
    // Xóa sản phẩm
    public function deleteProduct() {
        $id = $_GET['id'] ?? 0;
        $productModel = $this->loadModel('Product');
        $product = $productModel->getById($id);
        if ($productModel->delete($id)) {
            // Xóa file ảnh
            if ($product['image'] && file_exists('public/images/products/' . $product['image'])) {
                unlink('public/images/products/' . $product['image']);
            }
            $_SESSION['success'] = 'Xóa sản phẩm thành công.';
        } else {
            $_SESSION['error'] = 'Xóa sản phẩm thất bại. Có thể do ràng buộc khóa ngoại.';
        }
        $this->redirect('/mystore/admin/products');
    }
    // trong class AdminController của tệp mystore/app/controllers/AdminController.php

    public function orderDetail() {
        $orderId = $_GET['id'] ?? 0;
        if (!$orderId) {
            $this->redirect('/mystore/admin/orders');
            return;
        }

        $orderModel = $this->loadModel('Order');
        $order = $orderModel->getById($orderId);
        
        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng.';
            $this->redirect('/mystore/admin/orders');
            return;
        }
        
        // Lấy tổng tiền từ order_summary
        $order['totalAmount'] = $orderModel->getOrderSummary($orderId);
        $orderDetails = $orderModel->getOrderDetails($orderId);

        $data = [
            'title' => 'Chi tiết đơn hàng #' . $orderId,
            'order' => $order,
            'orderDetails' => $orderDetails,
        ];

        $this->loadView('admin/orders/detail', $data);
    }
    public function categories() {
        $categoryModel = $this->loadModel('Category');
        $categories = $categoryModel->getAll();
        $this->loadView('admin/categories/index', ['categories' => $categories]);
    }

    public function addCategory() {
        $this->loadView('admin/categories/add');
    }

    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            if (!empty($name)) {
                $categoryModel = $this->loadModel('Category');
                $categoryModel->create(['name' => $name]);
                $_SESSION['success'] = 'Thêm danh mục thành công.';
            } else {
                $_SESSION['error'] = 'Tên danh mục không được để trống.';
            }
        }
        $this->redirect('/mystore/admin/categories');
    }

    public function editCategory() {
        $id = $_GET['id'] ?? 0;
        $categoryModel = $this->loadModel('Category');
        $category = $categoryModel->getById($id);
        if ($category) {
            $this->loadView('admin/categories/edit', ['category' => $category]);
        } else {
            $_SESSION['error'] = 'Không tìm thấy danh mục.';
            $this->redirect('/mystore/admin/categories');
        }
    }

    public function updateCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['categoryId'] ?? 0;
            $name = $_POST['name'] ?? '';
            if (!empty($name) && !empty($id)) {
                $categoryModel = $this->loadModel('Category');
                $categoryModel->update($id, ['name' => $name]);
                $_SESSION['success'] = 'Cập nhật danh mục thành công.';
            } else {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            }
        }
        $this->redirect('/mystore/admin/categories');
    }

    public function deleteCategory() {
        $id = $_GET['id'] ?? 0;
        $categoryModel = $this->loadModel('Category');
        // Nâng cao: Nên kiểm tra xem danh mục có sản phẩm nào không trước khi xóa
        if ($categoryModel->delete($id)) {
            $_SESSION['success'] = 'Xóa danh mục thành công.';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục.';
        }
        $this->redirect('/mystore/admin/categories');
    }
    public function brands() {
        $brandModel = $this->loadModel('Brand');
        $brands = $brandModel->getAll();
        $this->loadView('admin/brands/index', ['brands' => $brands]);
    }

    public function addBrand() {
        $this->loadView('admin/brands/add');
    }

    public function storeBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            if (!empty($name)) {
                $brandModel = $this->loadModel('Brand');
                $brandModel->create(['name' => $name]);
                $_SESSION['success'] = 'Thêm thương hiệu thành công.';
            } else {
                $_SESSION['error'] = 'Tên thương hiệu không được để trống.';
            }
        }
        $this->redirect('/mystore/admin/brands');
    }

    public function editBrand() {
        $id = $_GET['id'] ?? 0;
        $brandModel = $this->loadModel('Brand');
        $brand = $brandModel->getById($id);
        if ($brand) {
            $this->loadView('admin/brands/edit', ['brand' => $brand]);
        } else {
            $_SESSION['error'] = 'Không tìm thấy thương hiệu.';
            $this->redirect('/mystore/admin/brands');
        }
    }

    public function updateBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['brandId'] ?? 0;
            $name = $_POST['name'] ?? '';
            if (!empty($name) && !empty($id)) {
                $brandModel = $this->loadModel('Brand');
                $brandModel->update($id, ['name' => $name]);
                $_SESSION['success'] = 'Cập nhật thương hiệu thành công.';
            } else {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            }
        }
        $this->redirect('/mystore/admin/brands');
    }

    public function deleteBrand() {
        $id = $_GET['id'] ?? 0;
        $brandModel = $this->loadModel('Brand');
        if ($brandModel->delete($id)) {
            $_SESSION['success'] = 'Xóa thương hiệu thành công.';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa thương hiệu.';
        }
        $this->redirect('/mystore/admin/brands');
    }
}