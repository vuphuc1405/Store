<?php
class OrderController extends Controller {
    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/mystore/login');
            return;
        }
        $orderModel = $this->loadModel('Order');
        $orders = $orderModel->getByUser($_SESSION['user_id']);
        $this->loadView('orders/index', ['title' => 'Đơn hàng của tôi', 'orders' => $orders]);
    }

    public function detail() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/mystore/login');
            return;
        }
        $orderId = $_GET['id'] ?? 0;
        if (!$orderId) {
            $this->redirect('/mystore/orders');
            return;
        }
        $orderModel = $this->loadModel('Order');
        $order = $orderModel->getById($orderId);
        if (!$order || $order['userId'] != $_SESSION['user_id']) {
            $this->redirect('/mystore/orders');
            return;
        }
        $order['totalAmount'] = $orderModel->getOrderSummary($orderId);
        $orderDetails = $orderModel->getOrderDetails($orderId);
        $this->loadView('orders/detail', ['title' => 'Chi tiết đơn hàng #' . $orderId, 'order' => $order, 'orderDetails' => $orderDetails]);
    }
    
    public function checkout() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/mystore/login');
            return;
        }
        $cartModel = $this->loadModel('Cart');
        $cartItems = $cartModel->getByUser($_SESSION['user_id']);
        if (empty($cartItems)) {
            $this->redirect('/mystore/cart');
            return;
        }

        $userModel = $this->loadModel('User');
        $user = $userModel->getById($_SESSION['user_id']);

        $total = array_reduce($cartItems, fn($sum, $item) => $sum + $item['total'], 0);
        
        $this->loadView('orders/checkout', [
            'title' => 'Thanh toán', 
            'cartItems' => $cartItems, 
            'total' => $total,
            'currentUser' => $user
        ]);
    }

    public function create() {
        if (!$this->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/cart');
            return;
        }

        $addressData = [
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'shipping_phone' => trim($_POST['shipping_phone'] ?? ''),
            'shipping_address' => trim($_POST['shipping_address'] ?? '')
        ];

        if (empty($addressData['customer_name']) || empty($addressData['shipping_phone']) || empty($addressData['shipping_address'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin giao hàng.';
            $this->redirect('/mystore/checkout');
            return;
        }

        $cartModel = $this->loadModel('Cart');
        $cartItems = $cartModel->getByUser($_SESSION['user_id']);
        if (empty($cartItems)) {
            $this->redirect('/mystore/cart');
            return;
        }

        $orderModel = $this->loadModel('Order');
        $orderId = $orderModel->createOrder($_SESSION['user_id'], $cartItems, $addressData);

        if ($orderId) {
            $cartModel->clearCart($_SESSION['user_id']);
            $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng của bạn là #' . $orderId;
            $this->redirect('/mystore/orders/detail?id=' . $orderId);
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi đặt hàng, vui lòng thử lại.';
            $this->redirect('/mystore/checkout');
        }
    }

    /**
     * PHƯƠNG THỨC MỚI: Chuẩn bị trang thanh toán cho luồng "Mua Ngay"
     */
    public function buyNowCheckout() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/mystore/login');
            return;
        }

        $productId = $_GET['product_id'] ?? 0;
        $quantity = $_GET['quantity'] ?? 1;

        if ($quantity < 1) $quantity = 1;

        $productModel = $this->loadModel('Product');
        $product = $productModel->getByIdWithCategory($productId);

        if (!$product) {
            $_SESSION['error'] = 'Sản phẩm không hợp lệ.';
            $this->redirect('/mystore/products');
            return;
        }

        $buyNowItem = [
            'productId' => $product['productId'],
            'name' => $product['name'],
            'quantity' => $quantity,
            'price' => $product['price'],
            'total' => $product['price'] * $quantity,
            'image' => $product['image']
        ];
        
        $cartItems = [$buyNowItem];
        $total = $buyNowItem['total'];

        $userModel = $this->loadModel('User');
        $user = $userModel->getById($_SESSION['user_id']);

        $this->loadView('orders/checkout', [
            'title' => 'Thanh toán ngay', 
            'cartItems' => $cartItems, 
            'total' => $total,
            'currentUser' => $user,
            'isBuyNow' => true,
            'buyNowProduct' => ['id' => $productId, 'qty' => $quantity]
        ]);
    }

    /**
     * PHƯƠNG THỨC MỚI: Tạo đơn hàng từ luồng "Mua Ngay"
     */
    public function createBuyNowOrder() {
        if (!$this->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/home');
            return;
        }

        $addressData = [
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'shipping_phone' => trim($_POST['shipping_phone'] ?? ''),
            'shipping_address' => trim($_POST['shipping_address'] ?? '')
        ];
        if (empty($addressData['customer_name']) || empty($addressData['shipping_phone']) || empty($addressData['shipping_address'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin giao hàng.';
            $this->redirect('/mystore/buy-now?product_id=' . $_POST['product_id'] . '&quantity=' . $_POST['quantity']);
            return;
        }
        
        $productId = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        
        $productModel = $this->loadModel('Product');
        $product = $productModel->getByIdWithCategory($productId);
        
        if (!$product) {
             $_SESSION['error'] = 'Sản phẩm không hợp lệ.';
             $this->redirect('/mystore/products');
             return;
        }

        $cartItems = [[
            'productId' => $product['productId'],
            'quantity' => $quantity,
            'price' => $product['price']
        ]];
        
        $orderModel = $this->loadModel('Order');
        $orderId = $orderModel->createOrder($_SESSION['user_id'], $cartItems, $addressData);

        if ($orderId) {
            $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng của bạn là #' . $orderId;
            $this->redirect('/mystore/orders/detail?id=' . $orderId);
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi đặt hàng, vui lòng thử lại.';
            $this->redirect('/mystore/buy-now?product_id=' . $productId . '&quantity=' . $quantity);
        }
    }
}