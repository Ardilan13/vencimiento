<?php
// app/views/users/editar.php
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <a href="/index.php?action=usuarios" class="text-blue-600 hover:text-blue-800 font-semibold">
            ← Volver a usuarios
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Editar Usuario</h1>
        <p class="text-gray-600 mb-8">Modificando: <span class="font-semibold"><?php echo htmlspecialchars($usuario_editar['nombre']); ?></span></p>

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

        <form method="POST" action="/index.php?action=editar_usuario&id=<?php echo $usuario_editar['id']; ?>" class="space-y-6">
            
            <!-- Nombre -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
                <input type="text" name="nombre" required
                       value="<?php echo htmlspecialchars($usuario_editar['nombre']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Juan Pérez">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required
                       value="<?php echo htmlspecialchars($usuario_editar['email']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="juan@example.com">
            </div>

            <!-- Contraseña (opcional) -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nueva Contraseña (Opcional)</label>
                <input type="password" name="password"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Dejar en blanco para no cambiar">
                <p class="text-gray-500 text-xs mt-1">Si dejas en blanco, se mantiene la contraseña actual</p>
            </div>

            <!-- Rol -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
                <select name="rol" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="superadmin" <?php echo $usuario_editar['rol'] === 'superadmin' ? 'selected' : ''; ?>>🔴 Super Administrador</option>
                    <option value="admin" <?php echo $usuario_editar['rol'] === 'admin' ? 'selected' : ''; ?>>🟣 Administrador</option>
                    <option value="encargado" <?php echo $usuario_editar['rol'] === 'encargado' ? 'selected' : ''; ?>>🔵 Encargado de Inventario</option>
                    <option value="vendedor" <?php echo $usuario_editar['rol'] === 'vendedor' ? 'selected' : ''; ?>>🟢 Vendedor</option>
                </select>
            </div>

            <!-- Sede -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sede Asignada</label>
                <select name="sede_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Sin sede (SuperAdmin)</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?php echo $sede['id']; ?>" <?php echo $usuario_editar['sede_id'] == $sede['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sede['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                <div class="flex gap-6">
                    <label class="flex items-center">
                        <input type="radio" name="estado" value="activo" <?php echo $usuario_editar['estado'] === 'activo' ? 'checked' : ''; ?> class="mr-2">
                        <span class="text-green-700 font-semibold">✓ Activo</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="estado" value="inactivo" <?php echo $usuario_editar['estado'] === 'inactivo' ? 'checked' : ''; ?> class="mr-2">
                        <span class="text-red-700 font-semibold">✗ Inactivo</span>
                    </label>
                </div>
            </div>

            <!-- Información -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600">
                <p><strong>ID:</strong> <?php echo $usuario_editar['id']; ?></p>
                <p><strong>Registrado:</strong> <?php echo isset($usuario_editar['fecha_creacion']) ? date('d/m/Y H:i', strtotime($usuario_editar['fecha_creacion'])) : 'N/A'; ?></p>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    Guardar Cambios
                </button>
                <a href="/index.php?action=usuarios" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition font-semibold text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
