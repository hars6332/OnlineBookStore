<?php
session_start();
require 'config.php';
$isLoggedIn = isset($_SESSION['user_id']);

$stmt = $conn->prepare("SELECT * FROM books");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Bookstore</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                    <a class="nav-link" href="orders.php">My Orders</a>
                   </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1 class="mt-5">Online Bookstore</h1>
        <div class="row">
            <?php foreach ($books as $book): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img height="300" width="100%" style="object-fit: contain;" src="assets/images/<?= $book['cover_image'] ?>" class="card-img-top" alt="<?= $book['title'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $book['title'] ?></h5>
                            <p class="card-text"><?= $book['author'] ?></p>
                            <p class="card-text">Rs<?= $book['price'] ?></p>
                            <a href="book.php?id=<?= $book['id'] ?>" class="btn btn-primary">View Details</a>
                            <a href="cart.php?action=add&bookId=<?= $book['id'] ?>" class="btn btn-success">Add to Cart</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
