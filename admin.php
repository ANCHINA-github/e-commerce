<?php
// admin.php - 后台管理页面
require_once 'config.php';

$products = getProducts();
$orders = getOrders();

$action = $_GET['action'] ?? '';
$productId = $_GET['id'] ?? '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_product') {
        // 添加新商品
        $newProduct = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'price' => floatval($_POST['price']),
            'stock' => intval($_POST['stock']),
            'image' => '',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // 处理图片上传
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadProductImage($_FILES['image']);
            if ($uploadResult['success']) {
                $newProduct['image'] = $uploadResult['filename'];
            }
        }
        
        // 生成唯一ID
        $newId = 'prod_' . uniqid();
        $products[$newId] = $newProduct;
        
        if (saveProducts($products)) {
            $message = "商品添加成功！";
            $messageType = "success";
        } else {
            $message = "商品添加失败，请重试！";
            $messageType = "error";
        }
        
    } elseif ($action === 'edit_product' && isset($_POST['product_id'])) {
        // 编辑商品
        $id = $_POST['product_id'];
        
        if (isset($products[$id])) {
            $products[$id]['name'] = trim($_POST['name']);
            $products[$id]['description'] = trim($_POST['description']);
            $products[$id]['price'] = floatval($_POST['price']);
            $products[$id]['stock'] = intval($_POST['stock']);
            $products[$id]['updated_at'] = date('Y-m-d H:i:s');
            
            // 处理图片上传
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = uploadProductImage($_FILES['image']);
                if ($uploadResult['success']) {
                    // 删除旧图片
                    if (!empty($products[$id]['image']) && file_exists(UPLOAD_DIR . $products[$id]['image'])) {
                        unlink(UPLOAD_DIR . $products[$id]['image']);
                    }
                    $products[$id]['image'] = $uploadResult['filename'];
                }
            }
            
            if (saveProducts($products)) {
                $message = "商品更新成功！";
                $messageType = "success";
            } else {
                $message = "商品更新失败，请重试！";
                $messageType = "error";
            }
        }
    } elseif ($action === 'delete_product' && isset($_POST['product_id'])) {
        // 删除商品
        $id = $_POST['product_id'];
        
        if (isset($products[$id])) {
            // 删除商品图片
            if (!empty($products[$id]['image']) && file_exists(UPLOAD_DIR . $products[$id]['image'])) {
                unlink(UPLOAD_DIR . $products[$id]['image']);
            }
            
            unset($products[$id]);
            
            if (saveProducts($products)) {
                $message = "商品删除成功！";
                $messageType = "success";
            } else {
                $message = "商品删除失败，请重试！";
                $messageType = "error";
            }
        }
    }
    
    // 重新加载数据
    $products = getProducts();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - 轻量级电商系统</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-cog"></i> 管理后台</h1>
            <div class="header-actions">
                <a href="index.php" class="back-link">
                    <i class="fas fa-store"></i> 返回商城
                </a>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i> 购物车
                </a>
            </div>
        </header>
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
                <button class="close-message" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>
        
        <div class="admin-tabs">
            <div class="tab-buttons">
                <button class="tab-button active" onclick="openTab('products')">商品管理</button>
                <button class="tab-button" onclick="openTab('orders')">订单管理</button>
                <button class="tab-button" onclick="openTab('add-product')">添加商品</button>
            </div>
            
            <!-- 商品管理标签页 -->
            <div id="products-tab" class="tab-content active">
                <h2><i class="fas fa-boxes"></i> 商品列表</h2>
                <?php if (empty($products)): ?>
                    <div class="no-data">
                        <i class="fas fa-box-open fa-3x"></i>
                        <p>暂无商品，请添加商品</p>
                    </div>
                <?php else: ?>
                    <div class="products-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>图片</th>
                                    <th>商品名称</th>
                                    <th>描述</th>
                                    <th>价格</th>
                                    <th>库存</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $id => $product): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                     class="product-thumb">
                                            <?php else: ?>
                                                <div class="no-image-thumb">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td class="description-cell"><?php echo htmlspecialchars($product['description']); ?></td>
                                        <td>¥<?php echo number_format($product['price'], 2); ?></td>
                                        <td>
                                            <span class="stock-badge <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                                <?php echo $product['stock']; ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="?action=edit&id=<?php echo $id; ?>" class="btn edit-btn">
                                                <i class="fas fa-edit"></i> 编辑
                                            </a>
                                            <form method="POST" class="delete-form">
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                                <button type="submit" class="btn delete-btn" 
                                                        onclick="return confirm('确定要删除这个商品吗？')">
                                                    <i class="fas fa-trash"></i> 删除
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 订单管理标签页 -->
            <div id="orders-tab" class="tab-content">
                <h2><i class="fas fa-receipt"></i> 订单列表</h2>
                <?php if (empty($orders)): ?>
                    <div class="no-data">
                        <i class="fas fa-clipboard-list fa-3x"></i>
                        <p>暂无订单</p>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $orderId => $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <span class="order-id">订单 #<?php echo substr($orderId, 0, 8); ?></span>
                                    <span class="order-date"><?php echo $order['created_at']; ?></span>
                                    <span class="order-total">总计: ¥<?php echo number_format($order['total'], 2); ?></span>
                                </div>
                                <div class="order-items">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="order-item">
                                            <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                            <span class="item-quantity">×<?php echo $item['quantity']; ?></span>
                                            <span class="item-subtotal">¥<?php echo number_format($item['subtotal'], 2); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 添加/编辑商品标签页 -->
            <div id="add-product-tab" class="tab-content">
                <h2>
                    <?php if ($action === 'edit' && isset($products[$productId])): ?>
                        <i class="fas fa-edit"></i> 编辑商品
                    <?php else: ?>
                        <i class="fas fa-plus-circle"></i> 添加商品
                    <?php endif; ?>
                </h2>
                
                <form method="POST" action="admin.php" enctype="multipart/form-data" class="product-form">
                    <?php if ($action === 'edit' && isset($products[$productId])): ?>
                        <input type="hidden" name="action" value="edit_product">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <?php $currentProduct = $products[$productId]; ?>
                    <?php else: ?>
                        <input type="hidden" name="action" value="add_product">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">商品名称 *</label>
                        <input type="text" id="name" name="name" required
                               value="<?php echo isset($currentProduct) ? htmlspecialchars($currentProduct['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">商品描述</label>
                        <textarea id="description" name="description" rows="4"><?php echo isset($currentProduct) ? htmlspecialchars($currentProduct['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">价格 (¥) *</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required
                                   value="<?php echo isset($currentProduct) ? $currentProduct['price'] : '0'; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="stock">库存数量 *</label>
                            <input type="number" id="stock" name="stock" min="0" required
                                   value="<?php echo isset($currentProduct) ? $currentProduct['stock'] : '0'; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">商品图片</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small>支持 JPG, PNG, GIF, WebP 格式，最大 2MB</small>
                        
                        <?php if (isset($currentProduct) && !empty($currentProduct['image'])): ?>
                            <div class="current-image">
                                <p>当前图片:</p>
                                <img src="uploads/<?php echo htmlspecialchars($currentProduct['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($currentProduct['name']); ?>"
                                     class="current-image-preview">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn submit-btn">
                            <?php if ($action === 'edit'): ?>
                                <i class="fas fa-save"></i> 更新商品
                            <?php else: ?>
                                <i class="fas fa-plus-circle"></i> 添加商品
                            <?php endif; ?>
                        </button>
                        
                        <?php if ($action === 'edit'): ?>
                            <a href="admin.php" class="btn cancel-btn">取消</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <footer>
            <p>轻量级电商系统 &copy; <?php echo date('Y'); ?> - 基于PHP/JSON构建</p>
        </footer>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
