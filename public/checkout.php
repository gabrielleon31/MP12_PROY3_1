<?php
// Habilitar la visualización de errores en PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el autoload de Stripe
require_once('../vendor/autoload.php');

// Configurar Stripe con tu clave secreta
\Stripe\Stripe::setApiKey('sk_test_51QxWJZDeBypX4ZzzTHr0i2PQZBtmRvbtXUQNk974ftxp2Z44XcWfhfdW4Jnb27wQRkrfckkJayiHTMWZ9DCOClLe00xbmVjy6l');

// Iniciar sesión para verificar si ya se procesó el pago
session_start();

// Verificar si ya se procesó el pago en esta sesión
if (isset($_SESSION['payment_processed']) && $_SESSION['payment_processed'] === true) {
    echo "Redirigiendo a success.php...";
    header('Location: ../public/success.php ');
    exit();
}

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

        // Verifica si el pago fue exitoso
        if ($charge->status == 'succeeded') {
            echo "El pago fue exitoso.";
            
            // Marcar el pago como procesado en la sesión para evitar duplicados
            $_SESSION['payment_processed'] = true;

            // Redirigir a la página de éxito
            header('Location: ../public/success.php');
            exit();
        } else {
            echo "El pago no fue exitoso. Estado del pago: " . $charge->status;
        }
    } catch (\Stripe\Exception\CardException $e) {
        // Mostrar error si el pago falla
        echo '<pre>';
        print_r($e);
        echo '</pre>';
        echo "Error en el pago: " . $e->getMessage();
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
            border-radius: 10px;
            padding: 6px;
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
            var stripe = Stripe('pk_test_51QxWJZDeBypX4ZzzOgJhTlOjo6WKSCZaeQ4rqqDj1uIDs5m8TUYER9nJfuriQ6ma8e49tm9BtMy0YvFbK0qHw4uQ0054Ezcijt');
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

                console.log("Formulario enviado, creando método de pago...");
                
                // Deshabilitar el botón de envío para evitar múltiples clics
                document.getElementById('submit').disabled = true;

                // Crear el método de pago con Stripe Elements
                const { paymentMethod, error } = await stripe.createPaymentMethod('card', card);

                if (error) {
                    console.error("Error al crear el método de pago:", error.message);
                    cardErrors.textContent = error.message;
                    // Habilitar el botón de nuevo en caso de error
                    document.getElementById('submit').disabled = false;
                } else {
                    console.log("Método de pago creado:", paymentMethod.id);

                    // Crear un campo oculto con el PaymentMethod ID
                    var paymentMethodInput = document.createElement('input');
                    paymentMethodInput.setAttribute('type', 'hidden');
                    paymentMethodInput.setAttribute('name', 'payment_method_id');
                    paymentMethodInput.setAttribute('value', paymentMethod.id);
                    paymentForm.appendChild(paymentMethodInput);

                    console.log("Formulario listo para enviar...");

                    // Usar requestSubmit() para enviar el formulario
                    paymentForm.requestSubmit();  // Este método es suficiente para enviar el formulario
                }
            });
        });
    </script>
</body>
</html>