<?php
// Script de depuración de base de datos
define('BASEPATH', 'dummy'); // Para engañar a los scripts si verifican BASEPATH
require('application/config/database.php');

$db = $db['default'];
$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

echo "<h1>🔍 Depuración de Base de Datos</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} .success{color:green;} .error{color:red;}</style>";

// 1. Verificar tabla movimientos
echo "<h2>1. Estructura de 'movimientos'</h2>";
$result = $mysqli->query("DESCRIBE movimientos");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ Error al describir tabla movimientos: " . $mysqli->error . "</p>";
}

// 2. Probar consulta de últimos movimientos (la que usa Jefe)
echo "<h2>2. Prueba de Consulta: Últimos Movimientos</h2>";
$sql = "SELECT m.*, p.codigo_interno, r.nombre_referencia, u.nombre as usuario_nombre
        FROM movimientos m
        LEFT JOIN productos p ON p.id_producto = m.id_producto
        LEFT JOIN referencias r ON r.id_referencia = p.id_referencia
        LEFT JOIN usuarios u ON u.id_usuario = m.id_usuario
        ORDER BY m.fecha_movimiento DESC
        LIMIT 5";

$result = $mysqli->query($sql);
if ($result) {
    echo "<p class='success'>✅ Consulta ejecutada correctamente</p>";
    echo "<p>Filas devueltas: " . $result->num_rows . "</p>";
} else {
    echo "<p class='error'>❌ Error en la consulta: " . $mysqli->error . "</p>";
}

// 3. Probar consulta de productos bajo stock
echo "<h2>3. Prueba de Consulta: Productos Bajo Stock</h2>";
$sql = "SELECT p.*, r.nombre_referencia, r.codigo_referencia, e.nombre_estado
        FROM productos p
        LEFT JOIN referencias r ON r.id_referencia = p.id_referencia
        LEFT JOIN estado_general e ON e.id_estado = p.id_estado
        WHERE p.cantidad_stock <= p.stock_minimo
        AND p.id_estado = 1
        ORDER BY p.cantidad_stock ASC";

$result = $mysqli->query($sql);
if ($result) {
    echo "<p class='success'>✅ Consulta ejecutada correctamente</p>";
    echo "<p>Filas devueltas: " . $result->num_rows . "</p>";
} else {
    echo "<p class='error'>❌ Error en la consulta: " . $mysqli->error . "</p>";
}

$mysqli->close();
?>
