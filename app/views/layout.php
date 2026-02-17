<?php
// app/views/layout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario = isset($_SESSION['usuario_id']) ? true : false;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_ENV['APP_NAME'] ?? 'Sistema de Inventario'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }

        .nav-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .nav-item:hover {
            background-color: rgba(99, 102, 241, 0.1);
            border-left-color: #6366f1;
        }

        .nav-item.active {
            background-color: rgba(99, 102, 241, 0.2);
            border-left-color: #6366f1;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--color-from) 0%, var(--color-to) 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border-left: 4px solid #16a34a;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .table-responsive {
            overflow-x: auto;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #cffafe;
            color: #164e63;
        }
    </style>
</head>

<body>
    <?php if ($usuario): ?>
        <div class="flex h-screen bg-gray-900">
            <!-- Sidebar -->
            <div class="sidebar w-64 text-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-700">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                        <i class="fas fa-pills"></i> Inventory
                    </h1>
                </div>

                <nav class="flex-1 p-4 space-y-2">
                    <?php
                    $action = $_GET['action'] ?? 'dashboard';
                    $menu = [
                        ['url' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'roles' => ['superadmin', 'admin', 'encargado', 'vendedor']],
                        ['url' => 'inventory', 'label' => 'Inventario', 'icon' => 'boxes', 'roles' => ['superadmin', 'admin', 'encargado']],
                        ['url' => 'alertas', 'label' => 'Alertas', 'icon' => 'bell', 'roles' => ['superadmin', 'admin', 'encargado']],
                        // ['url' => 'reportes', 'label' => 'Reportes', 'icon' => 'chart-line', 'roles' => ['superadmin', 'admin']],
                        ['url' => 'sedes', 'label' => 'Sedes', 'icon' => 'building', 'roles' => ['superadmin']],
                        ['url' => 'usuarios', 'label' => 'Usuarios', 'icon' => 'users', 'roles' => ['superadmin', 'admin']],
                    ];

                    $rol = $_SESSION['rol'] ?? 'vendedor';

                    foreach ($menu as $item):
                        if (!in_array($rol, $item['roles'])) continue;
                        $isActive = $action === $item['url'] ? 'active' : '';
                    ?>
                        <a href="/vencimiento/index.php?action=<?php echo $item['url']; ?>"
                            class="nav-item flex items-center space-x-3 p-3 rounded text-gray-300 hover:text-white <?php echo $isActive; ?>">
                            <i class="fas fa-<?php echo $item['icon']; ?> w-5"></i>
                            <span><?php echo $item['label']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="p-4 border-t border-gray-700">
                    <div class="flex items-center space-x-3 p-3 bg-gray-800 rounded mb-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="flex-1 text-sm">
                            <p class="font-semibold"><?php echo substr($_SESSION['usuario_nombre'] ?? 'Usuario', 0, 20); ?></p>
                            <p class="text-gray-400 text-xs"><?php echo ucfirst($_SESSION['rol'] ?? 'Usuario'); ?></p>
                        </div>
                    </div>
                    <a href="/vencimiento/index.php?action=logout" class="w-full flex items-center justify-center space-x-2 p-2 bg-red-500 hover:bg-red-600 rounded transition">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Bar -->
                <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-800">
                            <?php
                            $pageTitles = [
                                'dashboard' => 'Dashboard',
                                'inventory' => 'Inventario',
                                'crear_producto' => 'Crear Producto',
                                'agregar_lote' => 'Agregar Lote',
                                'alertas' => 'Alertas de Vencimiento',
                                'reportes' => 'Reportes',
                                'sedes' => 'Gestión de Sedes',
                                'usuarios' => 'Gestión de Usuarios'
                            ];
                            echo $pageTitles[$action] ?? 'Sistema de Inventario';
                            ?>
                        </h2>
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo date('d/m/Y H:i'); ?>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="flex-1 overflow-auto bg-gray-50 p-6">
                    <?php
                    if (isset($_GET['mensaje'])):
                    ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['mensaje']); ?>
                        </div>
                    <?php endif; ?>

                    <?php echo $contenido; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Contenido para usuarios no autenticados -->
        <div class="min-h-screen flex items-center justify-center">
            <?php echo $contenido; ?>
        </div>
    <?php endif; ?>
</body>

</html>