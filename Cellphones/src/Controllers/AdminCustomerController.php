<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Order;

class AdminCustomerController
{
    private $userModel;
    private $orderModel;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->checkAuth();
    }
    
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'search' => $_GET['search'] ?? null,
            'customer_group' => $_GET['customer_group'] ?? null
        ];
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $customers = $this->userModel->getAllCustomers($limit, $offset, $filters);
        $stats = $this->userModel->getStats();
        
        $data = [
            'customers' => $customers,
            'stats' => $stats,
            'filters' => $filters,
            'current_page' => $page,
            'limit' => $limit
        ];
        
        $this->render('admin/customers/index', $data);
    }
    
    public function view()
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/admin/customers');
        }
        
        $customer = $this->userModel->getById($id);
        
        if (!$customer || $customer['role'] !== 'customer') {
            $this->setFlash('error', 'Khách hàng không tồn tại');
            $this->redirect('/admin/customers');
        }
        
        $customerStats = $this->userModel->getCustomerStats($id);
        $customerOrders = $this->userModel->getCustomerOrders($id);
        
        $data = [
            'customer' => $customer,
            'customerStats' => $customerStats,
            'customerOrders' => $customerOrders
        ];
        
        $this->render('admin/customers/view', $data);
    }
    
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/admin/customers');
        }
        
        $customer = $this->userModel->getById($id);
        
        if (!$customer || $customer['role'] !== 'customer') {
            $this->setFlash('error', 'Khách hàng không tồn tại');
            $this->redirect('/admin/customers');
        }
        
        $data = [
            'customer' => $customer
        ];
        
        $this->render('admin/customers/edit', $data);
    }
    
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/customers');
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/admin/customers');
        }
        
        $data = $this->validateCustomerData($_POST, $id);
        
        if (empty($data['errors'])) {
            try {
                $this->userModel->update($id, $data);
                $this->setFlash('success', 'Thông tin khách hàng đã được cập nhật thành công');
                $this->redirect("/admin/customers/view?id={$id}");
            } catch (\Exception $e) {
                $this->setFlash('error', 'Lỗi khi cập nhật thông tin khách hàng: ' . $e->getMessage());
            }
        } else {
            $this->setFlash('error', implode('<br>', $data['errors']));
        }
        
        $this->redirect("/admin/customers/edit?id={$id}");
    }
    
    public function deactivate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/customers');
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $this->setFlash('error', 'ID khách hàng không hợp lệ');
            $this->redirect('/admin/customers');
        }
        
        try {
            $this->userModel->deactivate($id);
            $this->setFlash('success', 'Tài khoản khách hàng đã được vô hiệu hóa');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Lỗi khi vô hiệu hóa tài khoản: ' . $e->getMessage());
        }
        
        $this->redirect('/admin/customers');
    }
    
    public function activate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/customers');
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $this->setFlash('error', 'ID khách hàng không hợp lệ');
            $this->redirect('/admin/customers');
        }
        
        try {
            $this->userModel->activate($id);
            $this->setFlash('success', 'Tài khoản khách hàng đã được kích hoạt');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Lỗi khi kích hoạt tài khoản: ' . $e->getMessage());
        }
        
        $this->redirect('/admin/customers');
    }
    
    public function topCustomers()
    {
        $limit = $_GET['limit'] ?? 10;
        $topCustomers = $this->userModel->getTopCustomers($limit);
        
        $data = [
            'topCustomers' => $topCustomers,
            'limit' => $limit
        ];
        
        $this->render('admin/customers/top', $data);
    }
    
    public function export()
    {
        $filters = [
            'search' => $_GET['search'] ?? null,
            'customer_group' => $_GET['customer_group'] ?? null
        ];
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $customers = $this->userModel->getAllCustomers(null, 0, $filters);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=customers_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV headers
        fputcsv($output, [
            'Tên khách hàng',
            'Email',
            'Số điện thoại',
            'Địa chỉ',
            'Nhóm khách hàng',
            'Ngày đăng ký',
            'Trạng thái'
        ]);
        
        // CSV data
        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer['name'],
                $customer['email'],
                $customer['phone'],
                $customer['address'],
                $this->getCustomerGroupText($customer['customer_group']),
                date('d/m/Y', strtotime($customer['created_at'])),
                $customer['is_active'] ? 'Hoạt động' : 'Vô hiệu hóa'
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function validateCustomerData($data, $excludeId = null)
    {
        $errors = [];
        
        // Required fields
        if (empty($data['name'])) {
            $errors[] = 'Tên khách hàng không được để trống';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Email không được để trống';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        
        // Check email uniqueness
        if ($this->userModel->emailExists($data['email'], $excludeId)) {
            $errors[] = 'Email đã được sử dụng';
        }
        
        // Validate phone if provided
        if (!empty($data['phone']) && !preg_match('/^[0-9+\-\s()]+$/', $data['phone'])) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }
        
        // Validate customer group
        $validGroups = ['member', 'vip', 'dealer'];
        if (!empty($data['customer_group']) && !in_array($data['customer_group'], $validGroups)) {
            $errors[] = 'Nhóm khách hàng không hợp lệ';
        }
        
        return [
            'errors' => $errors,
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'customer_group' => $data['customer_group'] ?? 'member'
        ];
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
