<?php
$no = 1;
$date = date_create(); // tanggal sekarang
$nama_gedung_filter = isset($nama_gedung_filter) ? $nama_gedung_filter : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
    }

    h3 {
      text-align: center;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .filter-info {
      text-align: center;
      margin-bottom: 15px;
      font-size: 11px;
      color: #555;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    table,
    th,
    td {
      border: 1px solid #000;
    }

    th,
    td {
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f0f0f0;
      font-weight: bold;
    }

    .footer {
      margin-top: 15px;
    }
  </style>
</head>

<body>
  <h3>Laporan Aktivitas Kegiatan</h3>
  <?php if (!empty($nama_gedung_filter)): ?>
    <p class="filter-info"><strong>Gedung:</strong> <?php echo htmlspecialchars($nama_gedung_filter, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Gedung</th>
        <th>Tanggal Penyewaan</th>
        <th>Tanggal Approval</th>
        <th>Total Peserta</th>
        <th>Kegiatan</th>
        <th>Jam Kegiatan</th>
        <th>Nama Penyewa</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $totalSum = 0;
      foreach ($report as $row):
        $tp = !empty($row['TOTAL_PESERTA']) ? (int)$row['TOTAL_PESERTA'] : 0;
        $totalSum += $tp;
      ?>

        <?php
        $jamMulai = $row['JAM_MULAI'] ?? $row['JAM_PEMESANAN'] ?? '';
        $jamSelesai = $row['JAM_SELESAI'] ?? '';

        $jamText = '-';
        if ($jamMulai != '' && $jamSelesai != '') {
          $jamText = date('H:i', strtotime($jamMulai)) . ' - ' . date('H:i', strtotime($jamSelesai));
        } elseif ($jamMulai != '') {
          $jamText = date('H:i', strtotime($jamMulai));
        }
        ?>

        <tr>
          <td><?php echo $no++; ?></td>
          <td><?php echo $row['NAMA_GEDUNG']; ?></td>
          <td><?php echo $row['TANGGAL_FINAL_PEMESANAN']; ?></td>
          <td><?php echo $row['TANGGAL_APPROVAL']; ?></td>
          <td style="text-align: center;"><?php echo $tp; ?></td>
          <td><?php echo $row['DESKRIPSI_ACARA']; ?></td>
          <td><?php echo $jamText; ?></td>
          <td><?php echo $row['NAMA_LENGKAP']; ?></td>
        </tr>

      <?php endforeach; ?>
      <tr style="background-color: #f0f0f0; font-weight: bold;">
        <td colspan="4" style="text-align: right;">GRAND TOTAL PESERTA</td>
        <td style="text-align: center;"><?php echo $totalSum; ?></td>
        <td colspan="3"></td>
      </tr>
    </tbody>
  </table>

  <div class="footer">
    <strong>Periode</strong>
    <?php echo format_tanggal_indo($start_date); ?>
    -
    <?php echo format_tanggal_indo($end_date); ?>
    <br>

    <strong>Dicetak pada:</strong> <?php echo format_tanggal_indo(date('Y-m-d')); ?><br>
    <strong>Dicetak untuk:</strong> Administrator
  </div>

</body>

</html>