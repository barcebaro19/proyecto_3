<?php
$mysqli = new mysqli("localhost", "root", "", "bodega_inventario");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

echo "Tables:\n";
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    echo $row[0] . "\n";
}

echo "\nChecking 'inventario_impotado' columns if exists:\n";
$result = $mysqli->query("DESCRIBE inventario_impotado");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Table 'inventario_impotado' not found. Checking 'inventario_importado':\n";
    $result = $mysqli->query("DESCRIBE inventario_importado");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
    } else {
        echo "Table 'inventario_importado' not found either.\n";
    }
}

$mysqli->close();
?>
