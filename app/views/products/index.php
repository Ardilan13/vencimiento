<?php require 'app/middleware/AuthMiddleware.php'; ?>

<h1>Productos</h1>

<form method="POST">
    <input name="name" placeholder="Nombre" required>
    <input name="description" placeholder="Descripción">
    <button>Crear</button>
</form>

<hr>

<?php foreach ($products as $p): ?>

    <div>
        <b><?= $p['name'] ?></b>
        - <?= $p['description'] ?>
    </div>

<?php endforeach; ?>