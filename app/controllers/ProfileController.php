<?php
class ProfileController extends Controller {

    public function __construct() {
        // Đảm bảo người dùng đã đăng nhập trước khi truy cập bất kỳ phương thức nào
        if (!$this->isLoggedIn()) {
            $this->redirect('/mystore/login');
            exit();
        }
    }

    /**
     * Hiển thị trang thông tin cá nhân với thông tin của người dùng hiện tại.
     */
    public function index() {
        $userModel = $this->loadModel('User');
        $user = $userModel->getById($_SESSION['user_id']);

        $data = [
            'title' => 'Thông tin cá nhân',
            'user' => $user
        ];
        $this->loadView('profile/index', $data);
    }

    /**
     * Xử lý việc cập nhật thông tin cơ bản (họ tên, email).
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/profile');
            return;
        }

        $userModel = $this->loadModel('User');
        $userId = $_SESSION['user_id'];

        $data = [
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' =>trim($_POST['address'] ?? '')
        ];

        if (empty($data['customer_name']) || empty($data['email'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ họ tên và email.';
            $this->redirect('/mystore/profile');
            return;
        }

        if ($userModel->update($userId, $data)) {
            $_SESSION['success'] = 'Cập nhật thông tin thành công.';
            // Cập nhật lại tên trong session để hiển thị trên header
            $_SESSION['customer_name'] = $data['customer_name'];
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, không thể cập nhật thông tin.';
        }

        $this->redirect('/mystore/profile');
    }

    /**
     * Xử lý việc thay đổi mật khẩu.
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mystore/profile');
            return;
        }

        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ các trường mật khẩu.';
            $this->redirect('/mystore/profile');
            return;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            $this->redirect('/mystore/profile');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Mật khẩu mới không khớp.';
            $this->redirect('/mystore/profile');
            return;
        }

        $userModel = $this->loadModel('User');
        $userId = $_SESSION['user_id'];

        if ($userModel->changePassword($userId, $oldPassword, $newPassword)) {
            $_SESSION['success'] = 'Thay đổi mật khẩu thành công.';
        } else {
            $_SESSION['error'] = 'Mật khẩu cũ không đúng hoặc đã có lỗi xảy ra.';
        }

        $this->redirect('/mystore/profile');
    }
}