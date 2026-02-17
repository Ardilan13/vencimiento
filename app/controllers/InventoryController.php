<?php
// app/controllers/InventoryController.php

class InventoryController extends BaseController
{

    public function listado()
    {
        $this->validarAcceso();

        $usuario = $this->usuarioActual();
        $filtro_sede = $_GET['sede'] ?? $usuario['sede_id'];
        $filtro_categoria = $_GET['categoria'] ?? '';
        $filtro_estado = $_GET['estado'] ?? '';

        // Validar acceso a sede
        if ($usuario['rol'] !== 'superadmin') {
            $filtro_sede = $usuario['sede_id'];
        }

        // Obtener productos con lotes
        $inventario = $this->obtenerInventario($filtro_sede, $filtro_categoria, $filtro_estado);
        $sedes = $this->obtenerSedes();
        $categorias = $this->obtenerCategorias();

        $this->renderizar('inventory/listado', [
            'inventario' => $inventario,
            'sedes' => $sedes,
            'categorias' => $categorias,
            'filtro_sede' => $filtro_sede,
            'filtro_categoria' => $filtro_categoria,
            'filtro_estado' => $filtro_estado,
            'usuario' => $usuario
        ]);
    }

    private function obtenerInventario($sede_id, $categoria = '', $estado = '')
    {
        $query = "SELECT 
                    p.id as producto_id,
                    p.nombre,
                    p.codigo_sku,
                    c.nombre as categoria,
                    COUNT(lp.id) as total_lotes,
                    SUM(lp.cantidad_disponible) as stock_disponible,
                    MIN(lp.fecha_vencimiento) as proximo_vencimiento,
                    MIN(DATEDIFF(lp.fecha_vencimiento, CURDATE())) as dias_minimo,
                    SUM(CASE WHEN DATEDIFF(lp.fecha_vencimiento, CURDATE()) < 0 AND lp.cantidad_disponible > 0 THEN lp.cantidad_disponible ELSE 0 END) as cantidad_vencida
                 FROM productos p
                 LEFT JOIN categorias c ON p.categoria_id = c.id
                 LEFT JOIN lotes_productos lp ON p.id = lp.producto_id AND lp.sede_id = ? AND lp.cantidad_disponible > 0
                 WHERE p.estado = 'activo'";

        $params = [$sede_id];
        $types = 'i';

        if (!empty($categoria)) {
            $query .= " AND c.id = ?";
            $params[] = $categoria;
            $types .= 'i';
        }

        if (!empty($estado)) {
            if ($estado === 'vencido') {
                $query .= " AND DATEDIFF(lp.fecha_vencimiento, CURDATE()) < 0";
            } elseif ($estado === 'proxima_vencer') {
                $query .= " AND DATEDIFF(lp.fecha_vencimiento, CURDATE()) > 0 AND DATEDIFF(lp.fecha_vencimiento, CURDATE()) <= 7";
            }
        }

        $query .= " GROUP BY p.id, p.nombre, p.codigo_sku, c.nombre ORDER BY p.nombre ASC";

        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();

        $resultado = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $resultado[] = $row;
        }

