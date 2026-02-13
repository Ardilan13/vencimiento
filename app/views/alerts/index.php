<h1>⚠ Productos próximos a vencerse</h1>

<?php foreach ($expiring as $e): ?>

    <div style="color: <?= $e['days'] <= 7 ? 'red' : 'orange' ?>">

        <?= $e['name'] ?>
        | Caja: <?= $e['code'] ?>
        | Vence en <?= $e['days'] ?> días

    </div>

<?php endforeach; ?>