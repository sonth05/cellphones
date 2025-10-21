<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController
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
        // Get featured products
        $featuredProducts = $this->productModel->getFeatured(8);
        
        // Get latest products
        $latestProducts = $this->productModel->getAll(8, 0);
        
        // Get categories for navigation
        $categories = $this->categoryModel->getParents();
        
        $data = [
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
            'categories' => $categories
        ];
        
        $this->render('home', $data);
    }
    
    private function render($view, $data = [])
    {
        extract($data);
        ob_start();
        require __DIR__ . "/../Views/{$view}.php";
        $content = ob_get_clean();
        require __DIR__ . "/../Views/layouts/main.php";
    }
}


