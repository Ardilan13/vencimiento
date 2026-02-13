<?php
// app/views/reportes/index.php
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <h1 class="text-4xl font-bold text-gray-800 mb-8">Reportes de Inventario</h1>

    <!-- Selectores de Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="/claude/index.php?action=reportes" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Tipo de Reporte -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Reporte</label>
                <select name="tipo" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="vencimiento" <?php echo $tipo_reporte === 'vencimiento' ? 'selected' : ''; ?>>📅 Por Vencimiento</option>
                    <option value="stock_bajo" <?php echo $tipo_reporte === 'stock_bajo' ? 'selected' : ''; ?>>📦 Stock Bajo</option>
                    <option value="movimientos" <?php echo $tipo_reporte === 'movimientos' ? 'selected' : ''; ?>>📊 Movimientos</option>
                    <option value="inventario_general" <?php echo $tipo_reporte === 'inventario_general' ? 'selected' : ''; ?>>📋 Inventario General</option>
                </select>
            </div>

            <!-- Sede -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sede</label>
                <select name="sede" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?php echo $sede['id']; ?>" <?php echo $sede_id == $sede['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sede['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Fecha Desde (solo para algunos reportes) -->
            <?php if (in_array($tipo_reporte, ['vencimiento', 'movimientos'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Desde</label>
                    <input type="date" name="fecha_desde" value="<?php echo $fecha_desde; ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Fecha Hasta -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hasta</label>
                    <input type="date" name="fecha_hasta" value="<?php echo $fecha_hasta; ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            <?php endif; ?>

            <!-- Botón Generar -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- Reporte -->
    <?php if (!empty($reporte['datos'])): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Header del Reporte -->
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                <h2 class="text-xl font-bold text-white">
                    <?php echo htmlspecialchars($reporte['titulo']); ?>
                </h2>
                <p class="text-purple-100 text-sm mt-1">
                    <?php 
                    if (in_array($tipo_reporte, ['vencimiento', 'movimientos'])) {
                        echo 'Período: ' . date('d/m/Y', strtotime($fecha_desde)) . ' al ' . date('d/m/Y', strtotime($fecha_hasta));
                    } else {
                        echo 'Generado: ' . date('d/m/Y H:i');
                    }
                    ?>
                </p>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <?php foreach ($reporte['columnas'] as $columna): ?>
                                <th class="px-6 py-4 text-left font-semibold text-gray-700">
                                    <?php echo htmlspecialchars($columna); ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporte['datos'] as $fila): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <?php 
                                // Mapear columnas a propiedades del objeto
                                foreach ($reporte['columnas'] as $columna):
                                    $clave = strtolower(str_replace(' ', '_', $columna));
                                    $valor = $fila[$clave] ?? '-';
                                    
                                    // Formatear valores especiales
                                    if (strpos($clave, 'fecha') !== false && $valor !== '-') {
                                        $valor = date('d/m/Y', strtotime($valor));
                                    } elseif (strpos($clave, 'dias') !== false && is_numeric($valor)) {
                                        if ($valor < 0) {
                                            $valor = '<span class="text-red-700 font-semibold">Vencido hace ' . abs($valor) . '</span>';
                                        } elseif ($valor <= 7) {
                                            $valor = '<span class="text-orange-700 font-semibold">' . $valor . ' días</span>';
                                        }
                                    }
                                    ?>
                                    <td class="px-6 py-4 text-gray-700">
                                        <?php echo $valor; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer del Reporte -->
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-between items-center">
                <p class="text-gray-700 font-semibold">
                    Total de registros: <span class="text-lg text-purple-600"><?php echo count($reporte['datos']); ?></span>
                </p>
                <a href="/claude/index.php?action=exportar_reporte&tipo=<?php echo $tipo_reporte; ?>&sede=<?php echo $sede_id; ?>&fecha_desde=<?php echo $fecha_desde; ?>&fecha_hasta=<?php echo $fecha_hasta; ?>" 
                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold">
                    📥 Descargar CSV
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-lg text-gray-500 mb-4">
                📊 Selecciona un reporte y haz clic en "Generar" para ver los datos
            </p>
            <p class="text-gray-400 text-sm">
                Tipos disponibles: Por Vencimiento, Stock Bajo, Movimientos, Inventario General
            </p>
        </div>
    <?php endif; ?>
</div>
