<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'QuickCart Store'; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/mystore/public/css/style.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container">
                <a class="navbar-brand" href="/mystore/home">
                     <img src="/mystore/public/images/logo.svg" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="/mystore/home">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/mystore/products">Sản phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/mystore/about">Giới thiệu</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="/mystore/contact">Liên hệ</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="/mystore/warranty">Bảo Hành</a>
                        </li>
                    </ul>
                    <div class="header-actions d-flex align-items-center">
                        <form class="d-flex search-form" action="/mystore/products" method="GET">
                            <input class="form-control" type="search" name="search" placeholder="Tìm kiếm sản phẩm..." aria-label="Search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                        </form>
                        
                        <a href="/mystore/cart" class="header-icon position-relative ms-10">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-item-count-badge" 
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                style="<?php echo ($cartItemCount ?? 0) > 0 ? '' : 'display: none;'; ?>">
                                <?php echo $cartItemCount ?? 0; ?>
                            </span>
                        </a>
                        
                         <?php if(isset($_SESSION['user_id'])): ?>
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle header-icon d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i>
                                    <span class="ms-2"><?php echo htmlspecialchars($_SESSION['username'] ?? $_SESSION['username']); ?></span>
                                </a>
                               
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <span class="dropdown-item-text w-100 text-center">
                                            <strong><?php echo htmlspecialchars($_SESSION['customer_name'] ?? $_SESSION['username']); ?></strong>
                                        </span>
                                    </li>
                                     <li><hr class="dropdown-divider"></li>
                                    <?php if($_SESSION['role'] === 'Admin'): ?>
                                        <li><a class="dropdown-item" href="/mystore/admin">Trang quản trị</a></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="/mystore/profile">Quản lý tài khoản</a></li>
                                    <li><a class="dropdown-item" href="/mystore/orders">Lịch sử đơn hàng</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/mystore/logout">Đăng xuất</a></li>
                                </ul>
                            </div>
                       <?php else: ?>
                            <a href="/mystore/login" class="btn btn-primary btn-sm ms-2">
                                <i class="fas fa-right-to-bracket me-1"></i> Đăng nhập
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div style="position: fixed; top: 80px; right: 20px; z-index: 1050; min-width: 250px;">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>