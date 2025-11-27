<!DOCTYPE html>
<html>
<head>
    <title>Test de Acceso - Jefe</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { background: #10b981; color: white; }
        .error { background: #ef4444; color: white; }
        .info { background: #3b82f6; color: white; }
        h1 { color: #2563eb; }
        code { background: #1e293b; color: #60a5fa; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Test de Configuración - Rol Jefe</h1>

    <div class="box success">
        <h2>✅ Correcciones Aplicadas</h2>
        <ul>
            <li>Rutas actualizadas en <code>routes.php</code></li>
            <li>Métodos agregados a <code>Producto_model.php</code></li>
            <li>Propiedad <code>rol</code> agregada en <code>Usuario_model.php</code></li>
        </ul>
    </div>

    <div class="box info">
        <h2>🔑 Credenciales para Probar</h2>
        <p><strong>Correo:</strong> <code>jefe@bodega.com</code></p>
        <p><strong>Contraseña:</strong> <code>password</code></p>
    </div>

    <div class="box">
        <h2>🚀 Pasos para Iniciar Sesión</h2>
        <ol>
            <li>Ve a: <a href="http://localhost/proyecto_3/login">http://localhost/proyecto_3/login</a></li>
            <li>Ingresa el correo: <strong>jefe@bodega.com</strong></li>
            <li>Ingresa la contraseña: <strong>password</strong></li>
            <li>Haz clic en "Iniciar Sesión"</li>
            <li>Deberías ser redirigido a: <code>http://localhost/proyecto_3/jefe</code></li>
        </ol>
    </div>

    <div class="box">
        <h2>📋 Verificación de Rutas</h2>
        <p>Estas rutas deberían funcionar después del login:</p>
        <ul>
            <li><code>/jefe</code> → Dashboard principal</li>
            <li><code>/jefe/productos</code> → Lista de productos</li>
            <li><code>/jefe/reportes</code> → Reportes</li>
            <li><code>/jefe/perfil</code> → Perfil de usuario</li>
        </ul>
    </div>

    <div class="box error">
        <h2>⚠️ Si Aún No Funciona</h2>
        <p>Ejecuta este SQL en phpMyAdmin:</p>
        <pre style="background: #1e293b; color: #60a5fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
UPDATE usuarios 
SET contrasena = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    id_estado = 1
WHERE correo = 'jefe@bodega.com';
        </pre>
    </div>

    <div class="box">
        <h2>🔧 Enlaces Útiles</h2>
        <p>
            <a href="http://localhost/proyecto_3/login" style="display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">
                🔐 Ir al Login
            </a>
            <a href="http://localhost/proyecto_3/ver_usuarios.php" style="display: inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">
                👥 Ver Usuarios
            </a>
            <a href="http://localhost/phpmyadmin" style="display: inline-block; padding: 10px 20px; background: #f59e0b; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">
                🗄️ phpMyAdmin
            </a>
        </p>
    </div>

</body>
</html>
