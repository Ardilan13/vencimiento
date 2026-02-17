<?php
// app/views/sedes/listado.php
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-4xl font-bold text-gray-800">Gestión de Sedes</h1>
        <a href="/vencimiento/index.php?action=crear_sede" class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition">
            + Crear Sede
        </a>
    </div>

    <!-- Tabla de Sedes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($sedes)): ?>
            <div class="col-span-3 bg-white rounded-lg shadow p-12 text-center">
                <p class="text-xl text-gray-500 mb-4">No hay sedes registradas</p>
                <a href="/vencimiento/index.php?action=crear_sede" class="text-blue-600 hover:text-blue-800 font-semibold">
                    Crear la primera sede →
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($sedes as $sede): ?>
                <?php
                $estado_clase = $sede['estado'] === 'activa' ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-gray-50';
                $estado_icono = $sede['estado'] === 'activa' ? '🟢' : '⚫';
                ?>
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition border-l-4 <?php echo $estado_clase; ?> p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">
                                <?php echo htmlspecialchars($sede['nombre']); ?>
                            </h3>
                            <p class="text-gray-600">
                                <span class="font-semibold">📍 Ciudad:</span> <?php echo htmlspecialchars($sede['ciudad'] ?? 'N/A'); ?>
                            </p>
                        </div>
                        <span class="text-2xl"><?php echo $estado_icono; ?></span>
                    </div>

                    <div class="space-y-2 mb-4 text-gray-700">
                        <p>
                            <span class="font-semibold">📍 Dirección:</span> 
                            <?php echo htmlspecialchars($sede['direccion'] ?? 'No especificada'); ?>
                        </p>
                        <p>
                            <span class="font-semibold">☎️ Teléfono:</span> 
                            <?php echo htmlspecialchars($sede['telefono'] ?? 'No especificado'); ?>
                        </p>
                        <p>
                            <span class="font-semibold">👥 Usuarios activos:</span> 
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded inline-block">
                                <?php echo $sede['usuarios_activos']; ?>
                            </span>
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <a href="/vencimiento/index.php?action=editar_sede&id=<?php echo $sede['id']; ?>" 
                           class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center font-semibold">
                            Editar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Resumen -->
    <?php if (!empty($sedes)): ?>
        <div class="mt-8 bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Resumen de Sedes</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600"><?php echo count($sedes); ?></p>
                    <p class="text-gray-600">Total de Sedes</p>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">
                        <?php echo count(array_filter($sedes, fn($s) => $s['estado'] === 'activa')); ?>
                    </p>
                    <p class="text-gray-600">Sedes Activas</p>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">
                        <?php echo array_sum(array_column($sedes, 'usuarios_activos')); ?>
                    </p>
                    <p class="text-gray-600">Usuarios Totales</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
