<?php

require_once ROOT . '/app/config/database.php';

// 🔐 Si ya tienes middleware, aquí debería ir
require_once ROOT . '/app/middleware/AuthMiddleware.php';

$db = Database::connect();

/*
    Vamos a mostrar métricas importantes:

    ✔ productos totales
    ✔ cajas totales
    ✔ productos por vencerse
*/

$totalProducts = $db->query("SELECT COUNT(*) FROM products")
    ->fetchColumn();

$totalBoxes = $db->query("SELECT COUNT(*) FROM boxes")
    ->fetchColumn();

$expiring = $db->query("
    SELECT COUNT(*)
    FROM box_products
    WHERE expiration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
")->fetchColumn();


// 👇 Render de vista PRO
ob_start();

require ROOT . '/app/views/dashboard/index.php';

$content = ob_get_clean();

require ROOT . '/app/views/layout.php';
