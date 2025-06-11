<?php
class AuthController extends Controller {
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('/mystore/home');
            return;
        }
        
        $data = ['title' => 'Đăng nhập'];
        $this->loadView('auth/login', $data);
    }

    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/login');
            return;
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/mystore/login');
            return;
        }
        
        $userModel = $this->loadModel('User');
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['userId'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['customer_name'] = $user['customer_name']; // Thêm tên thật vào session
            $_SESSION['role'] = $user['role'];
            $_SESSION['success'] = 'Đăng nhập thành công';
            
            if ($user['role'] === 'Admin') {
                $this->redirect('/mystore/admin');
            } else {
                $this->redirect('/mystore/home');
            }
        } else {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
            $this->redirect('/mystore/login');
        }
    }

    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect('/mystore/home');
            return;
        }
        
        $data = ['title' => 'Đăng ký'];
        $this->loadView('auth/register', $data);
    }

    public function doRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/register');
            return;
        }
        
        $data = [
            'customer_name' => $_POST['customer_name'] ?? '', // Lấy dữ liệu tên thật
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'password' => $_POST['password'] ?? ''
        ];
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($data['customer_name']) || empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc';
            $this->redirect('/mystore/register');
            return;
        }
        
        if ($data['password'] !== $confirmPassword) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp';
            $this->redirect('/mystore/register');
            return;
        }
        
        $userModel = $this->loadModel('User');
        if ($userModel->register($data)) {
            $_SESSION['success'] = 'Đăng ký thành công. Vui lòng đăng nhập';
            $this->redirect('/mystore/login');
        } else {
            $_SESSION['error'] = 'Tên đăng nhập hoặc email đã tồn tại';
            $this->redirect('/mystore/register');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/mystore/home');
    }
}