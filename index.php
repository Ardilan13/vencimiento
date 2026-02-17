<?php
require_once 'app/config/Env.php';
require_once 'app/config/Database.php';
require_once 'app/controllers/BaseController.php';
require_once 'app/controllers/AuthController.php';
require_once 'app/controllers/DashboardController.php';
require_once 'app/controllers/InventoryController.php';
require_once 'app/controllers/UsersController.php';
require_once 'app/controllers/SedesController.php';
require_once 'app/controllers/AlertasController.php';
require_once 'app/controllers/ReportesController.php';

$db = new Database();
$conexion = $db->connect();

$action = $_GET['action'] ?? 'login';

try {
    switch ($action) {
        // Auth
        case 'login':
            $controller = new AuthController($conexion);
            $controller->login();
            break;
        case 'logout':
            $controller = new AuthController($conexion);
            $controller->logout();
            break;
        case 'register':
            $controller = new AuthController($conexion);
            $controller->register();
            break;

        // Dashboard
        case 'dashboard':
            $controller = new DashboardController($conexion);
            $controller->index();
            break;

        // Inventory
        case 'inventory':
            $controller = new InventoryController($conexion);
            $controller->listado();
            break;
        case 'crear_producto':
            $controller = new InventoryController($conexion);
            $controller->crearProducto();
            break;
        case 'agregar_lote':
            $controller = new InventoryController($conexion);
            $controller->agregarLote();
            break;
        case 'detalles_producto':
            $controller = new InventoryController($conexion);
            $controller->detallesProducto();
            break;

        // Usuarios
        case 'usuarios':
            $controller = new UsersController($conexion);
            $controller->listado();
            break;
        case 'crear_usuario':
            $controller = new UsersController($conexion);
            $controller->crear();
            break;
        case 'editar_usuario':
            $controller = new UsersController($conexion);
            $controller->editar();
            break;

        // Sedes
        case 'sedes':
            $controller = new SedesController($conexion);
            $controller->listado();
            break;
        case 'crear_sede':
            $controller = new SedesController($conexion);
            $controller->crear();
            break;
        case 'editar_sede':
            $controller = new SedesController($conexion);
            $controller->editar();
            break;

        // Alertas
        case 'alertas':
            $controller = new AlertasController($conexion);
            $controller->listado();
            break;
        case 'configurar_alertas':
            $controller = new AlertasController($conexion);
            $controller->configurar();
            break;
        case 'cambiar_estado_alerta':
            $controller = new AlertasController($conexion);
            $controller->cambiar_estado();
            break;

        // Reportes
        case 'reportes':
            $controller = new ReportesController($conexion);
            $controller->index();
            break;
        case 'exportar_reporte':
            $controller = new ReportesController($conexion);
            $controller->exportar();
            break;

        default:
            header('Location: /vencimiento/index.php?action=login');
            exit;
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
