<?php
// app/views/inventory/listado.php
?>
<div class="space-y-6">
    <!-- Filtros -->
    <div class="card p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-filter text-purple-600 mr-2"></i> Filtrar Inventario
        </h3>
        <form method="GET" action="/index.php?action=inventory" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="action" value="inventory">
            
            <!-- Sede -->
            <?php if ($usuario['rol'] === 'superadmin'): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sede</label>
                    <select name="sede" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition">
                        <option value="">Todas las sedes</option>
                        <?php foreach ($sedes as $sede): ?>
                            <option value="<?php echo $sede['id']; ?>" <?php echo ($filtro_sede == $sede['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sede['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Categoría -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Categoría</label>
                <select name="categoria" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>" <?php echo ($filtro_categoria == $categoria['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition">
                    <option value="">Todos</option>
                    <option value="disponible" <?php echo ($filtro_estado === 'disponible') ? 'selected' : ''; ?>>Disponibles</option>
                    <option value="proxima_vencer" <?php echo ($filtro_estado === 'proxima_vencer') ? 'selected' : ''; ?>>Próximos a Vencer</option>
                    <option value="vencido" <?php echo ($filtro_estado === 'vencido') ? 'selected' : ''; ?>>Vencidos</option>
                </select>
            </div>

            <!-- Botón -->
            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Acciones -->
    <div class="flex gap-3">
        <a href="/index.php?action=crear_producto" class="btn-primary">
            <i class="fas fa-plus mr-2"></i> Crear Producto
        </a>
        <a href="/index.php?action=agregar_lote" class="btn-primary">
            <i class="fas fa-cube mr-2"></i> Agregar Lote
        </a>
    </div>

    <!-- Tabla de Inventario -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-purple-600 to-pink-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Producto</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">SKU</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Categoría</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Lotes</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Stock</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Próx. Vencimiento</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($inventario)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p>No hay productos en el inventario</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inventario as $item): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['nombre']); ?></p>
                                        <?php if ($item['cantidad_vencida'] > 0): ?>
                                            <span class="status-badge badge-danger"><?php echo $item['cantidad_vencida']; ?> vencidas</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($item['codigo_sku']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($item['categoria']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="status-badge badge-info"><?php echo $item['total_lotes']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-800"><?php echo $item['stock_disponible'] ?? 0; ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if ($item['proximo_vencimiento']): ?>
                                        <?php 
                                        $dias = (new DateTime($item['proximo_vencimiento']))->diff(new DateTime())->days;
                                        $badge = $dias < 0 ? 'badge-danger' : ($dias <= 7 ? 'badge-warning' : 'badge-success');
                                        ?>
                                        <span class="status-badge <?php echo $badge; ?>">
                                            <?php echo date('d/m/Y', strtotime($item['proximo_vencimiento'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-500">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="/index.php?action=detalles_producto&id=<?php echo $item['producto_id']; ?>" 
                                       class="text-purple-600 hover:text-purple-800 font-semibold mr-3">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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

    .badge-success { background-color: #dcfce7; color: #166534; }
    .badge-warning { background-color: #fef3c7; color: #92400e; }
    .badge-danger { background-color: #fee2e2; color: #991b1b; }
    .badge-info { background-color: #cffafe; color: #164e63; }
</style>
