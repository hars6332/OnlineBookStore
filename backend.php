<?php
session_start();
require 'config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getBooks':
        try {
            $stmt = $conn->prepare("SELECT * FROM books");
            $stmt->execute();
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($books);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        break;

    case 'getCart':
        echo json_encode($_SESSION['cart'] ?? []);
        break;

    case 'add':
        $bookId = $_GET['bookId'] ?? null;
        if ($bookId !== null) {
            try {
                $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
                $stmt->bindParam(':id', $bookId, PDO::PARAM_INT);
                $stmt->execute();
                $book = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($book) {
                    $_SESSION['cart'][] = $book;
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        header('Location: cart.html');
        break;

    case 'remove':
        $bookId = $_GET['bookId'] ?? null;
        if ($bookId !== null) {
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($b) use ($bookId) {
                return $b['id'] != $bookId;
            });
        }
        header('Location: cart.html');
        break;

    case 'checkout':
        // Process the checkout (e.g., save to database, send email, etc.)
        // For simplicity, we just clear the cart and simulate a checkout.
        $name = $_POST['name'] ?? '';
        $address = $_POST['address'] ?? '';
        $_SESSION['cart'] = [];
        echo "Order placed successfully! Thank you, $name.";
        break;

    default:
        echo "Invalid action";
        break;
}
?>
