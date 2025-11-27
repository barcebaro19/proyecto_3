<?php
// Script de prueba para verificar el login del jefe
require('application/config/database.php');

$db = $db['default'];
$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

echo "<h2>Verificación del Usuario Jefe</h2>";

// 1. Verificar roles
echo "<h3>1. Roles en la base de datos:</h3>";
$result = $mysqli->query("SELECT * FROM roles");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre Rol</th><th>Descripción</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_rol'] . "</td>";
        echo "<td>" . $row['nombre_rol'] . "</td>";
        echo "<td>" . ($row['descripcion'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 2. Verificar usuarios con rol jefe
echo "<h3>2. Usuarios con rol 'jefe':</h3>";
$result = $mysqli->query("
    SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.id_rol, r.nombre_rol, u.id_estado
    FROM usuarios u
    LEFT JOIN roles r ON r.id_rol = u.id_rol
    WHERE r.nombre_rol = 'jefe'
");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_usuario'] . "</td>";
        echo "<td>" . $row['nombre'] . " " . $row['apellido'] . "</td>";
        echo "<td>" . $row['correo'] . "</td>";
        echo "<td>" . $row['nombre_rol'] . "</td>";
        echo "<td>" . ($row['id_estado'] == 1 ? 'Activo' : 'Inactivo') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>⚠️ No se encontraron usuarios con rol 'jefe'</p>";
    echo "<p>Ejecuta el archivo <strong>verificar_jefe.sql</strong> para crear un usuario jefe de prueba.</p>";
}

// 3. Probar verificación de contraseña
echo "<h3>3. Prueba de autenticación:</h3>";
$test_email = 'jefe@bodega.com';
$test_password = 'password';

$result = $mysqli->query("
    SELECT u.*, r.nombre_rol
    FROM usuarios u
    LEFT JOIN roles r ON r.id_rol = u.id_rol
    WHERE u.correo = '$test_email'
");

if ($result && $row = $result->fetch_assoc()) {
    echo "<p><strong>Usuario encontrado:</strong> " . $row['nombre'] . " " . $row['apellido'] . "</p>";
    echo "<p><strong>Correo:</strong> " . $row['correo'] . "</p>";
    echo "<p><strong>Rol:</strong> " . $row['nombre_rol'] . "</p>";
    
    // Verificar contraseña
    if (password_verify($test_password, $row['contrasena'])) {
        echo "<p style='color: green;'>✅ La contraseña 'password' es correcta</p>";
    } else {
        echo "<p style='color: red;'>❌ La contraseña 'password' NO es correcta</p>";
        echo "<p>Hash almacenado: " . substr($row['contrasena'], 0, 50) . "...</p>";
    }
} else {
    echo "<p style='color: red;'>❌ No se encontró el usuario con correo: $test_email</p>";
}

// 4. Instrucciones
echo "<h3>4. Instrucciones para iniciar sesión:</h3>";
echo "<ol>";
echo "<li>Asegúrate de que existe un usuario con rol 'jefe' en la base de datos</li>";
echo "<li>Verifica que el usuario esté activo (id_estado = 1)</li>";
echo "<li>Usa el correo y contraseña correctos</li>";
echo "<li>Si no existe, ejecuta el archivo <strong>verificar_jefe.sql</strong></li>";
echo "<li>Credenciales de prueba: <strong>jefe@bodega.com</strong> / <strong>password</strong></li>";
echo "</ol>";

$mysqli->close();
?>
