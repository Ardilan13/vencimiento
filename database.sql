-- Crear base de datos
CREATE DATABASE IF NOT EXISTS inventory_supplements;
USE inventory_supplements;

-- Tabla de sedes
CREATE TABLE sedes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    ciudad VARCHAR(100),
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('superadmin', 'admin', 'encargado', 'vendedor') DEFAULT 'vendedor',
    sede_id INT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE SET NULL,
    INDEX(email),
    INDEX(rol),
    INDEX(sede_id)
);

-- Tabla de categorías de productos
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    codigo_sku VARCHAR(50) UNIQUE,
    categoria_id INT NOT NULL,
    descripcion TEXT,
    precio_costo DECIMAL(10, 2) NOT NULL,
    precio_venta DECIMAL(10, 2) NOT NULL,
    stock_minimo INT DEFAULT 10,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    INDEX(nombre),
    INDEX(codigo_sku),
    INDEX(categoria_id)
);

-- Tabla de lotes de productos (para controlar vencimientos)
CREATE TABLE lotes_productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    sede_id INT NOT NULL,
    numero_lote VARCHAR(100),
    cantidad INT NOT NULL DEFAULT 0,
    cantidad_disponible INT NOT NULL DEFAULT 0,
    fecha_vencimiento DATE NOT NULL,
    fecha_ingreso DATE NOT NULL,
    estado ENUM('disponible', 'proxima_vencer', 'vencida', 'agotada') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    INDEX(fecha_vencimiento),
    INDEX(sede_id),
    INDEX(producto_id),
    UNIQUE KEY unique_lote (producto_id, sede_id, numero_lote)
);

-- Tabla de movimientos de inventario
CREATE TABLE movimientos_inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lote_producto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'merma') DEFAULT 'entrada',
    cantidad INT NOT NULL,
    motivo TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lote_producto_id) REFERENCES lotes_productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX(created_at),
    INDEX(lote_producto_id)
);

-- Tabla de alertas de vencimiento
CREATE TABLE alertas_vencimiento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lote_producto_id INT NOT NULL,
    sede_id INT NOT NULL,
    tipo_alerta ENUM('proxima_vencer', 'vencida') DEFAULT 'proxima_vencer',
    dias_restantes INT,
    estado ENUM('activa', 'resuelta', 'ignorada') DEFAULT 'activa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lote_producto_id) REFERENCES lotes_productos(id) ON DELETE CASCADE,
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    INDEX(sede_id),
    INDEX(estado)
);

-- Insertar sede de prueba
INSERT INTO sedes (nombre, direccion, telefono, ciudad) VALUES
('Sede Principal', 'Calle Principal 123', '555-0001', 'Bogotá'),
('Sede Zona Norte', 'Avenida Reforma 456', '555-0002', 'Bogotá'),
('Sede Centro', 'Carrera 7 789', '555-0003', 'Medellín');

-- Insertar usuario superadmin
INSERT INTO usuarios (nombre, email, password, rol, sede_id) VALUES
('Admin Sistema', 'admin@supplements.com', '$2y$10$YOixzK.8vG9HNS9E6bKWaOgEzI3.RfkrKuF5Zxq/jVXV6CzPWfKyG', 'superadmin', 1);

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion) VALUES
('Proteínas', 'Suplementos de proteína en polvo'),
('Aminoácidos', 'BCAA, EAA y otros aminoácidos'),
('Pre-Entreno', 'Suplementos pre-entreno'),
('Vitaminas', 'Vitaminas y minerales'),
('Quemadores de Grasa', 'Suplementos termogénicos'),
('Ganadores de Peso', 'Mass gainers y carbohidratos'),
('Creatina', 'Creatina monohidrato y variantes');

-- Insertar productos de ejemplo
INSERT INTO productos (nombre, codigo_sku, categoria_id, descripcion, precio_costo, precio_venta, stock_minimo) VALUES
('Whey Protein Gold Standard 2kg', 'SKU001', 1, 'Proteína whey isolada premium', 45000, 85000, 5),
('BCAA 2:1:1 500g', 'SKU002', 2, 'Aminoácidos ramificados', 25000, 50000, 10),
('C4 Pre-Entreno 390g', 'SKU003', 3, 'Pre-entreno con cafeína', 35000, 65000, 8),
('Vitamina D3 5000UI', 'SKU004', 4, 'Vitamina D3 en cápsulas', 20000, 45000, 15),
('Thermogenic Plus 180 cáps', 'SKU005', 5, 'Quemador de grasa', 40000, 75000, 6),
('Mass Gainer 2.5kg', 'SKU006', 6, 'Ganador de peso', 50000, 95000, 4),
('Creatina Monohidrato 300g', 'SKU007', 7, 'Creatina pura', 15000, 35000, 20);
