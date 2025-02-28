<?php
require_once('../stripe/autoload.php'); // Asegúrate de que la ruta sea correcta

// Configuración de la clave secreta de Stripe
\Stripe\Stripe::setApiKey('-'); // Usa tu clave secreta de Stripe
?>
