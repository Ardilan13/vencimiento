<?php
// app/views/inventory/crear_producto.php
?>
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            <i class="fas fa-plus-circle text-purple-600 mr-2"></i> Crear Nuevo Producto
        </h2>
        <p class="text-gray-600 mb-6">Completa el formulario para crear un nuevo producto</p>

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
            <!-- Nombre Producto -->
            <div>
                <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tag"></i> Nombre del Producto *
                </label>
                <input 
                    type="text" 
                    id="nombre" 
                    name="nombre" 
                    required
                    placeholder="Ej: Whey Protein Gold Standard 2kg"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>"
                >
            </div>

            <!-- Código SKU -->
            <div>
                <label for="codigo_sku" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-barcode"></i> Código SKU
                </label>
                <input 
                    type="text" 
                    id="codigo_sku" 
                    name="codigo_sku"
                    placeholder="Ej: SKU001"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    value="<?php echo htmlspecialchars($form_data['codigo_sku'] ?? ''); ?>"
                >
            </div>

            <!-- Categoría -->
            <div>
                <label for="categoria_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-th-list"></i> Categoría *
                </label>
                <select 
                    id="categoria_id" 
                    name="categoria_id" 
                    required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                >
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>"
                            <?php echo (isset($form_data['categoria_id']) && $form_data['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left"></i> Descripción
                </label>
                <textarea 
                    id="descripcion" 
                    name="descripcion"
                    rows="4"
                    placeholder="Descripción detallada del producto"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                ><?php echo htmlspecialchars($form_data['descripcion'] ?? ''); ?></textarea>
            </div>

            <!-- Precios -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="precio_costo" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign"></i> Precio de Costo *
                    </label>
                    <input 
                        type="number" 
                        id="precio_costo" 
                        name="precio_costo" 
                        required
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                        value="<?php echo htmlspecialchars($form_data['precio_costo'] ?? ''); ?>"
                    >
                </div>
                <div>
                    <label for="precio_venta" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign"></i> Precio de Venta *
                    </label>
                    <input 
                        type="number" 
                        id="precio_venta" 
                        name="precio_venta" 
                        required
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                        value="<?php echo htmlspecialchars($form_data['precio_venta'] ?? ''); ?>"
                    >
                </div>
            </div>

            <!-- Stock Mínimo -->
            <div>
                <label for="stock_minimo" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-cubes"></i> Stock Mínimo (por defecto)
                </label>
                <input 
                    type="number" 
                    id="stock_minimo" 
                    name="stock_minimo"
                    min="1"
                    value="<?php echo htmlspecialchars($form_data['stock_minimo'] ?? 10); ?>"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                >
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-6">
                <button 
                    type="submit" 
                    class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 px-6 rounded-lg hover:shadow-lg transform hover:scale-105 transition duration-200"
                >
                    <i class="fas fa-check-circle mr-2"></i> Crear Producto
                </button>
                <a 
                    href="/vencimiento/index.php?action=inventory" 
                    class="flex-1 bg-gray-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-600 transition text-center"
                >
                    <i class="fas fa-times-circle mr-2"></i> Cancelar
                </a>
            </div>
        </form>

        <!-- Información útil -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
            <p class="text-sm text-blue-800 font-semibold mb-2">
                <i class="fas fa-info-circle"></i> Información útil:
            </p>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• El código SKU debe ser único para cada producto</li>
                <li>• El precio de venta debe ser mayor al precio de costo</li>
                <li>• Los productos creados se pueden usar para agregar lotes</li>
            </ul>
        </div>
    </div>
</div>
