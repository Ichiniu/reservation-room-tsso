<?php
// Quick database check script
$db = new mysqli('localhost', 'root', '', 'bookingsmarts');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "=== PEMESANAN TABLE STRUCTURE ===\n";
$result = $db->query("DESCRIBE pemesanan");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== DATA FOR ID_PEMESANAN = 210 ===\n";
$result = $db->query("SELECT ID_PEMESANAN, USERNAME, TOTAL_PESERTA, ID_GEDUNG FROM pemesanan WHERE ID_PEMESANAN = 210");
if ($row = $result->fetch_assoc()) {
    print_r($row);
} else {
    echo "No data found\n";
}

$db->close();
