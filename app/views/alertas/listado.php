<?php
// app/views/alertas/listado.php
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-4xl font-bold text-gray-800">Alertas de Vencimiento</h1>
        <a href="/claude/index.php?action=configurar_alertas" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition">
            ⚙️ Configurar
        </a>
    </div>

    <!-- Información de configuración -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-8">
        <h3 class="font-semibold text-blue-900 mb-2">Umbrales Configurados</h3>
        <p class="text-blue-800">
            🔴 Crítico: 0-<?php echo $configuraciones['dias_critico']; ?> días | 
            🟠 Crítico: <?php echo $configuraciones['dias_critico']+1; ?>-<?php echo $configuraciones['dias_warning']; ?> días | 
            🟡 Warning: <?php echo $configuraciones['dias_warning']+1; ?>+ días
        </p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="/claude/index.php?action=alertas" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                <select name="estado" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="activa" <?php echo $filtro_estado === 'activa' ? 'selected' : ''; ?>>Activas</option>
                    <option value="resuelto" <?php echo $filtro_estado === 'resuelto' ? 'selected' : ''; ?>>Resueltas</option>
                    <option value="ignorado" <?php echo $filtro_estado === 'ignorado' ? 'selected' : ''; ?>>Ignoradas</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Alertas -->
    <div class="space-y-4">
        <?php if (empty($alertas)): ?>
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <p class="text-xl text-gray-500">
                    <?php echo $filtro_estado === 'activa' ? '✓ No hay alertas activas' : 'No hay alertas'; ?>
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($alertas as $alerta): ?>
                <?php
                $dias = $alerta['dias_para_vencer'];
                $tipo_badge = '';
                $icono = '';
                
                if ($dias < 0) {
                    $tipo_badge = 'bg-red-100 border-red-300';
                    $icono = '🔴';
                } elseif ($dias <= $configuraciones['dias_critico']) {
                    $tipo_badge = 'bg-orange-100 border-orange-300';
                    $icono = '🟠';
                } else {
                    $tipo_badge = 'bg-yellow-100 border-yellow-300';
                    $icono = '🟡';
                }
                ?>
                <div class="bg-white rounded-lg shadow border-l-4 <?php echo $alerta['clase_color']; ?> p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">
                                <?php echo htmlspecialchars($alerta['nombre']); ?>
                                <span class="text-gray-500 font-normal">( <?php echo htmlspecialchars($alerta['codigo_sku']); ?> )</span>
                            </h3>
                            <p class="text-gray-600">Lote: <?php echo htmlspecialchars($alerta['numero_lote']); ?></p>
                        </div>
                        <span class="text-3xl"><?php echo $icono; ?></span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                        <div class="bg-white rounded p-3">
                            <p class="text-gray-500 text-sm">Cantidad</p>
                            <p class="text-lg font-bold text-gray-800"><?php echo $alerta['cantidad_disponible']; ?></p>
                        </div>
                        <div class="bg-white rounded p-3">
                            <p class="text-gray-500 text-sm">Vencimiento</p>
                            <p class="text-lg font-bold text-gray-800"><?php echo date('d/m/Y', strtotime($alerta['fecha_vencimiento'])); ?></p>
                        </div>
                        <div class="bg-white rounded p-3">
                            <p class="text-gray-500 text-sm">Días</p>
                            <p class="text-lg font-bold text-gray-800">
                                <?php 
                                if ($dias < 0) {
                                    echo 'Vencido hace ' . abs($dias) . ' días';
                                } else {
                                    echo $dias . ' días';
                                }
                                ?>
                            </p>
                        </div>
                        <div class="bg-white rounded p-3">
                            <p class="text-gray-500 text-sm">Sede</p>
                            <p class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($alerta['sede_nombre']); ?></p>
                        </div>
                        <div class="bg-white rounded p-3">
                            <p class="text-gray-500 text-sm">Estado</p>
                            <p class="text-lg font-bold text-gray-800"><?php echo ucfirst($alerta['estado']); ?></p>
                        </div>
                    </div>

                    <?php if ($alerta['estado'] === 'activa'): ?>
                        <div class="flex gap-2">
                            <form method="POST" action="/claude/index.php?action=cambiar_estado_alerta" class="flex gap-2 flex-1">
                                <input type="hidden" name="lote_id" value="<?php echo $alerta['lote_producto_id']; ?>">
                                <button type="submit" name="estado" value="resuelto" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-semibold">
                                    ✓ Resuelto
                                </button>
                                <button type="submit" name="estado" value="ignorado" class="flex-1 bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition font-semibold">
                                    ✗ Ignorar
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
