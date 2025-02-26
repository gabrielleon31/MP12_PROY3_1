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
</head>
<body>
    <header>
        <h1>Tu Carrito</h1>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="checkout.php">Proceder al pago</a>
        </nav>
    </header>

    <main>
        <h2>Productos en tu carrito</h2>
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <ul>
                <?php
                $total = 0;
                $subtotal = 0; // Subtotal sin IVA
                foreach ($_SESSION['cart'] as $item):
                    // Obtener el producto de la base de datos
                    $stmt = $db->prepare('SELECT * FROM products WHERE id = :id');
                    $stmt->execute(['id' => $item['id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Verificar si el producto existe
                    if ($product) {
                        $subtotal_item = $product['price'] * $item['quantity'];
                        $subtotal += $subtotal_item;
                        $total += $subtotal_item;
                        echo "<li>{$product['name']} - \${$product['price']} x {$item['quantity']} = \${$subtotal_item} ";
                        echo "<a href='cart.php?action=remove&id={$product['id']}'>Eliminar</a></li>";
                    }
                endforeach;

                // Calcular el IVA (21%)
                $iva = $subtotal * 0.21;
                $total_con_iva = $subtotal + $iva;
                ?>
            </ul>
            <h3>Subtotal: $<?= number_format($subtotal, 2) ?></h3>
            <h3>IVA (21%): $<?= number_format($iva, 2) ?></h3>
            <h3>Total: $<?= number_format($total_con_iva, 2) ?></h3>
        <?php else: ?>
            <p>No hay productos en tu carrito.</p>
        <?php endif; ?>
    </main>
</body>
</html>