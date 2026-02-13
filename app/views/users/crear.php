<?php
// app/views/users/crear.php
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <a href="/index.php?action=usuarios" class="text-blue-600 hover:text-blue-800 font-semibold">
            ← Volver a usuarios
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Crear Nuevo Usuario</h1>

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

        <form method="POST" action="/index.php?action=crear_usuario" class="space-y-6">
            
            <!-- Nombre -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
                <input type="text" name="nombre" required
                       value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Juan Pérez">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required
                       value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="juan@example.com">
            </div>

            <!-- Contraseña -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Mínimo 6 caracteres">
                <p class="text-gray-500 text-xs mt-1">Mínimo 6 caracteres, con mayúsculas y números recomendados</p>
            </div>

            <!-- Rol -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
                <select name="rol" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Selecciona un rol</option>
                    <option value="superadmin" <?php echo ($form_data['rol'] ?? '') === 'superadmin' ? 'selected' : ''; ?>>🔴 Super Administrador (Acceso total)</option>
                    <option value="admin" <?php echo ($form_data['rol'] ?? '') === 'admin' ? 'selected' : ''; ?>>🟣 Administrador (Gestión de sede)</option>
                    <option value="encargado" <?php echo ($form_data['rol'] ?? '') === 'encargado' ? 'selected' : ''; ?>>🔵 Encargado de Inventario</option>
                    <option value="vendedor" <?php echo ($form_data['rol'] ?? '') === 'vendedor' ? 'selected' : ''; ?>>🟢 Vendedor (Solo lectura)</option>
                </select>
            </div>

            <!-- Sede -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Asignar a Sede</label>
                <select name="sede_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Sin sede (SuperAdmin)</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?php echo $sede['id']; ?>" <?php echo ($form_data['sede_id'] ?? '') == $sede['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sede['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    Crear Usuario
                </button>
                <a href="/index.php?action=usuarios" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition font-semibold text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
