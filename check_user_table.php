<?php
$conn = mysqli_connect('localhost', 'root', '', 'bookingsmarts');
$res = mysqli_query($conn, "DESCRIBE user");
echo "User Table:\n";
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
mysqli_close($conn);
