<?php
session_start();
include('../includes/db.php');
$db = getDB();

// Mostrar productos de la base de datos
$stmt = $db->query('SELECT * FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda de E-commerce</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Bienvenido a nuestra tienda</h1>
        <nav>
            <a href="cart.php">Carrito</a>
        </nav>
    </header>

    <main>
        <?php foreach ($products as $product): ?>
            <div class="product">
                <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" width="100">
                <h2><?= $product['name'] ?></h2>
                <p><?= $product['description'] ?></p>
                <p>Precio: $<?= number_format($product['price'], 2) ?></p>
                <a href="cart.php?action=add&id=<?= $product['id'] ?>">Añadir al carrito</a>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>