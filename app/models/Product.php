<?php
class Product extends Model {
    protected $table = 'product';
    protected $primaryKey = 'productId';

   public function getWithCategory() {
    $query = "SELECT p.*, c.name as category_name, b.name as brand_name, pp.price 
              FROM product p 
              LEFT JOIN category c ON p.categoryId = c.categoryId 
              LEFT JOIN brands b ON p.brandId = b.brandId
              LEFT JOIN product_price pp ON p.productId = pp.productId 
              WHERE pp.validTo IS NULL OR pp.validTo > NOW()
              ORDER BY p.productId DESC";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getByIdWithCategory($id) {
        $query = "SELECT p.*, c.name as category_name, pp.price 
                  FROM product p 
                  LEFT JOIN category c ON p.categoryId = c.categoryId 
                  LEFT JOIN product_price pp ON p.productId = pp.productId 
                  WHERE p.productId = :id AND (pp.validTo IS NULL OR pp.validTo > NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCategory($categoryId) {
        $query = "SELECT p.*, c.name as category_name, pp.price 
                  FROM product p 
                  LEFT JOIN category c ON p.categoryId = c.categoryId 
                  LEFT JOIN product_price pp ON p.productId = pp.productId 
                  WHERE p.categoryId = :categoryId AND (pp.validTo IS NULL OR pp.validTo > NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':categoryId', $categoryId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($keyword) {
        $query = "SELECT p.*, c.name as category_name, pp.price 
                  FROM product p 
                  LEFT JOIN category c ON p.categoryId = c.categoryId 
                  LEFT JOIN product_price pp ON p.productId = pp.productId 
                  WHERE (p.name LIKE :keyword OR p.description LIKE :keyword) 
                  AND (pp.validTo IS NULL OR pp.validTo > NOW())";
        $stmt = $this->db->prepare($query);
        $keyword = "%$keyword%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePrice($productId, $price) {
        try {
            $this->db->beginTransaction();
            
            $query = "UPDATE product_price SET validTo = NOW() WHERE productId = :productId AND validTo IS NULL";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
            
            $query = "INSERT INTO product_price (productId, price, validFrom) VALUES (:productId, :price, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':price', $price);
            $stmt->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getAllCategories() {
        $query = "SELECT * FROM category";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createProduct($productData, $price) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO product (name, description, categoryId, image, stock_quantity) VALUES (:name, :description, :categoryId, :image, :stock_quantity)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $productData['name']);
            $stmt->bindParam(':description', $productData['description']);
            $stmt->bindParam(':categoryId', $productData['categoryId']);
            $stmt->bindParam(':image', $productData['image']);
            $stmt->bindParam(':stock_quantity', $productData['stock_quantity']);
            $stmt->execute();
            
            $productId = $this->db->lastInsertId();

            $queryPrice = "INSERT INTO product_price (productId, price) VALUES (:productId, :price)";
            $stmtPrice = $this->db->prepare($queryPrice);
            $stmtPrice->bindParam(':productId', $productId);
            $stmtPrice->bindParam(':price', $price);
            $stmtPrice->execute();
            
            $this->db->commit();
            return $productId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateProduct($id, $data) {
        return $this->update($id, $data);
    }

    public function decreaseStock($productId, $quantity) {
        $query = "UPDATE " . $this->table . " 
                  SET stock_quantity = stock_quantity - :quantity 
                  WHERE productId = :productId AND stock_quantity >= :quantity";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}