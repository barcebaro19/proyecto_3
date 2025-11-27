<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Usuarios - SKIPPER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        h2 {
            color: #1e40af;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f0f9ff;
        }
        .success {
            background: #10b981;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .error {
            background: #ef4444;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            background: #f59e0b;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info {
            background: #3b82f6;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #10b981;
            color: white;
        }
        .badge-danger {
            background: #ef4444;
            color: white;
        }
        .badge-warning {
            background: #f59e0b;
            color: white;
        }
        .credentials {
            background: #1e293b;
            color: #f1f5f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .credentials h3 {
            color: #60a5fa;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <h1>🔍 Verificación de Usuarios - Sistema SKIPPER</h1>

<?php
require('application/config/database.php');

$db = $db['default'];
$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    echo '<div class="error">❌ Error de conexión: ' . $mysqli->connect_error . '</div>';
    die();
}

echo '<div class="success">✅ Conexión a la base de datos exitosa</div>';

// 1. Mostrar todos los roles
echo '<h2>📋 Roles Disponibles</h2>';
$result = $mysqli->query("SELECT * FROM roles");
if ($result && $result->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>ID</th><th>Nombre del Rol</th><th>Descripción</th></tr>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id_rol'] . '</td>';
        echo '<td><strong>' . $row['nombre_rol'] . '</strong></td>';
        echo '<td>' . ($row['descripcion'] ?? 'N/A') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<div class="warning">⚠️ No se encontraron roles</div>';
}

// 2. Mostrar todos los usuarios
echo '<h2>👥 Usuarios Registrados</h2>';
$result = $mysqli->query("
    SELECT 
        u.id_usuario,
        u.nombre,
        u.apellido,
        u.correo,
        u.id_rol,
        r.nombre_rol,
        u.id_estado,
        CASE WHEN u.id_estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado_texto
    FROM usuarios u
    LEFT JOIN roles r ON r.id_rol = u.id_rol
    ORDER BY u.id_usuario
");

if ($result && $result->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th></tr>';
    
    $usuarios_jefe = [];
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id_usuario'] . '</td>';
        echo '<td>' . $row['nombre'] . ' ' . $row['apellido'] . '</td>';
        echo '<td><strong>' . $row['correo'] . '</strong></td>';
        echo '<td>' . ($row['nombre_rol'] ?? 'Sin rol') . '</td>';
        echo '<td>';
        if ($row['id_estado'] == 1) {
            echo '<span class="badge badge-success">Activo</span>';
        } else {
            echo '<span class="badge badge-danger">Inactivo</span>';
        }
        echo '</td>';
        echo '</tr>';
        
        // Guardar usuarios con rol jefe
        if ($row['nombre_rol'] == 'jefe') {
            $usuarios_jefe[] = $row;
        }
    }
    echo '</table>';
    
    // 3. Mostrar credenciales para usuarios jefe
    if (count($usuarios_jefe) > 0) {
        echo '<div class="info">✅ Se encontraron ' . count($usuarios_jefe) . ' usuario(s) con rol JEFE</div>';
        
        foreach ($usuarios_jefe as $jefe) {
            echo '<div class="credentials">';
            echo '<h3>🔑 Credenciales para: ' . $jefe['nombre'] . ' ' . $jefe['apellido'] . '</h3>';
            echo '<p><strong>Correo:</strong> ' . $jefe['correo'] . '</p>';
            echo '<p><strong>Contraseña sugerida:</strong> password</p>';
            echo '<p><strong>Estado:</strong> ' . $jefe['estado_texto'] . '</p>';
            echo '<p><strong>URL de acceso:</strong> <a href="http://localhost/proyecto_3/login" style="color: #60a5fa;">http://localhost/proyecto_3/login</a></p>';
            
            if ($jefe['id_estado'] != 1) {
                echo '<p style="color: #fbbf24;">⚠️ ADVERTENCIA: Este usuario está INACTIVO. Necesitas activarlo.</p>';
            }
            echo '</div>';
        }
    } else {
        echo '<div class="warning">⚠️ No se encontraron usuarios con rol JEFE</div>';
        echo '<div class="info">';
        echo '<h3>💡 Solución: Ejecuta este SQL en phpMyAdmin</h3>';
        echo '<pre style="background: white; padding: 15px; border-radius: 5px; color: #1e293b;">';
        echo "-- Obtener ID del rol jefe\n";
        echo "SET @id_rol_jefe = (SELECT id_rol FROM roles WHERE nombre_rol = 'jefe' LIMIT 1);\n\n";
        echo "-- Crear usuario jefe\n";
        echo "INSERT INTO usuarios (nombre, apellido, correo, contrasena, id_rol, id_estado, fecha_creacion)\n";
        echo "VALUES ('Jefe', 'Principal', 'jefe@bodega.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', @id_rol_jefe, 1, NOW());";
        echo '</pre>';
        echo '</div>';
    }
    
} else {
    echo '<div class="error">❌ No se encontraron usuarios en la base de datos</div>';
}

// 4. Instrucciones finales
echo '<h2>📝 Instrucciones</h2>';
echo '<div class="info">';
echo '<ol>';
echo '<li>Si ves un usuario con rol "jefe" arriba, usa ese correo para iniciar sesión</li>';
echo '<li>La contraseña predeterminada es: <strong>password</strong></li>';
echo '<li>Si el usuario está INACTIVO, ejecuta este SQL:</li>';
echo '</ol>';
echo '<pre style="background: white; padding: 15px; border-radius: 5px; color: #1e293b; margin-top: 10px;">';
echo "UPDATE usuarios SET id_estado = 1, contrasena = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE correo = 'jefe@bodega.com';";
echo '</pre>';
echo '</div>';

echo '<div style="margin-top: 30px; text-align: center;">';
echo '<a href="http://localhost/proyecto_3/login" class="btn">🚀 Ir al Login</a>';
echo '<a href="http://localhost/phpmyadmin" class="btn">🗄️ Abrir phpMyAdmin</a>';
echo '</div>';

$mysqli->close();
?>

</body>
</html>
