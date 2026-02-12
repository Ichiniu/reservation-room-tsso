<?php
$conn = mysqli_connect('localhost', 'root', '', 'bookingsmarts');
$res = mysqli_query($conn, "SELECT * FROM user LIMIT 5");
echo "User Sample:\n";
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
mysqli_close($conn);
