<?php include 'app/views/layouts/header.php'; ?>

<div class="container section-padding">
    <h1 class="section-title" style="text-align: center;"><?php echo $title; ?></h1>
    <div class="row g-5 justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-5">
                    <h4 class="mb-4">Thông tin liên hệ</h4>
                    <ul class="list-unstyled">
                        <li class="d-flex mb-3 align-items-start">
                            <i class="fas fa-map-marker-alt fa-lg text-primary mt-1 me-3"></i>
                            <div>
                                <strong>Địa chỉ:</strong><br>
                                123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh
                            </div>
                        </li>
                        <li class="d-flex mb-3 align-items-start">
                            <i class="fas fa-phone-alt fa-lg text-primary mt-1 me-3"></i>
                            <div>
                                <strong>Điện thoại:</strong><br>
                                <a href="tel:0987654321" class="text-decoration-none text-dark">0987-654-321</a>
                            </div>
                        </li>
                        <li class="d-flex mb-3 align-items-start">
                            <i class="fas fa-envelope fa-lg text-primary mt-1 me-3"></i>
                            <div>
                                <strong>Email:</strong><br>
                                <a href="mailto:info@hpstore.com" class="text-decoration-none text-dark">info@hpstore.com</a>
                            </div>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-clock fa-lg text-primary mt-1 me-3"></i>
                            <div>
                                <strong>Giờ làm việc:</strong><br>
                                Thứ 2 - Chủ Nhật: 8:00 AM - 9:00 PM
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>