<?php
session_start();
require_once __DIR__ . '/src/bootstrap.php';

use App\Router;

// Initialize router and define routes
$router = new Router();

// Public routes (Customer interface)
$router->get('/', 'CustomerController@index');
$router->get('/products', 'CustomerController@products');
$router->get('/product', 'CustomerController@product');
$router->get('/category', 'CustomerController@category');
$router->get('/search', 'CustomerController@search');
$router->get('/profile', 'CustomerController@profile');
$router->get('/orders', 'CustomerController@orders');

// Auth routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotPassword');
$router->post('/forgot-password', 'AuthController@resetPassword');

// Cart/checkout routes
$router->get('/cart', 'CartController@view');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/clear', 'CartController@clear');
$router->get('/checkout', 'CheckoutController@show');
$router->post('/checkout', 'CheckoutController@placeOrder');
$router->get('/order-success', 'CheckoutController@success');

// Admin routes (protected)
$router->get('/admin', 'AdminController@dashboard');

// Mirrored UI preview
$router->get('/mirror', 'MirrorController@index');

// Admin Products
$router->get('/admin/products', 'AdminProductController@index');
$router->get('/admin/products/create', 'AdminProductController@create');
$router->post('/admin/products/store', 'AdminProductController@store');
$router->get('/admin/products/edit', 'AdminProductController@edit');
$router->post('/admin/products/update', 'AdminProductController@update');
$router->post('/admin/products/delete', 'AdminProductController@delete');

// Admin Orders
$router->get('/admin/orders', 'AdminOrderController@index');
$router->get('/admin/orders/view', 'AdminOrderController@view');
$router->post('/admin/orders/status', 'AdminOrderController@updateStatus');
$router->post('/admin/orders/shipping', 'AdminOrderController@updateShipping');
$router->get('/admin/orders/export', 'AdminOrderController@export');

// Admin Customers
$router->get('/admin/customers', 'AdminCustomerController@index');
$router->get('/admin/customers/view', 'AdminCustomerController@view');
$router->get('/admin/customers/edit', 'AdminCustomerController@edit');
$router->post('/admin/customers/update', 'AdminCustomerController@update');
$router->post('/admin/customers/deactivate', 'AdminCustomerController@deactivate');
$router->post('/admin/customers/activate', 'AdminCustomerController@activate');
$router->get('/admin/customers/top', 'AdminCustomerController@topCustomers');
$router->get('/admin/customers/export', 'AdminCustomerController@export');

// Admin Reports (Manager only)
$router->get('/admin/reports', 'ReportController@index');
$router->get('/admin/reports/sales', 'ReportController@sales');
$router->get('/admin/reports/products', 'ReportController@products');
$router->get('/admin/reports/customers', 'ReportController@customers');
$router->get('/admin/reports/export', 'ReportController@export');

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>


