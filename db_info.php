<?php
$c = mysqli_connect("localhost", "root", "", "bookingsmarts");
if (!$c) die("Connect failed");

function check($table)
{
    global $c;
    echo "--- $table ---\n";
    $res = $c->query("DESCRIBE $table");
    if (!$res) {
        echo "Error: " . $c->error . "\n";
        return;
    }
    while ($r = $res->fetch_assoc()) {
        echo $r['Field'] . " (" . $r['Type'] . ")\n";
    }
}

check("pemesanan");
check("V_PEMESANAN");
check("gedung");
check("catering");
