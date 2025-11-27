# 🔐 CREDENCIALES DE ACCESO - SISTEMA SKIPPER

## 📋 Usuarios Registrados en el Sistema

### 👨‍💼 **ADMINISTRADOR**
- **Correo:** `admin@bodega.com`
- **Contraseña:** `admin123`
- **Rol:** Administrador
- **Acceso a:** Panel completo de administración
- **URL después de login:** `http://localhost/proyecto_3/admin/dashboard`

---

### 👔 **JEFE DE OPERACIONES**
- **Correo:** `jefe@bodega.com`
- **Contraseña:** `jefe123`
- **Rol:** Jefe
- **Acceso a:** Dashboard, Productos (solo lectura), Reportes, Perfil
- **URL después de login:** `http://localhost/proyecto_3/jefe`

---

### 👷 **OPERARIO** (Si existe)
- **Correo:** `operario@bodega.com`
- **Contraseña:** `operario123`
- **Rol:** Operario
- **Acceso a:** Funciones limitadas de operación
- **URL después de login:** `http://localhost/proyecto_3/operario/dashboard`

---

## 🔧 Usuarios Adicionales Detectados en la Base de Datos

Según la imagen que compartiste, también tienes estos usuarios:

1. **admin@bodega.com** - Administrador principal
2. **jefe@bodega.com** - Jefe de operaciones
3. **bsebastianballesteros@gmail.com** - Usuario personalizado
4. **STIVEN@AYUDA.COM** - Usuario personalizado

---

## ⚠️ IMPORTANTE: Verificación de Contraseñas

Las contraseñas están hasheadas en la base de datos. Si no puedes iniciar sesión con las credenciales anteriores, necesitas:

### Opción 1: Actualizar la contraseña en la base de datos

Ejecuta este SQL en phpMyAdmin para establecer contraseñas conocidas:

```sql
-- Actualizar contraseña del JEFE
UPDATE usuarios 
SET contrasena = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE correo = 'jefe@bodega.com';
-- Nueva contraseña: password

-- Actualizar contraseña del ADMIN
UPDATE usuarios 
SET contrasena = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE correo = 'admin@bodega.com';
-- Nueva contraseña: password
```

Después de ejecutar esto, podrás iniciar sesión con:
- **Correo:** jefe@bodega.com
- **Contraseña:** password

### Opción 2: Crear un nuevo usuario Jefe

```sql
-- Primero, obtener el ID del rol jefe
SET @id_rol_jefe = (SELECT id_rol FROM roles WHERE nombre_rol = 'jefe' LIMIT 1);

-- Crear nuevo usuario jefe
INSERT INTO usuarios (
    nombre, 
    apellido, 
    correo, 
    contrasena, 
    id_rol, 
    id_estado, 
    fecha_creacion
) VALUES (
    'Jefe',
    'Principal',
    'jefe.nuevo@bodega.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    @id_rol_jefe,
    1,
    NOW()
);
```

Credenciales del nuevo usuario:
- **Correo:** jefe.nuevo@bodega.com
- **Contraseña:** password

---

## 🎯 Mapeo de Roles a Interfaces

| Rol | Nombre en BD | Ruta de Acceso | Funcionalidades |
|-----|--------------|----------------|-----------------|
| **Administrador** | `administrador` | `/admin/dashboard` | ✅ Crear/Editar/Eliminar todo<br>✅ Gestión de usuarios<br>✅ Reportes completos<br>✅ Configuración del sistema |
| **Jefe** | `jefe` | `/jefe` | ✅ Ver productos (solo lectura)<br>✅ Reportes y estadísticas<br>✅ Dashboard con gráficos<br>❌ No puede crear/editar/eliminar |
| **Operario** | `operario` | `/operario/dashboard` | ✅ Registrar movimientos<br>✅ Ver inventario<br>❌ Funciones limitadas |

---

## 🔍 Verificar tu Usuario Actual

Para verificar qué rol tiene tu usuario actual, ejecuta en phpMyAdmin:

```sql
SELECT 
    u.id_usuario,
    u.nombre,
    u.apellido,
    u.correo,
    r.nombre_rol,
    u.id_estado,
    CASE WHEN u.id_estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado_texto
FROM usuarios u
LEFT JOIN roles r ON r.id_rol = u.id_rol
WHERE u.correo = 'TU_CORREO_AQUI';
```

Reemplaza `TU_CORREO_AQUI` con el correo que estás intentando usar.

---

## 🚀 Pasos para Iniciar Sesión

1. Ve a: `http://localhost/proyecto_3/login`
2. Ingresa tu correo y contraseña
3. Serás redirigido automáticamente según tu rol:
   - **Administrador** → `/admin/dashboard`
   - **Jefe** → `/jefe`
   - **Operario** → `/operario/dashboard`

---

## 📞 Solución de Problemas

### ❌ "Correo o contraseña incorrectos"
- Verifica que el usuario esté activo (`id_estado = 1`)
- Verifica que el rol esté correctamente asignado
- Ejecuta el script `test_jefe_login.php` para diagnóstico

### ❌ "No tiene permisos para acceder a esta sección"
- El rol del usuario no coincide con la interfaz
- Verifica el campo `id_rol` en la tabla `usuarios`

### ❌ Error 404 después de login
- Verifica que exista el controlador para ese rol
- Revisa las rutas en `application/config/routes.php`

---

**Fecha de creación:** 27 de noviembre de 2025
**Sistema:** SKIPPER - Gestión de Inventario Inteligente
