<?php include 'app/views/layouts/header.php'; ?>

<div class="container section-padding">
    <h1 class="section-title" style="text-align: left;"><?php echo $title; ?></h1>

    <ul class="nav nav-pills mb-4" id="profile-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="pill" data-bs-target="#info-pane" type="button" role="tab" aria-controls="info-pane" aria-selected="true">
                <i class="fas fa-user-edit me-2"></i>Thông tin tài khoản
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="pill" data-bs-target="#password-pane" type="button" role="tab" aria-controls="password-pane" aria-selected="false">
                <i class="fas fa-key me-2"></i>Đổi mật khẩu
            </button>
        </li>
    </ul>

    <div class="tab-content" id="profile-tabContent">
        <div class="tab-pane fade show active" id="info-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h5 class="card-title mb-4">Cập nhật thông tin tài khoản</h5>
                    <form action="/mystore/profile/update" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Tên đăng nhập</label>
                                <input type="text" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" disabled readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Họ và tên</label>
                                <input type="text" id="customer_name" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($user['customer_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                             <div class="col-md-6">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Nhập địa chỉ để dùng làm mặc định khi thanh toán"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Lưu thay đổi</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="password-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                     <h5 class="card-title mb-4">Thay đổi mật khẩu</h5>
                    <form action="/mystore/profile/change-password" method="POST">
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Mật khẩu cũ</label>
                            <input type="password" id="old_password" name="old_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>