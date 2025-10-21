<?php

namespace App\Controllers;

use App\Models\Product;

class CartController
{
    private $productModel;
    
    public function __construct()
    {
        $this->productModel = new Product();
    }
    
    public function view()
    {
        $cartItems = $this->getCartItems();
        $total = $this->calculateTotal($cartItems);
        
        $data = [
            'cartItems' => $cartItems,
            'total' => $total
        ];
        
        $this->render('cart/view', $data);
    }
    
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }
        
        $productId = $_POST['product_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if (!$productId || $quantity <= 0) {
            $this->setFlash('error', 'Thông tin sản phẩm không hợp lệ');
            $this->redirect('/');
        }
        
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $this->setFlash('error', 'Sản phẩm không tồn tại');
            $this->redirect('/');
        }
        
        if ($product['stock'] < $quantity) {
            $this->setFlash('error', 'Số lượng sản phẩm không đủ');
            $this->redirect('/');
        }
        
        $this->addToCart($productId, $quantity);
        
        $this->setFlash('success', 'Đã thêm sản phẩm vào giỏ hàng');
        $this->redirect('/cart');
    }
    
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
        }
        
        $productId = $_POST['product_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        if (!$productId) {
            $this->setFlash('error', 'Thông tin sản phẩm không hợp lệ');
            $this->redirect('/cart');
        }
        
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $this->setFlash('error', 'Sản phẩm không tồn tại');
            $this->redirect('/cart');
        }
        
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            $this->setFlash('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
        } else {
            if ($product['stock'] < $quantity) {
                $this->setFlash('error', 'Số lượng sản phẩm không đủ');
                $this->redirect('/cart');
            }
            
            $this->updateCartItem($productId, $quantity);
            $this->setFlash('success', 'Đã cập nhật số lượng sản phẩm');
        }
        
        $this->redirect('/cart');
    }
    
    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
        }
        
        $productId = $_POST['product_id'] ?? null;
        
        if (!$productId) {
            $this->setFlash('error', 'Thông tin sản phẩm không hợp lệ');
            $this->redirect('/cart');
        }
        
        $this->removeFromCart($productId);
        $this->setFlash('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
        $this->redirect('/cart');
    }
    
    public function clear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
        }
        
        $this->clearCart();
        $this->setFlash('success', 'Đã xóa tất cả sản phẩm khỏi giỏ hàng');
        $this->redirect('/cart');
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
    
    private function addToCart($productId, $quantity)
    {
        $sessionId = $this->getSessionId();
        
        if (!isset($_SESSION['cart'][$sessionId])) {
            $_SESSION['cart'][$sessionId] = [];
        }
        
        if (isset($_SESSION['cart'][$sessionId][$productId])) {
            $_SESSION['cart'][$sessionId][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$sessionId][$productId] = $quantity;
        }
    }
    
    private function updateCartItem($productId, $quantity)
    {
        $sessionId = $this->getSessionId();
        
        if (isset($_SESSION['cart'][$sessionId][$productId])) {
            $_SESSION['cart'][$sessionId][$productId] = $quantity;
        }
    }
    
    private function removeFromCart($productId)
    {
        $sessionId = $this->getSessionId();
        
        if (isset($_SESSION['cart'][$sessionId][$productId])) {
            unset($_SESSION['cart'][$sessionId][$productId]);
        }
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
