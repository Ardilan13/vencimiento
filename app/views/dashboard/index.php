<?php
// app/views/dashboard/index.php
?>
<div class="space-y-6">
    <?php if ($usuario['rol'] === 'superadmin'): ?>
        <!-- Dashboard SuperAdmin -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Sedes -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Total de Sedes</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $dashboard['total_sedes']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Productos -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Productos Activos</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $dashboard['total_productos']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-boxes text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Alertas Activas -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Alertas de Vencimiento</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $dashboard['alertas_activas']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bell text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Lotes Próximos Vencer -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Próximos a Vencer</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $dashboard['lotes_proximos_vencer']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hourglass-end text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lotes Vencidos -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Productos Vencidos
            </h3>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <p class="text-red-800 font-semibold"><?php echo $dashboard['lotes_vencidos']; ?> lotes vencidos en el sistema</p>
                <p class="text-red-700 text-sm mt-1">Se recomienda proceder con la eliminación de estos productos.</p>
            </div>
        </div>

        <!-- Información por Sede -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-bar text-purple-600 mr-2"></i> Resumen por Sede
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sede</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Lotes</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Vencidos</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($dashboard['sedes_info'] as $sede): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($sede['nombre']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo $sede['cantidad']; ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="status-badge badge-danger"><?php echo $sede['vencidos'] ?? 0; ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="/index.php?action=inventory&sede=<?php echo $sede['id']; ?>" class="text-purple-600 hover:text-purple-800 font-semibold">
                                        Ver Inventario
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- Dashboard Usuario Regular / Encargado -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Productos -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Productos en Sede</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $dashboard['total_productos']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-boxes text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Lotes -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Total de Lotes</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $dashboard['total_lotes']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cube text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Stock Total -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Stock Total</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $dashboard['stock_total']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Alertas Activas -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Alertas Activas</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $dashboard['alertas_activas']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bell text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos Próximos a Vencer -->
        <?php if (!empty($dashboard['proximos_vencer'])): ?>
            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-hourglass-end text-yellow-600 mr-2"></i> Próximos a Vencer (7 días)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-yellow-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Producto</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha Vencimiento</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Días Restantes</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($dashboard['proximos_vencer'] as $producto): ?>
                                <tr class="hover:bg-yellow-50 transition">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                        <?php echo htmlspecialchars($producto['nombre']); ?>
                                        <span class="text-gray-500 text-xs ml-2">(<?php echo htmlspecialchars($producto['codigo_sku']); ?>)</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($producto['fecha_vencimiento'])); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="status-badge badge-warning"><?php echo $producto['dias_para_vencer']; ?> días</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo $producto['cantidad_disponible']; ?> unidades</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Productos Vencidos -->
        <?php if (!empty($dashboard['vencidos'])): ?>
            <div class="card p-6 border-l-4 border-red-500">
                <h3 class="text-lg font-bold text-red-800 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i> Productos Vencidos
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Producto</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha Vencimiento</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($dashboard['vencidos'] as $producto): ?>
                                <tr class="hover:bg-red-50 transition">
                                    <td class="px-6 py-4 text-sm font-semibold text-red-800">
                                        <?php echo htmlspecialchars($producto['nombre']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-red-600"><?php echo date('d/m/Y', strtotime($producto['fecha_vencimiento'])); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="status-badge badge-danger"><?php echo $producto['cantidad_disponible']; ?> unidades</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>