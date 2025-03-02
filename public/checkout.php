<?php
// Habilitar la visualización de errores en PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el autoload de Stripe
require_once('../vendor/autoload.php');

// Configurar Stripe con tu clave secreta
\Stripe\Stripe::setApiKey('-');

// Verificar si el formulario es enviado para realizar el pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_method_id'])) {
    $amount = isset($_POST['total']) ? intval($_POST['total']) * 100 : 0;  // Convertir el total a centavos y validar

    if ($amount <= 0) {
        die("Error: El monto total no es válido.");
    }

    try {
        // Crear un pago con Stripe (PaymentIntent)
        $charge = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'payment_method' => $_POST['payment_method_id'],
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);

        // Imprimir respuesta de Stripe para depuración
        echo "<pre>";
        print_r($charge);
        echo "</pre>";
        exit();

        // Redirigir a la página de éxito
        header('Location: success.php');
        exit();
    } catch (\Stripe\Exception\CardException $e) {
        // Mostrar error si el pago falla
        echo '<pre>';
        print_r($e);
        echo '</pre>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <script src="https://js.stripe.com/v3/"></script> <!-- Script de Stripe -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Estilos adicionales para Stripe Elements */
        #payment-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        #card-element {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Estilo de los botones */
        button#submit {
            background-color: #5469d4;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 1.1rem;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        button#submit:hover {
            background-color: #4353b3;
        }

        /* Estilos de los mensajes de error */
        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-top: 10px;
            text-align: center;
        }

        /* Estilos del header */
        header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        header a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }

        /* Estilo general para todo el body */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            font-size: 2rem;
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Realiza tu pago</h1>
    </header>

    <form action="checkout.php" method="POST" id="payment-form">
        <input type="hidden" name="total" value="<?= isset($_POST['total']) ? htmlspecialchars($_POST['total']) : 0 ?>">

        <div id="card-element">
            <!-- Aquí aparecerá el campo para la tarjeta -->
        </div>

        <button type="submit" id="submit">Pagar</button>

        <div class="error-message" id="card-errors"></div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Crear una instancia de Stripe usando tu clave pública
            var stripe = Stripe('-');
            var elements = stripe.elements();
            var card = elements.create('card');
            card.mount('#card-element');

            // Obtener el formulario de pago
            var paymentForm = document.getElementById('payment-form');
            var cardErrors = document.getElementById('card-errors');

            if (!paymentForm) {
                console.error("No se encontró el formulario #payment-form");
                return;
            }

            paymentForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                // Crear el método de pago con Stripe Elements
                const { paymentMethod, error } = await stripe.createPaymentMethod('card', card);

                if (error) {
                    console.error("Error en el pago:", error.message);
                    cardErrors.textContent = error.message;
                } else {
                    console.log("Método de pago creado:", paymentMethod.id);

                    // Crear un campo oculto con el PaymentMethod ID
                    var paymentMethodInput = document.createElement('input');
                    paymentMethodInput.setAttribute('type', 'hidden');
                    paymentMethodInput.setAttribute('name', 'payment_method_id');
                    paymentMethodInput.setAttribute('value', paymentMethod.id);
                    paymentForm.appendChild(paymentMethodInput);

                    console.log("Enviando formulario...");

                    // Verifica que paymentForm es un formulario HTML válido antes de hacer submit
                    if (paymentForm instanceof HTMLFormElement) {
                        // Ahora puedes utilizar el submit de manera segura
                        paymentForm.submit();
                    } else {
                        console.error("El formulario no es válido para enviar.");
                    }
                }
            });
        });

    </script>
</body>
</html>
