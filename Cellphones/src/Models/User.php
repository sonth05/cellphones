<?php

namespace App\Models;

use App\Lib\Database;
use PDO;

class User
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create($data)
    {
        $sql = "INSERT INTO users (name, email, password_hash, role, phone, address, customer_group) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['email'],
            $data['password_hash'],
            $data['role'] ?? 'customer',
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['customer_group'] ?? 'member'
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }
    
    public function getByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->query($sql, [$email])->fetch();
    }
    
    public function authenticate($email, $password)
    {
        $user = $this->getByEmail($email);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
    
    public function update($id, $data)
    {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, customer_group = ? WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['customer_group'] ?? 'member',
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function updatePassword($id, $newPasswordHash)
    {
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        return $this->db->query($sql, [$newPasswordHash, $id]);
    }
    
    public function deactivate($id)
    {
        $sql = "UPDATE users SET is_active = 0 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function activate($id)
    {
        $sql = "UPDATE users SET is_active = 1 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function getAllCustomers($limit = null, $offset = 0, $filters = [])
    {
        $sql = "SELECT * FROM users WHERE role = 'customer'";
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['customer_group'])) {
            $sql .= " AND customer_group = ?";
            $params[] = $filters['customer_group'];
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function getAllStaff($limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM users WHERE role IN ('manager', 'staff') ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params = [$limit, $offset];
        } else {
            $params = [];
        }
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function getCustomerStats($userId)
    {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) as total_spent,
                    MAX(created_at) as last_order_date,
                    AVG(CASE WHEN status = 'delivered' THEN total ELSE NULL END) as avg_order_value
                FROM orders 
                WHERE user_id = ?";
        
        return $this->db->query($sql, [$userId])->fetch();
    }
    
    public function getCustomerOrders($userId, $limit = 10)
    {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        return $this->db->query($sql, [$userId, $limit])->fetchAll();
    }
    
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        return $this->db->query($sql, $params)->fetchColumn() > 0;
    }
    
    public function getStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as total_customers,
                    SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) as total_staff,
                    SUM(CASE WHEN role = 'manager' THEN 1 ELSE 0 END) as total_managers,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN customer_group = 'vip' THEN 1 ELSE 0 END) as vip_customers
                FROM users";
        
        return $this->db->query($sql)->fetch();
    }
    
    public function getNewCustomers($days = 30)
    {
        $sql = "SELECT COUNT(*) FROM users 
                WHERE role = 'customer' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        return $this->db->query($sql, [$days])->fetchColumn();
    }
    
    public function getTopCustomers($limit = 10)
    {
        $sql = "SELECT 
                    u.id,
                    u.name,
                    u.email,
                    u.phone,
                    u.customer_group,
                    COUNT(o.id) as total_orders,
                    SUM(CASE WHEN o.status = 'delivered' THEN o.total ELSE 0 END) as total_spent
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id
                WHERE u.role = 'customer'
                GROUP BY u.id, u.name, u.email, u.phone, u.customer_group
                ORDER BY total_spent DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit])->fetchAll();
    }
    
    public function generatePasswordHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    public function validatePassword($password)
    {
        // At least 8 characters, contains at least one letter and one number
        return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/', $password);
    }
    
    public function generateResetToken($email)
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // In a real application, you would store this in a password_resets table
        // For now, we'll return the token for demo purposes
        return [
            'token' => $token,
            'expires' => $expires
        ];
    }
}
