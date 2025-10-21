<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản trị CellphoneS</title>
    <link href="/public/css/admin.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-mobile-alt"></i> CellphoneS</h2>
                <p>Hệ thống quản trị</p>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="/admin">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/admin/products">
                            <i class="fas fa-box"></i>
                            Sản phẩm
                        </a>
                    </li>
                    <li>
                        <a href="/admin/orders">
                            <i class="fas fa-shopping-cart"></i>
                            Đơn hàng
                        </a>
                    </li>
                    <li>
                        <a href="/admin/customers">
                            <i class="fas fa-users"></i>
                            Khách hàng
                        </a>
                    </li>
                    <?php if (\App\Middleware\AuthMiddleware::getUser()['role'] === 'manager'): ?>
                    <li>
                        <a href="/admin/reports">
                            <i class="fas fa-chart-bar"></i>
                            Báo cáo
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="/" target="_blank">
                            <i class="fas fa-external-link-alt"></i>
                            Xem website
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <h1>Dashboard</h1>
                    <p>Chào mừng trở lại, <?= htmlspecialchars(\App\Middleware\AuthMiddleware::getUser()['name']) ?>!</p>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span><?= htmlspecialchars(\App\Middleware\AuthMiddleware::getUser()['name']) ?></span>
                        <span class="role-badge"><?= \App\Middleware\AuthMiddleware::getUser()['role'] === 'manager' ? 'Quản lý' : 'Nhân viên' ?></span>
                    </div>
                    <a href="/logout" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </header>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= number_format($orderStats['total_orders'] ?? 0) ?></h3>
                            <p>Tổng đơn hàng</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= number_format($orderStats['total_revenue'] ?? 0) ?>₫</h3>
                            <p>Doanh thu</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= number_format($productStats['total_products'] ?? 0) ?></h3>
                            <p>Sản phẩm</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= number_format($userStats['total_customers'] ?? 0) ?></h3>
                            <p>Khách hàng</p>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="charts-row">
                    <div class="chart-container">
                        <h3>Doanh thu theo ngày</h3>
                        <canvas id="revenueChart"></canvas>
                    </div>
                    
                    <div class="chart-container">
                        <h3>Đơn hàng theo trạng thái</h3>
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
                
                <!-- Tables Row -->
                <div class="tables-row">
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Đơn hàng gần đây</h3>
                            <a href="/admin/orders" class="view-all">Xem tất cả</a>
                        </div>
                        <div class="table-content">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><?= number_format($order['total']) ?>₫</td>
                                        <td>
                                            <span class="status-badge status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-header">
                            <h3>Sản phẩm sắp hết hàng</h3>
                            <a href="/admin/products" class="view-all">Xem tất cả</a>
                        </div>
                        <div class="table-content">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Tồn kho</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowStockProducts as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td>
                                            <span class="stock-badge <?= $product['stock'] <= 5 ? 'low' : 'medium' ?>">
                                                <?= $product['stock'] ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($product['price']) ?>₫</td>
                                        <td>
                                            <span class="status-badge status-<?= $product['is_active'] ? 'active' : 'inactive' ?>">
                                                <?= $product['is_active'] ? 'Hoạt động' : 'Tạm dừng' ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($dailyStats, 'date')) ?>,
                datasets: [{
                    label: 'Doanh thu',
                    data: <?= json_encode(array_column($dailyStats, 'revenue')) ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Orders Chart
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Đang xử lý', 'Đã xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'],
                datasets: [{
                    data: [
                        <?= $orderStats['pending_orders'] ?? 0 ?>,
                        <?= $orderStats['confirmed_orders'] ?? 0 ?>,
                        <?= $orderStats['shipping_orders'] ?? 0 ?>,
                        <?= $orderStats['delivered_orders'] ?? 0 ?>,
                        <?= $orderStats['cancelled_orders'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#17a2b8',
                        '#6f42c1',
                        '#28a745',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>