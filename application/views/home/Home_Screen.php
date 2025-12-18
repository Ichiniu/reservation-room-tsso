<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$user = $this->uri->segment(2);
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed" href="<?= base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="<?= base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?= base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Home</title>
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen text-black
  bg-slate-200">
  
  <!-- NAVBAR -->
  <?php $this->load->view('components/navbar'); ?>

  <!-- MAIN CONTENT -->
  <main class="py-8 sm:py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      <!-- JUDUL SECTION -->
      <section class="flex flex-col sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">
            Pilih Gedung
          </h2>
          <p class="mt-2 text-sm text-slate-700">
            Silakan pilih gedung yang ingin dipesan.
          </p>
        </div>
      </section>

      <!-- GRID GEDUNG (3 kolom di desktop) -->
      <section>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach (array_slice($res, 0, 3) as $row):
            $path = $row['PATH'];
            $img_name = $row['IMG_NAME'];
          ?>
            <article class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
              
              <!-- GAMBAR GEDUNG -->
              <div class="relative">
                <img src="<?= $path . $img_name; ?>"
                     alt="<?= htmlspecialchars($row['NAMA_GEDUNG']); ?>"
                     class="h-52 sm:h-56 w-full object-cover">
                <!-- Badge kapasitas -->
                <div class="absolute bottom-2 right-2 inline-flex items-center px-2 py-1 rounded-full bg-black/60">
                  <span class="text-[11px] font-medium text-slate-50">
                    <?= $row['KAPASITAS']; ?> Orang
                  </span>
                </div>
              </div>

              <!-- ISI KARTU -->
              <div class="flex-1 flex flex-col p-4 space-y-2">
                <h3 class="text-base font-semibold text-slate-900">
                  <?= $row['NAMA_GEDUNG']; ?>
                </h3>
                <p class="text-sm text-slate-500">
                  Kapasitas hingga
                  <span class="font-semibold text-slate-800">
                    <?= $row['KAPASITAS']; ?> orang
                  </span>
                  &mdash; cocok untuk meeting, presentasi, atau acara internal.
                </p>
                <div class="pt-3 mt-auto flex justify-end">
                  <a href="<?= site_url('home/details/'.$row['ID_GEDUNG']); ?>"
                     class="inline-flex items-center text-sm font-medium text-sky-600 hover:text-sky-700">
                    DETAILS
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="1.8">
                      <path d="M8 5l7 7-7 7" />
                    </svg>
                  </a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <?php if (empty($res)): ?>
          <div class="mt-8 text-center text-sm text-slate-500">
            Belum ada data gedung yang tersedia.
          </div>
        <?php endif; ?>
      </section>
    </div>
  </main>

  <!-- FOOTER -->
  <?php $this->load->view('components/Footer'); ?>

</body>
</html>
