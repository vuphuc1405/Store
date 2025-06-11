<?php
class Review extends Model {
    protected $table = 'product_reviews';
    protected $primaryKey = 'reviewId';

    public function getByProductId($productId) {
        $query = "SELECT r.*, u.customer_name 
                  FROM " . $this->table . " r
                  JOIN user u ON r.userId = u.userId
                  WHERE r.productId = :productId
                  ORDER BY r.createdAt DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($productId) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(reviewId) as review_count
                  FROM " . $this->table . "
                  WHERE productId = :productId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAverageRatingsForProducts(array $productIds) {
        if (empty($productIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $query = "SELECT productId, AVG(rating) as avg_rating, COUNT(reviewId) as review_count
                  FROM " . $this->table . "
                  WHERE productId IN ($placeholders)
                  GROUP BY productId";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($productIds);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ratingsByProduct = [];
        foreach ($results as $result) {
            $ratingsByProduct[$result['productId']] = $result;
        }
        return $ratingsByProduct;
    }


    public function hasUserReviewedProduct($userId, $productId) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE userId = :userId AND productId = :productId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}