<?php
// cart.php - 购物车页面
require_once 'config.php';

$products = getProducts();
$cart = $_SESSION['cart'];
$total = 0;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>购物车 - 轻量级电商系统</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-shopping-cart"></i> 我的购物车</h1>
            <div class="header-actions">
                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> 继续购物
                </a>
                <a href="admin.php" class="admin-link">
                    <i class="fas fa-cog"></i> 管理后台
                </a>
            </div>
        </header>
        
        <?php if (empty($cart)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart fa-4x"></i>
                <h3>购物车空空如也</h3>
                <p>快去挑选心仪的商品吧！</p>
                <a href="index.php" class="btn">去购物</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <?php foreach ($cart as $productId => $item): ?>
                        <?php if (isset($products[$productId])): ?>
                            <?php 
                            $product = $products[$productId];
                            $subtotal = $product['price'] * $item['quantity'];
                            $total += $subtotal;
                            ?>
                            <div class="cart-item">
                                <div class="cart-item-image">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="cart-item-info">
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <p class="item-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <div class="item-price">单价: ¥<?php echo number_format($product['price'], 2); ?></div>
                                </div>
                                
                                <div class="cart-item-quantity">
                                    <form method="POST" action="process_order.php" class="update-form">
                                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                        <input type="hidden" name="action" value="update_cart">
                                        <div class="quantity-control">
                                            <button type="button" class="qty-btn minus" onclick="changeCartQuantity(this, -1)">-</button>
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="<?php echo $product['stock']; ?>" class="quantity-input">
                                            <button type="button" class="qty-btn plus" onclick="changeCartQuantity(this, 1)">+</button>
                                        </div>
                                        <button type="submit" class="btn update-btn">更新</button>
                                    </form>
                                </div>
                                
                                <div class="cart-item-subtotal">
                                    <span class="subtotal">¥<?php echo number_format($subtotal, 2); ?></span>
                                    <form method="POST" action="process_order.php" class="remove-form">
                                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                        <input type="hidden" name="action" value="remove_from_cart">
                                        <button type="submit" class="btn remove-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3>订单汇总</h3>
                    <div class="summary-details">
                        <div class="summary-row">
                            <span>商品总价:</span>
                            <span>¥<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>运费:</span>
                            <span>¥0.00</span>
                        </div>
                        <hr>
                        <div class="summary-row total">
                            <span>总计:</span>
                            <span>¥<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                    
                    <form method="POST" action="process_order.php" class="checkout-form">
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit" class="btn checkout-btn">
                            <i class="fas fa-check-circle"></i> 结算订单
                        </button>
                    </form>
                    
                    <a href="index.php" class="btn continue-shopping">
                        <i class="fas fa-arrow-left"></i> 继续购物
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <footer>
            <p>轻量级电商系统 &copy; <?php echo date('Y'); ?> - 基于PHP/JSON构建</p>
        </footer>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
