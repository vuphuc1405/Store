<?php include 'app/views/layouts/header.php'; ?>

<div class="container auth-container">
    <div class="auth-form-wrapper">
        <div class="text-center mb-5">
            <h2 class="display-6">Đăng nhập</h2>
        </div>
        
        <form method="POST" action="/mystore/login">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Tên đăng nhập" required>
                <label for="username">Tên đăng nhập</label>
            </div>
            
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                <label for="password">Mật khẩu</label>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Đăng nhập</button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted">Chưa có tài khoản? <a href="/mystore/register" class="text-decoration-none fw-bold">Đăng ký ngay</a></p>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>