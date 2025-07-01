<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Admin - <?php echo $title ?? 'HPStore'; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        
        <link href="/mystore/public/css/admin_style.css" rel="stylesheet" />
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    </head>
    <body>
        <div id="layoutSidenav">
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav" id="sidenavAccordion">
        <a class="sidebar-brand" href="/mystore/home">
            <img src="/mystore/public/images/logo.svg" alt="Logo">
        </a>
        <div class="sb-sidenav-menu">
            <div class="nav flex-column">
                <a class="nav-link active" href="/mystore/admin">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Bảng điều khiển
                </a>
                
                <a class="nav-link" href="/mystore/admin/products">
                    <div class="sb-nav-link-icon"><i class="fas fa-box-open"></i></div>
                    Sản phẩm
                </a>
                <a class="nav-link" href="/mystore/admin/categories">
                    <div class="sb-nav-link-icon"><i class="fas fa-sitemap"></i></div>
                    Danh mục
                </a>
                <a class="nav-link" href="/mystore/admin/brands">
                    <div class="sb-nav-link-icon"><i class="fas fa-copyright"></i></div>
                    Thương hiệu
                </a>
                <a class="nav-link" href="/mystore/admin/orders">
                    <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                    Đơn hàng
                </a>
                <a class="nav-link" href="/mystore/admin/users">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Người dùng
                </a>
                <a class="nav-link" href="/mystore/admin/reports/sales">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                    Báo cáo doanh thu
                </a>
            </div>
        </div>
    </nav>
</div>
<div id="layoutSidenav_content">
    <header class="admin-header d-flex justify-content-end align-items-center">
        <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i> <?php echo htmlspecialchars($_SESSION['customer_name'] ?? ''); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="/mystore/profile">Tài khoản</a></li>
                <li><a class="dropdown-item" href="/mystore/home">Xem trang chủ</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="/mystore/logout">Đăng xuất</a></li>
            </ul>
        </div>
    </header>
<main>