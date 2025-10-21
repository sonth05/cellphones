<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\User;

class AdminOrderController
{
    private $orderModel;
    private $userModel;
    
    public function __construct()
    {
        $this->orderModel = new Order();
        $this->userModel = new User();
        $this->checkAuth();
    }
    
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'status' => $_GET['status'] ?? null,
            'payment_status' => $_GET['payment_status'] ?? null,
            'search' => $_GET['search'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $orders = $this->orderModel->getAllForAdmin($limit, $offset, $filters);
        $stats = $this->orderModel->getStats();
        
        $data = [
            'orders' => $orders,
            'stats' => $stats,
            'filters' => $filters,
            'current_page' => $page,
            'limit' => $limit
        ];
        
        $this->render('admin/orders/index', $data);
    }
    
    public function view()
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/admin/orders');
        }
        
        $order = $this->orderModel->getById($id);
        
        if (!$order) {
            $this->setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('/admin/orders');
        }
        
        $data = [
            'order' => $order
        ];
        
        $this->render('admin/orders/view', $data);
    }
    
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/orders');
        }
        
        $orderId = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $notes = $_POST['notes'] ?? null;
        
        if (!$orderId || !$status) {
            $this->setFlash('error', 'Thông tin không hợp lệ');
            $this->redirect('/admin/orders');
        }
        
        $validStatuses = ['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled', 'returned'];
        
        if (!in_array($status, $validStatuses)) {
            $this->setFlash('error', 'Trạng thái không hợp lệ');
            $this->redirect('/admin/orders');
        }
        
        try {
            $userId = $_SESSION['user']['id'];
            $this->orderModel->updateStatus($orderId, $status, $notes, $userId);
            $this->setFlash('success', 'Trạng thái đơn hàng đã được cập nhật');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage());
        }
        
        $this->redirect("/admin/orders/view?id={$orderId}");
    }
    
    public function updateShipping()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/orders');
        }
        
        $orderId = $_POST['order_id'] ?? null;
        $shippingCode = $_POST['shipping_code'] ?? null;
        $shippingCompany = $_POST['shipping_company'] ?? null;
        
        if (!$orderId) {
            $this->setFlash('error', 'ID đơn hàng không hợp lệ');
            $this->redirect('/admin/orders');
        }
        
        try {
            $this->orderModel->updateShippingInfo($orderId, $shippingCode, $shippingCompany);
            
            // Update status to shipping if not already
            $order = $this->orderModel->getById($orderId);
            if ($order['status'] !== 'shipping') {
                $userId = $_SESSION['user']['id'];
                $this->orderModel->updateStatus($orderId, 'shipping', 'Đơn hàng đã được giao cho đơn vị vận chuyển', $userId);
            }
            
            $this->setFlash('success', 'Thông tin vận chuyển đã được cập nhật');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Lỗi khi cập nhật thông tin vận chuyển: ' . $e->getMessage());
        }
        
        $this->redirect("/admin/orders/view?id={$orderId}");
    }
    
    public function export()
    {
        $filters = [
            'status' => $_GET['status'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $orders = $this->orderModel->getAllForAdmin(null, 0, $filters);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=orders_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV headers
        fputcsv($output, [
            'Mã đơn hàng',
            'Khách hàng',
            'Email',
            'Số điện thoại',
            'Tổng tiền',
            'Trạng thái',
            'Phương thức thanh toán',
            'Trạng thái thanh toán',
            'Ngày tạo'
        ]);
        
        // CSV data
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['order_number'],
                $order['customer_name'],
                $order['customer_email'],
                $order['customer_phone'],
                number_format($order['total'], 0, ',', '.') . ' VND',
                $this->getStatusText($order['status']),
                $this->getPaymentMethodText($order['payment_method']),
                $this->getPaymentStatusText($order['payment_status']),
                date('d/m/Y H:i', strtotime($order['created_at']))
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function getStatusText($status)
    {
        $statusTexts = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy',
            'returned' => 'Trả hàng'
        ];
        
        return $statusTexts[$status] ?? $status;
    }
    
    private function getPaymentMethodText($method)
    {
        $methodTexts = [
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'credit_card' => 'Thẻ tín dụng',
            'ewallet' => 'Ví điện tử'
        ];
        
        return $methodTexts[$method] ?? $method;
    }
    
    private function getPaymentStatusText($status)
    {
        $statusTexts = [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];
        
        return $statusTexts[$status] ?? $status;
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
