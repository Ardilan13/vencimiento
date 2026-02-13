<?php
// app/views/alertas/configurar.php
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <a href="/index.php?action=alertas" class="text-blue-600 hover:text-blue-800 font-semibold">
            ← Volver a alertas
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Configurar Alertas de Vencimiento</h1>

        <?php if (!empty($errores)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="text-red-800 font-semibold mb-2">Errores encontrados:</h3>
                <ul class="text-red-700">
                    <?php foreach ($errores as $error): ?>
                        <li>• <?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/index.php?action=configurar_alertas" class="space-y-8">
            
            <!-- Información de tipos de alerta -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-semibold text-blue-900 mb-4">Tipos de Alerta</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔴</span>
                        <p class="text-blue-800"><strong>Vencido:</strong> Productos con fecha de vencimiento pasada</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🟠</span>
                        <p class="text-blue-800"><strong>Crítico:</strong> Menos días de los configurados</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🟡</span>
                        <p class="text-blue-800"><strong>Warning:</strong> Entre crítico y warning configurados</p>
                    </div>
                </div>
            </div>

            <!-- Días Crítico -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    🟠 Días Crítico (En rojo)
                </label>
                <p class="text-gray-600 text-sm mb-3">
                    ¿En cuántos días o menos deseas que sea CRÍTICO? (Ej: 1 = Hoy o mañana)
                </p>
                <input type="number" name="dias_critico" required min="0" max="30"
                       value="<?php echo $configuraciones['dias_critico'] ?? 1; ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg">
                <div class="mt-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
                    <p class="text-orange-800 text-sm">
                        <strong>Ejemplo:</strong> Si configuras 1, los productos que vencen hoy o mañana (0-1 días) mostrarán alerta 🟠 CRÍTICA
                    </p>
                </div>
            </div>

            <!-- Días Warning -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    🟡 Días Warning (En amarillo)
                </label>
                <p class="text-gray-600 text-sm mb-3">
                    ¿En cuántos días o menos deseas que sea WARNING? (Ej: 7 = Una semana)
                </p>
                <input type="number" name="dias_warning" required min="1" max="90"
                       value="<?php echo $configuraciones['dias_warning'] ?? 7; ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg">
                <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-yellow-800 text-sm">
                        <strong>Ejemplo:</strong> Si configuras 7, los productos que vencen en 7 días o menos mostrarán alerta 🟡 WARNING
                    </p>
                </div>
            </div>

            <!-- Vista Previa -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Vista Previa de tu Configuración</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-4">
                        <span class="text-2xl">🔴</span>
                        <p class="text-gray-700">
                            <strong>Vencido:</strong> Fecha vencimiento < hoy
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-2xl">🟠</span>
                        <p class="text-gray-700">
                            <strong>Crítico:</strong> 0 a <?php echo htmlspecialchars($configuraciones['dias_critico'] ?? 1); ?> días
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-2xl">🟡</span>
                        <p class="text-gray-700">
                            <strong>Warning:</strong> <?php echo htmlspecialchars(($configuraciones['dias_critico'] ?? 1) + 1); ?> a <?php echo htmlspecialchars($configuraciones['dias_warning'] ?? 7); ?> días
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información importante -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-semibold text-blue-900 mb-2">📌 Importante</h3>
                <ul class="text-blue-800 space-y-1 text-sm">
                    <li>✓ Estas configuraciones se aplican a todo el sistema</li>
                    <li>✓ Se pueden cambiar en cualquier momento</li>
                    <li>✓ Las alertas se recalculan automáticamente</li>
                    <li>✓ Recomendado: Crítico=1, Warning=7</li>
                </ul>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    💾 Guardar Configuración
                </button>
                <a href="/index.php?action=alertas" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition font-semibold text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
