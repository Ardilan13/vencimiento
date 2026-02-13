<?php
// app/controllers/UsersController.php

class UsersController extends BaseController {

    public function listado() {
        $this->validarAcceso(['superadmin', 'admin']);
        
        $usuario = $this->usuarioActual();
        $filtro_rol = $_GET['rol'] ?? '';
        $filtro_estado = $_GET['estado'] ?? '';

        $usuarios = $this->obtenerUsuarios($filtro_rol, $filtro_estado);
        $roles = $this->obtenerRoles();

        $this->renderizar('users/listado', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'filtro_rol' => $filtro_rol,
            'filtro_estado' => $filtro_estado,
            'usuario' => $usuario
        ]);
    }

    public function crear() {
        $this->validarAcceso(['superadmin', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->guardarUsuario();
        }

        $usuario = $this->usuarioActual();
        $sedes = $this->obtenerSedes();
        $roles = $this->obtenerRoles();

        $this->renderizar('users/crear', [
            'sedes' => $sedes,
            'roles' => $roles,
            'usuario' => $usuario
        ]);
    }

    private function guardarUsuario() {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? 'vendedor';
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
        if (!in_array($rol, ['superadmin', 'admin', 'encargado', 'vendedor'])) {
            $errores[] = 'Rol inválido';
        }

        if (!empty($errores)) {
            return $this->renderizar('users/crear', [
                'errores' => $errores,
                'sedes' => $this->obtenerSedes(),
                'roles' => $this->obtenerRoles(),
                'form_data' => $_POST
            ]);
        }

        // Verificar si email existe
        $checkQuery = "SELECT id FROM usuarios WHERE email = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();

        if ($checkStmt->get_result()->num_rows > 0) {
            return $this->renderizar('users/crear', [
                'errores' => ['El email ya está registrado'],
                'sedes' => $this->obtenerSedes(),
                'roles' => $this->obtenerRoles(),
                'form_data' => $_POST
            ]);
        }

        // Insertar usuario
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $estado = 'activo';

        $insertQuery = "INSERT INTO usuarios (nombre, email, password, rol, sede_id, estado) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $this->db->prepare($insertQuery);
        $insertStmt->bind_param('ssssis', $nombre, $email, $passwordHash, $rol, $sede_id, $estado);

        if ($insertStmt->execute()) {
            header('Location: /claude/index.php?action=usuarios&mensaje=Usuario creado correctamente');
            exit;
        } else {
            return $this->renderizar('users/crear', [
                'errores' => ['Error al crear el usuario'],
                'sedes' => $this->obtenerSedes(),
                'roles' => $this->obtenerRoles(),
                'form_data' => $_POST
            ]);
        }
    }

    public function editar() {
        $this->validarAcceso(['superadmin', 'admin']);

        $usuario_id = $_GET['id'] ?? '';
        if (empty($usuario_id)) {
            header('Location: /claude/index.php?action=usuarios');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->actualizarUsuario($usuario_id);
        }

        $usuario_editar = $this->obtenerUsuarioPorId($usuario_id);
        if (!$usuario_editar) {
            header('Location: /claude/index.php?action=usuarios');
            exit;
        }

        $sedes = $this->obtenerSedes();
        $roles = $this->obtenerRoles();
        $usuario = $this->usuarioActual();

        $this->renderizar('users/editar', [
            'usuario_editar' => $usuario_editar,
            'sedes' => $sedes,
            'roles' => $roles,
            'usuario' => $usuario
        ]);
    }

    private function actualizarUsuario($usuario_id) {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $sede_id = $_POST['sede_id'] ?? null;
        $estado = $_POST['estado'] ?? 'activo';
        $password = $_POST['password'] ?? '';
        $errores = [];

        if (empty($nombre)) {
            $errores[] = 'El nombre es requerido';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Email válido es requerido';
        }

        if (!empty($errores)) {
            return $this->renderizar('users/editar', [
                'errores' => $errores,
                'usuario_editar' => $this->obtenerUsuarioPorId($usuario_id),
                'sedes' => $this->obtenerSedes(),
                'roles' => $this->obtenerRoles(),
                'form_data' => $_POST
            ]);
        }

        // Si cambió email, verificar que no exista
        $usuarioActual = $this->obtenerUsuarioPorId($usuario_id);
        if ($usuarioActual['email'] !== $email) {
            $checkQuery = "SELECT id FROM usuarios WHERE email = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bind_param('s', $email);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                return $this->renderizar('users/editar', [
                    'errores' => ['El email ya está registrado'],
                    'usuario_editar' => $usuarioActual,
                    'sedes' => $this->obtenerSedes(),
                    'roles' => $this->obtenerRoles()
                ]);
            }
        }

        // Actualizar usuario
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $updateQuery = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, sede_id = ?, estado = ?, password = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bind_param('sssissi', $nombre, $email, $rol, $sede_id, $estado, $passwordHash, $usuario_id);
        } else {
            $updateQuery = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, sede_id = ?, estado = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bind_param('ssssisi', $nombre, $email, $rol, $sede_id, $estado, $usuario_id);
        }

        if ($updateStmt->execute()) {
            header('Location: /claude/index.php?action=usuarios&mensaje=Usuario actualizado correctamente');
            exit;
        } else {
            return $this->renderizar('users/editar', [
                'errores' => ['Error al actualizar el usuario'],
                'usuario_editar' => $usuarioActual,
                'sedes' => $this->obtenerSedes(),
                'roles' => $this->obtenerRoles()
            ]);
        }
    }

    private function obtenerUsuarios($rol = '', $estado = '') {
        $query = "SELECT u.*, s.nombre as sede_nombre FROM usuarios u 
                  LEFT JOIN sedes s ON u.sede_id = s.id 
                  WHERE 1=1";

        $params = [];
        $types = '';

        if (!empty($rol)) {
            $query .= " AND u.rol = ?";
            $params[] = $rol;
            $types .= 's';
        }

        if (!empty($estado)) {
            $query .= " AND u.estado = ?";
            $params[] = $estado;
            $types .= 's';
        }

        $query .= " ORDER BY u.nombre ASC";

        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();

        $usuarios = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        return $usuarios;
    }

    private function obtenerUsuarioPorId($usuario_id) {
        $query = "SELECT u.*, s.nombre as sede_nombre FROM usuarios u 
                  LEFT JOIN sedes s ON u.sede_id = s.id 
                  WHERE u.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) return null;
        
        return $result->fetch_assoc();
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

    private function obtenerRoles() {
        return [
            'superadmin' => 'Super Administrador',
            'admin' => 'Administrador',
            'encargado' => 'Encargado de Inventario',
            'vendedor' => 'Vendedor'
        ];
    }
}
?>
