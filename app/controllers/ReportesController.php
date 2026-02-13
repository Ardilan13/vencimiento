<?php
// app/controllers/ReportesController.php

class ReportesController extends BaseController {

    public function index() {
        $this->validarAcceso(['superadmin', 'admin', 'encargado']);
        
        $usuario = $this->usuarioActual();
        $tipo_reporte = $_GET['tipo'] ?? 'vencimiento';
        $sede_id = $_GET['sede'] ?? $usuario['sede_id'];
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-t');

        // Si no es superadmin, forzar su sede
        if ($usuario['rol'] !== 'superadmin') {
            $sede_id = $usuario['sede_id'];
        }

        $reporte = [];
        switch ($tipo_reporte) {
            case 'vencimiento':
                $reporte = $this->reporteVencimiento($sede_id, $fecha_desde, $fecha_hasta);
                break;
            case 'stock_bajo':
                $reporte = $this->reporteStockBajo($sede_id);
                break;
            case 'movimientos':
                $reporte = $this->reporteMovimientos($sede_id, $fecha_desde, $fecha_hasta);
                break;
            case 'inventario_general':
                $reporte = $this->reporteInventarioGeneral($sede_id);
                break;
        }

        $sedes = $this->obtenerSedes($usuario);

        $this->renderizar('reportes/index', [
            'reporte' => $reporte,
            'tipo_reporte' => $tipo_reporte,
            'sede_id' => $sede_id,
            'sedes' => $sedes,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'usuario' => $usuario
        ]);
    }

    public function exportar() {
        $this->validarAcceso(['superadmin', 'admin', 'encargado']);

        $usuario = $this->usuarioActual();
        $tipo_reporte = $_GET['tipo'] ?? 'vencimiento';
        $sede_id = $_GET['sede'] ?? $usuario['sede_id'];
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-t');

        if ($usuario['rol'] !== 'superadmin') {
            $sede_id = $usuario['sede_id'];
        }

        $reporte = [];
        $nombreArchivo = '';

        switch ($tipo_reporte) {
            case 'vencimiento':
                $reporte = $this->reporteVencimiento($sede_id, $fecha_desde, $fecha_hasta);
                $nombreArchivo = 'reporte_vencimiento_' . date('Ymd');
                break;
            case 'stock_bajo':
                $reporte = $this->reporteStockBajo($sede_id);
                $nombreArchivo = 'reporte_stock_bajo_' . date('Ymd');
                break;
            case 'inventario_general':
                $reporte = $this->reporteInventarioGeneral($sede_id);
                $nombreArchivo = 'reporte_inventario_' . date('Ymd');
                break;
        }

        $this->exportarCSV($reporte, $nombreArchivo);
    }

    private function reporteVencimiento($sede_id, $fecha_desde, $fecha_hasta) {
        $query = "SELECT p.nombre, p.codigo_sku, lp.numero_lote, lp.cantidad_disponible,
                         lp.fecha_vencimiento, DATEDIFF(lp.fecha_vencimiento, CURDATE()) as dias_para_vencer,
                         c.nombre as categoria, s.nombre as sede
                  FROM lotes_productos lp
                  JOIN productos p ON lp.producto_id = p.id
                  JOIN categorias c ON p.categoria_id = c.id
                  JOIN sedes s ON lp.sede_id = s.id
                  WHERE lp.sede_id = ? 
                  AND lp.cantidad_disponible > 0
                  AND lp.fecha_vencimiento BETWEEN ? AND ?
                  ORDER BY lp.fecha_vencimiento ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iss', $sede_id, $fecha_desde, $fecha_hasta);
        $stmt->execute();

        $datos = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }

