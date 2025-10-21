<?php

namespace App\Models;

use App\Lib\Database;
use PDO;

class Order
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getDb()
    {
        return $this->db;
    }
    
    public function create($data)
    {
        $this->db->beginTransaction();
        
        try {
            // Generate order number
            $orderNumber = $this->generateOrderNumber();
            
            // Insert order
            $sql = "INSERT INTO orders (order_number, user_id, total, discount_amount, shipping_fee, 
                    status, payment_method, payment_status, recipient_name, recipient_phone, 
                    recipient_address, recipient_city, recipient_district, recipient_ward, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $orderNumber,
                $data['user_id'],
                $data['total'],
                $data['discount_amount'] ?? 0,
                $data['shipping_fee'] ?? 0,
                $data['status'] ?? 'pending',
                $data['payment_method'],
                $data['payment_status'] ?? 'pending',
                $data['recipient_name'],
                $data['recipient_phone'],
                $data['recipient_address'],
                $data['recipient_city'] ?? null,
                $data['recipient_district'] ?? null,
                $data['recipient_ward'] ?? null,
                $data['notes'] ?? null
            ];
            
            $this->db->query($sql, $params);
            $orderId = $this->db->lastInsertId();
            
            // Insert order items
            foreach ($data['items'] as $item) {
                $this->addOrderItem($orderId, $item);
            }
            
            // Add status history
            $this->addStatusHistory($orderId, 'pending', 'Đơn hàng được tạo');
            
            $this->db->commit();
            return $orderId;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    private function addOrderItem($orderId, $item)
    {
        $sql = "INSERT INTO order_items (order_id, product_id, product_name, product_sku, 
                quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $orderId,
            $item['product_id'],
            $item['product_name'],
            $item['product_sku'],
            $item['quantity'],
            $item['unit_price'],
            $item['total_price']
        ];
        
        return $this->db->query($sql, $params);
    }
    
    private function addStatusHistory($orderId, $status, $notes = null, $updatedBy = null)
    {
        $sql = "INSERT INTO order_status_history (order_id, status, notes, updated_by) 
                VALUES (?, ?, ?, ?)";
        
        return $this->db->query($sql, [$orderId, $status, $notes, $updatedBy]);
    }
    
    private function generateOrderNumber()
    {
        $prefix = 'CP';
        $date = date('Ymd');
        
        // Get today's order count
        $sql = "SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()";
        $count = $this->db->query($sql)->fetchColumn();
        
        return $prefix . $date . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
    
    public function getById($id)
    {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id = ?";
        
        $order = $this->db->query($sql, [$id])->fetch();
        
        if ($order) {
            $order['items'] = $this->getOrderItems($id);
            $order['status_history'] = $this->getStatusHistory($id);
        }
        
        return $order;
    }
    
    public function getByOrderNumber($orderNumber)
    {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.order_number = ?";
        
        $order = $this->db->query($sql, [$orderNumber])->fetch();
        
        if ($order) {
            $order['items'] = $this->getOrderItems($order['id']);
            $order['status_history'] = $this->getStatusHistory($order['id']);
        }
        
        return $order;
    }
    
    public function getByUserId($userId, $limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params = [$userId, $limit, $offset];
        } else {
            $params = [$userId];
        }
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    private function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, p.image as product_image, p.slug as product_slug 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        
        return $this->db->query($sql, [$orderId])->fetchAll();
    }
    
    private function getStatusHistory($orderId)
    {
        $sql = "SELECT osh.*, u.name as updated_by_name 
                FROM order_status_history osh 
                LEFT JOIN users u ON osh.updated_by = u.id 
                WHERE osh.order_id = ? 
                ORDER BY osh.created_at ASC";
        
        return $this->db->query($sql, [$orderId])->fetchAll();
    }
    
    public function getAllForAdmin($limit = null, $offset = 0, $filters = [])
    {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE ? OR u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY o.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function updateStatus($orderId, $status, $notes = null, $updatedBy = null)
    {
        $this->db->beginTransaction();
        
        try {
            // Update order status
            $sql = "UPDATE orders SET status = ? WHERE id = ?";
            $this->db->query($sql, [$status, $orderId]);
            
            // Add status history
            $this->addStatusHistory($orderId, $status, $notes, $updatedBy);
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function updatePaymentStatus($orderId, $paymentStatus)
    {
        $sql = "UPDATE orders SET payment_status = ? WHERE id = ?";
        return $this->db->query($sql, [$paymentStatus, $orderId]);
    }
    
    public function updateShippingInfo($orderId, $shippingCode, $shippingCompany)
    {
        $sql = "UPDATE orders SET shipping_code = ?, shipping_company = ? WHERE id = ?";
        return $this->db->query($sql, [$shippingCode, $shippingCompany, $orderId]);
    }
    
    public function getStats($dateFrom = null, $dateTo = null)
    {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_orders,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                    SUM(CASE WHEN status = 'shipping' THEN 1 ELSE 0 END) as shipping_orders,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) as total_revenue,
                    AVG(CASE WHEN status = 'delivered' THEN total ELSE NULL END) as avg_order_value
                FROM orders";
        
        $params = [];
        
        if ($dateFrom) {
            $sql .= " WHERE DATE(created_at) >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= ($dateFrom ? " AND" : " WHERE") . " DATE(created_at) <= ?";
            $params[] = $dateTo;
        }
        
        return $this->db->query($sql, $params)->fetch();
    }
    
    public function getDailyStats($days = 30)
    {
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as order_count,
                    SUM(total) as revenue,
                    SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) as delivered_revenue
                FROM orders 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
        
        return $this->db->query($sql, [$days])->fetchAll();
    }
    
    public function getTopProducts($limit = 10, $dateFrom = null, $dateTo = null)
    {
        $sql = "SELECT 
                    p.name as product_name,
                    p.sku,
                    SUM(oi.quantity) as total_sold,
                    SUM(oi.total_price) as total_revenue
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status = 'delivered'";
        
        $params = [];
        
        if ($dateFrom) {
            $sql .= " AND DATE(o.created_at) >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= ($dateFrom ? " AND" : " AND") . " DATE(o.created_at) <= ?";
            $params[] = $dateTo;
        }
        
        $sql .= " GROUP BY oi.product_id, p.name, p.sku
                  ORDER BY total_sold DESC
                  LIMIT ?";
        
        $params[] = $limit;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
}
