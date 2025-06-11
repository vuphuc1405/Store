<?php
class Order extends Model {
    protected $table = 'order_base';
    protected $primaryKey = 'orderId';

    /**
     * Lấy tất cả đơn hàng cho trang admin, bao gồm tổng tiền.
     */
    public function getAllForAdmin() {
        $query = "SELECT ob.*, os.totalAmount 
                  FROM order_base ob 
                  LEFT JOIN order_summary os ON ob.orderId = os.orderId 
                  ORDER BY ob.orderDate DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createOrder($userId, $cartItems, $addressData) {
        // Tải model Product một lần để tái sử dụng
        require_once 'app/models/Product.php';
        $productModel = new Product(); 

        try {
            $this->db->beginTransaction();
            
            // BƯỚC 1: KIỂM TRA VÀ TRỪ KHO
            foreach ($cartItems as $item) {
                $stockUpdated = $productModel->decreaseStock($item['productId'], $item['quantity']);
                if (!$stockUpdated) {
                    $this->db->rollBack();
                    $product = $productModel->getById($item['productId']);
                    $_SESSION['error'] = "Rất tiếc, sản phẩm \"" . ($product['name'] ?? 'Không xác định') . "\" không đủ số lượng tồn kho.";
                    return false;
                }
            }

            // BƯỚC 2: TẠO ĐƠN HÀNG (NẾU TẤT CẢ SẢN PHẨM ĐỀU ĐỦ HÀNG)
            $query = "INSERT INTO order_base (userId, status, customer_name, shipping_phone, shipping_address) 
                      VALUES (:userId, :status, :customer_name, :shipping_phone, :shipping_address)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':userId', $userId);
            $stmt->bindValue(':status', 'Pending');
            $stmt->bindValue(':customer_name', $addressData['customer_name']);
            $stmt->bindValue(':shipping_phone', $addressData['shipping_phone']);
            $stmt->bindValue(':shipping_address', $addressData['shipping_address']);
            $stmt->execute();
            
            $orderId = $this->db->lastInsertId();
            $totalAmount = 0;
            
            foreach ($cartItems as $item) {
                $queryDetail = "INSERT INTO orderdetail_base (orderId, productId, quantity) VALUES (:orderId, :productId, :quantity)";
                $stmtDetail = $this->db->prepare($queryDetail);
                $stmtDetail->bindParam(':orderId', $orderId);
                $stmtDetail->bindParam(':productId', $item['productId']);
                $stmtDetail->bindParam(':quantity', $item['quantity']);
                $stmtDetail->execute();
                
                $orderDetailId = $this->db->lastInsertId();
                
                $queryPrice = "INSERT INTO orderdetail_price (orderDetailId, price) VALUES (:orderDetailId, :price)";
                $stmtPrice = $this->db->prepare($queryPrice);
                $stmtPrice->bindParam(':orderDetailId', $orderDetailId);
                $stmtPrice->bindParam(':price', $item['price']);
                $stmtPrice->execute();
                
                $totalAmount += $item['price'] * $item['quantity'];
            }
            
            $querySummary = "INSERT INTO order_summary (orderId, totalAmount) VALUES (:orderId, :totalAmount)";
            $stmtSummary = $this->db->prepare($querySummary);
            $stmtSummary->bindParam(':orderId', $orderId);
            $stmtSummary->bindParam(':totalAmount', $totalAmount);
            $stmtSummary->execute();
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log($e->getMessage());
            return false;
        }
    }
    public function getByUser($userId) {
        $query = "SELECT ob.*, os.totalAmount 
                  FROM order_base ob 
                  LEFT JOIN order_summary os ON ob.orderId = os.orderId 
                  WHERE ob.userId = :userId 
                  ORDER BY ob.orderDate DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($orderId) {
        $query = "SELECT od.*, p.name, p.image, odp.price, (od.quantity * odp.price) as total
                  FROM orderdetail_base od 
                  JOIN product p ON od.productId = p.productId 
                  LEFT JOIN orderdetail_price odp ON od.orderDetailId = odp.orderDetailId 
                  WHERE od.orderId = :orderId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($orderId, $status) {
        $query = "UPDATE order_base SET status = :status WHERE orderId = :orderId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':orderId', $orderId);
        return $stmt->execute();
    }

    public function getOrderSummary($orderId) {
        $query = "SELECT totalAmount FROM order_summary WHERE orderId = :orderId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['totalAmount'] : 0;
    }
}