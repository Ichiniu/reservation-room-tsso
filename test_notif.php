<?php
$conn = mysqli_connect('localhost', 'root', '', 'bookingsmarts');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$res = mysqli_query($conn, "SELECT COUNT(*) as c FROM notifications WHERE username='admin' AND read_at IS NULL");
$row = mysqli_fetch_assoc($res);
echo "Unread Admin: " . $row['c'] . "\n";

$res2 = mysqli_query($conn, "SELECT * FROM notifications WHERE username='admin' ORDER BY id DESC LIMIT 5");
while ($row2 = mysqli_fetch_assoc($res2)) {
    print_r($row2);
}
mysqli_close($conn);
