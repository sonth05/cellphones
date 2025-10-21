<?php
$title = 'Giỏ hàng - CellphoneS';
$content = ob_get_clean();
ob_start();
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item active">Giỏ hàng</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng của bạn
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($cartItems)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= BASE_URL ?>/public/<?= $item['product']['image'] ?>" 
                                                         alt="<?= htmlspecialchars($item['product']['name']) ?>" 
                                                         class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                    <div>
                                                        <h6 class="mb-1"><?= htmlspecialchars($item['product']['name']) ?></h6>
                                                        <p class="text-muted small mb-0"><?= htmlspecialchars($item['product']['brand']) ?></p>
                                                        <p class="text-muted small mb-0">SKU: <?= htmlspecialchars($item['product']['sku']) ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price-info">
                                                    <span class="fw-bold text-danger"><?= number_format($item['product']['price'], 0, ',', '.') ?>đ</span>
                                                    <?php if ($item['product']['original_price'] && $item['product']['original_price'] > $item['product']['price']): ?>
                                                        <br><span class="text-muted small text-decoration-line-through"><?= number_format($item['product']['original_price'], 0, ',', '.') ?>đ</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="quantity-controls d-flex align-items-center">
                                                    <form method="POST" action="/cart/update" class="d-inline">
                                                        <input type="hidden" name="product_id" value="<?= $item['product']['id'] ?>">
                                                        <div class="input-group" style="width: 120px;">
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decreaseQuantity(<?= $item['product']['id'] ?>)">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                                                   class="form-control form-control-sm text-center" 
                                                                   min="1" max="<?= $item['product']['stock'] ?>" 
                                                                   id="qty-<?= $item['product']['id'] ?>">
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="increaseQuantity(<?= $item['product']['id'] ?>, <?= $item['product']['stock'] ?>)">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-outline-primary mt-1 w-100">Cập nhật</button>
                                                    </form>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-danger h5"><?= number_format($item['subtotal'], 0, ',', '.') ?>đ</span>
                                            </td>
                                            <td>
                                                <form method="POST" action="/cart/remove" class="d-inline">
                                                    <input type="hidden" name="product_id" value="<?= $item['product']['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Cart Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="/products" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                            </a>
                            <form method="POST" action="/cart/clear" class="d-inline">
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')">
                                    <i class="fas fa-trash me-2"></i>Xóa tất cả
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                            <h4>Giỏ hàng trống</h4>
                            <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                            <a href="/products" class="btn btn-danger">
                                <i class="fas fa-shopping-bag me-2"></i>Bắt đầu mua sắm
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <?php if (!empty($cartItems)): ?>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span><?= number_format($total, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span>
                            <?php 
                            $shippingFee = $total >= 500000 ? 0 : 30000;
                            echo $shippingFee > 0 ? number_format($shippingFee, 0, ',', '.') . 'đ' : 'Miễn phí';
                            ?>
                        </span>
                    </div>
                    <?php if ($total < 500000): ?>
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            Mua thêm <?= number_format(500000 - $total, 0, ',', '.') ?>đ để được miễn phí vận chuyển
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between h5 mb-3">
                        <strong>Tổng cộng:</strong>
                        <strong class="text-danger"><?= number_format($total + $shippingFee, 0, ',', '.') ?>đ</strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="/checkout" class="btn btn-danger btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Thanh toán
                            </a>
                        <?php else: ?>
                            <a href="/login" class="btn btn-danger btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để thanh toán
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mt-4">
                        <h6>Phương thức thanh toán:</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="payment-method text-center p-2 border rounded">
                                    <i class="fas fa-money-bill-wave text-success fa-lg"></i>
                                    <p class="small mb-0 mt-1">COD</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="payment-method text-center p-2 border rounded">
                                    <i class="fas fa-credit-card text-primary fa-lg"></i>
                                    <p class="small mb-0 mt-1">Thẻ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function decreaseQuantity(productId) {
    const input = document.getElementById('qty-' + productId);
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function increaseQuantity(productId, maxStock) {
    const input = document.getElementById('qty-' + productId);
    if (parseInt(input.value) < maxStock) {
        input.value = parseInt(input.value) + 1;
    }
}
</script>

<?php require __DIR__ . '/../../layouts/main.php'; ?>
