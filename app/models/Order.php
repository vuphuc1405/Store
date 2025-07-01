<?php

class Order extends Model
{
    /* -------------------------------------------------
       Cấu hình bảng và khóa chính
    --------------------------------------------------*/
    protected $table      = 'order_base';
    protected $primaryKey = 'orderId';

    /* -------------------------------------------------
       1) TẠO ĐƠN HÀNG MỚI
       - Lưu shipping_info  → lấy shippingId
       - Lưu order_base     → lấy orderId
       - Lưu chi tiết (orderdetail_base + orderdetail_price)
       - Trừ kho (Product::decreaseStock)
       - Triggers trong DB tự cập nhật order_summary
    --------------------------------------------------*/
    public function createOrder(int $userId, array $cartItems, array $addressData)
    {
        require_once 'app/models/Product.php';
        $productModel = new Product();

        try {
            $this->db->beginTransaction();

            /* 1. Trừ kho */
            foreach ($cartItems as $item) {
                if (!$productModel->decreaseStock($item['productId'], $item['quantity'])) {
                    $this->db->rollBack();
                    $_SESSION['error'] = 'Không đủ tồn kho cho sản phẩm ID: ' . $item['productId'];
                    return false;
                }
            }

            /* 2. Lưu shipping_info */
            $sqlShip = "INSERT INTO shipping_info (userId, customer_name, shipping_phone, shipping_address)
                        VALUES (:uid, :cname, :phone, :addr)";
            $this->db->prepare($sqlShip)->execute([
                ':uid'   => $userId,
                ':cname' => $addressData['customer_name'],
                ':phone' => $addressData['shipping_phone'],
                ':addr'  => $addressData['shipping_address']
            ]);
            $shippingId = $this->db->lastInsertId();

            /* 3. Tạo order_base */
            $sqlOrder = "INSERT INTO order_base (userId, shippingId, status)
                         VALUES (:uid, :shipId, :status)";
            $this->db->prepare($sqlOrder)->execute([
                ':uid'    => $userId,
                ':shipId' => $shippingId,
                ':status' => 'Chờ xử lý'
            ]);
            $orderId = $this->db->lastInsertId();

            /* 4. Lưu chi tiết đơn hàng */
            $sqlDetail = "INSERT INTO orderdetail_base (orderId, productId, quantity)
                          VALUES (:oid, :pid, :qty)";
            $sqlPrice  = "INSERT INTO orderdetail_price (orderDetailId, price)
                          VALUES (:odid, :price)";
            $stmtDetail = $this->db->prepare($sqlDetail);
            $stmtPrice  = $this->db->prepare($sqlPrice);

            foreach ($cartItems as $item) {
                /* 4.1 orderdetail_base */
                $stmtDetail->execute([
                    ':oid' => $orderId,
                    ':pid' => $item['productId'],
                    ':qty' => $item['quantity']
                ]);
                $orderDetailId = $this->db->lastInsertId();

                /* 4.2 orderdetail_price */
                $stmtPrice->execute([
                    ':odid' => $orderDetailId,
                    ':price'=> $item['price']
                ]);
            }

            /* 5. Commit – triggers sẽ tự ghi order_summary */
            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['sql_error'] = $e->getMessage();
            return false;
        }
    }

    /* -------------------------------------------------
       2) CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
    --------------------------------------------------*/
    public function updateStatus(int $orderId, string $status): bool
    {
        $sql = "UPDATE order_base SET status = :status WHERE orderId = :oid";
        return $this->db->prepare($sql)->execute([
            ':status' => $status,
            ':oid'    => $orderId
        ]);
    }

