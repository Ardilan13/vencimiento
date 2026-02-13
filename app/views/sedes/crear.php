<?php
// app/views/sedes/crear.php
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <a href="/claude/index.php?action=sedes" class="text-blue-600 hover:text-blue-800 font-semibold">
            ← Volver a sedes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Crear Nueva Sede</h1>

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

        <form method="POST" action="/claude/index.php?action=crear_sede" class="space-y-6">
            
            <!-- Nombre -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre de la Sede</label>
                <input type="text" name="nombre" required
                       value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Sede Centro">
            </div>

            <!-- Ciudad -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Ciudad</label>
                <input type="text" name="ciudad" required
                       value="<?php echo htmlspecialchars($form_data['ciudad'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Cartagena">
            </div>

            <!-- Dirección -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Dirección</label>
                <textarea name="direccion" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                          placeholder="Calle Principal #123, Apto 4A"><?php echo htmlspecialchars($form_data['direccion'] ?? ''); ?></textarea>
            </div>

            <!-- Teléfono -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                <input type="tel" name="telefono"
                       value="<?php echo htmlspecialchars($form_data['telefono'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="+57 1 2345678">
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    Crear Sede
                </button>
                <a href="/claude/index.php?action=sedes" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition font-semibold text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
