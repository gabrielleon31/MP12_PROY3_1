<?php
try {
    // Conectar a la base de datos SQLite
    $db = new PDO('sqlite:public/ecommerce.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Eliminar productos existentes (si lo deseas)
    $db->exec("DELETE FROM products");

    // Crear la tabla products si no existe
    $db->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image_url TEXT NOT NULL
    )");

    // Insertar productos de nuevo si es necesario
    $db->exec("INSERT OR IGNORE INTO products (name, description, price, image_url) VALUES
        ('Air Jordan 1 Retro x Dior', 'Edición limitada de lujo de Air Jordan 1 con diseño de Dior, con una paleta de colores gris y detalles premium.', 18775, '../assets/images/Air Jordan 1 de Jordan x Dior.png'),
        ('Air Jordan 1 Retro High OG SE', 'Versión premium del clásico Air Jordan 1 High OG, una de las zapatillas más icónicas de la historia del baloncesto.', 13336, '../assets/images/Air Jordan 1 Retro High OG SE.png'),
        ('Air Jordan 4 Retro Eminem Carhartt', 'Edición limitada colaborativa entre Eminem y Carhartt sobre el modelo Jordan 4, con detalles inspirados en la moda de trabajo.', 57518, '../assets/images/Jordan 4 Retro Eminem Carhartt.png')
    ");

    echo "Base de datos configurada correctamente con nuevos productos.";
} catch (PDOException $e) {
    echo "Error al configurar la base de datos: " . $e->getMessage();
}
?>