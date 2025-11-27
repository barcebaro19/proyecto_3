<?php
// Script de diagnóstico de Login para Jefe
define('BASEPATH', 'dummy');
require('application/config/database.php');

$db = $db['default'];
$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

echo "<h1>🕵️ Diagnóstico de Login - Jefe</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;background:#f0f2f5;} .box{background:white;padding:20px;margin:15px 0;border-radius:8px;box-shadow:0 2px 5px rgba(0,0,0,0.1);} .ok{color:green;font-weight:bold;} .fail{color:red;font-weight:bold;} code{background:#eee;padding:2px 5px;border-radius:3px;}</style>";

// 1. Verificar el usuario en la BD
echo "<div class='box'>";
echo "<h2>1. Verificando Usuario en Base de Datos</h2>";
$correo = 'jefe@bodega.com';
$sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
$result = $mysqli->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<p class='ok'>✅ Usuario encontrado: " . $user['correo'] . "</p>";
    echo "<p><strong>ID:</strong> " . $user['id_usuario'] . "</p>";
    echo "<p><strong>Estado:</strong> " . ($user['id_estado'] == 1 ? '<span class="ok">Activo (1)</span>' : '<span class="fail">Inactivo (' . $user['id_estado'] . ')</span>') . "</p>";
    echo "<p><strong>Rol ID:</strong> " . $user['id_rol'] . "</p>";
    echo "<p><strong>Hash de contraseña:</strong> " . substr($user['contrasena'], 0, 20) . "...</p>";
    
    // 2. Verificar la contraseña
    echo "<h2>2. Verificando Contraseña 'password'</h2>";
    $password_ingresada = 'password';
    if (password_verify($password_ingresada, $user['contrasena'])) {
        echo "<p class='ok'>✅ La contraseña 'password' COINCIDE con el hash en la BD.</p>";
    } else {
        echo "<p class='fail'>❌ La contraseña 'password' NO coincide.</p>";
        echo "<p>⚠️ <strong>Solución:</strong> Ejecuta el botón de abajo para resetearla.</p>";
        
        // Botón para arreglar
        echo "<form method='post'>";
        echo "<input type='hidden' name='fix_password' value='1'>";
        echo "<button type='submit' style='background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;'>🔧 REPARAR CONTRASEÑA AHORA</button>";
        echo "</form>";
    }
    
    // 3. Verificar Rol
    echo "<h2>3. Verificando Rol</h2>";
    $rol_sql = "SELECT * FROM roles WHERE id_rol = " . $user['id_rol'];
    $rol_res = $mysqli->query($rol_sql);
    if ($rol_res && $rol_row = $rol_res->fetch_assoc()) {
        echo "<p>Nombre del Rol: <strong>" . $rol_row['nombre_rol'] . "</strong></p>";
        if (strtolower($rol_row['nombre_rol']) == 'jefe') {
            echo "<p class='ok'>✅ El rol es correcto.</p>";
        } else {
            echo "<p class='fail'>❌ El rol NO es 'jefe'. Es '" . $rol_row['nombre_rol'] . "'.</p>";
        }
    }
    
} else {
    echo "<p class='fail'>❌ Usuario '$correo' NO encontrado.</p>";
}
echo "</div>";

// Lógica para arreglar contraseña
if (isset($_POST['fix_password'])) {
    $new_hash = password_hash('password', PASSWORD_BCRYPT);
    $update_sql = "UPDATE usuarios SET contrasena = '$new_hash', id_estado = 1 WHERE correo = '$correo'";
    if ($mysqli->query($update_sql)) {
        echo "<div class='box ok' style='background:#dcfce7;'>✅ Contraseña actualizada correctamente a 'password'. <a href='login'>Intenta iniciar sesión ahora</a></div>";
    } else {
        echo "<div class='box fail'>❌ Error al actualizar: " . $mysqli->error . "</div>";
    }
}

// 4. Verificar Sesiones
echo "<div class='box'>";
echo "<h2>4. Verificando Sistema de Sesiones</h2>";
$session_path = session_save_path();
echo "<p>Ruta de sesiones: <code>$session_path</code></p>";
if (is_writable($session_path)) {
    echo "<p class='ok'>✅ La carpeta de sesiones es escribible.</p>";
} else {
    echo "<p class='fail'>❌ La carpeta de sesiones NO es escribible o no existe.</p>";
    echo "<p>Esto impide que inicies sesión.</p>";
}
echo "</div>";
?>
