<?php
$username = $this->session->userdata('username');
// $flag     = $flag ?? 0; // aman jika variabel belum diset
// ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<body class="bg-white text-slate-800">

<!-- ================= NAVBAR ================= -->
<header class="bg-white border-b border-black/5 sticky top-0 z-30">
  <div class="max-w-8xl mx-auto px-10">
    <div class="flex items-center justify-between h-16">

      <!-- ===== BRAND ===== -->
      <div class="flex items-center gap-5">
        <div class="h-9 w-9 rounded-lg bg-white border border-black/10 flex items-center justify-center">
          <img src="<?= base_url('assets/login/LogoTSNew.png'); ?>"
               class="h-10 w-10 object-contain"
               alt="Logo">
        </div>

        <div class="leading-tight">
          <div class="text-[10px] font-semibold tracking-[0.25em] uppercase text-slate-500">
            Smart Office
          </div>
          <div class="text-sm font-semibold text-slate-800">
            SIRERU
          </div>
        </div>
      </div>

      <!-- ===== MENU ===== -->
      <nav class="hidden md:flex items-center gap-5 mx-10 text-[11px] font-semibold tracking-widest text-slate-700">

        <a href="<?= site_url('home'); ?>"
           class="flex items-center gap-1 mx-5 hover:text-slate-900 transition">
          <i class="bi bi-house-door text-base"></i>
          <span>HOME</span>
        </a>

        <a href="<?= site_url('home/jadwal'); ?>"
           class="flex items-center gap-1 mx-5 hover:text-slate-900 transition">
          <i class="bi bi-calendar-week text-base"></i>
          <span>JADWAL</span>
        </a>

        <!-- PEMESANAN + NOTIFIKASI -->
        <a href="<?= site_url('home/pemesanan'); ?>"
           class="relative flex items-center gap-1 mx-5 hover:text-slate-900 transition">
          <i class="bi bi-journal-text text-base"></i>
          <span>PEMESANAN</span>

          <?php if (!empty($flag) && $flag > 0): ?>
            <span
              class="ml-1 rounded-full bg-red-500 text-[10px] leading-none
                     text-white px-1.5 py-0.5">
              <?= $flag; ?>
            </span>
          <?php endif; ?>
        </a>

        <a href="<?= site_url('home/view-catering'); ?>"
           class="flex items-center gap-1 mx-5 hover:text-slate-900 transition">
          <i class="bi bi-cup-hot text-base"></i>
          <span>CATERING</span>
        </a>

        <a href="<?= site_url('home/pembayaran'); ?>"
           class="flex items-center gap-1 mx-5 hover:text-slate-900 transition">
          <i class="bi bi-credit-card text-base"></i>
          <span>TRANSAKSI</span>
        </a>

      </nav>

      <!-- ===== PROFILE ===== -->
      <div class="relative">
        <button type="button"
          class="profile-toggle flex items-center gap-2 px-3 py-2 rounded-full
                 bg-white hover:bg-slate-100 border border-black/10 transition">

          <i class="bi bi-person-circle text-slate-700"></i>

          <span class="text-xs font-medium text-slate-700">
            <?= htmlspecialchars($username); ?>
          </span>

          <i class="bi bi-chevron-down text-xs text-slate-600"></i>
        </button>

        <!-- DROPDOWN -->
        <div
          class="profile-menu hidden absolute right-0 mt-2 w-48
                 bg-white rounded-xl shadow-md border border-black/10 text-sm">

          <a href="<?= site_url('edit_data/'.$username); ?>"
             class="flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
            <i class="bi bi-pencil-square"></i>
            Edit Data Diri
          </a>

          <a href="<?= site_url('edit_foto/'.$username); ?>"
             class="flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
            <i class="bi bi-camera"></i>
            Edit Foto Profil
          </a>

          <div class="border-t border-black/5">
            <a href="<?= site_url('home/home/logout'); ?>"
               class="flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
              <i class="bi bi-box-arrow-right"></i>
              Logout
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</header>

<!-- ================= DROPDOWN SCRIPT ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.querySelector('.profile-toggle');
  const menu = document.querySelector('.profile-menu');

  toggle.addEventListener('click', function (e) {
    e.stopPropagation();
    menu.classList.toggle('hidden');
  });

  document.addEventListener('click', function () {
    menu.classList.add('hidden');
  });
});
</script>

</body>
