<?php
$mysqli = new mysqli("localhost", "root", "", "bodega_inventario");
if ($mysqli->connect_errno) exit("Connect failed");

echo "Checking 'productos' count:\n";
$result = $mysqli->query("SELECT COUNT(*) as count FROM productos");
$row = $result->fetch_assoc();
echo "Total Products: " . $row['count'] . "\n";

echo "\nSample Products (Recent):\n";
$result = $mysqli->query("SELECT * FROM productos ORDER BY id_producto DESC LIMIT 5");
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id_producto'] . " | RefID: " . $row['id_referencia'] . " | Talla: " . $row['talla'] . " | Color: " . $row['color'] . " | Stock: " . $row['cantidad_stock'] . "\n";
}

echo "\nChecking 'inventario_impotado' explicitly:\n";
$result = $mysqli->query("SHOW TABLES LIKE 'inventario_impotado'");
if ($result->num_rows > 0) {
    echo "Table 'inventario_impotado' EXISTS.\n";
} else {
    echo "Table 'inventario_impotado' DOES NOT EXIST.\n";
}
?>
