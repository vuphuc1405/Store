<?php
class CartController extends Controller {
    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/mystore/login');
            return;
        }
        
        $cartModel = $this->loadModel('Cart');
        $cartItems = $cartModel->getByUser($_SESSION['user_id']);
        
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['total'];
        }
        
        $data = [
            'title' => 'Giỏ hàng',
            'cartItems' => $cartItems,
            'total' => $total
        ];
        
        $this->loadView('cart/index', $data);
    }

    public function add() {
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
            return;
        }
        
        $productId = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;

        // --- LOGIC KIỂM TRA TỒN KHO ---
        $productModel = $this->loadModel('Product');
        $product = $productModel->getById($productId);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
            return;
        }

        $cartModel = $this->loadModel('Cart');
        $quantityInCart = $cartModel->getQuantityInCart($_SESSION['user_id'], $productId);
        $totalQuantityNeeded = $quantityInCart + $quantity;

        if ($product['stock_quantity'] < $totalQuantityNeeded) {
            echo json_encode(['success' => false, 'message' => 'Số lượng tồn kho không đủ!']);
            return;
        }
        // --- KẾT THÚC LOGIC KIỂM TRA ---
        
        if ($cartModel->addToCart($_SESSION['user_id'], $productId, $quantity)) {
            $newCount = $cartModel->getCartItemCount($_SESSION['user_id']);
            echo json_encode([
                'success' => true, 
                'message' => 'Đã thêm vào giỏ hàng', 
                'cartItemCount' => $newCount
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
    }

    public function update() {
    if (!$this->isLoggedIn()) {
        $this->redirect('/mystore/login');
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('/mystore/cart');
        return;
    }
    
    $cartId = $_POST['cartId'] ?? 0;
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($quantity < 1) {
        $quantity = 1;
    }
    
    $cartModel = $this->loadModel('Cart');
    $cartItem = $cartModel->getById($cartId);

    // Kiểm tra xem sản phẩm trong giỏ có thuộc về người dùng hiện tại không
    if (!$cartItem || $cartItem['userId'] != $_SESSION['user_id']) {
        $_SESSION['error'] = 'Yêu cầu không hợp lệ.';
        $this->redirect('/mystore/cart');
        return;
    }
    
    // === BẮT ĐẦU LOGIC KIỂM TRA TỒN KHO ===
    $productModel = $this->loadModel('Product');
    $product = $productModel->getById($cartItem['productId']);

    if (!$product) {
        $_SESSION['error'] = 'Sản phẩm không còn tồn tại.';
        $this->redirect('/mystore/cart');
        return;
    }

    if ($quantity > $product['stock_quantity']) {
        $_SESSION['error'] = 'Số lượng tồn kho không đủ!';
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/mystore/cart');
        return;
    }
    // === KẾT THÚC LOGIC KIỂM TRA TỒN KHO ===
    
    if ($cartModel->updateQuantity($cartId, $quantity)) {
        $_SESSION['success'] = 'Cập nhật giỏ hàng thành công';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật';
    }
    
    $this->redirect($_SERVER['HTTP_REFERER'] ?? '/mystore/cart');
}

    public function remove() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/mystore/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/cart');
            return;
        }
        
        $cartId = $_POST['cartId'] ?? 0;
        
        $cartModel = $this->loadModel('Cart');
        if ($cartModel->removeFromCart($cartId)) {
            $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra';
        }
        
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/mystore/cart');
    }
}