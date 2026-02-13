<?php
// app/views/inventory/detalles_producto.php
?>
<div class="space-y-6">
    <!-- Info Principal del Producto -->
    <div class="card p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Nombre del Producto</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($producto['nombre']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Código SKU</p>
                <p class="text-xl font-semibold text-purple-600"><?php echo htmlspecialchars($producto['codigo_sku']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Categoría</p>
                <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($producto['categoria']); ?></p>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t">
            <p class="text-sm text-gray-600 font-semibold mb-2">Descripción</p>
            <p class="text-gray-700"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
        </div>
    </div>

    <!-- Estadísticas del Producto -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card p-6">
            <p class="text-gray-600 text-sm font-semibold">Stock Total</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo $producto['stock'] ?? 0; ?></h3>
        </div>
        <div class="card p-6">
            <p class="text-gray-600 text-sm font-semibold">Total de Lotes</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo $producto['total_lotes'] ?? 0; ?></h3>
        </div>
        <div class="card p-6">
            <p class="text-gray-600 text-sm font-semibold">Cantidad Vencida</p>
            <h3 class="text-3xl font-bold text-red-600"><?php echo $producto['cantidad_vencida'] ?? 0; ?></h3>
        </div>
        <div class="card p-6">
            <p class="text-gray-600 text-sm font-semibold">Próx. Vencimiento</p>
            <?php if ($producto['proximo_vencimiento']): ?>
                <h3 class="text-xl font-bold text-gray-800"><?php echo date('d/m/Y', strtotime($producto['proximo_vencimiento'])); ?></h3>
            <?php else: ?>
                <p class="text-gray-500">Sin lotes</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Precios -->
    <div class="card p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-dollar-sign text-purple-600 mr-2"></i> Información de Precios
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-600 font-semibold mb-1">Precio de Costo</p>
                <p class="text-2xl font-bold text-blue-900">$<?php echo number_format($producto['precio_costo'], 2, ',', '.'); ?></p>
            </div>
            <div class="p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-green-600 font-semibold mb-1">Precio de Venta</p>
                <p class="text-2xl font-bold text-green-900">$<?php echo number_format($producto['precio_venta'], 2, ',', '.'); ?></p>
            </div>
            <div class="p-4 bg-purple-50 rounded-lg">
                <p class="text-sm text-purple-600 font-semibold mb-1">Margen de Ganancia</p>
                <?php $ganancia = (($producto['precio_venta'] - $producto['precio_costo']) / $producto['precio_costo']) * 100; ?>
                <p class="text-2xl font-bold text-purple-900"><?php echo number_format($ganancia, 2, ',', '.'); ?>%</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Lotes -->
    <div class="card">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-boxes text-purple-600 mr-2"></i> Lotes Registrados
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Número de Lote</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha Ingreso</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha Vencimiento</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Días Restantes</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cantidad</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Disponible</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($lotes)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p>No hay lotes registrados para este producto</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lotes as $lote): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800"><?php echo htmlspecialchars($lote['numero_lote']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($lote['fecha_ingreso'])); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($lote['fecha_vencimiento'])); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <?php
                                    $dias = $lote['dias_para_vencer'];
                                    if ($dias < 0) {
                                        $badge_class = 'badge-danger';
                                        $dias_text = abs($dias) . ' días vencido';
                                    } elseif ($dias <= 7) {
                                        $badge_class = 'badge-warning';
                                        $dias_text = $dias . ' días';
                                    } else {
                                        $badge_class = 'badge-success';
                                        $dias_text = $dias . ' días';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $badge_class; ?>"><?php echo $dias_text; ?></span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800"><?php echo $lote['cantidad']; ?></td>
                                <td class="px-6 py-4 font-semibold text-gray-800"><?php echo $lote['cantidad_disponible']; ?></td>
                                <td class="px-6 py-4">
                                    <?php
                                    $estado_badge = '';
                                    $estado_clase = '';

                                    if ($lote['dias_para_vencer'] < 0) {
                                        $estado_badge = 'Vencido';
                                        $estado_clase = 'badge-danger';
                                    } elseif ($lote['dias_para_vencer'] <= 7) {
                                        $estado_badge = 'Próx. Vencer';
                                        $estado_clase = 'badge-warning';
                                    } else {
                                        $estado_badge = 'Disponible';
                                        $estado_clase = 'badge-success';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $estado_clase; ?>"><?php echo $estado_badge; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex gap-3">
        <a href="/index.php?action=agregar_lote" class="btn-primary">
            <i class="fas fa-plus-circle mr-2"></i> Agregar Nuevo Lote
        </a>
        <a href="/index.php?action=inventory" class="btn-primary bg-gray-500 hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Inventario
        </a>
    </div>
</div>

<style>
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success {
        background-color: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }
</style>