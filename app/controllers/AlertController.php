<?php

require 'app/config/database.php';

$db = Database::connect();

$expiring = $db->query("SELECT 
    p.name,
    b.code,
    quantity,
    expiration_date,
    DATEDIFF(expiration_date, CURDATE()) as days

FROM box_products bp
JOIN products p ON p.id = bp.product_id
JOIN boxes b ON b.id = bp.box_id

WHERE expiration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY expiration_date
")->fetchAll(PDO::FETCH_ASSOC);

require 'app/views/alerts/index.php';
