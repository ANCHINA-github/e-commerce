<?php
// config.php - 系统配置文件
session_start();

// 系统常量定义
define('PRODUCTS_FILE', __DIR__ . '/data/products.json');
define('ORDERS_FILE', __DIR__ . '/data/orders.json');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB

// 确保目录存在
if (!file_exists(dirname(PRODUCTS_FILE))) {
    mkdir(dirname(PRODUCTS_FILE), 0777, true);
}
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!file_exists(dirname(ORDERS_FILE))) {
    mkdir(dirname(ORDERS_FILE), 0777, true);
}

// 初始化购物车
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 商品数据操作函数
function getProducts() {
    if (!file_exists(PRODUCTS_FILE)) {
        file_put_contents(PRODUCTS_FILE, json_encode([]));
        return [];
    }
    
    $data = file_get_contents(PRODUCTS_FILE);
    return json_decode($data, true) ?: [];
}

function saveProducts($products) {
    $result = file_put_contents(PRODUCTS_FILE, json_encode($products, JSON_PRETTY_PRINT));
    return $result !== false;
}

function getOrders() {
    if (!file_exists(ORDERS_FILE)) {
        file_put_contents(ORDERS_FILE, json_encode([]));
        return [];
    }
    
    $data = file_get_contents(ORDERS_FILE);
    return json_decode($data, true) ?: [];
}

function saveOrders($orders) {
    $result = file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT));
    return $result !== false;
}

// 图片上传函数
function uploadProductImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => '文件上传错误'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => '文件大小超过限制(2MB)'];
    }
    
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => '只允许上传JPEG, PNG, GIF或WebP图片'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('product_') . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => '文件保存失败'];
}
?>
