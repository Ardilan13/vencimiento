<?php
// app/controllers/BaseController.php

class BaseController {
    protected $db;
    protected $usuario = null;

    public function __construct($db) {
        $this->db = $db;
        $this->verificarSesion();
    }

    protected function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id'])) {
            if ($this->noEsRutaPublica()) {
                header('Location: /vencimiento/index.php?action=login');
                exit;
            }
        } else {
            $this->cargarUsuario();
        }
    }

    protected function cargarUsuario() {
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id) return;

        $query = "SELECT u.*, s.nombre as sede_nombre FROM usuarios u 
                  LEFT JOIN sedes s ON u.sede_id = s.id 
                  WHERE u.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $this->usuario = $result->fetch_assoc();
        } else {
            session_destroy();
            header('Location: /vencimiento/index.php?action=login');
            exit;
        }
    }

    protected function noEsRutaPublica() {
        $action = $_GET['action'] ?? 'dashboard';
        $rutasPublicas = ['login', 'logout', 'register'];
        return !in_array($action, $rutasPublicas);
    }

    protected function usuarioActual() {
        return $this->usuario;
    }

    protected function validarAcceso($rolesPermitidos = []) {
        if (!$this->usuario) {
            header('Location: /vencimiento/index.php?action=login');
            exit;
        }

        if (!empty($rolesPermitidos) && !in_array($this->usuario['rol'], $rolesPermitidos)) {
            http_response_code(403);
            echo "Acceso denegado. No tiene permisos para acceder a este recurso.";
            exit;
        }
    }

    protected function validarSedeAcceso($sede_id) {
        if ($this->usuario['rol'] === 'superadmin') {
            return true;
        }
        
        return $this->usuario['sede_id'] == $sede_id;
    }

    protected function responderJSON($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    protected function renderizar($vista, $datos = []) {
        extract($datos);
        $archivo = __DIR__ . '/../views/' . $vista . '.php';
        
        if (!file_exists($archivo)) {
            die("Vista no encontrada: $vista");
        }

        ob_start();
        include $archivo;
        $contenido = ob_get_clean();
        
        include __DIR__ . '/../views/layout.php';
    }
}
?>
