# Sistema de Inventario de Suplementos - Documentación Completa

## 📋 Descripción General

Sistema MVC completo en PHP para la gestión de inventario de productos (suplementos, proteínas, etc.) con control de vencimientos por lotes, roles de usuario y manejo por sedes.

## 🎯 Características Principales

- ✅ **Autenticación de Usuarios** con roles específicos (SuperAdmin, Admin, Encargado, Vendedor)
- ✅ **Gestión por Sedes** - Control independiente de inventario por cada sucursal
- ✅ **Control de Vencimientos** - Sistema automático de alertas para productos próximos a vencer
- ✅ **Gestión de Lotes** - Múltiples lotes del mismo producto con diferentes fechas de vencimiento
- ✅ **Dashboard Dinámico** - Vistas diferentes según el rol del usuario
- ✅ **Responsive Design** - Interfaz moderna con Tailwind CSS
- ✅ **Gestión de Productos** - Crear y categorizar productos
- ✅ **Movimientos de Inventario** - Registro automático de entradas, salidas y ajustes

## 📁 Estructura del Proyecto

```
proyecto-inventario/
│
├── index.php                          # Router principal
├── .env                              # Variables de entorno
├── database.sql                      # Script SQL para crear BD
│
└── app/
    ├── config/
    │   ├── Database.php             # Clase de conexión a BD
    │   └── Env.php                  # Cargador de variables .env
    │
    ├── controllers/
    │   ├── BaseController.php       # Controlador base con autenticación
    │   ├── AuthController.php       # Login, registro, logout
    │   ├── DashboardController.php  # Dashboard principal
    │   └── InventoryController.php  # Gestión de inventario
    │
    └── views/
        ├── layout.php               # Layout principal
        ├── auth/
        │   ├── login.php           # Formulario de login
        │   └── register.php        # Formulario de registro
        ├── dashboard/
        │   └── index.php           # Dashboard
        └── inventory/
            ├── listado.php         # Listado de inventario
            ├── crear_producto.php  # Crear nuevo producto
            ├── agregar_lote.php    # Agregar lote
            └── detalles_producto.php # Detalles del producto
```

## 🚀 Instalación y Configuración

### 1. Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache, Nginx, etc.)
- Composer (opcional, pero recomendado)

### 2. Pasos de Instalación

#### Paso 1: Clonar o Descargar el Proyecto
```bash
git clone tu-repositorio-url
cd proyecto-inventario
```

#### Paso 2: Configurar la Base de Datos

**Opción A - Usando MySQL Workbench o phpMyAdmin:**
1. Abre MySQL Workbench o phpMyAdmin
2. Crea una nueva base de datos llamada `inventory_supplements`
3. Abre el archivo `database.sql`
4. Ejecuta el script completo

**Opción B - Desde terminal:**
```bash
mysql -u root -p < database.sql
```

#### Paso 3: Configurar Variables de Entorno
1. Abre el archivo `.env` en la raíz del proyecto
2. Configura los siguientes valores:

```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=tu_contraseña
DB_NAME=inventory_supplements
DB_PORT=3306

APP_NAME=Inventory System
APP_ENV=development
APP_DEBUG=true
```

#### Paso 4: Configurar el Servidor Web

**Para Apache:**
```apache
<VirtualHost *:80>
    ServerName inventory.local
    DocumentRoot /ruta/al/proyecto
    
    <Directory /ruta/al/proyecto>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Para desarrollo rápido (PHP built-in):**
```bash
php -S localhost:8000
```

#### Paso 5: Acceder a la Aplicación
```
http://localhost:8000
o
http://inventory.local
```

## 🔐 Credenciales por Defecto

El sistema viene con un usuario SuperAdmin predeterminado:

```
Email: admin@supplements.com
Contraseña: password123
Rol: SuperAdmin
```

**⚠️ IMPORTANTE:** Cambia estas credenciales en producción

## 👥 Roles y Permisos

### SuperAdmin
- Acceso a todas las sedes
- Gestión de usuarios
- Gestión de sedes
- Ver dashboard global
- Crear y editar productos
- Generar reportes

### Admin
- Acceso a su sede asignada
- Gestión de usuarios en su sede
- Gestión de inventario
- Ver alertas de vencimiento
- Crear lotes

### Encargado
- Acceso a su sede asignada
- Gestión de inventario
- Agregar lotes
- Ver alertas

### Vendedor
- Acceso a su sede asignada
- Solo lectura del inventario
- Ver stock disponible

## 📊 Base de Datos - Tablas Principales

### usuarios
Almacena usuarios del sistema con roles y sede asignada

```sql
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('superadmin', 'admin', 'encargado', 'vendedor') DEFAULT 'vendedor',
    sede_id INT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    last_login TIMESTAMP NULL
);
```

### productos
Almacena los productos disponibles

```sql
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    codigo_sku VARCHAR(50) UNIQUE,
    categoria_id INT NOT NULL,
    descripcion TEXT,
    precio_costo DECIMAL(10, 2) NOT NULL,
    precio_venta DECIMAL(10, 2) NOT NULL,
    stock_minimo INT DEFAULT 10,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);
