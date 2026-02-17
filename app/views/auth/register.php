<?php
// app/views/auth/register.php
?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-600 via-pink-500 to-red-500 p-4">
    <div class="w-full max-w-md">
        <!-- Card Principal -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header Decorativo -->
            <div class="h-32 bg-gradient-to-r from-purple-600 to-pink-600 flex items-end justify-center pb-6">
                <div class="text-white text-center">
                    <i class="fas fa-user-plus text-5xl mb-2"></i>
                    <h1 class="text-2xl font-bold">Crear Cuenta</h1>
                </div>
            </div>

            <!-- Contenido -->
            <div class="p-8">
                <p class="text-gray-600 text-center mb-6">Completa el formulario para registrarte</p>

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

                <form method="POST" action="/vencimiento/index.php?action=register" class="space-y-4">
                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user"></i> Nombre Completo
                        </label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            required 
                            placeholder="Juan Pérez"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition bg-gray-50"
                            value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            placeholder="tu@email.com"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition bg-gray-50"
                            value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                        >
                    </div>

                    <!-- Sede -->
                    <div>
                        <label for="sede_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-building"></i> Sede
                        </label>
                        <select 
                            id="sede_id" 
                            name="sede_id" 
                            required
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition bg-gray-50"
                        >
                            <option value="">Selecciona una sede</option>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?php echo $sede['id']; ?>" 
                                    <?php echo (isset($form_data['sede_id']) && $form_data['sede_id'] == $sede['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sede['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            minlength="6"
                            placeholder="Mínimo 6 caracteres"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition bg-gray-50"
                        >
                    </div>

                    <!-- Confirmar Password -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock"></i> Confirmar Contraseña
                        </label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            required 
                            minlength="6"
                            placeholder="Repite tu contraseña"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition bg-gray-50"
                        >
                    </div>

                    <!-- Botón Registrar -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 px-4 rounded-lg hover:shadow-lg transform hover:scale-105 transition duration-200 mt-6"
                    >
                        <i class="fas fa-user-plus mr-2"></i> Crear Cuenta
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        ¿Ya tienes cuenta? 
                        <a href="/vencimiento/index.php?action=login" class="text-purple-600 hover:text-pink-600 font-semibold transition">
                            Inicia sesión aquí
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-6 text-white text-sm">
            <p>© 2024 Sistema de Inventario. Todos los derechos reservados.</p>
        </div>
    </div>
</div>

<style>
    input::placeholder, select::placeholder {
        color: #9ca3af;
    }

    input:focus, select:focus {
        box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
    }
</style>
