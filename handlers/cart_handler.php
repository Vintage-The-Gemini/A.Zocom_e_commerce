<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false];

try {
    switch ($data['action']) {
        case 'add':
            $productId = $data['productId'];
            $_SESSION['cart'][$productId] = [
                'name' => $data['productName'],
                'brand' => $data['productBrand'],
                'image' => $data['productImage'],
                'quantity' => isset($_SESSION['cart'][$productId]) ?
                    $_SESSION['cart'][$productId]['quantity'] + 1 : 1
            ];
            $response = [
                'success' => true,
                'cartCount' => count($_SESSION['cart']),
                'message' => 'Product added to cart'
            ];
            break;

        case 'update':
            $productId = $data['productId'];
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] = max(1, $data['quantity']);
                $response = [
                    'success' => true,
                    'cartCount' => count($_SESSION['cart']),
                    'message' => 'Cart updated'
                ];
            }
            break;

        case 'remove':
            if (isset($_SESSION['cart'][$data['productId']])) {
                unset($_SESSION['cart'][$data['productId']]);
                $response = [
                    'success' => true,
                    'cartCount' => count($_SESSION['cart']),
                    'message' => 'Item removed'
                ];
            }
            break;

        case 'clear':
            $_SESSION['cart'] = [];
            $response = [
                'success' => true,
                'cartCount' => 0,
                'message' => 'Cart cleared'
            ];
            break;
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

echo json_encode($response);
