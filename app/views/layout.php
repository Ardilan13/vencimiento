<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="/vencimiento/assets/css/output.css" rel="stylesheet">

    <title>Inventario</title>

</head>

<body class="bg-gray-100">

    <nav class="bg-slate-900 text-white p-4 flex gap-6">

        <a href="?url=/" class="font-bold">Dashboard</a>
        <a href="?url=products">Productos</a>
        <a href="?url=boxes">Cajas</a>
        <a href="?url=alerts">Vencimientos</a>

    </nav>

    <div class="p-8">
        <?= $content ?>
    </div>

</body>

</html>