<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Middleware\AuthMiddleware;

class CustomerController
{
    private $productModel;
    private $categoryModel;
    private $userModel;
    
    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->userModel = new User();
    }
    
    public function index()
    {
        // Get featured products
        $featuredProducts = $this->productModel->getFeatured(8);
        
        // Get categories
        $categories = $this->categoryModel->getAll();
        
        // Get user info if logged in
        $user = AuthMiddleware::getUser();
        
        $this->render('customer/home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'user' => $user
        ]);
    }
    
    public function products()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'category' => $_GET['category'] ?? null,
            'brand' => $_GET['brand'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'search' => $_GET['search'] ?? null,
            'sort' => $_GET['sort'] ?? 'newest'
        ];
        
        $products = $this->productModel->getAll($limit, $offset, $filters);
        $totalProducts = $this->productModel->getCount($filters);
        $totalPages = ceil($totalProducts / $limit);
        
        $categories = $this->categoryModel->getAll();
        $brands = $this->productModel->getBrands();
        
        $this->render('customer/products', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_products' => $totalProducts
            ]
        ]);
    }
    
    public function product()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /products');
            exit;
        }
        
        $product = $this->productModel->getById($id);
        if (!$product) {
            header('Location: /products');
            exit;
        }
        
        // Increment view count
        $this->productModel->incrementViewCount($id);
        
        // Get related products
        $relatedProducts = $this->productModel->getRelated($id, 4);
        
        // Get reviews
        $reviews = $this->productModel->getReviews($id);
        
        $this->render('customer/product', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews
        ]);
    }
    
    public function category()
    {
        $slug = $_GET['slug'] ?? null;
        if (!$slug) {
            header('Location: /products');
            exit;
        }
        
        $category = $this->categoryModel->getBySlug($slug);
        if (!$category) {
            header('Location: /products');
            exit;
        }
        
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'category' => $category['id'],
            'brand' => $_GET['brand'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'search' => $_GET['search'] ?? null,
            'sort' => $_GET['sort'] ?? 'newest'
        ];
        
        $products = $this->productModel->getAll($limit, $offset, $filters);
        $totalProducts = $this->productModel->getCount($filters);
        $totalPages = ceil($totalProducts / $limit);
        
        $subcategories = $this->categoryModel->getByParent($category['id']);
        $brands = $this->productModel->getBrandsByCategory($category['id']);
        
        $this->render('customer/category', [
            'category' => $category,
            'products' => $products,
            'subcategories' => $subcategories,
            'brands' => $brands,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_products' => $totalProducts
            ]
        ]);
    }
    
    public function search()
    {
        $query = $_GET['q'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        if (empty($query)) {
            header('Location: /products');
            exit;
        }
        
        $filters = [
            'search' => $query,
            'sort' => $_GET['sort'] ?? 'relevance'
        ];
        
        $products = $this->productModel->getAll($limit, $offset, $filters);
        $totalProducts = $this->productModel->getCount($filters);
        $totalPages = ceil($totalProducts / $limit);
        
        $this->render('customer/search', [
            'query' => $query,
            'products' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_products' => $totalProducts
            ]
        ]);
    }
    
    public function profile()
    {
        AuthMiddleware::checkCustomer();
        
        $user = AuthMiddleware::getUser();
        $userDetails = $this->userModel->getById($user['id']);
        
        $this->render('customer/profile', [
            'user' => $userDetails
        ]);
    }
    
    public function orders()
    {
        AuthMiddleware::checkCustomer();
        
        $user = AuthMiddleware::getUser();
        $orders = $this->userModel->getCustomerOrders($user['id']);
        
        $this->render('customer/orders', [
            'orders' => $orders
        ]);
    }
    
    private function render($view, $data = [])
    {
        extract($data);
        
        // Set layout for customer interface
        $layout = 'customer';
        
        ob_start();
        include __DIR__ . "/../Views/{$view}.php";
        $content = ob_get_clean();
        
        include __DIR__ . "/../Views/layouts/{$layout}.php";
    }
}
