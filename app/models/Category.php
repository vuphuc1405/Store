<?php
class Category extends Model {
    protected $table = 'category';
    protected $primaryKey = 'categoryId';

    public function getWithProductCount() {
        $query = "SELECT c.*, COUNT(p.productId) as product_count 
                  FROM category c 
                  LEFT JOIN product p ON c.categoryId = p.categoryId 
                  GROUP BY c.categoryId";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}