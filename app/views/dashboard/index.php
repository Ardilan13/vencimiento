<h1 class="text-3xl font-bold mb-8">
    Dashboard de Inventario
</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Productos -->
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Productos</p>

        <h2 class="text-4xl font-bold mt-2">
            <?= $totalProducts ?>
        </h2>
    </div>

    <!-- Cajas -->
    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">Cajas / Lotes</p>

        <h2 class="text-4xl font-bold mt-2">
            <?= $totalBoxes ?>
        </h2>
    </div>

    <!-- Vencimientos -->
    <div class="bg-white p-6 rounded-2xl shadow border-l-8 border-red-500">
        <p class="text-gray-500">Por vencerse (30 días)</p>

        <h2 class="text-4xl font-bold mt-2 text-red-600">
            <?= $expiring ?>
        </h2>
    </div>

</div>