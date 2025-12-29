<?php
$no = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran</title>

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
  <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet" type="text/css">
</head>

<body class="min-h-screen text-black bg-slate-200">
  <?php $this->load->view('components/navbar'); ?>
  <?php $this->load->view('components/header'); ?>

  <div class="max-w-7xl mx-auto px-4 py-8">
    <section class="rounded-3xl bg-white shadow-xl p-6">
      <div class="overflow-x-auto rounded-2xl ring-1 ring-black/5">
        <table class="min-w-full bordered">
          <thead>
            <tr>
              <th class="px-4 py-3 text-left">No</th>
              <th class="px-4 py-3 text-left">ID Pemesanan</th>
              <th class="px-4 py-3 text-left">ID Transaksi</th>
              <th class="px-4 py-3 text-left">Atas Nama</th>
              <th class="px-4 py-3 text-left">Tanggal Transfer</th>
              <th class="px-4 py-3 text-left">Jumlah Transfer</th>
              <th class="px-4 py-3 text-left">Total Tagihan</th>
              <th class="px-4 py-3 text-left">Terhutang</th>
              <th class="px-4 py-3 text-left">Status</th>
            </tr>
          </thead>

<tbody>
<?php if (!empty($res)) : ?>
  <?php foreach ($res as $row) : ?>
    <?php
      $kode   = isset($row['KODE_PEMESANAN']) ? $row['KODE_PEMESANAN'] : '';
      $idraw  = isset($row['ID_PEMESANAN_RAW']) ? $row['ID_PEMESANAN_RAW'] : '';
      $idbyr  = isset($row['ID_PEMBAYARAN']) ? $row['ID_PEMBAYARAN'] : '';
      $atas   = isset($row['ATAS_NAMA_PENGIRIM']) ? $row['ATAS_NAMA_PENGIRIM'] : '-';
      $tgl    = isset($row['TANGGAL_TRANSFER']) ? $row['TANGGAL_TRANSFER'] : '-';
      $nom    = isset($row['NOMINAL_TRANSFER']) ? (int)$row['NOMINAL_TRANSFER'] : 0;
      $total  = isset($row['TOTAL_TAGIHAN']) ? (int)$row['TOTAL_TAGIHAN'] : 0;
      $status = isset($row['STATUS_VERIF']) ? $row['STATUS_VERIF'] : '-';
      $hutang = max(0, $total - $nom);
    ?>
    <tr>
      <td class="px-4 py-3"><?php echo $no++; ?></td>
      <td class="px-4 py-3"><?php echo htmlspecialchars($kode.$idraw); ?></td>
      <td class="px-4 py-3"><?php echo 'BYR'.htmlspecialchars($idbyr); ?></td>
      <td class="px-4 py-3"><?php echo htmlspecialchars($atas); ?></td>
      <td class="px-4 py-3"><?php echo htmlspecialchars($tgl); ?></td>
      <td class="px-4 py-3"><?php echo 'Rp.'.number_format($nom); ?></td>
      <td class="px-4 py-3"><?php echo 'Rp.'.number_format($total); ?></td>
      <td class="px-4 py-3"><?php echo 'Rp.'.number_format($hutang); ?></td>
      <td class="px-4 py-3"><?php echo htmlspecialchars($status); ?></td>
    </tr>
  <?php endforeach; ?>
<?php else : ?>
  <tr><td colspan="9" class="px-4 py-3">Belum ada pembayaran yang dikonfirmasi.</td></tr>
<?php endif; ?>
</tbody>


        </table>
      </div>
    </section>
  </div>

  <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
  <script src="<?php echo base_url(); ?>assets/home/index.js"></script>
</body>
</html>
