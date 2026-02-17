<?php
// app/views/users/listado.php
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-4xl font-bold text-gray-800">Gestión de Usuarios</h1>
        <a href="/vencimiento/index.php?action=crear_usuario" class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition">
            + Crear Usuario
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="/vencimiento/index.php?action=usuarios" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
                <select name="rol" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Todos los roles</option>
                    <option value="superadmin" <?php echo $filtro_rol === 'superadmin' ? 'selected' : ''; ?>>Super Administrador</option>
                    <option value="admin" <?php echo $filtro_rol === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="encargado" <?php echo $filtro_rol === 'encargado' ? 'selected' : ''; ?>>Encargado de Inventario</option>
                    <option value="vendedor" <?php echo $filtro_rol === 'vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="activo" <?php echo $filtro_estado === 'activo' ? 'selected' : ''; ?>>Activo</option>
                    <option value="inactivo" <?php echo $filtro_estado === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Usuarios Registrados (<?php echo count($usuarios); ?>)</h2>
        </div>

        <?php if (empty($usuarios)): ?>
            <div class="p-6 text-center text-gray-500">
                <p class="text-lg">No hay usuarios registrados</p>
                <a href="/vencimiento/index.php?action=crear_usuario" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Crear el primer usuario →</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Nombre</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Email</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Rol</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Sede</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Estado</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $user): ?>
                            <?php
                            $rol_clase = '';
                            $rol_texto = '';
                            switch ($user['rol']) {
                                case 'superadmin':
                                    $rol_clase = 'bg-red-100 text-red-800';
                                    $rol_texto = '🔴 Super Admin';
                                    break;
                                case 'admin':
                                    $rol_clase = 'bg-purple-100 text-purple-800';
                                    $rol_texto = '🟣 Admin';
                                    break;
                                case 'encargado':
                                    $rol_clase = 'bg-blue-100 text-blue-800';
                                    $rol_texto = '🔵 Encargado';
                                    break;
                                case 'vendedor':
                                    $rol_clase = 'bg-green-100 text-green-800';
                                    $rol_texto = '🟢 Vendedor';
                                    break;
                            }

                            $estado_clase = $user['estado'] === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                            $estado_icono = $user['estado'] === 'activo' ? '✓' : '✗';
                            ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    <?php echo htmlspecialchars($user['nombre']); ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block <?php echo $rol_clase; ?> px-3 py-1 rounded-full text-xs font-semibold">
                                        <?php echo $rol_texto; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo htmlspecialchars($user['sede_nombre'] ?? 'Sin sede'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block <?php echo $estado_clase; ?> px-3 py-1 rounded-full text-xs font-semibold">
                                        <?php echo $estado_icono; ?> <?php echo ucfirst($user['estado']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="/vencimiento/index.php?action=editar_usuario&id=<?php echo $user['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-semibold inline-block mr-4">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
