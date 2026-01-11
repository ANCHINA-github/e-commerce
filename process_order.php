<?php
// process_order.php - 处理购物车和订单逻辑
require_once 'config.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_to_cart':
        addToCart();
        break;
        
    case 'update_cart':
        updateCart();
        break;
        
    case 'remove_from_cart':
        removeFromCart();
        break;
        
    case 'checkout':
        checkout();
        break;
        
    default:
        header('Location: index.php');
        exit;
}

function addToCart() {
    $productId = $_POST['product_id'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);
    
    $products = getProducts();
    
    if (!isset($products[$productId])) {
        $_SESSION['error'] = '商品不存在';
        header('Location: index.php');
        exit;
    }
    
    if ($products[$productId]['stock'] < $quantity) {
        $_SESSION['error'] = '库存不足';
        header('Location: index.php');
        exit;
    }
    
    // 添加到购物车
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'quantity' => $quantity,
            'added_at' => date('Y-m-d H:i:s')
        ];
    }
    
    // 减少库存
    $products[$productId]['stock'] -= $quantity;
    saveProducts($products);
    
    $_SESSION['success'] = '商品已添加到购物车';
    header('Location: cart.php');
    exit;
}

function updateCart() {
    $productId = $_POST['product_id'] ?? '';
    $newQuantity = intval($_POST['quantity'] ?? 1);
    
    $products = getProducts();
    
    if (!isset($products[$productId]) || !isset($_SESSION['cart'][$productId])) {
        header('Location: cart.php');
        exit;
    }
    
    $oldQuantity = $_SESSION['cart'][$productId]['quantity'];
    $quantityDiff = $newQuantity - $oldQuantity;
    
    // 检查库存是否足够
    if ($products[$productId]['stock'] < $quantityDiff) {
        $_SESSION['error'] = '库存不足';
        header('Location: cart.php');
        exit;
    }
    
    if ($newQuantity <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
    }
    
    // 更新库存
    $products[$productId]['stock'] -= $quantityDiff;
    saveProducts($products);
    
    header('Location: cart.php');
    exit;
}

function removeFromCart() {
    $productId = $_POST['product_id'] ?? '';
    
    $products = getProducts();
    
    if (isset($_SESSION['cart'][$productId]) && isset($products[$productId])) {
        // 恢复库存
        $products[$productId]['stock'] += $_SESSION['cart'][$productId]['quantity'];
        saveProducts($products);
        
        // 从购物车移除
        unset($_SESSION['cart'][$productId]);
    }
    
    header('Location: cart.php');
    exit;
}

function checkout() {
    $cart = $_SESSION['cart'];
    $products = getProducts();
    $orders = getOrders();
    
    if (empty($cart)) {
        $_SESSION['error'] = '购物车为空';
        header('Location: cart.php');
        exit;
    }
    
    // 验证库存是否足够
    foreach ($cart as $productId => $item) {
        if (!isset($products[$productId]) || $products[$productId]['stock'] < $item['quantity']) {
            $_SESSION['error'] = '部分商品库存不足，请更新购物车';
            header('Location: cart.php');
            exit;
        }
    }
    
    // 创建订单
    $orderId = 'order_' . uniqid();
    $orderItems = [];
    $total = 0;
    
    foreach ($cart as $productId => $item) {
        $product = $products[$productId];
        $subtotal = $product['price'] * $item['quantity'];
        
        $orderItems[] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'quantity' => $item['quantity'],
            'price' => $product['price'],
            'subtotal' => $subtotal
        ];
        
        $total += $subtotal;
    }
    
    $order = [
        'items' => $orderItems,
        'total' => $total,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // 保存订单
    $orders[$orderId] = $order;
    saveOrders($orders);
    
    // 清空购物车
    $_SESSION['cart'] = [];
    
    $_SESSION['success'] = '订单提交成功！订单号：' . substr($orderId, 0, 8);
    header('Location: index.php');
    exit;
}
?>
