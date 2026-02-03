<?php
// Nhận dữ liệu JSON từ request body
$json_data = file_get_contents('php://input');
$order_data = json_decode($json_data, true);

if ($order_data) {
    $order_data['order_id'] = 'ORD' . time();
    $order_data['created_at'] = date('Y-m-d H:i:s');

    $file_path = './json/orders.json';

    // Đọc dữ liệu cũ
    $orders = [];
    if (file_exists($file_path)) {
        $orders = json_decode(file_get_contents($file_path), true) ?: [];
    }

    // Thêm đơn hàng mới
    $orders[] = $order_data;

    // Lưu lại
    if (file_put_contents($file_path, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        http_response_code(200);
        echo json_encode(["message" => "Order saved"]);
    } else {
        http_response_code(500);
    }
} else {
    http_response_code(400);
}
