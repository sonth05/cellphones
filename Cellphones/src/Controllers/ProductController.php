<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;

class ProductController
{
    private $productModel;
    private $categoryModel;
    
    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'brand' => $_GET['brand'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'search' => $_GET['search'] ?? null,
            'featured' => $_GET['featured'] ?? null
        ];
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $products = $this->productModel->getAll($limit, $offset, $filters);
        $categories = $this->categoryModel->getParents();
        $brands = $this->productModel->getBrands();
        
        $data = [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters,
            'current_page' => $page,
            'limit' => $limit
        ];
        
        $this->render('products/index', $data);
    }
    
    public function view()
    {
        $slug = $_GET['slug'] ?? '';
        
        if (empty($slug)) {
            $this->redirect('/');
        }
        
        $product = $this->productModel->getBySlug($slug);
        
        if (!$product) {
            $this->render('errors/404');
            return;
        }
        
        // Increment view count
        $this->productModel->incrementViewCount($product['id']);
        
        // Get related products
        $relatedProducts = $this->productModel->getRelated($product['id'], $product['category_id']);
        
        $data = [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ];
        
        $this->render('products/view', $data);
    }
    
    public function search()
    {
        $query = $_GET['q'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $filters = ['search' => $query];
        $products = $this->productModel->getAll($limit, $offset, $filters);
        $categories = $this->categoryModel->getParents();
        
        $data = [
            'products' => $products,
            'categories' => $categories,
            'query' => $query,
            'current_page' => $page,
            'limit' => $limit
        ];
        
        $this->render('products/search', $data);
    }
    
    public function category()
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
        
        $this->render('products/category', $data);
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
