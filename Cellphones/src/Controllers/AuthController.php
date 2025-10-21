<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    public function showLogin()
    {
        // Redirect if already logged in
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $this->render('auth/login');
    }
    
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/login');
        }
        
        $user = $this->userModel->authenticate($email, $password);
        
        if (!$user) {
            $this->setFlash('error', 'Email hoặc mật khẩu không chính xác');
            $this->redirect('/login');
        }
        
        if (!$user['is_active']) {
            $this->setFlash('error', 'Tài khoản đã bị vô hiệu hóa');
            $this->redirect('/login');
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['user_address'] = $user['address'];
        $_SESSION['customer_group'] = $user['customer_group'] ?? 'member';
        
        $this->setFlash('success', 'Đăng nhập thành công');
        
        // Redirect based on role
        if (in_array($user['role'], ['manager', 'staff'])) {
            $this->redirect('/admin');
        } else {
            $this->redirect('/');
        }
    }
    
    public function showRegister()
    {
        // Redirect if already logged in
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $this->render('auth/register');
    }
    
    public function register()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        
        $errors = [];
        
        // Validation
        if (empty($name)) {
            $errors[] = 'Tên không được để trống';
        }
        
        if (empty($email)) {
            $errors[] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        } elseif ($this->userModel->emailExists($email)) {
            $errors[] = 'Email đã được sử dụng';
        }
        
        if (empty($password)) {
            $errors[] = 'Mật khẩu không được để trống';
        } elseif (!$this->userModel->validatePassword($password)) {
            $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái và số';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Mật khẩu nhập lại không khớp';
        }
        
        if (!empty($phone) && !preg_match('/^[0-9+\-\s()]+$/', $phone)) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->render('auth/register', [
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ]);
            return;
        }
        
        try {
            $passwordHash = $this->userModel->generatePasswordHash($password);
            
            $this->userModel->create([
                'name' => $name,
                'email' => $email,
                'password_hash' => $passwordHash,
                'phone' => $phone,
                'role' => 'customer'
            ]);
            
            $this->setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập');
            $this->redirect('/login');
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Có lỗi xảy ra khi đăng ký: ' . $e->getMessage());
            $this->redirect('/register');
        }
    }
    
    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_phone']);
        unset($_SESSION['user_address']);
        unset($_SESSION['customer_group']);
        $this->setFlash('success', 'Đã đăng xuất thành công');
        $this->redirect('/');
    }
    
    public function forgotPassword()
    {
        $this->render('auth/forgot-password');
    }
    
    public function resetPassword()
    {
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $this->setFlash('error', 'Vui lòng nhập email');
            $this->redirect('/forgot-password');
        }
        
        $user = $this->userModel->getByEmail($email);
        
        if (!$user) {
            $this->setFlash('error', 'Email không tồn tại trong hệ thống');
            $this->redirect('/forgot-password');
        }
        
        // In a real application, you would send an email with reset link
        $resetData = $this->userModel->generateResetToken($email);
        
        $this->setFlash('success', 'Hướng dẫn đặt lại mật khẩu đã được gửi đến email của bạn');
        $this->redirect('/login');
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
    
    private function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }
}