        return [
            'titulo' => 'Reporte de Productos por Vencer',
            'columnas' => ['Producto', 'SKU', 'Lote', 'Cantidad', 'Vencimiento', 'Días', 'Categoría', 'Sede'],
            'datos' => $datos
        ];
    }

    private function reporteStockBajo($sede_id) {
        $query = "SELECT p.nombre, p.codigo_sku, p.stock_minimo, 
                         SUM(lp.cantidad_disponible) as stock_actual,
                         c.nombre as categoria, s.nombre as sede
                  FROM productos p
                  LEFT JOIN lotes_productos lp ON p.id = lp.producto_id AND lp.sede_id = ? AND lp.cantidad_disponible > 0
                  JOIN categorias c ON p.categoria_id = c.id
                  JOIN sedes s ON ? = s.id
                  WHERE p.estado = 'activo'
                  GROUP BY p.id
                  HAVING stock_actual IS NULL OR stock_actual < p.stock_minimo
                  ORDER BY stock_actual ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $sede_id, $sede_id);
        $stmt->execute();

        $datos = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $row['stock_actual'] = $row['stock_actual'] ?? 0;
            $datos[] = $row;
        }

        return [
            'titulo' => 'Reporte de Stock Bajo',
            'columnas' => ['Producto', 'SKU', 'Stock Mínimo', 'Stock Actual', 'Categoría', 'Sede'],
            'datos' => $datos
        ];
    }

    private function reporteMovimientos($sede_id, $fecha_desde, $fecha_hasta) {
        $query = "SELECT mi.*, u.nombre as usuario_nombre, p.nombre as producto_nombre,
                         lp.numero_lote
                  FROM movimientos_inventario mi
                  JOIN usuarios u ON mi.usuario_id = u.id
                  JOIN lotes_productos lp ON mi.lote_producto_id = lp.id
                  JOIN productos p ON lp.producto_id = p.id
                  WHERE lp.sede_id = ?
                  AND mi.fecha BETWEEN ? AND ?
                  ORDER BY mi.fecha DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iss', $sede_id, $fecha_desde, $fecha_hasta);
        $stmt->execute();

        $datos = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }

        return [
            'titulo' => 'Reporte de Movimientos',
            'columnas' => ['Fecha', 'Producto', 'Lote', 'Tipo', 'Cantidad', 'Usuario', 'Motivo'],
            'datos' => $datos
        ];
    }

    private function reporteInventarioGeneral($sede_id) {
        $query = "SELECT p.nombre, p.codigo_sku, COUNT(DISTINCT lp.id) as total_lotes,
                         SUM(lp.cantidad_disponible) as stock_total, p.stock_minimo,
                         MIN(lp.fecha_vencimiento) as proximo_vencimiento,
                         c.nombre as categoria
                  FROM productos p
                  LEFT JOIN lotes_productos lp ON p.id = lp.producto_id AND lp.sede_id = ? AND lp.cantidad_disponible > 0
                  JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.estado = 'activo'
                  GROUP BY p.id
                  ORDER BY p.nombre ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $sede_id);
        $stmt->execute();

        $datos = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $row['stock_total'] = $row['stock_total'] ?? 0;
            $datos[] = $row;
        }

        return [
            'titulo' => 'Reporte de Inventario General',
            'columnas' => ['Producto', 'SKU', 'Lotes', 'Stock', 'Mínimo', 'Próximo Venc.', 'Categoría'],
            'datos' => $datos
        ];
    }

    private function exportarCSV($reporte, $nombreArchivo) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

        // Escribir título
        fputcsv($output, [$reporte['titulo']]);
        fputcsv($output, []); // Línea en blanco

        // Escribir columnas
        fputcsv($output, $reporte['columnas']);

        // Escribir datos
        foreach ($reporte['datos'] as $fila) {
            $valores = [];
            foreach ($reporte['columnas'] as $columna) {
                $clave = strtolower(str_replace(' ', '_', $columna));
                $valores[] = $fila[$clave] ?? '';
            }
            fputcsv($output, $valores);
        }

        fclose($output);
        exit;
    }

    private function obtenerSedes($usuario) {
        if ($usuario['rol'] === 'superadmin') {
            $query = "SELECT id, nombre FROM sedes WHERE estado = 'activa' ORDER BY nombre";
            $result = $this->db->query($query);
        } else {
            $query = "SELECT id, nombre FROM sedes WHERE id = ? AND estado = 'activa'";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $usuario['sede_id']);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        $sedes = [];
        while ($row = $result->fetch_assoc()) {
            $sedes[] = $row;
        }

        return $sedes;
    }
}
?>
