<?php
class AdminController extends Controller {
    public function __construct() {
        if (!$this->isLoggedIn() || !$this->isAdmin()) {
            $this->redirect('/mystore/login');
            exit();
        }
    }

    public function index() {
        $productModel = $this->loadModel('Product');
        $orderModel = $this->loadModel('Order');
        $userModel = $this->loadModel('User');
        
        $totalProducts = $productModel->countAll();
        $totalOrders = count($orderModel->getAllForAdmin());
        $totalUsers = count($userModel->getAll());
        $totalRevenue = $orderModel->getTotalRevenue();
        
        $data = [
            'title' => 'Quản trị hệ thống',
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
        ];
        
        $this->loadView('admin/dashboard', $data);
    }

    public function products() {
        $productModel = $this->loadModel('Product');
        $products = $productModel->getWithCategory();
        $this->loadView('admin/products/index', ['title' => 'Quản lý sản phẩm', 'products' => $products]);
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

    /**
     * VIẾT LẠI: Xử lý lưu sản phẩm và thuộc tính theo cách chuẩn hóa.
     */
    public function storeProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/admin/products');
            return;
        }

        // Xử lý upload ảnh
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "public/images/products/";
            $imageName = time() . '_' . basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $imageName;
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $_SESSION['error'] = 'Có lỗi khi tải ảnh lên.';
                $this->redirect('/mystore/admin/products/add');
                return;
            }
        }

        // Chuẩn bị dữ liệu cho bảng `product`
        $productData = [
            'name' => $_POST['name'],
            'categoryId' => $_POST['categoryId'],
            'brandId' => $_POST['brandId'],
            'stock_quantity' => $_POST['stock_quantity'],
            'image' => $imageName
        ];
        
        $price = preg_replace('/[^\d]/', '', $_POST['price']);

        // Chuẩn bị dữ liệu thuộc tính từ form
        $attributesData = [];
        // MODIFICATION: Read attributes from the 'description' JSON field
        if (isset($_POST['description']) && !empty($_POST['description'])) {
            $decodedDescription = json_decode($_POST['description'], true);
            if (is_array($decodedDescription)) {
                foreach ($decodedDescription as $attr) {
                    // Ensure 'key' and 'value' exist and are not empty
                    if (isset($attr['key']) && !empty($attr['key']) && isset($attr['value']) && !empty($attr['value'])) {
                        $attributesData[] = ['key' => trim($attr['key']), 'value' => trim($attr['value'])];
                    }
                }
            }
        }
        
        // Gọi phương thức mới trong Model để tạo sản phẩm và thuộc tính
        $productModel = $this->loadModel('Product');
        if ($productModel->createProductWithAttributes($productData, $price, $attributesData)) {
            $_SESSION['success'] = 'Thêm sản phẩm thành công.';
            $this->redirect('/mystore/admin/products');
        } else {
            $errorMessage = 'Thêm sản phẩm thất bại.';
            if (isset($_SESSION['sql_error'])) {
                // Ghi lỗi vào log để debug, không nên hiển thị cho người dùng cuối
                error_log('SQL Error in storeProduct: ' . $_SESSION['sql_error']);
                unset($_SESSION['sql_error']);
                $errorMessage .= ' Vui lòng liên hệ quản trị viên.';
            }
            $_SESSION['error'] = $errorMessage;
            $this->redirect('/mystore/admin/products/add');
        }
    }

    /**
     * VIẾT LẠI: Hiển thị form sửa với dữ liệu từ các bảng được JOIN.
     */
    public function editProduct() {
        $id = $_GET['id'] ?? 0;
        if(!$id) {
            $this->redirect('/mystore/admin/products');
            return;
        }

        $productModel = $this->loadModel('Product');
        $product = $productModel->getByIdWithDetails($id);

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
    
    /**
     * VIẾT LẠI: Xử lý cập nhật sản phẩm và thuộc tính.
     */
    public function updateProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/admin/products');
            return;
        }
        
        $productId = $_POST['productId'];
        $productModel = $this->loadModel('Product');
        $currentProduct = $productModel->getByIdWithDetails($productId);

        $imageName = $currentProduct['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "public/images/products/";
            $imageName = time() . '_' . basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $imageName;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                if ($currentProduct['image'] && file_exists($targetDir . $currentProduct['image'])) {
                    unlink($targetDir . $currentProduct['image']);
                }
            } else {
                $imageName = $currentProduct['image'];
            }
        }

        $productData = [
            'name' => $_POST['name'],
            'categoryId' => $_POST['categoryId'],
            'brandId' => $_POST['brandId'],
            'stock_quantity' => $_POST['stock_quantity'],
            'image' => $imageName
        ];

        $submittedPrice = preg_replace('/[^\d]/', '', $_POST['price']);
        $priceData = [
            'value' => $submittedPrice,
            'has_changed' => ($currentProduct['price'] != $submittedPrice)
        ];

        $attributesData = [];
        // MODIFICATION: Read attributes from the 'description' JSON field
        if (isset($_POST['description']) && !empty($_POST['description'])) {
            $decodedDescription = json_decode($_POST['description'], true);
            if (is_array($decodedDescription)) {
                foreach ($decodedDescription as $attr) {
                    // Ensure 'key' and 'value' exist and are not empty
                    if (isset($attr['key']) && !empty($attr['key']) && isset($attr['value']) && !empty($attr['value'])) {
                        $attributesData[] = ['key' => trim($attr['key']), 'value' => trim($attr['value'])];
                    }
                }
            }
        }

        if ($productModel->updateProductWithAttributes($productId, $productData, $priceData, $attributesData)) {
            $_SESSION['success'] = 'Cập nhật sản phẩm thành công.';
            $this->redirect('/mystore/admin/products');
        } else {
            $_SESSION['error'] = 'Cập nhật sản phẩm thất bại.';
            $this->redirect('/mystore/admin/products/edit?id=' . $productId);
        }
    }

    public function deleteProduct() {
        $id = $_GET['id'] ?? 0;
        $productModel = $this->loadModel('Product');
        $product = $productModel->getById($id); // Chỉ cần lấy thông tin cơ bản để xóa ảnh
        if ($productModel->delete($id)) { // Model nên tự xử lý xóa các record liên quan
            if ($product['image'] && file_exists('public/images/products/' . $product['image'])) {
                unlink('public/images/products/' . $product['image']);
            }
            $_SESSION['success'] = 'Xóa sản phẩm thành công.';
        } else {
            $_SESSION['error'] = 'Xóa sản phẩm thất bại. Có thể do ràng buộc khóa ngoại (sản phẩm đã có trong đơn hàng).';
        }
        $this->redirect('/mystore/admin/products');
    }

    public function orders() {
        $orderModel = $this->loadModel('Order');
        $orders = $orderModel->getAllForAdmin();
        $this->loadView('admin/orders/index', ['title' => 'Quản lý đơn hàng', 'orders' => $orders]);
    }

    public function orderDetail() {
        $orderId = $_GET['id'] ?? 0;
        if (!$orderId) {
            $this->redirect('/mystore/admin/orders');
            return;
        }

        $orderModel = $this->loadModel('Order');
        // Use the new getDetailedOrderById method to fetch all necessary order information
        $order = $orderModel->getDetailedOrderById($orderId);
        
        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng.';
            $this->redirect('/mystore/admin/orders');
            return;
        }
        
        // No need to call getOrderSummary separately anymore as it's included in getDetailedOrderById
        // $orderSummary = $orderModel->getOrderSummary($orderId); 
        // $order['totalAmount'] = $orderSummary['totalAmount'] ?? 0; 

        $orderDetails = $orderModel->getOrderDetails($orderId);

        $data = [
            'title' => 'Chi tiết đơn hàng #' . $orderId,
            'order' => $order,
            'orderDetails' => $orderDetails,
        ];

        $this->loadView('admin/orders/detail', $data);
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

    public function users() {
        $userModel = $this->loadModel('User');
        $users = $userModel->getAll();
        $this->loadView('admin/users/index', ['title' => 'Quản lý người dùng', 'users' => $users]);
    }

    // Các hàm CRUD cho Category
    public function categories() {
        $categoryModel = $this->loadModel('Category');
        $categories = $categoryModel->getAll();
        $this->loadView('admin/categories/index', ['title' => 'Quản lý danh mục', 'categories' => $categories]);
    }

    public function addCategory() {
        $this->loadView('admin/categories/add', ['title' => 'Thêm danh mục mới']);
    }

    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
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
            $this->loadView('admin/categories/edit', ['title' => 'Chỉnh sửa danh mục', 'category' => $category]);
        } else {
            $_SESSION['error'] = 'Không tìm thấy danh mục.';
            $this->redirect('/mystore/admin/categories');
        }
    }

    public function updateCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['categoryId'] ?? 0;
            $name = trim($_POST['name'] ?? '');
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
        if ($categoryModel->delete($id)) {
            $_SESSION['success'] = 'Xóa danh mục thành công.';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục. Có thể danh mục này đang được sử dụng.';
        }
        $this->redirect('/mystore/admin/categories');
    }

    // Các hàm CRUD cho Brand
    public function brands() {
        $brandModel = $this->loadModel('Brand');
        $brands = $brandModel->getAll();
        $this->loadView('admin/brands/index', ['title' => 'Quản lý thương hiệu', 'brands' => $brands]);
    }

    public function addBrand() {
        $this->loadView('admin/brands/add', ['title' => 'Thêm thương hiệu mới']);
    }

    public function storeBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
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
            $this->loadView('admin/brands/edit', ['title' => 'Chỉnh sửa thương hiệu', 'brand' => $brand]);
        } else {
            $_SESSION['error'] = 'Không tìm thấy thương hiệu.';
            $this->redirect('/mystore/admin/brands');
        }
    }

    public function updateBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['brandId'] ?? 0;
            $name = trim($_POST['name'] ?? '');
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
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa thương hiệu. Có thể thương hiệu này đang được sử dụng.';
        }
        $this->redirect('/mystore/admin/brands');
    }
    
    public function salesReport() {
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');

        $orderModel = $this->loadModel('Order');
        $salesData = $orderModel->getMonthlySales($year, $month);

        $totalMonthlyRevenue = array_reduce($salesData, function($sum, $item) {
            return $sum + $item['totalRevenue'];
        }, 0);

        $data = [
            'title' => 'Báo cáo doanh thu',
            'salesData' => $salesData,
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'totalMonthlyRevenue' => $totalMonthlyRevenue
        ];

        $this->loadView('admin/reports/sales', $data);
    }
}