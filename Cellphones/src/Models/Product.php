<?php

namespace App\Models;

use App\Lib\Database;
use PDO;

class Product
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function getAll($limit = null, $offset = 0, $filters = [])
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['brand'])) {
            $sql .= " AND p.brand = ?";
            $params[] = $filters['brand'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['featured'])) {
            $sql .= " AND p.is_featured = 1";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function getById($id)
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ? AND p.is_active = 1";
        return $this->db->query($sql, [$id])->fetch();
    }
    
    public function getBySlug($slug)
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.slug = ? AND p.is_active = 1";
        return $this->db->query($sql, [$slug])->fetch();
    }
    
    public function getFeatured($limit = 8)
    {
        return $this->getAll($limit, 0, ['featured' => true]);
    }
    
    public function getRelated($productId, $categoryId, $limit = 4)
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
                ORDER BY p.created_at DESC 
                LIMIT ?";
        return $this->db->query($sql, [$categoryId, $productId, $limit])->fetchAll();
    }
    
    public function getBrands()
    {
        $sql = "SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' AND is_active = 1 ORDER BY brand";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function updateStock($productId, $quantity)
    {
        $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        return $this->db->query($sql, [$quantity, $productId]);
    }
    
    public function incrementViewCount($productId)
    {
        $sql = "UPDATE products SET view_count = view_count + 1 WHERE id = ?";
        return $this->db->query($sql, [$productId]);
    }
    
    public function getLowStock($threshold = 10)
    {
        $sql = "SELECT * FROM products WHERE stock <= ? AND is_active = 1 ORDER BY stock ASC";
        return $this->db->query($sql, [$threshold])->fetchAll();
    }
    
    // Admin methods
    public function create($data)
    {
        $sql = "INSERT INTO products (category_id, name, slug, sku, price, original_price, stock, 
                min_stock_level, brand, image, gallery, description, specifications, weight, 
                dimensions, warranty_period, is_featured, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['sku'],
            $data['price'],
            $data['original_price'] ?? null,
            $data['stock'],
            $data['min_stock_level'] ?? 5,
            $data['brand'] ?? null,
            $data['image'] ?? null,
            $data['gallery'] ?? null,
            $data['description'] ?? null,
            $data['specifications'] ?? null,
            $data['weight'] ?? null,
            $data['dimensions'] ?? null,
            $data['warranty_period'] ?? null,
            $data['is_featured'] ?? false,
            $data['is_active'] ?? true
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function update($id, $data)
    {
        $sql = "UPDATE products SET category_id = ?, name = ?, slug = ?, sku = ?, price = ?, 
                original_price = ?, stock = ?, min_stock_level = ?, brand = ?, image = ?, 
                gallery = ?, description = ?, specifications = ?, weight = ?, dimensions = ?, 
                warranty_period = ?, is_featured = ?, is_active = ? WHERE id = ?";
        
        $params = [
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['sku'],
            $data['price'],
            $data['original_price'] ?? null,
            $data['stock'],
            $data['min_stock_level'] ?? 5,
            $data['brand'] ?? null,
            $data['image'] ?? null,
            $data['gallery'] ?? null,
            $data['description'] ?? null,
            $data['specifications'] ?? null,
            $data['weight'] ?? null,
            $data['dimensions'] ?? null,
            $data['warranty_period'] ?? null,
            $data['is_featured'] ?? false,
            $data['is_active'] ?? true,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id)
    {
        $sql = "DELETE FROM products WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function getAllForAdmin($limit = null, $offset = 0, $filters = [])
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.brand LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'low_stock') {
                $sql .= " AND p.stock <= p.min_stock_level";
            } elseif ($filters['status'] === 'out_of_stock') {
                $sql .= " AND p.stock = 0";
            }
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function getStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_products,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products,
                    SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                    SUM(CASE WHEN stock <= min_stock_level THEN 1 ELSE 0 END) as low_stock,
                    AVG(price) as avg_price,
                    SUM(stock) as total_stock
                FROM products";
        return $this->db->query($sql)->fetch();
    }
}
