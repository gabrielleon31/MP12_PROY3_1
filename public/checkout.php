<?php
// Incluir el autoload de Stripe
require_once('../vendor/autoload.php');

// Configurar Stripe con tu clave secreta
\Stripe\Stripe::setApiKey('sk_test_51QxWJZDeBypX4ZzzTHr0i2PQZBtmRvbtXUQNk974ftxp2Z44XcWfhfdW4Jnb27wQRkrfckkJayiHTMWZ9DCOClLe00xbmVjy6l'); // Usa tu clave secreta de Stripe

// Verificar si el formulario es enviado para realizar el pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_method_id'])) {
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
    <form action="checkout.php" method="POST" id="payment-form">
        <input type="hidden" name="total" value="<?= $_POST['total'] ?>"> <!-- Total del carrito -->

        <div id="card-element">
            <!-- Aquí aparecerá el campo para la tarjeta -->
        </div>

        <button type="submit" id="submit">Pagar</button>
    </form>

    <script>
        // Crear una instancia de Stripe usando tu clave pública
        var stripe = Stripe('pk_test_51QxWJZDeBypX4ZzzOgJhTlOjo6WKSCZaeQ4rqqDj1uIDs5m8TUYER9nJfuriQ6ma8e49tm9BtMy0YvFbK0qHw4uQ0054Ezcijt'); // Tu clave pública de Stripe
        var elements = stripe.elements();
        var card = elements.create('card');
        card.mount('#card-element');

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            // Crear el método de pago con el Stripe Elements
            const {paymentMethod, error} = await stripe.createPaymentMethod('card', card);

            if (error) {
                // Mostrar error si ocurre
                alert(error.message);
            } else {
                // Agregar el método de pago al formulario y enviarlo
                var paymentMethodInput = document.createElement('input');
                paymentMethodInput.setAttribute('type', 'hidden');
                paymentMethodInput.setAttribute('name', 'payment_method_id');
                paymentMethodInput.setAttribute('value', paymentMethod.id); // Se usa paymentMethod.id
                form.appendChild(paymentMethodInput);

                // Enviar el formulario
                form.submit();
            }
        });
    </script>
</body>
</html>
