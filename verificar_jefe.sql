-- Script para verificar y configurar el usuario Jefe

-- 1. Verificar que existe el rol "jefe"
SELECT * FROM roles WHERE nombre_rol = 'jefe';

-- 2. Si no existe, crearlo
INSERT IGNORE INTO roles (nombre_rol, descripcion, fecha_creacion) 
VALUES ('jefe', 'Jefe de Operaciones', NOW());

-- 3. Obtener el ID del rol jefe
SET @id_rol_jefe = (SELECT id_rol FROM roles WHERE nombre_rol = 'jefe' LIMIT 1);

-- 4. Verificar usuarios con rol jefe
SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.id_rol, r.nombre_rol, u.id_estado
FROM usuarios u
LEFT JOIN roles r ON r.id_rol = u.id_rol
WHERE r.nombre_rol = 'jefe';

-- 5. Si no existe un usuario jefe, crear uno de prueba
-- NOTA: Cambia el correo y la contraseña según tus necesidades
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
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    @id_rol_jefe,
    1,
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM usuarios u2 
    JOIN roles r2 ON r2.id_rol = u2.id_rol 
    WHERE r2.nombre_rol = 'jefe'
);

-- 6. Verificar el resultado final
SELECT u.id_usuario, u.nombre, u.apellido, u.correo, r.nombre_rol, u.id_estado
FROM usuarios u
LEFT JOIN roles r ON r.id_rol = u.id_rol
WHERE r.nombre_rol = 'jefe';
