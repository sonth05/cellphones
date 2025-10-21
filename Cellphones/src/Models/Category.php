<?php

namespace App\Models;

use App\Lib\Database;
use PDO;

class Category
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function getAll($parentId = null, $activeOnly = true)
    {
        $sql = "SELECT * FROM categories WHERE 1=1";
        $params = [];
        
        if ($parentId !== null) {
            $sql .= " AND parent_id = ?";
            $params[] = $parentId;
        }
        
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        
        $sql .= " ORDER BY sort_order ASC, name ASC";
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function getById($id)
    {
        $sql = "SELECT * FROM categories WHERE id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }
    
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM categories WHERE slug = ? AND is_active = 1";
        return $this->db->query($sql, [$slug])->fetch();
    }
    
    public function getParents()
    {
        return $this->getAll(null);
    }
    
    public function getChildren($parentId)
    {
        return $this->getAll($parentId);
    }
    
    public function getTree()
    {
        $categories = $this->getAll();
        return $this->buildTree($categories);
    }
    
    private function buildTree($categories, $parentId = null, $level = 0)
    {
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['level'] = $level;
                $category['children'] = $this->buildTree($categories, $category['id'], $level + 1);
                $tree[] = $category;
            }
        }
        
        return $tree;
    }
    
    public function getBreadcrumb($categoryId)
    {
        $breadcrumb = [];
        $category = $this->getById($categoryId);
        
        while ($category) {
            array_unshift($breadcrumb, $category);
            $category = $category['parent_id'] ? $this->getById($category['parent_id']) : null;
        }
        
        return $breadcrumb;
    }
    
    public function getProductCount($categoryId, $includeChildren = true)
    {
        if ($includeChildren) {
            $categoryIds = $this->getAllDescendants($categoryId);
            $categoryIds[] = $categoryId;
        } else {
            $categoryIds = [$categoryId];
        }
        
        $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
        $sql = "SELECT COUNT(*) FROM products WHERE category_id IN ($placeholders) AND is_active = 1";
        
        return $this->db->query($sql, $categoryIds)->fetchColumn();
    }
    
    private function getAllDescendants($parentId)
    {
        $descendants = [];
        $children = $this->getChildren($parentId);
        
        foreach ($children as $child) {
            $descendants[] = $child['id'];
            $descendants = array_merge($descendants, $this->getAllDescendants($child['id']));
        }
        
        return $descendants;
    }
    
    // Admin methods
    public function create($data)
    {
        $sql = "INSERT INTO categories (parent_id, name, slug, description, image, sort_order, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['parent_id'] ?? null,
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['image'] ?? null,
            $data['sort_order'] ?? 0,
            $data['is_active'] ?? true
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function update($id, $data)
    {
        $sql = "UPDATE categories SET parent_id = ?, name = ?, slug = ?, description = ?, 
                image = ?, sort_order = ?, is_active = ? WHERE id = ?";
        
        $params = [
            $data['parent_id'] ?? null,
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['image'] ?? null,
            $data['sort_order'] ?? 0,
            $data['is_active'] ?? true,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id)
    {
        // Check if category has products
        $productCount = $this->getProductCount($id, false);
        if ($productCount > 0) {
            throw new \Exception("Không thể xóa danh mục có sản phẩm");
        }
        
        // Check if category has children
        $children = $this->getChildren($id);
        if (!empty($children)) {
            throw new \Exception("Không thể xóa danh mục có danh mục con");
        }
        
        $sql = "DELETE FROM categories WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function generateSlug($name, $parentId = null)
    {
        $slug = $this->slugify($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $parentId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugify($text)
    {
        // Remove Vietnamese accents and convert to lowercase
        $text = $this->removeVietnameseAccents($text);
        $text = strtolower($text);
        
        // Replace spaces and special characters with hyphens
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $text = trim($text, '-');
        
        return $text;
    }
    
    private function removeVietnameseAccents($str)
    {
        $accents = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ'
        ];
        
        $noAccents = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd'
        ];
        
        return str_replace($accents, $noAccents, $str);
    }
    
    private function slugExists($slug, $parentId = null)
    {
        $sql = "SELECT COUNT(*) FROM categories WHERE slug = ?";
        $params = [$slug];
        
        if ($parentId !== null) {
            $sql .= " AND parent_id = ?";
            $params[] = $parentId;
        }
        
        return $this->db->query($sql, $params)->fetchColumn() > 0;
    }
}
