<?php
// Incluir el autoload de Stripe
require_once('../stripe/autoload.php');  // Asegúrate de que la ruta sea correcta

// Configurar Stripe con tu clave secreta
\Stripe\Stripe::setApiKey('sk_test_TU_CLAVE_SECRETA'); // Usa tu clave secreta de Stripe

// Verificar si el formulario es enviado para realizar el pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['total'] * 100;  // Convertir el total a centavos (Stripe maneja los montos en centavos)

    try {
        // Crear un pago con Stripe (PaymentIntent)
        $charge = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'payment_method' => $_POST['payment_method_id'],
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);

        // Redirigir a la página de éxito
        header('Location: success.php');
        exit();
    } catch (\Stripe\Exception\CardException $e) {
        // Mostrar error si el pago falla
        echo 'Error al realizar el pago: ' . $e->getError()->message;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <script src="https://js.stripe.com/v3/"></script> <!-- Script de Stripe -->
</head>
<body>
    <h1>Realiza tu pago</h1>
    <form action="checkout.php" method="POST">
        <input type="hidden" name="total" value="<?= $_POST['total'] ?>"> <!-- Total del carrito -->

        <div id="card-element">
            <!-- Aquí aparecerá el campo para la tarjeta -->
        </div>

        <button type="submit">Pagar</button>
    </form>

    <script>
        var stripe = Stripe('pk_test_TU_CLAVE_PUBLICA');  // Tu clave pública de Stripe
        var elements = stripe.elements();
        var card = elements.create('card');
        card.mount('#card-element');
    </script>
</body>
</html>