<?php
class Product extends Model {
    protected $table = 'product';
    protected $primaryKey = 'productId';

    /**
     * Sửa đổi để lấy thông tin sản phẩm cùng với danh mục và thương hiệu.
     * Thông số kỹ thuật sẽ được lấy riêng trong phương thức khác để tối ưu.
     */
    public function getWithCategory($limit = null, $offset = null) {
        $query = "SELECT p.*, c.name as category_name, b.name as brand_name, pp.price 
                  FROM product p 
                  LEFT JOIN category c ON p.categoryId = c.categoryId 
                  LEFT JOIN brands b ON p.brandId = b.brandId
                  LEFT JOIN product_price pp ON p.productId = pp.productId 
                  WHERE pp.validTo IS NULL OR pp.validTo > NOW()
                  ORDER BY p.productId DESC";

        if ($limit !== null && $offset !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($query);

        if ($limit !== null && $offset !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * VIẾT LẠI: Lấy thông tin chi tiết của một sản phẩm, bao gồm cả các thông số kỹ thuật
     * bằng cách JOIN với các bảng attributes.
     */
    public function getByIdWithDetails($id) {
        // Lấy thông tin cơ bản của sản phẩm
        $query = "SELECT p.*, c.name as category_name, b.name as brand_name, pp.price 
                  FROM product p 
                  LEFT JOIN category c ON p.categoryId = c.categoryId 
                  LEFT JOIN brands b ON p.brandId = b.brandId
                  LEFT JOIN product_price pp ON p.productId = pp.productId 
                  WHERE p.productId = :id AND (pp.validTo IS NULL OR pp.validTo > NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return false;
        }

        // Lấy các thông số kỹ thuật (attributes)
        $queryAttrs = "SELECT a.name, pa.value 
                       FROM product_attributes pa
                       JOIN attributes a ON pa.attributeId = a.attributeId
                       WHERE pa.productId = :productId";
        $stmtAttrs = $this->db->prepare($queryAttrs);
        $stmtAttrs->bindParam(':productId', $id, PDO::PARAM_INT);
        $stmtAttrs->execute();
        $product['attributes'] = $stmtAttrs->fetchAll(PDO::FETCH_ASSOC);

        return $product;
    }

    /**
     * VIẾT LẠI HOÀN TOÀN: Tạo sản phẩm mới cùng với các thông số kỹ thuật.
     * Sử dụng transaction để đảm bảo toàn vẹn dữ liệu.
     */
    public function createProductWithAttributes($productData, $price, $attributesData) {
        try {
            $this->db->beginTransaction();

            // Bước 1: Thêm vào bảng `product`
            $queryProduct = "INSERT INTO product (name, categoryId, brandId, image, stock_quantity) 
                             VALUES (:name, :categoryId, :brandId, :image, :stock_quantity)";
            $stmtProduct = $this->db->prepare($queryProduct);
            $stmtProduct->bindParam(':name', $productData['name']);
            $stmtProduct->bindParam(':categoryId', $productData['categoryId'], PDO::PARAM_INT);
            $stmtProduct->bindParam(':brandId', $productData['brandId'], PDO::PARAM_INT);
            $stmtProduct->bindParam(':image', $productData['image']);
            $stmtProduct->bindParam(':stock_quantity', $productData['stock_quantity'], PDO::PARAM_INT);
            $stmtProduct->execute();
            $productId = $this->db->lastInsertId();

            // Bước 2: Thêm giá vào bảng `product_price`
            $queryPrice = "INSERT INTO product_price (productId, price) VALUES (:productId, :price)";
            $stmtPrice = $this->db->prepare($queryPrice);
            $stmtPrice->bindParam(':productId', $productId);
            $stmtPrice->bindParam(':price', $price);
            $stmtPrice->execute();

            // Bước 3: Xử lý các thông số kỹ thuật
            if (!empty($attributesData)) {
                foreach ($attributesData as $attr) {
                    $attrName = trim($attr['key']);
                    $attrValue = trim($attr['value']);

                    if (empty($attrName) || empty($attrValue)) {
                        continue; // Bỏ qua các thuộc tính rỗng
                    }

                    // Tìm hoặc tạo mới attribute trong bảng `attributes`
                    $attributeId = $this->findOrCreateAttribute($attrName);
                    
                    // Thêm vào bảng `product_attributes`
                    $queryPa = "INSERT INTO product_attributes (productId, attributeId, value) VALUES (:productId, :attributeId, :value)";
                    $stmtPa = $this->db->prepare($queryPa);
                    $stmtPa->bindParam(':productId', $productId, PDO::PARAM_INT);
                    $stmtPa->bindParam(':attributeId', $attributeId, PDO::PARAM_INT);
                    $stmtPa->bindParam(':value', $attrValue);
                    $stmtPa->execute();
                }
            }
            
            $this->db->commit();
            return $productId;

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['sql_error'] = $e->getMessage();
            return false;
        }
    }

    /**
     * VIẾT LẠI HOÀN TOÀN: Cập nhật sản phẩm và các thông số kỹ thuật.
     */
    public function updateProductWithAttributes($productId, $productData, $priceData, $attributesData) {
        try {
            $this->db->beginTransaction();

            // Bước 1: Cập nhật bảng `product`
            $queryProduct = "UPDATE product SET name = :name, categoryId = :categoryId, brandId = :brandId, 
                             image = :image, stock_quantity = :stock_quantity WHERE productId = :productId";
            $stmtProduct = $this->db->prepare($queryProduct);
            $stmtProduct->bindParam(':name', $productData['name']);
            $stmtProduct->bindParam(':categoryId', $productData['categoryId'], PDO::PARAM_INT);
            $stmtProduct->bindParam(':brandId', $productData['brandId'], PDO::PARAM_INT);
            $stmtProduct->bindParam(':image', $productData['image']);
            $stmtProduct->bindParam(':stock_quantity', $productData['stock_quantity'], PDO::PARAM_INT);
            $stmtProduct->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmtProduct->execute();

            // Bước 2: Cập nhật giá nếu có thay đổi
            if ($priceData['has_changed']) {
                $this->updatePrice($productId, $priceData['value']);
            }

            // Bước 3: Xóa tất cả các thuộc tính cũ của sản phẩm
            $stmtDelete = $this->db->prepare("DELETE FROM product_attributes WHERE productId = :productId");
            $stmtDelete->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Bước 4: Thêm lại các thuộc tính mới
            if (!empty($attributesData)) {
                foreach ($attributesData as $attr) {
                    $attrName = trim($attr['key']);
                    $attrValue = trim($attr['value']);
                    if (empty($attrName) || empty($attrValue)) continue;

                    $attributeId = $this->findOrCreateAttribute($attrName);
                    
                    $queryPa = "INSERT INTO product_attributes (productId, attributeId, value) VALUES (:productId, :attributeId, :value)";
                    $stmtPa = $this->db->prepare($queryPa);
                    $stmtPa->bindParam(':productId', $productId, PDO::PARAM_INT);
                    $stmtPa->bindParam(':attributeId', $attributeId, PDO::PARAM_INT);
                    $stmtPa->bindParam(':value', $attrValue);
                    $stmtPa->execute();
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['sql_error'] = $e->getMessage();
            return false;
        }
    }

    /**
     * Phương thức trợ giúp: Tìm ID của một thuộc tính theo tên, nếu không có thì tạo mới.
     */
    private function findOrCreateAttribute($name) {
        // Tìm xem attribute đã tồn tại chưa
        $stmtFind = $this->db->prepare("SELECT attributeId FROM attributes WHERE name = :name");
        $stmtFind->bindParam(':name', $name);
        $stmtFind->execute();
        $result = $stmtFind->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['attributeId'];
        } else {
            // Nếu chưa có, tạo mới
            $stmtCreate = $this->db->prepare("INSERT INTO attributes (name) VALUES (:name)");
            $stmtCreate->bindParam(':name', $name);
            $stmtCreate->execute();
            return $this->db->lastInsertId();
        }
    }

    // Các phương thức khác như countAll, getByCategory, search, updatePrice, decreaseStock giữ nguyên...
    
    public function countAll() {
        $query = "SELECT COUNT(p.productId) as total
                  FROM product p
                  JOIN product_price pp ON p.productId = pp.productId
                  WHERE pp.validTo IS NULL OR pp.validTo > NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    public function countByCategory($categoryId) {
        $query = "SELECT COUNT(p.productId) as total
                  FROM product p
                  JOIN product_price pp ON p.productId = pp.productId
                  WHERE p.categoryId = :categoryId AND (pp.validTo IS NULL OR pp.validTo > NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    public function getByCategory($categoryId, $limit, $offset) {
        $query = "SELECT p.*, c.name as category_name, pp.price 
                  FROM product p 
                  LEFT JOIN category c ON p.categoryId = c.categoryId 
                  LEFT JOIN product_price pp ON p.productId = pp.productId 
                  WHERE p.categoryId = :categoryId AND (pp.validTo IS NULL OR pp.validTo > NOW())
                  ORDER BY p.productId DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePrice($productId, $price) {
        try {
            $this->db->beginTransaction();
            
            $queryOld = "UPDATE product_price SET validTo = NOW() WHERE productId = :productId AND validTo IS NULL";
            $stmtOld = $this->db->prepare($queryOld);
            $stmtOld->bindParam(':productId', $productId);
            $stmtOld->execute();
            
            $queryNew = "INSERT INTO product_price (productId, price, validFrom) VALUES (:productId, :price, NOW())";
            $stmtNew = $this->db->prepare($queryNew);
            $stmtNew->bindParam(':productId', $productId);
            $stmtNew->bindParam(':price', $price);
            $stmtNew->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

public function decreaseStock($productId, $quantity) {
    $query = "UPDATE product 
              SET stock_quantity = stock_quantity - :quantity 
              WHERE productId = :productId AND stock_quantity >= :quantity";

    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}


    public function countBySearch($search) {
    $query = "SELECT COUNT(*) FROM product WHERE name LIKE :search";
    $stmt = $this->db->prepare($query);
    $like = "%" . $search . "%";
    $stmt->bindParam(':search', $like);
    $stmt->execute();
    return $stmt->fetchColumn();
}
public function search($search, $limit, $offset) {
    $query = "SELECT p.*, c.name as category_name, b.name as brand_name, pp.price 
              FROM product p 
              LEFT JOIN category c ON p.categoryId = c.categoryId 
              LEFT JOIN brands b ON p.brandId = b.brandId
              LEFT JOIN product_price pp ON p.productId = pp.productId 
              WHERE (pp.validTo IS NULL OR pp.validTo > NOW())
              AND p.name LIKE :search
              ORDER BY p.productId DESC
              LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($query);
    $like = "%" . $search . "%";
    $stmt->bindParam(':search', $like);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}