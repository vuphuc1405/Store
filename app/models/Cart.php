<?php
class Cart extends Model {
    protected $table = 'cart';
    protected $primaryKey = 'cartId';

    public function getByUser($userId) {
        $query = "SELECT c.*, p.name, p.image, pp.price, (c.quantity * pp.price) as total
                  FROM cart c 
                  JOIN product p ON c.productId = p.productId 
                  LEFT JOIN product_price pp ON p.productId = pp.productId 
                  WHERE c.userId = :userId AND (pp.validTo IS NULL OR pp.validTo > NOW())
                  ORDER BY c.added_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        $query = "SELECT * FROM cart WHERE userId = :userId AND productId = :productId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $query = "UPDATE cart SET quantity = quantity + :quantity WHERE userId = :userId AND productId = :productId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':productId', $productId);
            return $stmt->execute();
        } else {
            return $this->create(['userId' => $userId, 'productId' => $productId, 'quantity' => $quantity]);
        }
    }

    public function updateQuantity($cartId, $quantity) {
        $query = "UPDATE cart SET quantity = :quantity WHERE cartId = :cartId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':cartId', $cartId);
        return $stmt->execute();
    }

    public function removeFromCart($cartId) {
        return $this->delete($cartId);
    }

    public function clearCart($userId) {
        $query = "DELETE FROM cart WHERE userId = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    public function getQuantityInCart($userId, $productId) {
        $query = "SELECT quantity FROM " . $this->table . " WHERE userId = :userId AND productId = :productId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['quantity'] : 0;
    }

    public function getCartItemCount($userId) {
        $query = "SELECT COUNT(cartId) as itemCount FROM " . $this->table . " WHERE userId = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['itemCount'] : 0;
    }
}