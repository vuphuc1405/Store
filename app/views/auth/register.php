<?php include 'app/views/layouts/header.php'; ?>

<div class="container auth-container">
     <div class="auth-form-wrapper">
        <div class="text-center mb-5">
            <h2 class="display-6">Tạo tài khoản</h2>
            <p class="text-muted">Bắt đầu hành trình mua sắm của bạn.</p>
        </div>
        
        <form method="POST" action="/mystore/register">
            <div class="form-floating mb-3">
                 <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Họ và tên" required>
                 <label for="customer_name">Họ và tên *</label>
            </div>
            <div class="form-floating mb-3">
                 <input type="text" class="form-control" id="username" name="username" placeholder="Tên đăng nhập" required>
                 <label for="username">Tên đăng nhập *</label>
            </div>
            <div class="form-floating mb-3">
                 <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                 <label for="email">Email *</label>
            </div>
             <div class="form-floating mb-3">
                 <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                 <label for="password">Mật khẩu *</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                <label for="confirm_password">Xác nhận mật khẩu *</label>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Đăng ký</button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted">Đã có tài khoản? <a href="/mystore/login" class="text-decoration-none fw-bold">Đăng nhập</a></p>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>