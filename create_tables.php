<?php
// Configuración de la base de datos
$config = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'bodega_inventario',
    'dbdriver' => 'mysqli'
];

// Conectar a MySQL
$conn = new mysqli($config['hostname'], $config['username'], $config['password']);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear la base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8 COLLATE utf8_general_ci";
if ($conn->query($sql) === TRUE) {
    echo "Base de datos '{$config['database']}' creada o ya existente<br>";
} else {
    die("Error al crear la base de datos: " . $conn->error);
}

// Seleccionar la base de datos
$conn->select_db($config['database']);

// Crear tabla de usuarios
$sql = "CREATE TABLE IF NOT EXISTS `usuarios` (
    `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `contrasena` VARCHAR(255) NOT NULL,
    `rol` ENUM('administrador', 'jefe', 'bodeguero') NOT NULL DEFAULT 'bodeguero',
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `ultimo_acceso` DATETIME DEFAULT NULL,
    `id_estado` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'usuarios' creada exitosamente<br>";
    
    // Insertar usuario administrador por defecto si no existe
    $email = 'admin@bodega.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $check = $conn->query("SELECT id_usuario FROM usuarios WHERE email = '$email'");
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol) 
                VALUES ('Administrador', '$email', '$password', 'administrador')";
        if ($conn->query($sql) === TRUE) {
            echo "Usuario administrador creado exitosamente<br>";
            echo "Email: admin@bodega.com<br>";
            echo "Contraseña: admin123<br>";
        } else {
            echo "Error al crear usuario administrador: " . $conn->error . "<br>";
        }
    } else {
        echo "El usuario administrador ya existe<br>";
    }
} else {
    echo "Error al crear tabla 'usuarios': " . $conn->error . "<br>";
}

// Crear tabla de categorías
$sql = "CREATE TABLE IF NOT EXISTS `categorias` (
    `id_categoria` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `descripcion` TEXT,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `id_estado` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'categorias' creada exitosamente<br>";
} else {
    echo "Error al crear tabla 'categorias': " . $conn->error . "<br>";
}

// Crear tabla de productos
$sql = "CREATE TABLE IF NOT EXISTS `productos` (
    `id_producto` INT(11) NOT NULL AUTO_INCREMENT,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `nombre` VARCHAR(255) NOT NULL,
    `descripcion` TEXT,
    `id_categoria` INT(11) NOT NULL,
    `stock` INT(11) NOT NULL DEFAULT 0,
    `stock_minimo` INT(11) NOT NULL DEFAULT 5,
    `precio_compra` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `precio_venta` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `id_estado` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_producto`),
    FOREIGN KEY (`id_categoria`) REFERENCES `categorias`(`id_categoria`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'productos' creada exitosamente<br>";
} else {
    echo "Error al crear tabla 'productos': " . $conn->error . "<br>";
}

// Crear tabla de movimientos de inventario
$sql = "CREATE TABLE IF NOT EXISTS `movimientos_inventario` (
    `id_movimiento` INT(11) NOT NULL AUTO_INCREMENT,
    `id_producto` INT(11) NOT NULL,
    `tipo` ENUM('entrada', 'salida') NOT NULL,
    `cantidad` INT(11) NOT NULL,
    `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `id_usuario` INT(11) NOT NULL,
    `observaciones` TEXT,
    PRIMARY KEY (`id_movimiento`),
    FOREIGN KEY (`id_producto`) REFERENCES `productos`(`id_producto`) ON DELETE CASCADE,
    FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'movimientos_inventario' creada exitosamente<br>";
} else {
    echo "Error al crear tabla 'movimientos_inventario': " . $conn->error . "<br>";
}

$conn->close();

echo "<h3>Proceso completado. <a href='login'>Ir al inicio de sesión</a></h3>";
?>
