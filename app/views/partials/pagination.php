<?php if (isset($totalPages) && $totalPages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center mt-5">
        <?php
        // Lấy đường dẫn cơ sở của URL, ví dụ: /mystore/products
        $basePath = strtok($_SERVER["REQUEST_URI"], '?');

        // Lấy tất cả các tham số truy vấn hiện tại ngoại trừ 'page'
        $queryParams = $_GET;
        unset($queryParams['page']);

        // Xây dựng chuỗi truy vấn cho các bộ lọc hiện có (ví dụ: search=... hoặc id=...)
        $queryString = http_build_query($queryParams);

        // Hàm để tạo URL cuối cùng cho một số trang nhất định
        function generate_pagination_url($basePath, $queryString, $pageNumber) {
            if (!empty($queryString)) {
                // Nếu có các tham số khác, bắt đầu với chúng và nối thêm tham số trang
                return $basePath . '?' . $queryString . '&page=' . $pageNumber;
            } else {
                // Nếu không có tham số khác, chỉ cần thêm tham số trang
                return $basePath . '?page=' . $pageNumber;
            }
        }

        // Nút Previous
        if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo generate_pagination_url($basePath, $queryString, $currentPage - 1); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">&laquo;</a>
            </li>
        <?php endif; ?>

        <?php
        // Các nút số trang
        $window = 1; // Số link hiển thị xung quanh trang hiện tại
        $lastPageRendered = 0;
        for ($i = 1; $i <= $totalPages; $i++):
            if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $window && $i <= $currentPage + $window)):
                if ($lastPageRendered != 0 && $i > $lastPageRendered + 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
        ?>
            <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo generate_pagination_url($basePath, $queryString, $i); ?>"><?php echo $i; ?></a>
            </li>
        <?php
            $lastPageRendered = $i;
            endif;
        endfor;
        
        // Hiển thị dấu '...' và trang cuối nếu cần
        if ($lastPageRendered < $totalPages - 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        if ($lastPageRendered < $totalPages) {
             echo '<li class="page-item"><a class="page-link" href="' . generate_pagination_url($basePath, $queryString, $totalPages) . '">' . $totalPages . '</a></li>';
        }
        ?>

        <?php // Nút Next
        if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo generate_pagination_url($basePath, $queryString, $currentPage + 1); ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">&raquo;</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>