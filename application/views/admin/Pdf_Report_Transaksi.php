<?php
$no    = 1;
$total = 0;

// FIX: buat tanggal sekarang (aman PHP 5.x)
$date = date_create(); // now

// amankan data report
$report_rows = (isset($report) && is_array($report)) ? $report : array();

// helper format tanggal (aman kalau kosong)
function fmtTanggal($tgl)
{
    return format_tanggal_indo($tgl);
}

// helper format uang
function fmtRupiah($angka)
{
    $angka = (float)$angka;
    return "Rp." . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Transaksi</title>
    <style>
        body {
            font-family: courier, monospace;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        th {
            text-align: left;
        }

        small {
            font-size: 10px;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="center">
        <b>
            <h3 style="margin:0;">Laporan Rekapitulasi Transaksi</h3>
        </b>
    </div>
    <br>

    <table>
        <tr>
            <th style="width:40px;">No</th>
            <th>Kode Pembayaran</th>
            <th>Kode Pemesanan</th>
            <th>Atas Nama</th>
            <th>Bank</th>
            <th>Tanggal Transfer</th>
            <th>Jumlah Transfer</th>
        </tr>

        <?php if (count($report_rows) > 0): ?>
            <?php foreach ($report_rows as $row): ?>

                <?php
                // --- KODE PEMBAYARAN ---
                $kodePembayaran = ($row['KODE_PEMBAYARAN'] ?? 'PB0000') .
                    ($row['ID_PEMBAYARAN'] ?? '');

                // --- KODE PEMESANAN (fallback) ---
                $idPemesanan = $row['ID_PEMESANAN_RAW'] ?? $row['ID_PEMESANAN'] ?? '';

                $kodePemesanan = ($row['KODE_PEMESANAN'] ?? '') . $idPemesanan;

                // --- BANK ---
                $bank = (!empty($row['BANK_PENGIRIM'])) ? $row['BANK_PENGIRIM'] : '-';

                // --- TANGGAL ---
                $tglTransfer = $row['TANGGAL_TRANSFER'] ?? '';
                $tglCetak    = fmtTanggal($tglTransfer);

                // --- NOMINAL ---
                $nominal = (float)($row['NOMINAL_TRANSFER'] ?? 0);
                $total  += $nominal;

                // --- ATAS NAMA (INTERNAL 2 baris) ---
                $perusahaan = strtoupper(trim((string)($row['perusahaan'] ?? '')));
                $isInternal = ($perusahaan == 'INTERNAL');

                if ($isInternal) {
                    $nama = (!empty($row['NAMA_LENGKAP'])) ? $row['NAMA_LENGKAP'] : '-';
                    $pt   = (!empty($row['nama_perusahaan'])) ? $row['nama_perusahaan'] : 'PT Tiga Serangkai Pustaka Mandiri';
                    $dept = (!empty($row['departemen'])) ? $row['departemen'] : '-';

                    $atasNamaHtml =
                        htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') .
                        "<br><small>" .
                        htmlspecialchars($pt, ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($dept, ENT_QUOTES, 'UTF-8') .
                        "</small>";
                } else {
                    // non internal: fallback ATAS_NAMA_PENGIRIM / ATAS_NAMA
                    $atasNama = $row['ATAS_NAMA_PENGIRIM'] ?? $row['ATAS_NAMA'] ?? '-';

                    $atasNamaHtml = htmlspecialchars($atasNama, ENT_QUOTES, 'UTF-8');
                }
                ?>

                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($kodePembayaran, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($kodePemesanan, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $atasNamaHtml; ?></td>
                    <td><?php echo htmlspecialchars($bank, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $tglCetak; ?></td>
                    <td><?php echo fmtRupiah($nominal); ?></td>
                </tr>

            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="center">Tidak ada data transaksi pada periode ini.</td>
            </tr>
        <?php endif; ?>

        <tr>
            <td colspan="6"><b>Total Transfer:</b></td>
            <td><b><?php echo fmtRupiah($total); ?></b></td>
        </tr>
    </table>

    <br>
    <b>
        Periode
        <?php echo format_tanggal_indo($start_date); ?>
        -
        <?php echo format_tanggal_indo($end_date); ?>
    </b>
    <br>

    <b>Dicetak pada: </b>
    <b><?php echo format_tanggal_indo(date('Y-m-d')); ?></b>
    <br>

    <b>Dicetak untuk: Administrator</b>
</body>

</html>