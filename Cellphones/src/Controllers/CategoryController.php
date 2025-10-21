<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController
{
    private $categoryModel;
    private $productModel;
    
    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->productModel = new Product();
    }
    
    public function list()
    {
        $categories = $this->categoryModel->getTree();
        
        $data = [
            'categories' => $categories
        ];
        
        $this->render('categories/list', $data);
    }
    
    public function view()
    {
        $slug = $_GET['slug'] ?? '';
        
        if (empty($slug)) {
            $this->redirect('/');
        }
        
        $category = $this->categoryModel->getBySlug($slug);
        
        if (!$category) {
            $this->render('errors/404');
            return;
        }
        
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'category_id' => $category['id'],
            'brand' => $_GET['brand'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null
        ];
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $products = $this->productModel->getAll($limit, $offset, $filters);
        $subcategories = $this->categoryModel->getChildren($category['id']);
        $brands = $this->productModel->getBrands();
        $breadcrumb = $this->categoryModel->getBreadcrumb($category['id']);
        
        $data = [
            'category' => $category,
            'products' => $products,
            'subcategories' => $subcategories,
            'brands' => $brands,
            'breadcrumb' => $breadcrumb,
            'filters' => $filters,
            'current_page' => $page,
            'limit' => $limit
        ];
        
        $this->render('categories/view', $data);
    }
    
    private function render($view, $data = [])
    {
        extract($data);
        require __DIR__ . "/../Views/{$view}.php";
    }
    
    private function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
}
