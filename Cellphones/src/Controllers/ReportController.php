<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class ReportController
{
    private $orderModel;
    private $productModel;
    private $userModel;
    
    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->userModel = new User();
        $this->checkAuth();
    }
    
    public function index()
    {
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
        $dateTo = $_GET['date_to'] ?? date('Y-m-d'); // Today
        
        // Get various statistics
        $orderStats = $this->orderModel->getStats($dateFrom, $dateTo);
        $productStats = $this->productModel->getStats();
        $userStats = $this->userModel->getStats();
        
        // Get daily revenue chart data
        $days = 30;
        $dailyStats = $this->orderModel->getDailyStats($days);
        
        // Get top products
        $topProducts = $this->orderModel->getTopProducts(10, $dateFrom, $dateTo);
        
        // Get top customers
        $topCustomers = $this->userModel->getTopCustomers(10);
        
        // Get low stock products
        $lowStockProducts = $this->productModel->getLowStock();
        
        $data = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'orderStats' => $orderStats,
            'productStats' => $productStats,
            'userStats' => $userStats,
            'dailyStats' => $dailyStats,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
            'lowStockProducts' => $lowStockProducts
        ];
        
        $this->render('admin/reports/index', $data);
    }
    
    public function sales()
    {
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');
        $groupBy = $_GET['group_by'] ?? 'day'; // day, week, month
        
        $salesData = $this->getSalesData($dateFrom, $dateTo, $groupBy);
        
        $data = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'groupBy' => $groupBy,
            'salesData' => $salesData
        ];
        
        $this->render('admin/reports/sales', $data);
    }
    
    public function products()
    {
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');
        
        $topProducts = $this->orderModel->getTopProducts(50, $dateFrom, $dateTo);
        $productStats = $this->productModel->getStats();
        
        $data = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'topProducts' => $topProducts,
            'productStats' => $productStats
        ];
        
        $this->render('admin/reports/products', $data);
    }
    
    public function customers()
    {
        $topCustomers = $this->userModel->getTopCustomers(50);
        $userStats = $this->userModel->getStats();
        $newCustomers = $this->userModel->getNewCustomers(30);
        
        $data = [
            'topCustomers' => $topCustomers,
            'userStats' => $userStats,
            'newCustomers' => $newCustomers
        ];
        
        $this->render('admin/reports/customers', $data);
    }
    
    public function export()
    {
        $type = $_GET['type'] ?? 'sales';
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');
        
        switch ($type) {
            case 'sales':
                $this->exportSalesReport($dateFrom, $dateTo);
                break;
            case 'products':
                $this->exportProductsReport($dateFrom, $dateTo);
                break;
            case 'customers':
                $this->exportCustomersReport();
                break;
            default:
                $this->redirect('/admin/reports');
        }
    }
    
    private function exportSalesReport($dateFrom, $dateTo)
    {
        $dailyStats = $this->orderModel->getDailyStats(365, $dateFrom, $dateTo);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=sales_report_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Ngày', 'Số đơn hàng', 'Doanh thu', 'Doanh thu đã giao']);
        
        foreach ($dailyStats as $stat) {
            fputcsv($output, [
                $stat['date'],
                $stat['order_count'],
                number_format($stat['revenue'], 0, ',', '.') . ' VND',
                number_format($stat['delivered_revenue'], 0, ',', '.') . ' VND'
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportProductsReport($dateFrom, $dateTo)
    {
        $topProducts = $this->orderModel->getTopProducts(100, $dateFrom, $dateTo);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=products_report_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Sản phẩm', 'Mã SKU', 'Số lượng bán', 'Doanh thu']);
        
        foreach ($topProducts as $product) {
            fputcsv($output, [
                $product['product_name'],
                $product['sku'],
                $product['total_sold'],
                number_format($product['total_revenue'], 0, ',', '.') . ' VND'
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportCustomersReport()
    {
        $topCustomers = $this->userModel->getTopCustomers(100);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=customers_report_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Tên khách hàng', 'Email', 'Số điện thoại', 'Nhóm khách hàng', 'Số đơn hàng', 'Tổng chi tiêu']);
        
        foreach ($topCustomers as $customer) {
            fputcsv($output, [
                $customer['name'],
                $customer['email'],
                $customer['phone'],
                $this->getCustomerGroupText($customer['customer_group']),
                $customer['total_orders'],
                number_format($customer['total_spent'], 0, ',', '.') . ' VND'
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function getSalesData($dateFrom, $dateTo, $groupBy)
    {
        $sql = "SELECT ";
        
        switch ($groupBy) {
            case 'week':
                $sql .= "YEAR(created_at) as year, WEEK(created_at) as period, ";
                $sql .= "CONCAT('Tuần ', WEEK(created_at), ' - ', YEAR(created_at)) as period_label, ";
                break;
            case 'month':
                $sql .= "YEAR(created_at) as year, MONTH(created_at) as period, ";
                $sql .= "CONCAT(MONTH(created_at), '/', YEAR(created_at)) as period_label, ";
                break;
            default: // day
                $sql .= "DATE(created_at) as period, ";
                $sql .= "DATE_FORMAT(created_at, '%d/%m/%Y') as period_label, ";
                break;
        }
        
        $sql .= "COUNT(*) as order_count,
                 SUM(total) as total_revenue,
                 SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) as delivered_revenue
                 FROM orders 
                 WHERE DATE(created_at) >= ? AND DATE(created_at) <= ?
                 GROUP BY ";
        
        if ($groupBy === 'day') {
            $sql .= "DATE(created_at)";
        } else {
            $sql .= "YEAR(created_at), " . ($groupBy === 'week' ? 'WEEK' : 'MONTH') . "(created_at)";
        }
        
        $sql .= " ORDER BY period ASC";
        
        return $this->orderModel->getDb()->query($sql, [$dateFrom, $dateTo])->fetchAll();
    }
    
    private function getCustomerGroupText($group)
    {
        $groupTexts = [
            'member' => 'Thành viên',
            'vip' => 'VIP',
            'dealer' => 'Đại lý'
        ];
        
        return $groupTexts[$group] ?? $group;
    }
    
    private function checkAuth()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
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
}
