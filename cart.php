<?php
session_start();
require 'config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $bookId = $_GET['bookId'] ?? null;
        if ($bookId) {
            $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
            $stmt->bindParam(':id', $bookId, PDO::PARAM_INT);
            $stmt->execute();
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($book) {
                $_SESSION['cart'][] = $book;
            }
        }
        header('Location: cart.php');
        break;

    case 'remove':
        $bookId = $_GET['bookId'] ?? null;
        if ($bookId !== null) {
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($b) use ($bookId) {
                return $b['id'] != $bookId;
            });
        }
        header('Location: cart.php');
        break;

    default:
        break;
}

$cart = $_SESSION['cart'] ?? [];
$total = array_sum(array_column($cart, 'price'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Shopping Cart</h1>
        <div id="cart">
            <?php if (empty($cart)): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <?php foreach ($cart as $item): ?>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h5><?= $item['title'] ?></h5>
                            <p><?= $item['author'] ?></p>
                        </div>
                        <div class="col-md-2">Rs<?= $item['price'] ?></div>
                        <div class="col-md-2"><a href="cart.php?action=remove&bookId=<?= $item['id'] ?>" class="btn btn-danger">Remove</a></div>
                    </div>
                <?php endforeach; ?>
                <div class="row">
                    <div class="col-md-8"><h3>Total</h3></div>
                    <div class="col-md-2"><h3>Rs<?= $total ?></h3></div>
                    <div class="col-md-2"></div>
                </div>
                <a href="checkout.php" class="btn btn-success mt-3">Proceed to Checkout</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
