<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;

class CheckoutController
{
    private $orderModel;
    private $productModel;
    
    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->checkAuth();
    }
    
    public function show()
    {
        $cartItems = $this->getCartItems();
        
        if (empty($cartItems)) {
            $this->setFlash('error', 'Giỏ hàng trống');
            $this->redirect('/cart');
        }
        
        $total = $this->calculateTotal($cartItems);
        $shippingFee = $this->calculateShippingFee($total);
        
        $data = [
            'cartItems' => $cartItems,
            'total' => $total,
            'shippingFee' => $shippingFee,
            'grandTotal' => $total + $shippingFee,
            'user' => $_SESSION['user']
        ];
        
        $this->render('checkout/show', $data);
    }
    
    public function placeOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/checkout');
        }
        
        $cartItems = $this->getCartItems();
        
        if (empty($cartItems)) {
            $this->setFlash('error', 'Giỏ hàng trống');
            $this->redirect('/cart');
        }
        
        $orderData = $this->validateOrderData($_POST);
        
        if (!empty($orderData['errors'])) {
            $this->setFlash('error', implode('<br>', $orderData['errors']));
            $this->redirect('/checkout');
        }
        
        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item['product']['stock'] < $item['quantity']) {
                $this->setFlash('error', 'Sản phẩm "' . $item['product']['name'] . '" không đủ hàng');
                $this->redirect('/checkout');
            }
        }
        
        try {
            // Calculate totals
            $subtotal = $this->calculateTotal($cartItems);
            $shippingFee = $this->calculateShippingFee($subtotal);
            $total = $subtotal + $shippingFee;
            
            // Prepare order items
            $orderItems = [];
            foreach ($cartItems as $item) {
                $orderItems[] = [
                    'product_id' => $item['product']['id'],
                    'product_name' => $item['product']['name'],
                    'product_sku' => $item['product']['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product']['price'],
                    'total_price' => $item['subtotal']
                ];
            }
            
            // Create order
            $orderData['user_id'] = $_SESSION['user']['id'];
            $orderData['total'] = $total;
            $orderData['shipping_fee'] = $shippingFee;
            $orderData['items'] = $orderItems;
            
            $orderId = $this->orderModel->create($orderData);
            
            // Update product stock
            foreach ($cartItems as $item) {
                $this->productModel->updateStock($item['product']['id'], $item['quantity']);
            }
            
            // Clear cart
            $this->clearCart();
            
            $this->setFlash('success', 'Đặt hàng thành công! Mã đơn hàng: #' . $orderId);
            $this->redirect("/order-success?order_id={$orderId}");
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
            $this->redirect('/checkout');
        }
    }
    
    public function success()
    {
        $orderId = $_GET['order_id'] ?? null;
        
        if (!$orderId) {
            $this->redirect('/');
        }
        
        $order = $this->orderModel->getById($orderId);
        
        if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $this->setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('/');
        }
        
        $data = [
            'order' => $order
        ];
        
        $this->render('checkout/success', $data);
    }
    
    private function validateOrderData($data)
    {
        $errors = [];
        
        // Required fields
        if (empty($data['recipient_name'])) {
            $errors[] = 'Tên người nhận không được để trống';
        }
        
        if (empty($data['recipient_phone'])) {
            $errors[] = 'Số điện thoại người nhận không được để trống';
        } elseif (!preg_match('/^[0-9+\-\s()]+$/', $data['recipient_phone'])) {
            $errors[] = 'Số điện thoại không hợp lệ';
        }
        
        if (empty($data['recipient_address'])) {
            $errors[] = 'Địa chỉ nhận hàng không được để trống';
        }
        
        if (empty($data['payment_method'])) {
            $errors[] = 'Vui lòng chọn phương thức thanh toán';
        }
        
        $validPaymentMethods = ['cod', 'bank_transfer', 'credit_card', 'ewallet'];
        if (!in_array($data['payment_method'], $validPaymentMethods)) {
            $errors[] = 'Phương thức thanh toán không hợp lệ';
        }
        
        return [
            'errors' => $errors,
            'recipient_name' => $data['recipient_name'] ?? '',
            'recipient_phone' => $data['recipient_phone'] ?? '',
            'recipient_address' => $data['recipient_address'] ?? '',
            'recipient_city' => $data['recipient_city'] ?? null,
            'recipient_district' => $data['recipient_district'] ?? null,
            'recipient_ward' => $data['recipient_ward'] ?? null,
            'payment_method' => $data['payment_method'] ?? '',
            'notes' => $data['notes'] ?? null
        ];
    }
    
    private function getCartItems()
    {
        $sessionId = $this->getSessionId();
        $cart = $_SESSION['cart'][$sessionId] ?? [];
        
        $items = [];
        foreach ($cart as $productId => $quantity) {
            $product = $this->productModel->getById($productId);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product['price'] * $quantity
                ];
            }
        }
        
        return $items;
    }
    
    private function calculateTotal($cartItems)
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }
    
    private function calculateShippingFee($total)
    {
        // Free shipping for orders over 500,000 VND
        if ($total >= 500000) {
            return 0;
        }
        
        // Default shipping fee
        return 30000;
    }
    
    private function clearCart()
    {
        $sessionId = $this->getSessionId();
        $_SESSION['cart'][$sessionId] = [];
    }
    
    private function getSessionId()
    {
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = session_id();
        }
        return $_SESSION['cart_session_id'];
    }
    
    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $this->setFlash('error', 'Vui lòng đăng nhập để tiếp tục');
            $this->redirect('/login');
        }
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
