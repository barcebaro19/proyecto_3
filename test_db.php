<?php
require('application/config/database.php');
$db = $db['default'];
$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SHOW COLUMNS FROM referencias");
if ($result) {
    echo "Columns in 'referencias':\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error describing table: " . $mysqli->error;
}

$result = $mysqli->query("SELECT * FROM referencias LIMIT 5");
if ($result) {
    echo "\nData in 'referencias':\n";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
}
$mysqli->close();
