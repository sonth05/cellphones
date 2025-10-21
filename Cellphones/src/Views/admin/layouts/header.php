<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin - CellphoneS' ?></title>
    <meta name="description" content="Quản trị hệ thống CellphoneS">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/public/images/favicon.ico">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/css/admin.css" rel="stylesheet">
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <img src="<?= BASE_URL ?>/public/images/logo-white.png" alt="CellphoneS" height="40" class="mb-2">
                <h5 class="text-white">Admin Panel</h5>
            </div>
            
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="/admin" class="d-flex align-items-center">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                </li>
                
                <li>
                    <a href="#productsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle d-flex align-items-center">
                        <i class="fas fa-box me-2"></i>
                        Quản lý sản phẩm
                    </a>
                    <ul class="collapse list-unstyled" id="productsSubmenu">
                        <li><a href="/admin/products">Danh sách sản phẩm</a></li>
                        <li><a href="/admin/products/create">Thêm sản phẩm</a></li>
                        <li><a href="/admin/categories">Danh mục</a></li>
                    </ul>
                </li>
                
                <li>
                    <a href="#ordersSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle d-flex align-items-center">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Quản lý đơn hàng
                    </a>
                    <ul class="collapse list-unstyled" id="ordersSubmenu">
                        <li><a href="/admin/orders">Danh sách đơn hàng</a></li>
                        <li><a href="/admin/orders?status=pending">Đơn chờ xử lý</a></li>
                    </ul>
                </li>
                
                <li>
                    <a href="#customersSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle d-flex align-items-center">
                        <i class="fas fa-users me-2"></i>
                        Quản lý khách hàng
                    </a>
                    <ul class="collapse list-unstyled" id="customersSubmenu">
                        <li><a href="/admin/customers">Danh sách khách hàng</a></li>
                        <li><a href="/admin/customers/top">Khách hàng VIP</a></li>
                    </ul>
                </li>
                
                <?php if ($_SESSION['user']['role'] === 'manager'): ?>
                <li>
                    <a href="#reportsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle d-flex align-items-center">
                        <i class="fas fa-chart-bar me-2"></i>
                        Báo cáo thống kê
                    </a>
                    <ul class="collapse list-unstyled" id="reportsSubmenu">
                        <li><a href="/admin/reports">Tổng quan</a></li>
                        <li><a href="/admin/reports/sales">Báo cáo doanh thu</a></li>
                        <li><a href="/admin/reports/products">Báo cáo sản phẩm</a></li>
                        <li><a href="/admin/reports/customers">Báo cáo khách hàng</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <li>
                    <a href="/" class="d-flex align-items-center" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        Xem website
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-2">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </div>
                        <div>
                            <small class="text-white fw-bold"><?= htmlspecialchars($_SESSION['user']['name']) ?></small><br>
                            <small class="text-white-50"><?= ucfirst($_SESSION['user']['role']) ?></small>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="/logout" class="btn btn-outline-light btn-sm w-100">
                        <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div id="content" class="content">
            <!-- Top Bar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="badge bg-danger rounded-pill">3</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Thông báo</h6></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle text-warning me-2"></i>5 sản phẩm sắp hết hàng</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-cart text-info me-2"></i>3 đơn hàng mới</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-plus text-success me-2"></i>10 khách hàng mới</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="#">Xem tất cả</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="main-content">
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="container-fluid pt-3">
                        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endforeach; ?>
                        <?php unset($_SESSION['flash']); ?>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
            });
            
            // Auto hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
</body>
</html>
