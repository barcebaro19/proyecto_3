<?php
$mysqli = new mysqli("localhost", "root", "", "bodega_inventario");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

echo "Tables:\n";
$result = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
    echo $row[0] . "\n";
}

$target = 'inventario_impotado';
if (in_array($target, $tables)) {
    echo "\nFound '$target'. Structure:\n";
    $result = $mysqli->query("DESCRIBE $target");
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " | " . $row['Type'] . "\n";
    }
    
    echo "\nSample Data:\n";
    $result = $mysqli->query("SELECT * FROM $target LIMIT 3");
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "\nTable '$target' NOT found.\n";
}
?>
