<?php
// Quick test apakah COALESCE bekerja
$conn = mysqli_connect('localhost', 'root', '', 'bookingsmarts');

echo "=== TEST REVENUE FILTER (dengan COALESCE) ===\n\n";

$tests = [
    ['bulan' => 1, 'tahun' => 2026],
    ['bulan' => 2, 'tahun' => 2026],
    ['bulan' => 3, 'tahun' => 2026],
];

foreach ($tests as $test) {
    $q = mysqli_query($conn, "
        SELECT SUM(NOMINAL_TRANSFER) as total 
        FROM pembayaran 
        WHERE STATUS_VERIF = 'CONFIRMED'
        AND MONTH(COALESCE(TANGGAL_TRANSFER, TANGGAL_PEMESANAN)) = {$test['bulan']}
        AND YEAR(COALESCE(TANGGAL_TRANSFER, TANGGAL_PEMESANAN)) = {$test['tahun']}
    ");
    $r = mysqli_fetch_assoc($q);
    $bulanName = ['', 'Januari', 'Februari', 'Maret'][$test['bulan']];
    echo "$bulanName {$test['tahun']}: Rp " . number_format($r['total'] ?: 0, 0, ',', '.') . "\n";
}

mysqli_close($conn);
