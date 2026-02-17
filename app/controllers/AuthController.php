<?php
// app/controllers/AuthController.php

class AuthController extends BaseController {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->procesarLogin();
        }

        $this->renderizar('auth/login', []);
    }

    private function procesarLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($email) || empty($password)) {
            $error = 'Email y contraseña son requeridos';
        } else {
            $query = "SELECT * FROM usuarios WHERE email = ? AND estado = 'activo'";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error = 'Credenciales inválidas';
            } else {
                $usuario = $result->fetch_assoc();

                if (!password_verify($password, $usuario['password'])) {
                    $error = 'Credenciales inválidas';
                } else {
                    // Iniciar sesión
                    session_start();
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['rol'] = $usuario['rol'];
                    $_SESSION['sede_id'] = $usuario['sede_id'];

                    // Actualizar último login
                    $updateQuery = "UPDATE usuarios SET last_login = NOW() WHERE id = ?";
                    $updateStmt = $this->db->prepare($updateQuery);
                    $updateStmt->bind_param('i', $usuario['id']);
                    $updateStmt->execute();

                    header('Location: /vencimiento/index.php?action=dashboard');
                    exit;
                }
            }
        }

        $this->renderizar('auth/login', ['error' => $error]);
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /vencimiento/index.php?action=login');
        exit;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->procesarRegistro();
        }

        $this->renderizar('auth/register', []);
    }

    private function procesarRegistro() {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $sede_id = $_POST['sede_id'] ?? null;
        $errores = [];

        // Validaciones
        if (empty($nombre)) {
            $errores[] = 'El nombre es requerido';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Email válido es requerido';
        }
        if (empty($password) || strlen($password) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        if ($password !== $password_confirm) {
            $errores[] = 'Las contraseñas no coinciden';
        }

        if (!empty($errores)) {
            $sedes = $this->obtenerSedes();
            return $this->renderizar('auth/register', [
                'errores' => $errores,
                'sedes' => $sedes,
                'form_data' => $_POST
            ]);
        }

        // Verificar si email existe
        $checkQuery = "SELECT id FROM usuarios WHERE email = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();

        if ($checkStmt->get_result()->num_rows > 0) {
            $sedes = $this->obtenerSedes();
            return $this->renderizar('auth/register', [
                'errores' => ['El email ya está registrado'],
                'sedes' => $sedes,
                'form_data' => $_POST
            ]);
        }

        // Insertar usuario
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $rol = 'encargado'; // Por defecto
        $estado = 'activo';

        $insertQuery = "INSERT INTO usuarios (nombre, email, password, rol, sede_id, estado) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $this->db->prepare($insertQuery);
        $insertStmt->bind_param('ssssis', $nombre, $email, $passwordHash, $rol, $sede_id, $estado);

        if ($insertStmt->execute()) {
            $_SESSION['user_id'] = $insertStmt->insert_id;
            $_SESSION['rol'] = $rol;

            header('Location: /vencimiento/index.php?action=login&mensaje=Registro exitoso');
            exit;
        } else {
            $sedes = $this->obtenerSedes();
            return $this->renderizar('auth/register', [
                'errores' => ['Error al registrar el usuario'],
                'sedes' => $sedes,
                'form_data' => $_POST
            ]);
        }
    }

    private function obtenerSedes() {
        $query = "SELECT id, nombre FROM sedes WHERE estado = 'activa' ORDER BY nombre";
        $result = $this->db->query($query);
        $sedes = [];

        while ($row = $result->fetch_assoc()) {
            $sedes[] = $row;
        }

        return $sedes;
    }
}
?>
