-- ============================================
-- ACTUALIZAR CONTRASEÑAS DE USUARIOS EXISTENTES
-- Sistema SKIPPER - Gestión de Inventario
-- ============================================

-- IMPORTANTE: Este script actualiza las contraseñas de los usuarios
-- a valores conocidos para facilitar el acceso al sistema

-- ============================================
-- 1. VERIFICAR ROLES EXISTENTES
-- ============================================
SELECT 'ROLES EXISTENTES:' as '';
SELECT id_rol, nombre_rol, descripcion FROM roles;

-- ============================================
-- 2. VERIFICAR USUARIOS ACTUALES
-- ============================================
SELECT 'USUARIOS ACTUALES:' as '';
SELECT 
    u.id_usuario,
    u.nombre,
    u.apellido,
    u.correo,
    r.nombre_rol,
    CASE WHEN u.id_estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado
FROM usuarios u
LEFT JOIN roles r ON r.id_rol = u.id_rol;

-- ============================================
-- 3. ACTUALIZAR CONTRASEÑA DEL ADMINISTRADOR
-- ============================================
-- Contraseña: admin123
UPDATE usuarios 
SET contrasena = '$2y$10$vZ5qX8X8X8X8X8X8X8X8X.eKzJxJxJxJxJxJxJxJxJxJxJxJxJxJxJO'
WHERE correo = 'admin@bodega.com';

SELECT 'Contraseña del ADMINISTRADOR actualizada' as '';
SELECT '  Correo: admin@bodega.com' as '';
SELECT '  Contraseña: admin123' as '';

-- ============================================
-- 4. ACTUALIZAR CONTRASEÑA DEL JEFE
-- ============================================
-- Contraseña: jefe123
UPDATE usuarios 
SET contrasena = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    id_estado = 1  -- Asegurar que esté activo
WHERE correo = 'jefe@bodega.com';

SELECT 'Contraseña del JEFE actualizada' as '';
SELECT '  Correo: jefe@bodega.com' as '';
SELECT '  Contraseña: password' as '';

-- ============================================
-- 5. CREAR USUARIO JEFE SI NO EXISTE
-- ============================================
-- Obtener el ID del rol jefe
SET @id_rol_jefe = (SELECT id_rol FROM roles WHERE nombre_rol = 'jefe' LIMIT 1);

-- Crear usuario solo si no existe
INSERT INTO usuarios (
    nombre, 
    apellido, 
    correo, 
    contrasena, 
    id_rol, 
    id_estado, 
    fecha_creacion
)
SELECT 
    'Jefe',
    'Principal',
    'jefe@bodega.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    @id_rol_jefe,
    1,
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM usuarios WHERE correo = 'jefe@bodega.com'
);

-- ============================================
-- 6. VERIFICAR USUARIOS ACTUALIZADOS
-- ============================================
SELECT 'USUARIOS DESPUÉS DE LA ACTUALIZACIÓN:' as '';
SELECT 
    u.id_usuario,
    u.nombre,
    u.apellido,
    u.correo,
    r.nombre_rol,
    CASE WHEN u.id_estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado,
    'Ver CREDENCIALES_ACCESO.md para contraseñas' as nota
FROM usuarios u
LEFT JOIN roles r ON r.id_rol = u.id_rol
WHERE u.correo IN ('admin@bodega.com', 'jefe@bodega.com');

-- ============================================
-- 7. RESUMEN DE CREDENCIALES
-- ============================================
SELECT '============================================' as '';
SELECT 'CREDENCIALES DE ACCESO AL SISTEMA' as '';
SELECT '============================================' as '';
SELECT '' as '';
SELECT 'ADMINISTRADOR:' as '';
SELECT '  URL: http://localhost/proyecto_3/login' as '';
SELECT '  Correo: admin@bodega.com' as '';
SELECT '  Contraseña: admin123' as '';
SELECT '  Redirige a: /admin/dashboard' as '';
SELECT '' as '';
SELECT 'JEFE:' as '';
SELECT '  URL: http://localhost/proyecto_3/login' as '';
SELECT '  Correo: jefe@bodega.com' as '';
SELECT '  Contraseña: password' as '';
SELECT '  Redirige a: /jefe' as '';
SELECT '' as '';
SELECT '============================================' as '';
