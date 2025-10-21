<?php
use App\Models\Category;
$navCategories = [];
try {
    $navCategories = (new Category())->getParents();
} catch (\Throwable $e) {
    $navCategories = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CellphoneS - Điện thoại, Laptop, Phụ kiện chính hãng' ?></title>
    <meta name="description" content="<?= $description ?? 'CellphoneS - Hệ thống bán lẻ điện thoại, laptop, phụ kiện công nghệ chính hãng với giá tốt nhất' ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/public/images/favicon.ico">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/styles.css" rel="stylesheet">
    <?php if (!empty($useMirrorStyles)): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/mirror/cellphones/next/_next/static/css/67dc4512c6b98b4b.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>/mirror/cellphones/next/_next/static/css/b91386235ea798f1.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>/mirror/cellphones/next/_next/static/css/327e35f56b60ecdc.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>/mirror/cellphones/next/_next/static/css/d38014f8cbe8121a.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>/mirror/cellphones/next/_next/static/css/d85d410052c2b2f8.css">
    <?php endif; ?>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <?php if (!empty($useMirrorStyles)): ?>
        <script async src="<?= BASE_URL ?>/mirror/cellphones/next/_next/static/chunks/main-app-5518523dddf30468.js"></script>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <!-- Top Bar -->
        <div class="top-bar bg-dark text-white py-2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone me-2"></i>
                            <span>Hotline: <strong>1900.2091</strong></span>
                            <span class="mx-3">|</span>
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span>Hệ thống 63 tỉnh thành</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <?php if (isset($_SESSION['user'])): ?>
                                <div class="dropdown">
                                    <a class="text-white text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user me-1"></i>
                                        <?= htmlspecialchars($_SESSION['user']['name']) ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i>Tài khoản của tôi</a></li>
                                        <li><a class="dropdown-item" href="/orders"><i class="fas fa-shopping-bag me-2"></i>Đơn hàng</a></li>
                                        <li><a class="dropdown-item" href="/wishlist"><i class="fas fa-heart me-2"></i>Yêu thích</a></li>
                                        <?php if (in_array($_SESSION['user']['role'], ['manager', 'staff'])): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="/admin"><i class="fas fa-cog me-2"></i>Quản trị</a></li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <a href="/login" class="text-white text-decoration-none me-3">
                                    <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                                </a>
                                <a href="/register" class="text-white text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i>Đăng ký
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Header -->
        <div class="main-navbar bg-white shadow-sm">
            <div class="container">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <!-- Logo -->
                        <a class="navbar-brand" href="/">
                            <img src="<?= BASE_URL ?>/public/images/logo.png" alt="CellphoneS" height="40">
                        </a>
                        
                        <!-- Search Bar -->
                        <div class="search-bar flex-grow-1 mx-4">
                            <form action="/search" method="GET" class="d-flex">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="q" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                                    <button class="btn btn-danger" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Cart & Actions -->
                        <div class="d-flex align-items-center">
                            <!-- Cart -->
                            <a href="/cart" class="cart-link position-relative me-3">
                                <i class="fas fa-shopping-cart fa-lg text-danger"></i>
                                <span class="cart-count badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
                                    <?= getCartCount() ?>
                                </span>
                            </a>
                            
                            <!-- Mobile Menu Toggle -->
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <div class="navigation-menu bg-danger">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-dark">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="/">Trang chủ</a>
                            </li>
                            <?php foreach ($navCategories as $cat): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/category?slug=<?= htmlspecialchars($cat['slug']) ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="container mt-3">
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        </div>
    <?php endif; ?>

<?php
// Helper function to get cart count
if (!function_exists('getCartCount')) {
    function getCartCount() {
        if (!isset($_SESSION['cart'])) {
            return 0;
        }
        $sessionId = $_SESSION['cart_session_id'] ?? session_id();
        $cart = $_SESSION['cart'][$sessionId] ?? [];
        $count = 0;
        foreach ($cart as $quantity) {
            $count += $quantity;
        }
        return $count;
    }
}
?>
