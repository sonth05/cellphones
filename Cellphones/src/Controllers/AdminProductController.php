<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;

class AdminProductController
{
    private $productModel;
    private $categoryModel;
    
    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->checkAuth();
    }
    
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'search' => $_GET['search'] ?? null,
            'category_id' => $_GET['category_id'] ?? null,
            'status' => $_GET['status'] ?? null
        ];
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $products = $this->productModel->getAllForAdmin($limit, $offset, $filters);
        $categories = $this->categoryModel->getAll();
        $stats = $this->productModel->getStats();
        
        $data = [
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
            'filters' => $filters,
            'current_page' => $page,
            'limit' => $limit
        ];
        
        $this->render('admin/products/index', $data);
    }
    
    public function create()
    {
        $categories = $this->categoryModel->getAll();
        
        $data = [
            'categories' => $categories,
            'product' => null
        ];
        
        $this->render('admin/products/create', $data);
    }
    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/products');
        }
        
        $data = $this->validateProductData($_POST);
        
        if (empty($data['errors'])) {
            try {
                $this->productModel->create($data);
                $this->setFlash('success', 'Sản phẩm đã được tạo thành công');
                $this->redirect('/admin/products');
            } catch (\Exception $e) {
                $this->setFlash('error', 'Lỗi khi tạo sản phẩm: ' . $e->getMessage());
            }
        } else {
            $this->setFlash('error', implode('<br>', $data['errors']));
        }
        
        $categories = $this->categoryModel->getAll();
        
        $data = [
            'categories' => $categories,
            'product' => $_POST
        ];
        
        $this->render('admin/products/create', $data);
    }
    
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/admin/products');
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->setFlash('error', 'Sản phẩm không tồn tại');
            $this->redirect('/admin/products');
        }
        
        $categories = $this->categoryModel->getAll();
        
        $data = [
            'product' => $product,
            'categories' => $categories
        ];
        
        $this->render('admin/products/edit', $data);
    }
    
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/products');
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/admin/products');
        }
        
        $data = $this->validateProductData($_POST, $id);
        
        if (empty($data['errors'])) {
            try {
                $this->productModel->update($id, $data);
                $this->setFlash('success', 'Sản phẩm đã được cập nhật thành công');
                $this->redirect('/admin/products');
            } catch (\Exception $e) {
                $this->setFlash('error', 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
            }
        } else {
            $this->setFlash('error', implode('<br>', $data['errors']));
        }
        
        $this->redirect("/admin/products/edit?id={$id}");
    }
    
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/products');
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $this->setFlash('error', 'ID sản phẩm không hợp lệ');
            $this->redirect('/admin/products');
        }
        
        try {
            $this->productModel->delete($id);
            $this->setFlash('success', 'Sản phẩm đã được xóa thành công');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Lỗi khi xóa sản phẩm: ' . $e->getMessage());
        }
        
        $this->redirect('/admin/products');
    }
    
    private function validateProductData($data, $excludeId = null)
    {
        $errors = [];
        
        // Required fields
        if (empty($data['name'])) {
            $errors[] = 'Tên sản phẩm không được để trống';
        }
        
        if (empty($data['sku'])) {
            $errors[] = 'Mã SKU không được để trống';
        }
        
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            $errors[] = 'Giá sản phẩm phải là số dương';
        }
        
        if (!isset($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
            $errors[] = 'Số lượng tồn kho phải là số không âm';
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        // Validate category
        if (!empty($data['category_id'])) {
            $category = $this->categoryModel->getById($data['category_id']);
            if (!$category) {
                $errors[] = 'Danh mục không hợp lệ';
            }
        }
        
        return [
            'errors' => $errors,
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'] ?? '',
            'slug' => $data['slug'] ?? '',
            'sku' => $data['sku'] ?? '',
            'price' => $data['price'] ?? 0,
            'original_price' => $data['original_price'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'min_stock_level' => $data['min_stock_level'] ?? 5,
            'brand' => $data['brand'] ?? null,
            'image' => $data['image'] ?? null,
            'description' => $data['description'] ?? null,
            'specifications' => $data['specifications'] ?? null,
            'weight' => $data['weight'] ?? null,
            'dimensions' => $data['dimensions'] ?? null,
            'warranty_period' => $data['warranty_period'] ?? null,
            'is_featured' => isset($data['is_featured']),
            'is_active' => isset($data['is_active'])
        ];
    }
    
    private function generateSlug($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    private function checkAuth()
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['manager', 'staff'])) {
            $this->redirect('/login');
        }
    }
    
    private function render($view, $data = [])
    {
        extract($data);
        require __DIR__ . "/../../src/Views/{$view}.php";
    }
    
    private function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
    
    private function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }
}
