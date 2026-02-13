<?php
// app/controllers/DashboardController.php

class DashboardController extends BaseController
{

    public function index()
    {
        $this->validarAcceso();

        $usuario = $this->usuarioActual();
        $dashboard = [];

        if ($usuario['rol'] === 'superadmin') {
            $dashboard = $this->obtenerDashboardSuperAdmin();
        } else {
            $dashboard = $this->obtenerDashboardSedeAdmin($usuario['sede_id']);
        }

        $this->renderizar('dashboard/index', [
            'usuario' => $usuario,
            'dashboard' => $dashboard
        ]);
    }

    private function obtenerDashboardSuperAdmin()
    {
        $dashboard = [];

        // Total de sedes
        $sedesQuery = "SELECT COUNT(*) as total FROM sedes WHERE estado = 'activa'";
        $dashboard['total_sedes'] = $this->db->query($sedesQuery)->fetch_assoc()['total'];

        // Total de productos
        $productosQuery = "SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'";
        $dashboard['total_productos'] = $this->db->query($productosQuery)->fetch_assoc()['total'];

        // Total de lotes en todas las sedes
        $lotesQuery = "SELECT COUNT(*) as total FROM lotes_productos WHERE cantidad_disponible > 0";
        $dashboard['total_lotes_activos'] = $this->db->query($lotesQuery)->fetch_assoc()['total'];

        // Alertas de vencimiento globales - Lotes próximos a vencer (próximos 7 días)
        $alertasQuery = "SELECT COUNT(*) as total FROM lotes_productos 
                         WHERE cantidad_disponible > 0 
                         AND DATEDIFF(fecha_vencimiento, CURDATE()) > 0 
                         AND DATEDIFF(fecha_vencimiento, CURDATE()) <= 7";
        $dashboard['alertas_activas'] = $this->db->query($alertasQuery)->fetch_assoc()['total'];

        // Lotes próximos a vencer (próximos 7 días)
        $lotesVenciendoQuery = "SELECT COUNT(*) as total FROM lotes_productos 
                               WHERE cantidad_disponible > 0
                               AND DATEDIFF(fecha_vencimiento, CURDATE()) > 0 
                               AND DATEDIFF(fecha_vencimiento, CURDATE()) <= 7";
        $dashboard['lotes_proximos_vencer'] = $this->db->query($lotesVenciendoQuery)->fetch_assoc()['total'];

        // Lotes vencidos
        $lotesVencidosQuery = "SELECT COUNT(*) as total FROM lotes_productos 
                              WHERE cantidad_disponible > 0
                              AND DATEDIFF(fecha_vencimiento, CURDATE()) < 0";
        $dashboard['lotes_vencidos'] = $this->db->query($lotesVencidosQuery)->fetch_assoc()['total'];

        // Datos por sede
        $sedePorLoteQuery = "SELECT s.id,s.nombre, COUNT(lp.id) as cantidad, 
                            SUM(CASE WHEN DATEDIFF(lp.fecha_vencimiento, CURDATE()) < 0 THEN 1 ELSE 0 END) as vencidos
                            FROM sedes s 
                            LEFT JOIN lotes_productos lp ON s.id = lp.sede_id AND lp.cantidad_disponible > 0
                            WHERE s.estado = 'activa'
                            GROUP BY s.id, s.nombre
                            ORDER BY cantidad DESC";
        $dashboard['sedes_info'] = [];
        $result = $this->db->query($sedePorLoteQuery);
        while ($row = $result->fetch_assoc()) {
            $dashboard['sedes_info'][] = $row;
        }

        return $dashboard;
    }

    private function obtenerDashboardSedeAdmin($sede_id)
    {
        $dashboard = [];

        // Total de productos en esta sede
        $productosQuery = "SELECT COUNT(DISTINCT lp.producto_id) as total 
                         FROM lotes_productos lp 
                         WHERE lp.sede_id = ? AND lp.cantidad_disponible > 0";
        $stmt = $this->db->prepare($productosQuery);
        $stmt->bind_param('i', $sede_id);
        $stmt->execute();
        $dashboard['total_productos'] = $stmt->get_result()->fetch_assoc()['total'];

        // Total de lotes activos
        $lotesQuery = "SELECT COUNT(*) as total FROM lotes_productos 
                      WHERE sede_id = ? AND cantidad_disponible > 0";
        $stmt = $this->db->prepare($lotesQuery);
        $stmt->bind_param('i', $sede_id);
        $stmt->execute();
        $dashboard['total_lotes'] = $stmt->get_result()->fetch_assoc()['total'];

        // Stock total
        $stockQuery = "SELECT SUM(cantidad_disponible) as total FROM lotes_productos WHERE sede_id = ? AND cantidad_disponible > 0";
        $stmt = $this->db->prepare($stockQuery);
        $stmt->bind_param('i', $sede_id);
        $stmt->execute();
        $dashboard['stock_total'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        // Alertas activas - próximos a vencer
        $alertasQuery = "SELECT COUNT(*) as total FROM lotes_productos 
                        WHERE sede_id = ? 
                        AND cantidad_disponible > 0
                        AND DATEDIFF(fecha_vencimiento, CURDATE()) > 0 
                        AND DATEDIFF(fecha_vencimiento, CURDATE()) <= 7";
        $stmt = $this->db->prepare($alertasQuery);
        $stmt->bind_param('i', $sede_id);
        $stmt->execute();
        $dashboard['alertas_activas'] = $stmt->get_result()->fetch_assoc()['total'];

        // Productos próximos a vencer
        $proxVencerQuery = "SELECT p.nombre, p.codigo_sku, lp.fecha_vencimiento, 
                           DATEDIFF(lp.fecha_vencimiento, CURDATE()) as dias_para_vencer,
                           lp.cantidad_disponible
                           FROM lotes_productos lp
                           JOIN productos p ON lp.producto_id = p.id
                           WHERE lp.sede_id = ? 
                           AND lp.cantidad_disponible > 0
                           AND DATEDIFF(lp.fecha_vencimiento, CURDATE()) > 0 
                           AND DATEDIFF(lp.fecha_vencimiento, CURDATE()) <= 7
                           ORDER BY lp.fecha_vencimiento ASC
                           LIMIT 10";
        $stmt = $this->db->prepare($proxVencerQuery);
        $stmt->bind_param('i', $sede_id);
        $stmt->execute();
        $dashboard['proximos_vencer'] = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $dashboard['proximos_vencer'][] = $row;
        }

        // Productos vencidos
        $vencidosQuery = "SELECT p.nombre, p.codigo_sku, lp.fecha_vencimiento, 
                         DATEDIFF(lp.fecha_vencimiento, CURDATE()) as dias_para_vencer,
                         lp.cantidad_disponible
                         FROM lotes_productos lp
                         JOIN productos p ON lp.producto_id = p.id
                         WHERE lp.sede_id = ? 
                         AND lp.cantidad_disponible > 0
                         AND DATEDIFF(lp.fecha_vencimiento, CURDATE()) < 0
                         ORDER BY lp.fecha_vencimiento DESC
                         LIMIT 10";
        $stmt = $this->db->prepare($vencidosQuery);
        $stmt->bind_param('i', $sede_id);
        $stmt->execute();
        $dashboard['vencidos'] = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $dashboard['vencidos'][] = $row;
        }

        return $dashboard;
    }
}
