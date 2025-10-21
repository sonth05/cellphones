<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Middleware\AuthMiddleware;

class AdminController
{
    private $orderModel;
    private $productModel;
    private $userModel;
    
    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->userModel = new User();
        AuthMiddleware::checkStaff();
    }
    
    public function dashboard()
    {
        // Get statistics for dashboard
        $orderStats = $this->orderModel->getStats();
        $productStats = $this->productModel->getStats();
        $userStats = $this->userModel->getStats();
        
        // Get recent orders
        $recentOrders = $this->orderModel->getAllForAdmin(10);
        
        // Get low stock products
        $lowStockProducts = $this->productModel->getLowStock();
        
        // Get daily revenue for chart
        $dailyStats = $this->orderModel->getDailyStats(30);
        
        // Get top products
        $topProducts = $this->orderModel->getTopProducts(5);
        
        $data = [
            'orderStats' => $orderStats,
            'productStats' => $productStats,
            'userStats' => $userStats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'dailyStats' => $dailyStats,
            'topProducts' => $topProducts
        ];
        
        $this->render('admin/dashboard', $data);
    }
    
    // Removed checkAuth method - now using AuthMiddleware
    
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
