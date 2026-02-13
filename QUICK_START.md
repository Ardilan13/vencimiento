# 🚀 Guía de Instalación Rápida

## ⏱️ 5 Minutos para empezar

### Paso 1: Crear la Base de Datos

```bash
# Abre tu cliente MySQL y ejecuta:
mysql -u root -p < database.sql
```

O manualmente en phpMyAdmin/MySQL Workbench:
1. Copia todo el contenido de `database.sql`
2. Pégalo en la consola SQL
3. Ejecuta

### Paso 2: Configurar .env

Abre `.env` y verifica:
```env
DB_HOST=localhost
DB_USER=root              # Tu usuario MySQL
DB_PASSWORD=              # Tu contraseña (dejar vacío si no hay)
DB_NAME=inventory_supplements
DB_PORT=3306
```

### Paso 3: Iniciar Servidor PHP

Opción A - PHP built-in (más fácil):
```bash
php -S localhost:8000
```

Opción B - Apache/Nginx:
- Configura el DocumentRoot a la carpeta del proyecto
- Accede mediante tu dominio configurado

### Paso 4: Acceder a la Aplicación

Abre en tu navegador:
```
http://localhost:8000
```

### Paso 5: Iniciar Sesión

Usa las credenciales de prueba:
```
Email: admin@supplements.com
Contraseña: password123
```

## ✅ Verificación de Instalación

Si ves:
- ✅ Página de login colorida → **¡Perfecto!**
- ✅ Dashboard cargado → **Sistema listo**
- ❌ Error de conexión → Revisa `.env` y MySQL
- ❌ Página en blanco → Verifica permisos de archivos

## 📊 Primeros Pasos en el Sistema

### 1. Crear tu primer Producto
- Ve a: Crear Producto
- Completa los campos
- Haz clic en "Crear Producto"

### 2. Agregar un Lote
- Ve a: Agregar Lote
- Selecciona un producto
- Define fecha de vencimiento
- Ingresa cantidad
- Guarda

### 3. Ver Inventario
- Ve a: Inventario
- Usa los filtros
- Haz clic en "Ver" para detalles

## 🔐 Cambiar Credenciales de Admin

**En terminal MySQL:**
```sql
USE inventory_supplements;

-- Cambiar contraseña (ejemplo: nuevacontraseña123)
UPDATE usuarios 
SET password = '$2y$10$...[hash bcrypt]...' 
WHERE email = 'admin@supplements.com';
```

O usa un generador online de bcrypt para crear el hash.

## 📁 Estructura Básica

```
proyecto/
├── index.php          ← Punto de entrada
├── .env              ← Configuración
├── database.sql      ← Script BD
├── README.md         ← Documentación
└── app/              ← Código fuente
    ├── config/       ← Configuración
    ├── controllers/  ← Lógica
    └── views/        ← Vistas HTML
```

## 🎯 Próximos Pasos Recomendados

1. **Crear Sedes**
   - Accede con SuperAdmin
   - Ve a: Sedes
   - Agrega tus sucursales

2. **Crear Usuarios**
   - Ve a: Usuarios
   - Agrega encargados por sede
   - Asigna roles

3. **Crear Categorías** (desde DB)
   ```sql
   INSERT INTO categorias (nombre, descripcion) VALUES
   ('Mi Categoría', 'Descripción');
   ```

4. **Llenar Inventario**
   - Crea productos
   - Agrega lotes con fechas
   - Monitorea vencimientos

## 🆘 Errores Comunes y Soluciones

### "Error de conexión a BD"
```
✓ Verifica que MySQL esté corriendo
✓ Confirma usuario/contraseña en .env
✓ Verifica que la BD existe
```

### "Archivo no encontrado" (Error 404)
```
✓ Usa URL: http://localhost:8000/index.php?action=login
✓ Verifica que todos los archivos están en su lugar
```

### "Token de sesión inválido"
```
✓ Limpia cookies del navegador
✓ Intenta incógnito/privado
✓ Reinicia el servidor
```

### "Permiso denegado" (Error 403)
```
✓ Verifica permisos de carpetas: chmod 755
✓ Asegura que el usuario web puede escribir en tmp
```

## 📱 Acceso Móvil

El sistema es completamente responsive:
```
http://localhost:8000 (desde tu teléfono en red local)
```

Solo necesitas reemplazar "localhost" con la IP de tu máquina:
```
http://192.168.1.100:8000 (ejemplo)
```

## 🎓 Tutorial Completo

### Escenario: Tienda de Suplementos

1. **Crear Sede "Bogotá"**
   - Dirección, teléfono, ciudad

2. **Crear Categoría "Proteínas"**
   - Desde la BD o admin

3. **Crear Producto "Whey Protein"**
   - Precio costo: $40.000
   - Precio venta: $80.000

4. **Agregar Lote**
   - Cantidad: 100 unidades
   - Vencimiento: 2025-12-31

5. **Monitorear en Dashboard**
   - Ver stock total
   - Recibir alertas automáticas

## 🔄 Mantener el Sistema

### Diario
- Revisar alertas de vencimiento
- Agregar nuevos lotes si es necesario

### Semanal
- Revisar movimientos de inventario
- Validar stocks

### Mensual
- Hacer respaldo de BD
- Revisar productos vencidos
- Generar reportes

## 💾 Respaldar Base de Datos

```bash
mysqldump -u root -p inventory_supplements > backup_$(date +%Y%m%d).sql
```

## 🚢 Deploy a Producción

1. Cambiar `APP_ENV=production` en `.env`
2. Cambiar `APP_DEBUG=false` en `.env`
3. Usar HTTPS obligatorio
4. Cambiar todas las contraseñas por defecto
5. Configurar permisos de archivos: `chmod 700`
6. Hacer backup de BD
7. Implementar WAF (Web Application Firewall)

## 📞 Problemas Avanzados

**Para errores técnicos detallados:**
- Revisa `php_error_log`
- Activa `APP_DEBUG=true` en `.env`
- Verifica permisos de carpetas
- Usa `php -l` para validar sintaxis

---

## ✨ ¡Listo!

Tu sistema de inventario está completamente funcional.

🎉 **¡A disfrutar!**
