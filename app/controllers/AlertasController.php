<?php
// app/controllers/AlertasController.php

class AlertasController extends BaseController
{

    public function listado()
    {
        $this->validarAcceso(['superadmin', 'admin', 'encargado']);

        $usuario = $this->usuarioActual();
        $filtro_tipo = $_GET['tipo'] ?? '';
        $filtro_estado = $_GET['estado'] ?? 'activa';

        $alertas = $this->obtenerAlertas($usuario, $filtro_tipo, $filtro_estado);
        $configuraciones = $this->obtenerConfiguraciones();

        $this->renderizar('alertas/listado', [
            'alertas' => $alertas,
            'configuraciones' => $configuraciones,
            'filtro_tipo' => $filtro_tipo,
            'filtro_estado' => $filtro_estado,
            'usuario' => $usuario
        ]);
    }

    public function configurar()
    {
        $this->validarAcceso(['superadmin', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->guardarConfiguracion();
        }

        $usuario = $this->usuarioActual();
        $configuraciones = $this->obtenerConfiguraciones();

        $this->renderizar('alertas/configurar', [
            'configuraciones' => $configuraciones,
            'usuario' => $usuario
        ]);
    }

    private function guardarConfiguracion()
    {
        $dias_critico = $_POST['dias_critico'] ?? 1;
        $dias_warning = $_POST['dias_warning'] ?? 7;
        $errores = [];
        $clave = 'alertas_config';

        if (empty($dias_critico) || !is_numeric($dias_critico) || $dias_critico < 0) {
            $errores[] = 'Días crítico debe ser un número válido';
        }
        if (empty($dias_warning) || !is_numeric($dias_warning) || $dias_warning < 0) {
            $errores[] = 'Días warning debe ser un número válido';
        }

        if (!empty($errores)) {
            return $this->renderizar('alertas/configurar', [
                'errores' => $errores,
                'configuraciones' => $this->obtenerConfiguraciones()
            ]);
        }

        // Verificar si existe configuración
        $checkQuery = "SELECT id FROM configuraciones WHERE clave = 'alertas_config'";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute();
        $existe = $checkStmt->get_result()->num_rows > 0;

        $config_valor = json_encode([
            'dias_critico' => (int)$dias_critico,
            'dias_warning' => (int)$dias_warning
        ]);

        if ($existe) {
            $updateQuery = "UPDATE configuraciones SET valor = ? WHERE clave = 'alertas_config'";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bind_param('s', $config_valor);
            $resultado = $updateStmt->execute();
        } else {
            $insertQuery = "INSERT INTO configuraciones (clave, valor) VALUES (?, ?)";
            $insertStmt = $this->db->prepare($insertQuery);
            $insertStmt->bind_param('ss', $clave, $config_valor);
            $resultado = $insertStmt->execute();
        }

        if ($resultado) {
            header('Location: /vencimiento/index.php?action=alertas&mensaje=Configuración guardada correctamente');
            exit;
        } else {
            return $this->renderizar('alertas/configurar', [
                'errores' => ['Error al guardar la configuración'],
                'configuraciones' => $this->obtenerConfiguraciones()
            ]);
        }
    }

    public function cambiar_estado()
    {
        $this->validarAcceso(['superadmin', 'admin', 'encargado']);

        $lote_id = $_POST['lote_id'] ?? '';
        $estado = $_POST['estado'] ?? '';

        if (empty($lote_id) || !in_array($estado, ['activa', 'resuelto', 'ignorado'])) {
            $this->responderJSON(['error' => 'Datos inválidos'], 400);
        }

        $updateQuery = "UPDATE alertas_vencimiento SET estado = ? WHERE lote_producto_id = ?";
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->bind_param('si', $estado, $lote_id);

        if ($updateStmt->execute()) {
            $this->responderJSON(['success' => true, 'mensaje' => 'Alerta actualizada']);
        } else {
            $this->responderJSON(['error' => 'Error al actualizar'], 500);
        }
    }

    private function obtenerAlertas($usuario, $filtro_tipo = '', $filtro_estado = '')
    {
        $query = "SELECT av.*, p.nombre, p.codigo_sku, lp.fecha_vencimiento,
                         DATEDIFF(lp.fecha_vencimiento, CURDATE()) as dias_para_vencer,
                         lp.cantidad_disponible, s.nombre as sede_nombre
                  FROM alertas_vencimiento av
                  JOIN lotes_productos lp ON av.lote_producto_id = lp.id
                  JOIN productos p ON lp.producto_id = p.id
                  JOIN sedes s ON lp.sede_id = s.id
                  WHERE 1=1";

        $params = [];
        $types = '';

        // Si no es superadmin, filtrar por sede
        if ($usuario['rol'] !== 'superadmin') {
            $query .= " AND lp.sede_id = ?";
            $params[] = $usuario['sede_id'];
            $types .= 'i';
        }

        if (!empty($filtro_estado)) {
            $query .= " AND av.estado = ?";
            $params[] = $filtro_estado;
            $types .= 's';
        }

        $query .= " ORDER BY av.fecha_creacion DESC";

        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();

        $alertas = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            // Determinar tipo de alerta según días
            $dias = $row['dias_para_vencer'];
            if ($dias < 0) {
                $row['tipo'] = 'vencido';
                $row['clase_color'] = 'bg-red-100 border-l-4 border-red-500';
            } elseif ($dias <= 1) {
                $row['tipo'] = 'critico';
                $row['clase_color'] = 'bg-orange-100 border-l-4 border-orange-500';
            } elseif ($dias <= 7) {
                $row['tipo'] = 'warning';
                $row['clase_color'] = 'bg-yellow-100 border-l-4 border-yellow-500';
            }

            $alertas[] = $row;
        }

        return $alertas;
    }

    private function obtenerConfiguraciones()
    {
        $query = "SELECT * FROM configuraciones WHERE clave = 'alertas_config'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $config = $result->fetch_assoc();
            return json_decode($config['valor'], true);
        }

        // Valores por defecto
        return [
            'dias_critico' => 1,
            'dias_warning' => 7
        ];
    }
}
