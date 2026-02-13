<?php
// app/views/inventory/agregar_lote.php
?>
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            <i class="fas fa-cube text-purple-600 mr-2"></i> Agregar Lote de Producto
        </h2>
        <p class="text-gray-600 mb-6">Completa el formulario para registrar un nuevo lote con fecha de vencimiento</p>

        <?php if (!empty($errores)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <p class="text-red-800 font-semibold mb-2">
                    <i class="fas fa-exclamation-circle"></i> Errores encontrados:
                </p>
                <ul class="text-red-700 text-sm space-y-1">
                    <?php foreach ($errores as $error): ?>
                        <li>• <?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <!-- Producto -->
            <div>
                <label for="producto_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-box"></i> Selecciona Producto *
                </label>
                <select 
                    id="producto_id" 
                    name="producto_id" 
                    required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    onchange="actualizarInfoProducto()"
                >
                    <option value="">-- Selecciona un producto --</option>
                    <?php foreach ($productos as $producto): ?>
                        <option 
                            value="<?php echo $producto['id']; ?>"
                            data-sku="<?php echo htmlspecialchars($producto['codigo_sku']); ?>"
                            data-precio="<?php echo $producto['precio_venta']; ?>"
                            <?php echo (isset($form_data['producto_id']) && $form_data['producto_id'] == $producto['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($producto['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Info Producto -->
            <div id="infoProducto" class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500 hidden">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-blue-600 font-semibold">Código SKU</p>
                        <p id="productoSku" class="text-blue-900 font-semibold">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-600 font-semibold">Precio de Venta</p>
                        <p id="productoPrecio" class="text-blue-900 font-semibold">-</p>
                    </div>
                </div>
            </div>

            <!-- Número de Lote -->
            <div>
                <label for="numero_lote" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-hashtag"></i> Número/Código de Lote
                </label>
                <input 
                    type="text" 
                    id="numero_lote" 
                    name="numero_lote"
                    placeholder="Ej: LOTE-2024-001"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    value="<?php echo htmlspecialchars($form_data['numero_lote'] ?? ''); ?>"
                >
            </div>

            <!-- Cantidad -->
            <div>
                <label for="cantidad" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calculator"></i> Cantidad de Unidades *
                </label>
                <input 
                    type="number" 
                    id="cantidad" 
                    name="cantidad" 
                    required
                    min="1"
                    placeholder="Ej: 100"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    value="<?php echo htmlspecialchars($form_data['cantidad'] ?? ''); ?>"
                >
            </div>

            <!-- Fecha de Vencimiento -->
            <div>
                <label for="fecha_vencimiento" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-times"></i> Fecha de Vencimiento *
                </label>
                <input 
                    type="date" 
                    id="fecha_vencimiento" 
                    name="fecha_vencimiento" 
                    required
                    min="<?php echo date('Y-m-d'); ?>"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    value="<?php echo htmlspecialchars($form_data['fecha_vencimiento'] ?? ''); ?>"
                    onchange="calcularDiasVencimiento()"
                >
            </div>

            <!-- Información de Vencimiento -->
            <div id="diasVencimiento" class="p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500 hidden">
                <p class="text-yellow-800 font-semibold">
                    <i class="fas fa-hourglass-end mr-2"></i> Falta <span id="diasNum">0</span> días para el vencimiento
                </p>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-6">
                <button 
                    type="submit" 
                    class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 px-6 rounded-lg hover:shadow-lg transform hover:scale-105 transition duration-200"
                >
                    <i class="fas fa-plus-circle mr-2"></i> Agregar Lote
                </button>
                <a 
                    href="/index.php?action=inventory" 
                    class="flex-1 bg-gray-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-600 transition text-center"
                >
                    <i class="fas fa-times-circle mr-2"></i> Cancelar
                </a>
            </div>
        </form>

        <!-- Información útil -->
        <div class="mt-8 space-y-4">
            <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-blue-800 font-semibold mb-2">
                    <i class="fas fa-info-circle"></i> ¿Cómo funcionan los lotes?
                </p>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Cada lote tiene su propia fecha de vencimiento</li>
                    <li>• El sistema calcula automáticamente los días restantes</li>
                    <li>• Se generan alertas para lotes próximos a vencer (7 días)</li>
                    <li>• Puedes tener múltiples lotes del mismo producto con diferentes vencimientos</li>
                </ul>
            </div>

            <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                <p class="text-sm text-green-800 font-semibold mb-2">
                    <i class="fas fa-lightbulb"></i> Recomendaciones:
                </p>
                <ul class="text-sm text-green-700 space-y-1">
                    <li>• Establece fechas de vencimiento realistas</li>
                    <li>• Usa un sistema de códigos de lote consistente</li>
                    <li>• Revisa regularmente los lotes próximos a vencer</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function actualizarInfoProducto() {
    const select = document.getElementById('producto_id');
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('infoProducto');

    if (select.value) {
        document.getElementById('productoSku').textContent = option.getAttribute('data-sku');
        document.getElementById('productoPrecio').textContent = '$' + parseFloat(option.getAttribute('data-precio')).toLocaleString('es-CO');
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
}

function calcularDiasVencimiento() {
    const fechaInput = document.getElementById('fecha_vencimiento').value;
    const diasDiv = document.getElementById('diasVencimiento');

    if (fechaInput) {
        const fecha = new Date(fechaInput);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        const diferencia = fecha - hoy;
        const dias = Math.ceil(diferencia / (1000 * 60 * 60 * 24));

        document.getElementById('diasNum').textContent = dias;

        if (dias > 0) {
            diasDiv.classList.remove('hidden');
        } else {
            diasDiv.classList.add('hidden');
        }
    }
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function() {
    actualizarInfoProducto();
    calcularDiasVencimiento();
});
</script>
