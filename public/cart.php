<?php
session_start();
include('../includes/db.php');
$db = getDB();

// Verificar si se añadió un producto al carrito
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Verificar si el producto ya está en el carrito
    $product_found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity']++;
            $product_found = true;
            break;
        }
    }

    // Si no estaba en el carrito, agregarlo
    if (!$product_found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'quantity' => 1
        ];
    }

    // Redirigir al carrito
    header('Location: cart.php');
    exit();
}

// Verificar si se quiere eliminar un producto
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Recorrer el carrito y eliminar el producto
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    // Redirigir al carrito después de eliminar
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Tu Carrito</h1>
        <nav>
            <a href="index.php">
                <img src="../assets/images/home.png" alt="Casa" width="30">
            </a>     
            <a href="checkout.php">
                <img src="../assets/images/pago.png" alt="pago" width="30">
            </a>
        </nav>
    </header>

    <main>
    <h2>Productos en tu carrito</h2>
    <div class="cart-container">
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <ul class="cart-items">
                <?php
                $total = 0;
                $subtotal = 0;
                foreach ($_SESSION['cart'] as $item):
                    $stmt = $db->prepare('SELECT * FROM products WHERE id = :id');
                    $stmt->execute(['id' => $item['id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($product) {
                        $subtotal_item = $product['price'] * $item['quantity'];
                        $subtotal += $subtotal_item;
                        echo "<li>{$product['name']} - \${$product['price']} x {$item['quantity']} = \${$subtotal_item} ";
                        echo "<a href='cart.php?action=remove&id={$product['id']}'>Eliminar</a></li>";
                    }
                endforeach;

                $iva = $subtotal * 0.21;
                $total_con_iva = $subtotal + $iva;
                ?>
            </ul>
            <div class="cart-summary">
                <h3>Subtotal: $<?= number_format($subtotal, 2) ?></h3>
                <h3>IVA (21%): $<?= number_format($iva, 2) ?></h3>
                <h3>Total: $<?= number_format($total_con_iva, 2) ?></h3>
            </div>
        <?php else: ?>
            <p>No hay productos en tu carrito.</p>
        <?php endif; ?>
    </div>
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