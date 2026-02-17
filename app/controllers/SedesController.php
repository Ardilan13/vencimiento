<?php
// app/controllers/SedesController.php

class SedesController extends BaseController {

    public function listado() {
        $this->validarAcceso(['superadmin']);
        
        $usuario = $this->usuarioActual();
        $sedes = $this->obtenerSedes();

        $this->renderizar('sedes/listado', [
            'sedes' => $sedes,
            'usuario' => $usuario
        ]);
    }

    public function crear() {
        $this->validarAcceso(['superadmin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->guardarSede();
        }

        $usuario = $this->usuarioActual();
        $this->renderizar('sedes/crear', [
            'usuario' => $usuario
        ]);
    }

    private function guardarSede() {
        $nombre = $_POST['nombre'] ?? '';
        $ciudad = $_POST['ciudad'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $errores = [];

        if (empty($nombre)) {
            $errores[] = 'El nombre es requerido';
        }
        if (empty($ciudad)) {
            $errores[] = 'La ciudad es requerida';
        }

        if (!empty($errores)) {
            return $this->renderizar('sedes/crear', [
                'errores' => $errores,
                'form_data' => $_POST
            ]);
        }

        // Verificar si sede existe
        $checkQuery = "SELECT id FROM sedes WHERE nombre = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bind_param('s', $nombre);
        $checkStmt->execute();

        if ($checkStmt->get_result()->num_rows > 0) {
            return $this->renderizar('sedes/crear', [
                'errores' => ['La sede ya existe'],
                'form_data' => $_POST
            ]);
        }

        // Insertar sede
        $estado = 'activa';
        $insertQuery = "INSERT INTO sedes (nombre, ciudad, direccion, telefono, estado) 
                       VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $this->db->prepare($insertQuery);
        $insertStmt->bind_param('sssss', $nombre, $ciudad, $direccion, $telefono, $estado);

        if ($insertStmt->execute()) {
            header('Location: /vencimiento/index.php?action=sedes&mensaje=Sede creada correctamente');
            exit;
        } else {
            return $this->renderizar('sedes/crear', [
                'errores' => ['Error al crear la sede'],
                'form_data' => $_POST
            ]);
        }
    }

    public function editar() {
        $this->validarAcceso(['superadmin']);

        $sede_id = $_GET['id'] ?? '';
        if (empty($sede_id)) {
            header('Location: /vencimiento/index.php?action=sedes');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->actualizarSede($sede_id);
        }

        $sede = $this->obtenerSedePorId($sede_id);
        if (!$sede) {
            header('Location: /vencimiento/index.php?action=sedes');
            exit;
        }

        $usuario = $this->usuarioActual();
        $this->renderizar('sedes/editar', [
            'sede' => $sede,
            'usuario' => $usuario
        ]);
    }

    private function actualizarSede($sede_id) {
        $nombre = $_POST['nombre'] ?? '';
        $ciudad = $_POST['ciudad'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $estado = $_POST['estado'] ?? 'activa';
        $errores = [];

        if (empty($nombre)) {
            $errores[] = 'El nombre es requerido';
        }
        if (empty($ciudad)) {
            $errores[] = 'La ciudad es requerida';
        }

        if (!empty($errores)) {
            return $this->renderizar('sedes/editar', [
                'errores' => $errores,
                'sede' => $this->obtenerSedePorId($sede_id),
                'form_data' => $_POST
            ]);
        }

        // Verificar nombre único
        $sedeActual = $this->obtenerSedePorId($sede_id);
        if ($sedeActual['nombre'] !== $nombre) {
            $checkQuery = "SELECT id FROM sedes WHERE nombre = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bind_param('s', $nombre);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                return $this->renderizar('sedes/editar', [
                    'errores' => ['El nombre de la sede ya existe'],
                    'sede' => $sedeActual
                ]);
            }
        }

        // Actualizar sede
        $updateQuery = "UPDATE sedes SET nombre = ?, ciudad = ?, direccion = ?, telefono = ?, estado = ? WHERE id = ?";
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->bind_param('sssssi', $nombre, $ciudad, $direccion, $telefono, $estado, $sede_id);

        if ($updateStmt->execute()) {
            header('Location: /vencimiento/index.php?action=sedes&mensaje=Sede actualizada correctamente');
            exit;
        } else {
            return $this->renderizar('sedes/editar', [
                'errores' => ['Error al actualizar la sede'],
                'sede' => $sedeActual
            ]);
        }
    }

    private function obtenerSedes() {
        $query = "SELECT * FROM sedes ORDER BY nombre ASC";
        $result = $this->db->query($query);
        $sedes = [];

        while ($row = $result->fetch_assoc()) {
            // Contar usuarios por sede
            $countQuery = "SELECT COUNT(*) as total FROM usuarios WHERE sede_id = ? AND estado = 'activo'";
            $countStmt = $this->db->prepare($countQuery);
            $countStmt->bind_param('i', $row['id']);
            $countStmt->execute();
            $countResult = $countStmt->get_result()->fetch_assoc();
            $row['usuarios_activos'] = $countResult['total'];

            $sedes[] = $row;
        }

        return $sedes;
    }

    private function obtenerSedePorId($sede_id) {
        $query = "SELECT * FROM sedes WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $sede_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) return null;
        
        return $result->fetch_assoc();
    }
}
?>