```

### lotes_productos
Almacena lotes con fechas de vencimiento específicas

```sql
CREATE TABLE lotes_productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    sede_id INT NOT NULL,
    numero_lote VARCHAR(100),
    cantidad INT NOT NULL DEFAULT 0,
    cantidad_disponible INT NOT NULL DEFAULT 0,
    fecha_vencimiento DATE NOT NULL,
    fecha_ingreso DATE NOT NULL,
    estado ENUM('disponible', 'proxima_vencer', 'vencida', 'agotada') DEFAULT 'disponible'
);
```

### sedes
Almacena las diferentes sedes/sucursales

```sql
CREATE TABLE sedes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    ciudad VARCHAR(100),
    estado ENUM('activa', 'inactiva') DEFAULT 'activa'
);
```

## 🎨 Interfaz de Usuario

### Temas de Color
- **Primario:** Gradiente Púrpura (#667eea) a Rosa (#764ba2)
- **Secundario:** Gris (#1f2937 a #111827)
- **Acentos:** Rojo (alertas), Amarillo (precaución), Verde (éxito)

### Componentes
- **Sidebar:** Navegación principal con menú responsivo
- **Cards:** Componentes de información con sombras y hover effects
- **Tablas:** Tablas responsive con diseño moderno
- **Formularios:** Inputs con validación visual y feedback

## 🔧 Funcionalidades Detalladas

### 1. Sistema de Autenticación
- Login y registro de usuarios
- Validación de credenciales
- Cierre de sesión (logout)
- Control de acceso por roles

### 2. Dashboard
**Para SuperAdmin:**
- Total de sedes activas
- Total de productos
- Alertas de vencimiento globales
- Resumen por sede

**Para Otros Roles:**
- Productos en su sede
- Total de lotes
- Stock total
- Productos próximos a vencer
- Productos vencidos

### 3. Gestión de Productos
- Crear nuevos productos
- Asignar categorías
- Establecer precios (costo y venta)
- Definir stock mínimo
- Ver detalles del producto

### 4. Gestión de Lotes
- Crear lotes con número único
- Asignar fecha de vencimiento
- Controlar cantidad disponible
- Cálculo automático de días para vencer
- Alertas visuales por estado

### 5. Control de Vencimientos
- Cálculo automático de días restantes
- Alertas para próximos a vencer (7 días)
- Notificación de vencidos
- Sistema de estados (disponible, próxima_vencer, vencida, agotada)

## 🔄 Flujo de Datos

```
Usuario → Login → Autenticación → Dashboard
                      ↓
                 Verificar Rol
                      ↓
         ↙            ↓            ↘
    SuperAdmin    Admin/Encargado  Vendedor
        ↓              ↓              ↓
    Todas Sedes   Su Sede       Solo Lectura
```

## 📱 Rutas Disponibles

| Acción | URL | Descripción |
|--------|-----|-------------|
| Login | `?action=login` | Página de inicio de sesión |
| Registro | `?action=register` | Página de registro |
| Logout | `?action=logout` | Cerrar sesión |
| Dashboard | `?action=dashboard` | Panel principal |
| Inventario | `?action=inventory` | Listado de inventario |
| Crear Producto | `?action=crear_producto` | Crear nuevo producto |
| Agregar Lote | `?action=agregar_lote` | Agregar lote a producto |
| Detalles | `?action=detalles_producto&id=X` | Ver detalles del producto |

## 🛡️ Seguridad

### Implementado
- ✅ Hashing de contraseñas con bcrypt
- ✅ Protección contra SQL Injection (prepared statements)
- ✅ Validación de sesiones
- ✅ Control de acceso por roles
- ✅ Validación de entrada de datos

### Recomendaciones para Producción
- Implementar HTTPS obligatorio
- Usar variables de entorno para credenciales sensibles
- Implementar CSRF tokens
- Usar Content Security Policy (CSP)
- Realizar auditorías de seguridad regularmente
- Implementar rate limiting en login
- Usar cookies con bandera HttpOnly

## 🐛 Solución de Problemas

### Error de conexión a BD
```
Solución: Verifica que los datos en .env sean correctos
```

### Errores de permisos
```
Solución: Asegúrate de que el usuario en la BD tiene los permisos correctos
```

### Formularios no funcionan
```
Solución: Verifica que el método POST esté correctamente configurado
```

### Estilos no cargados
```
Solución: Asegúrate de que Tailwind CDN esté disponible
```

## 📈 Escalabilidad

El sistema está diseñado para:
- Soportar múltiples sedes
- Manejar miles de productos
- Gestionar millones de registros de movimientos
- Escalar horizontalmente agregando más servidores

## 🔄 Próximas Mejoras Sugeridas

- [ ] Exportar reportes a PDF/Excel
- [ ] Integración con sistema de ventas
- [ ] API REST para consumo externo
- [ ] Notificaciones por email
- [ ] Códigos de barras/QR
- [ ] Historial de cambios completo
- [ ] Dashboard en tiempo real
- [ ] Análisis de tendencias

## 📞 Soporte

Para problemas o preguntas:
1. Revisa la documentación
2. Verifica los logs de la aplicación
3. Consulta la base de datos directamente
4. Contacta al equipo de desarrollo

## 📄 Licencia

Este proyecto está bajo licencia MIT.

## 👨‍💻 Autor

Desarrollado como sistema de gestión de inventario profesional.

---

**Última actualización:** 2024
**Versión:** 1.0.0
