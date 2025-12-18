<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$user = $this->uri->segment(2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lihat Catering</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Material Icons & Materialize -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet">

</head>

<body class="min-h-screen bg-slate-200 text-slate-800">

<?php $this->load->view('components/navbar'); ?>
<?php $this->load->view('components/header'); ?>

<!-- ================= CONTENT ================= -->
<div class="max-w-6xl mx-auto px-4 py-8">

  <h5 class="text-xl font-semibold mb-6">Warsito Catering</h5>

  <!-- GRID CARD 2x2 -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <?php foreach($res as $row): ?>
      <div class="bg-white rounded-2xl shadow-md border border-black/5 p-6
                  hover:shadow-lg transition">

        <h3 class="text-lg font-semibold text-slate-800 mb-4">
          <?php echo $row['NAMA_PAKET']; ?>
        </h3>

        <div class="space-y-3 text-sm">

          <div class="flex">
            <div class="w-32 font-semibold text-slate-600">Menu Pembuka</div>
            <div class="flex-1 text-slate-700">
              <?php echo $row['MENU_PEMBUKA']; ?>
            </div>
          </div>

          <div class="flex">
            <div class="w-32 font-semibold text-slate-600">Menu Utama</div>
            <div class="flex-1 text-slate-700">
              <?php echo $row['MENU_UTAMA']; ?>
            </div>
          </div>

          <div class="flex">
            <div class="w-32 font-semibold text-slate-600">Menu Penutup</div>
            <div class="flex-1 text-slate-700">
              <?php echo $row['MENU_PENUTUP']; ?>
            </div>
          </div>

        </div>

        <!-- HARGA -->
        <div class="mt-6 flex items-center justify-between">
          <span class="text-sm text-slate-500">Harga / Porsi</span>
          <span class="text-lg font-semibold text-teal-700">
            Rp <?= number_format($row['HARGA']); ?>
          </span>
        </div>

      </div>
    <?php endforeach; ?>

  </div>
</div>

<!-- ================= JS ================= -->
<script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
<?php $this->load->view('components/footer'); ?>

</body>
</html>