    /* -------------------------------------------------
       3) LẤY DANH SÁCH ĐƠN HÀNG (ADMIN)
    --------------------------------------------------*/
    public function getAll(): array
    {
        $sql = "SELECT ob.*, si.customer_name, si.shipping_phone, si.shipping_address, os.totalAmount
                FROM order_base ob
                JOIN shipping_info si ON ob.shippingId = si.shippingId
                LEFT JOIN order_summary os ON ob.orderId = os.orderId
                ORDER BY ob.orderId DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -------------------------------------------------
       4) LẤY ĐƠN HÀNG CỦA NGƯỜI DÙNG
    --------------------------------------------------*/
    public function getByUser(int $userId): array
    {
        $sql = "SELECT ob.*, si.customer_name, si.shipping_phone, si.shipping_address, os.totalAmount
                FROM order_base ob
                JOIN shipping_info si ON ob.shippingId = si.shippingId
                LEFT JOIN order_summary os ON ob.orderId = os.orderId
                WHERE ob.userId = :uid
                ORDER BY ob.orderId DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -------------------------------------------------
       5) LẤY CHI TIẾT ĐƠN HÀNG
    --------------------------------------------------*/
    public function getOrderDetails(int $orderId): array
    {
        $sql = "SELECT odb.*, odp.price,
                       p.name  AS product_name,
                       p.image AS product_image
                FROM orderdetail_base  odb
                JOIN orderdetail_price odp ON odb.orderDetailId = odp.orderDetailId
                JOIN product p             ON odb.productId     = p.productId
                WHERE odb.orderId = :oid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':oid' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -------------------------------------------------
       6) LẤY TỔNG TIỀN ĐƠN HÀNG (order_summary)
    --------------------------------------------------*/
    public function getOrderSummary(int $orderId): ?array
    {
        $sql  = "SELECT * FROM order_summary WHERE orderId = :oid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':oid' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* -------------------------------------------------
       7) LẤY DOANH THU HÀNG THÁNG CHO BÁO CÁO
    --------------------------------------------------*/
    public function getMonthlySales(int $year, int $month): array
    {
        $sql = "SELECT 
                    p.name AS productName,
                    SUM(odb.quantity) AS totalQuantity,
                    SUM(odp.price * odb.quantity) AS totalRevenue
                FROM order_base ob
                JOIN orderdetail_base odb ON ob.orderId = odb.orderId
                JOIN orderdetail_price odp ON odb.orderDetailId = odp.orderDetailId
                JOIN product p ON odb.productId = p.productId
                WHERE YEAR(ob.orderDate) = :year AND MONTH(ob.orderDate) = :month
                AND ob.status = 'Đã giao'
                GROUP BY p.productId, p.name
                ORDER BY totalRevenue DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -------------------------------------------------
       8) LẤY TỔNG DOANH THU TOÀN BỘ (ADMIN DASHBOARD)
    --------------------------------------------------*/
   public function getTotalRevenue(): float
    {
        $sql = "SELECT SUM(os.totalAmount) AS grandTotalRevenue 
                FROM order_summary os
                JOIN order_base ob ON os.orderId = ob.orderId
                WHERE ob.status = 'Đã giao'"; // Chỉ tính tổng các đơn hàng có trạng thái 'Đã giao'
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['grandTotalRevenue'] ?? 0);
    }

    /* -------------------------------------------------
       9) LẤY TẤT CẢ ĐƠN HÀNG CHO ADMIN (có totalAmount)
    --------------------------------------------------*/
    public function getAllForAdmin(): array
    {
        $sql = "SELECT ob.*, si.customer_name, si.shipping_phone, si.shipping_address, os.totalAmount
                FROM order_base ob
                JOIN shipping_info si ON ob.shippingId = si.shippingId
                LEFT JOIN order_summary os ON ob.orderId = os.orderId
                ORDER BY ob.orderId DESC";
        $stmt = $this->db->query($sql); // Use query() for no params, or prepare/execute if params are needed
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -------------------------------------------------
       10) LẤY CHI TIẾT ĐƠN HÀNG ĐẦY ĐỦ (CHO ADMIN)
       Bao gồm order_base, shipping_info, và order_summary
    --------------------------------------------------*/
    public function getDetailedOrderById(int $orderId): ?array
    {
        $sql = "SELECT ob.*, si.customer_name, si.shipping_phone, si.shipping_address, os.totalAmount
                FROM order_base ob
                JOIN shipping_info si ON ob.shippingId = si.shippingId
                LEFT JOIN order_summary os ON ob.orderId = os.orderId
                WHERE ob.orderId = :orderId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}