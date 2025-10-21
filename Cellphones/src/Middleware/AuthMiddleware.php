<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
    
    public static function checkRole($allowedRoles = [])
    {
        self::checkAuth();
        
        if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
            http_response_code(403);
            echo 'Access denied. Insufficient permissions.';
            exit;
        }
    }
    
    public static function checkCustomer()
    {
        self::checkRole(['customer']);
    }
    
    public static function checkStaff()
    {
        self::checkRole(['staff', 'manager']);
    }
    
    public static function checkManager()
    {
        self::checkRole(['manager']);
    }
    
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
    
    public static function getUser()
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'phone' => $_SESSION['user_phone'] ?? null,
            'address' => $_SESSION['user_address'] ?? null,
            'customer_group' => $_SESSION['customer_group'] ?? 'member'
        ];
    }
    
    public static function redirectBasedOnRole()
    {
        if (!self::isLoggedIn()) {
            return;
        }
        
        $role = $_SESSION['user_role'];
        
        switch ($role) {
            case 'customer':
                header('Location: /');
                break;
            case 'staff':
            case 'manager':
                header('Location: /admin');
                break;
        }
        exit;
    }
}
