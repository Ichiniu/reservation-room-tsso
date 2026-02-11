<?php
define('BASEPATH', TRUE);
require_once 'system/database/DB.php';

$config = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'bookingsmarts',
    'dbdriver' => 'mysqli'
);

$db = DB($config, TRUE);

echo "Checking if TOTAL_PESERTA column exists...\n";
$exists = $db->field_exists('TOTAL_PESERTA', 'pemesanan');
echo "Field exists: " . ($exists ? 'YES' : 'NO') . "\n\n";

echo "Getting data for ID_PEMESANAN = 210...\n";
$query = $db->query("SELECT * FROM pemesanan WHERE ID_PEMESANAN = 210");
$row = $query->row_array();

if ($row) {
    echo "Found record:\n";
    echo "ID_PEMESANAN: " . $row['ID_PEMESANAN'] . "\n";
    echo "USERNAME: " . $row['USERNAME'] . "\n";
    echo "TOTAL_PESERTA: " . (isset($row['TOTAL_PESERTA']) ? $row['TOTAL_PESERTA'] : 'COLUMN NOT EXISTS') . "\n";
} else {
    echo "No record found\n";
}
