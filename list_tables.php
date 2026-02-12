<?php
$conn = mysqli_connect('localhost', 'root', '', 'bookingsmarts');
$res = mysqli_query($conn, "SHOW TABLES");
echo "Tables:\n";
while ($row = mysqli_fetch_row($res)) {
    echo "- " . $row[0] . "\n";
}
mysqli_close($conn);
