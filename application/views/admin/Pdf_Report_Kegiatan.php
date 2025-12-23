<?php
$no = 1;
$date = date_create(); // tanggal sekarang
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
</head>
<body>
  <center><b><h3>Laporan Aktivitas Kegiatan</h3></b></center>

  <font face="courier" size="13px">
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
      <tr>
        <th>No</th>
        <th>Gedung</th>
        <th>Tanggal Penyewaan</th>
        <th>Tanggal Approval</th>
        <th>Kegiatan</th>
        <th>Jam Kegiatan</th>
        <th>Nama Penyewa</th>
      </tr>

      <?php foreach($report as $row): ?>

        <?php
          $jamMulai = '';
          if (isset($row['JAM_MULAI']) && $row['JAM_MULAI'] != '') {
              $jamMulai = $row['JAM_MULAI'];
          } elseif (isset($row['JAM_PEMESANAN']) && $row['JAM_PEMESANAN'] != '') {
              $jamMulai = $row['JAM_PEMESANAN'];
          }

          $jamSelesai = '';
          if (isset($row['JAM_SELESAI']) && $row['JAM_SELESAI'] != '') {
              $jamSelesai = $row['JAM_SELESAI'];
          }

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
          <td><?php echo $row['DESKRIPSI_ACARA']; ?></td>
          <td><?php echo $jamText; ?></td>
          <td><?php echo $row['NAMA_LENGKAP']; ?></td>
        </tr>

      <?php endforeach; ?>

    </table>

    <br>
    <b>Periode</b>
    <?php echo date_format(date_create($start_date), 'd F Y'); ?>
    -
    <?php echo date_format(date_create($end_date), 'd F Y'); ?>
    <br>

    <b>Dicetak pada:</b> <?php echo date_format($date, "d M Y"); ?><br>
    <b>Dicetak untuk:</b> Administrator
  </font>

</body>
</html>
