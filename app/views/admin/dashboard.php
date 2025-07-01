<?php include 'app/views/admin/layouts/header.php'; ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Bảng điều khiển</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tổng quan</li>
    </ol>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="stats-title text-uppercase text-muted mb-2">Sản phẩm</h5>
                            <span class="stats-count font-weight-bold mb-0"><?php echo $totalProducts; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon text-primary">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="stats-title text-uppercase text-muted mb-2">Đơn hàng</h5>
                            <span class="stats-count font-weight-bold mb-0"><?php echo $totalOrders; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon text-success">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="stats-title text-uppercase text-muted mb-2">Người dùng</h5>
                            <span class="stats-count font-weight-bold mb-0"><?php echo $totalUsers; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon text-info">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="stats-title text-uppercase text-muted mb-2">Doanh thu</h5>
                            <span class="stats-count font-weight-bold mb-0"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon text-danger">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

<?php include 'app/views/admin/layouts/footer.php'; ?>