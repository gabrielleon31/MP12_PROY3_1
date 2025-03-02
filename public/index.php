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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Bienvenido a nuestra tienda</h1>
        <nav>
            <a href="cart.php">
                <img src="../assets/images/carrito.png" alt="Carrito de compras" width="30">
            </a>        
        </nav>
    </header>

    <main>
        <?php foreach ($products as $product): ?>
            <div class="product">
                <h2><?= $product['name'] ?></h2>
                <p><?= $product['description'] ?></p>
                <p>Precio: $<?= number_format($product['price'], 2) ?></p>
                <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" width="100">
                <a href="cart.php?action=add&id=<?= $product['id'] ?>">Añadir al carrito</a>
            </div>
        <?php endforeach; ?>
    </main>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 Tienda E-commerce. Todos los derechos reservados.</p>
            <nav>
                <a href="#">Términos y condiciones</a> | 
                <a href="#">Política de privacidad</a> | 
                <a href="#">Contacto</a>
            </nav>
        </div>
    </footer>
</body>
</html>
