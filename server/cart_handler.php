<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function addToCart($productId, $productName, $productBrand, $productImage)
{
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = [
            'name' => $productName,
            'brand' => $productBrand,
            'image' => $productImage,
            'quantity' => 1
        ];
    } else {
        $_SESSION['cart'][$productId]['quantity']++;
    }
    return ['success' => true, 'cartCount' => count($_SESSION['cart'])];
}

function updateQuantity($productId, $quantity)
{
    if (isset($_SESSION['cart'][$productId])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]);
        }
        return ['success' => true, 'cartCount' => count($_SESSION['cart'])];
    }
    return ['success' => false, 'error' => 'Product not found in cart'];
}

function removeFromCart($productId)
{
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        return ['success' => true, 'cartCount' => count($_SESSION['cart'])];
    }
    return ['success' => false, 'error' => 'Product not found in cart'];
}

function clearCart()
{
    $_SESSION['cart'] = [];
    return ['success' => true, 'cartCount' => 0];
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    switch ($action) {
        case 'add':
            echo json_encode(addToCart(
                $data['productId'],
                $data['productName'],
                $data['productBrand'],
                $data['productImage']
            ));
            break;

        case 'update':
            echo json_encode(updateQuantity($data['productId'], $data['quantity']));
            break;

        case 'remove':
            echo json_encode(removeFromCart($data['productId']));
            break;

        case 'clear':
            echo json_encode(clearCart());
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    exit;
}
