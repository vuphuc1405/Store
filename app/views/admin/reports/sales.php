<?php include 'app/views/admin/layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Báo cáo Doanh thu theo Tháng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/mystore/admin">Bảng điều khiển</a></li>
        <li class="breadcrumb-item active">Báo cáo doanh thu</li>
    </ol>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Chọn thời gian</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="/mystore/admin/reports/sales" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="month" class="form-label">Tháng</label>
                    <select name="month" id="month" class="form-select">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo ($selectedMonth == $m) ? 'selected' : ''; ?>>
                                Tháng <?php echo $m; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="year" class="form-label">Năm</label>
                    <select name="year" id="year" class="form-select">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo ($selectedYear == $y) ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Xem báo cáo</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                Kết quả kinh doanh tháng <?php echo htmlspecialchars($selectedMonth); ?>/<?php echo htmlspecialchars($selectedYear); ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($salesData)): ?>
                <div class="text-center p-5">
                    <p class="h4">Không có dữ liệu cho tháng đã chọn.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên sản phẩm</th>
                                <th class="text-center">Số lượng bán</th>
                                <th class="text-end">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesData as $index => $row): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($row['productName']); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($row['totalQuantity']); ?></td>
                                <td class="text-end fw-bold"><?php echo number_format($row['totalRevenue'], 0, ',', '.'); ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="3" class="text-end h5"><strong>Tổng doanh thu tháng:</strong></td>
                                <td class="text-end h5 fw-bolder text-danger">
                                    <?php echo number_format($totalMonthlyRevenue, 0, ',', '.'); ?>đ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'app/views/admin/layouts/footer.php'; ?>  