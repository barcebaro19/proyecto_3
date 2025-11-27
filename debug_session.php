<?php
// Script de depuración de sesión
session_start();

echo "<h1>🔍 Depuración de Sesión</h1>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { background: #10b981; color: white; }
    .error { background: #ef4444; color: white; }
    pre { background: #1e293b; color: #60a5fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    h2 { color: #2563eb; }
</style>";

// Verificar si hay sesión activa
if (isset($_SESSION) && !empty($_SESSION)) {
    echo "<div class='box success'>";
    echo "<h2>✅ Sesión Activa Detectada</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    if (isset($_SESSION['rol'])) {
        echo "<p><strong>Rol detectado:</strong> " . $_SESSION['rol'] . "</p>";
        
        // Mostrar a dónde debería redirigir
        $redirect_map = [
            'administrador' => 'admin/dashboard',
            'jefe' => 'jefe',
            'operario' => 'operario/dashboard'
        ];
        
        $redirect = isset($redirect_map[$_SESSION['rol']]) ? $redirect_map[$_SESSION['rol']] : 'dashboard';
        echo "<p><strong>Debería redirigir a:</strong> <code>http://localhost/proyecto_3/$redirect</code></p>";
        echo "<p><a href='http://localhost/proyecto_3/$redirect' style='display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Ir Manualmente</a></p>";
    }
    echo "</div>";
} else {
    echo "<div class='box error'>";
    echo "<h2>❌ No hay sesión activa</h2>";
    echo "<p>Necesitas iniciar sesión primero</p>";
    echo "<p><a href='http://localhost/proyecto_3/login' style='display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Ir al Login</a></p>";
    echo "</div>";
}

// Verificar configuración de CodeIgniter
echo "<div class='box'>";
echo "<h2>📋 Información del Sistema</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Save Path:</strong> " . session_save_path() . "</p>";
echo "</div>";

// Botones de acción
echo "<div class='box'>";
echo "<h2>🔧 Acciones</h2>";
echo "<p>";
echo "<a href='http://localhost/proyecto_3/login' style='display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🔐 Ir al Login</a>";
echo "<a href='http://localhost/proyecto_3/logout' style='display: inline-block; padding: 10px 20px; background: #ef4444; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🚪 Cerrar Sesión</a>";
echo "<a href='http://localhost/proyecto_3/ver_usuarios.php' style='display: inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>👥 Ver Usuarios</a>";
echo "</p>";
echo "</div>";
?>
