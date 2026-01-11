<?php
// index.php - 前台商品展示
require_once 'config.php';

$products = getProducts();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>轻量级电商系统 - 商品列表</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-store"></i> 轻量级电商系统</h1>
            <div class="header-actions">
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    购物车
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
                <a href="admin.php" class="admin-link">
                    <i class="fas fa-cog"></i> 管理后台
                </a>
            </div>
        </header>
        
        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class="fas fa-box-open fa-3x"></i>
                    <h3>暂无商品</h3>
                    <p>管理员正在添加商品，请稍后再来</p>
                    <a href="admin.php" class="btn">前往后台添加商品</a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $id => $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                    <span>暂无图片</span>
                                </div>
                            <?php endif; ?>
                            <?php if ($product['stock'] <= 0): ?>
                                <span class="out-of-stock">售罄</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            
                            <div class="product-meta">
                                <span class="price">¥<?php echo number_format($product['price'], 2); ?></span>
                                <span class="stock">
                                    <?php if ($product['stock'] > 0): ?>
                                        库存: <?php echo $product['stock']; ?>件
                                    <?php else: ?>
                                        <span class="no-stock">缺货中</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            
                            <form method="POST" action="process_order.php" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                <input type="hidden" name="action" value="add_to_cart">
                                
                                <?php if ($product['stock'] > 0): ?>
                                    <div class="quantity-control">
                                        <button type="button" class="qty-btn minus" onclick="changeQuantity(this, -1)">-</button>
                                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="quantity-input">
                                        <button type="button" class="qty-btn plus" onclick="changeQuantity(this, 1)">+</button>
                                    </div>
                                    
                                    <button type="submit" class="btn add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> 加入购物车
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn disabled-btn" disabled>
                                        <i class="fas fa-times-circle"></i> 已售罄
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <footer>
            <p>轻量级电商系统 &copy; <?php echo date('Y'); ?> - 基于PHP/JSON构建</p>
        </footer>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
