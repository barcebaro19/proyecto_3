-- ============================================
-- SCRIPT SIMPLE: VERIFICAR Y CORREGIR USUARIOS
-- ============================================

-- 1. VER TODOS LOS USUARIOS
SELECT 
    u.id_usuario,
    u.nombre,
    u.apellido,
    u.correo,
    u.id_rol,
    r.nombre_rol,
    u.id_estado,
    CASE WHEN u.id_estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado
FROM usuarios u
LEFT JOIN roles r ON r.id_rol = u.id_rol;

-- 2. VER TODOS LOS ROLES
SELECT * FROM roles;

-- 3. ACTUALIZAR CONTRASEÑA DEL USUARIO jefe@bodega.com
-- Nueva contraseña será: password
UPDATE usuarios 
SET contrasena = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    id_estado = 1
WHERE correo = 'jefe@bodega.com';

-- 4. VERIFICAR LA ACTUALIZACIÓN
SELECT 
    u.id_usuario,
    u.nombre,
    u.correo,
    r.nombre_rol,
    'password' as nueva_contrasena,
    CASE WHEN u.id_estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado
FROM usuarios u
LEFT JOIN roles r ON r.id_rol = u.id_rol
WHERE u.correo = 'jefe@bodega.com';
