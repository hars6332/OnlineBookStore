<?php
require 'config.php';

$bookId = $_GET['id'] ?? null;
if ($bookId) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->bindParam(':id', $bookId, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $book['title'] ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5"><?= $book['title'] ?></h1>
        <div class="row">
            <div class="col-md-4">
                <img src="assets/images/<?= $book['cover_image'] ?>" class="img-fluid" alt="<?= $book['title'] ?>">
            </div>
            <div class="col-md-8">
                <h2><?= $book['title'] ?></h2>
                <p><?= $book['author'] ?></p>
                <p>$<?= $book['price'] ?></p>
                <p><?= $book['description'] ?></p>
                <a href="cart.php?action=add&bookId=<?= $book['id'] ?>" class="btn btn-success">Add to Cart</a>
            </div>
        </div>
    </div>
</body>
</html>