        return $resultado;
    }

    public function crearProducto()
    {
        $this->validarAcceso(['superadmin', 'admin', 'encargado']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->guardarProducto();
        }

        $categorias = $this->obtenerCategorias();

        $this->renderizar('inventory/crear_producto', [
            'categorias' => $categorias
        ]);
    }

    private function guardarProducto()
    {
        $nombre = $_POST['nombre'] ?? '';
        $codigo_sku = $_POST['codigo_sku'] ?? '';
        $categoria_id = $_POST['categoria_id'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio_costo = $_POST['precio_costo'] ?? '';
        $precio_venta = $_POST['precio_venta'] ?? '';
        $stock_minimo = $_POST['stock_minimo'] ?? 10;
        $errores = [];

        if (empty($nombre)) $errores[] = 'El nombre es requerido';
        if (empty($categoria_id)) $errores[] = 'La categoría es requerida';
        if (empty($precio_costo) || $precio_costo < 0) $errores[] = 'Precio de costo válido requerido';
        if (empty($precio_venta) || $precio_venta < 0) $errores[] = 'Precio de venta válido requerido';

        if (!empty($errores)) {
            return $this->renderizar('inventory/crear_producto', [
                'errores' => $errores,
                'categorias' => $this->obtenerCategorias(),
                'form_data' => $_POST
            ]);
        }

        $query = "INSERT INTO productos (nombre, codigo_sku, categoria_id, descripcion, precio_costo, precio_venta, stock_minimo) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssisddi', $nombre, $codigo_sku, $categoria_id, $descripcion, $precio_costo, $precio_venta, $stock_minimo);

        if ($stmt->execute()) {
            header('Location: /vencimiento/index.php?action=inventory&mensaje=Producto creado correctamente');
            exit;
        } else {
            return $this->renderizar('inventory/crear_producto', [
                'errores' => ['Error al crear el producto'],
                'categorias' => $this->obtenerCategorias(),
                'form_data' => $_POST
            ]);
        }
    }

    public function agregarLote()
    {
        $this->validarAcceso(['superadmin', 'admin', 'encargado']);

        $usuario = $this->usuarioActual();
        $sede_id = $usuario['sede_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->guardarLote();
        }

        $productos = $this->obtenerProductosDisponibles();

        $this->renderizar('inventory/agregar_lote', [
            'productos' => $productos,
            'sede_id' => $sede_id
        ]);
    }

    private function guardarLote()
    {
        $usuario = $this->usuarioActual();
        $producto_id = $_POST['producto_id'] ?? '';
        $cantidad = $_POST['cantidad'] ?? '';
        $numero_lote = $_POST['numero_lote'] ?? '';
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? '';
        $sede_id = $usuario['sede_id'];
        $errores = [];

        if (empty($producto_id) || !is_numeric($producto_id)) $errores[] = 'Producto válido requerido';
        if (empty($cantidad) || !is_numeric($cantidad) || $cantidad <= 0) $errores[] = 'Cantidad válida requerida';
        if (empty($fecha_vencimiento)) $errores[] = 'Fecha de vencimiento requerida';

        if (!empty($errores)) {
            return $this->renderizar('inventory/agregar_lote', [
                'errores' => $errores,
                'productos' => $this->obtenerProductosDisponibles(),
                'form_data' => $_POST
            ]);
        }

        // Verificar que el producto existe y pertenece a la categoría correcta
        $checkQuery = "SELECT id FROM productos WHERE id = ? AND estado = 'activo'";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bind_param('i', $producto_id);
        $checkStmt->execute();

        if ($checkStmt->get_result()->num_rows === 0) {
            return $this->renderizar('inventory/agregar_lote', [
                'errores' => ['Producto no válido'],
                'productos' => $this->obtenerProductosDisponibles(),
                'form_data' => $_POST
            ]);
        }

        // Insertar lote
        $cantidad = (int)$cantidad;
        $query = "INSERT INTO lotes_productos (producto_id, sede_id, numero_lote, cantidad, cantidad_disponible, fecha_vencimiento, fecha_ingreso) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iisiss', $producto_id, $sede_id, $numero_lote, $cantidad, $cantidad, $fecha_vencimiento);

        if ($stmt->execute()) {
            $lote_id = $stmt->insert_id;

            // Registrar movimiento
            $movQuery = "INSERT INTO movimientos_inventario (lote_producto_id, usuario_id, tipo_movimiento, cantidad, motivo) 
                        VALUES (?, ?, 'entrada', ?, 'Ingreso de lote')";
            $movStmt = $this->db->prepare($movQuery);
            $usuario_id = $usuario['id'];
            $movStmt->bind_param('iii', $lote_id, $usuario_id, $cantidad);
            $movStmt->execute();

            header('Location: /vencimiento/index.php?action=inventory&mensaje=Lote agregado correctamente');
            exit;
        } else {
            return $this->renderizar('inventory/agregar_lote', [
                'errores' => ['Error al agregar el lote'],
                'productos' => $this->obtenerProductosDisponibles(),
                'form_data' => $_POST
            ]);
        }
    }

    public function detallesProducto()
    {
        $this->validarAcceso();

        $producto_id = $_GET['id'] ?? '';
        if (empty($producto_id)) {
            header('Location: /vencimiento/index.php?action=inventory');
            exit;
        }

        $usuario = $this->usuarioActual();
        $producto = $this->obtenerDetallesProducto($producto_id, $usuario['sede_id']);
        $lotes = $this->obtenerLotesProducto($producto_id, $usuario['sede_id']);

        if (!$producto) {
            header('Location: /vencimiento/index.php?action=inventory');
            exit;
        }

        $this->renderizar('inventory/detalles_producto', [
            'producto' => $producto,
            'lotes' => $lotes,
            'usuario' => $usuario
        ]);
    }

    private function obtenerDetallesProducto($producto_id, $sede_id)
    {
        $query = "SELECT p.*, c.nombre as categoria 
                 FROM productos p
                 LEFT JOIN categorias c ON p.categoria_id = c.id
                 WHERE p.id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) return null;

        $producto = $result->fetch_assoc();

        // Obtener info agregada por sede - Calcular días dinámicamente
        $lotesQuery = "SELECT 
                        SUM(cantidad_disponible) as stock,
                        MIN(fecha_vencimiento) as proximo_vencimiento,
                        COUNT(*) as total_lotes,
                        SUM(CASE WHEN DATEDIFF(fecha_vencimiento, CURDATE()) < 0 AND cantidad_disponible > 0 THEN cantidad_disponible ELSE 0 END) as cantidad_vencida
                      FROM lotes_productos
                      WHERE producto_id = ? AND sede_id = ? AND cantidad_disponible > 0";

        $stmt = $this->db->prepare($lotesQuery);
        $stmt->bind_param('ii', $producto_id, $sede_id);
        $stmt->execute();
        $info = $stmt->get_result()->fetch_assoc();

        $producto = array_merge($producto, $info);
        return $producto;
    }

    private function obtenerLotesProducto($producto_id, $sede_id)
    {
        $query = "SELECT 
                    *,
                    DATEDIFF(fecha_vencimiento, CURDATE()) as dias_para_vencer
                 FROM lotes_productos 
                 WHERE producto_id = ? AND sede_id = ? AND cantidad_disponible > 0
                 ORDER BY fecha_vencimiento ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $producto_id, $sede_id);
        $stmt->execute();

        $lotes = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $lotes[] = $row;
        }

        return $lotes;
    }

    private function obtenerProductosDisponibles()
    {
        $query = "SELECT id, nombre, codigo_sku, precio_venta FROM productos WHERE estado = 'activo' ORDER BY nombre ASC";
        $result = $this->db->query($query);
        $productos = [];

        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }

        return $productos;
    }

    private function obtenerSedes()
    {
        $query = "SELECT id, nombre FROM sedes WHERE estado = 'activa' ORDER BY nombre";
        $result = $this->db->query($query);
        $sedes = [];

        while ($row = $result->fetch_assoc()) {
            $sedes[] = $row;
        }

        return $sedes;
    }

    private function obtenerCategorias()
    {
        $query = "SELECT id, nombre FROM categorias ORDER BY nombre";
        $result = $this->db->query($query);
        $categorias = [];

        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }

        return $categorias;
    }
}
