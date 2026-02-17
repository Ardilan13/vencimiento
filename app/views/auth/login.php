<?php
// app/views/auth/login.php
?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-600 via-pink-500 to-red-500 p-4">
    <div class="w-full max-w-md">
        <!-- Card Principal -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header Decorativo -->
            <div class="h-32 bg-gradient-to-r from-purple-600 to-pink-600 flex items-end justify-center pb-6">
                <div class="text-white text-center">
                    <i class="fas fa-pills text-5xl mb-2"></i>
                    <h1 class="text-2xl font-bold">Inventory System</h1>
                </div>
            </div>

            <!-- Contenido -->
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2 text-center">Bienvenido</h2>
                <p class="text-gray-600 text-center mb-8">Ingresa tus credenciales para continuar</p>

                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                            <div>
                                <p class="text-red-800 font-semibold">Error de autenticación</p>
                                <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/vencimiento/index.php?action=login" class="space-y-5">
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
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition bg-gray-50"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        >
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
                            placeholder="••••••••"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition bg-gray-50"
                        >
                    </div>

                    <!-- Botón Login -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 px-4 rounded-lg hover:shadow-lg transform hover:scale-105 transition duration-200 mt-8"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                    </button>
                </form>

                <!-- Información de Demo -->
                <div class="mt-8 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                    <p class="text-sm text-gray-700 mb-3"><strong>🔓 Credenciales de Demo:</strong></p>
                    <p class="text-sm text-gray-600 mb-1"><strong>Email:</strong> admin@supplements.com</p>
                    <p class="text-sm text-gray-600"><strong>Password:</strong> password123</p>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600 text-sm">
                        ¿No tienes cuenta? 
                        <a href="/vencimiento/index.php?action=register" class="text-purple-600 hover:text-pink-600 font-semibold transition">
                            Regístrate aquí
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
    input::placeholder {
        color: #9ca3af;
    }

    input:focus {
        box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
    }
</style>
