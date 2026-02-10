<?php
header('Content-Type: application/json');

$products = json_decode(file_get_contents('./products.json'), true);
$input = json_decode(file_get_contents('php://input'), true);

$cart = $input['cart'] ?? [];

if (!$cart) {
    echo json_encode([
        'ok' => false,
        'message' => 'Giỏ hàng trống'
    ]);
    exit;
}

$counts = array_count_values($cart);
$total = 0;
$items = [];

foreach ($counts as $id => $qty) {
    $product = array_values(array_filter($products, fn($p) => $p['id'] == $id))[0] ?? null;

    if (!$product) {
        echo json_encode([
            'ok' => false,
            'message' => "Sản phẩm ID $id không tồn tại"
        ]);
        exit;
    }

    if ($product['stock'] < $qty) {
        echo json_encode([
            'ok' => false,
            'message' => "❌ {$product['name']} chỉ còn {$product['stock']} sản phẩm"
        ]);
        exit;
    }

    $itemTotal = $product['price'] * $qty;
    $total += $itemTotal;

    $items[] = [
        'id' => $id,
        'name' => $product['name'],
        'price' => $product['price'],
        'qty' => $qty,
        'total' => $itemTotal
    ];
}

echo json_encode([
    'ok' => true,
    'items' => $items,
    'subtotal' => $total,
    'shipping' => 15000,
    'grandTotal' => $total + 15000
]);
