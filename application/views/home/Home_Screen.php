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

    <!-- Favicons-->
    <link rel="apple-touch-icon-precomposed" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">
    <title>Home</title>
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- (Opsional) CSS custom lama jika masih dipakai -->
    <!-- <link href="<?php echo base_url(); ?>assets/home-user/materialize/style.css" rel="stylesheet"> -->
</head>
<body class="min-h-screen text-slate-50
  bg-gradient-to-br
  from-[#034B4C] via-[#0A7F81] to-[#2CC7C0]
  selection:bg-white/20 selection:text-white">

    <!-- HEADER / NAVBAR -->
<header class="sticky top-0 z-30">
  <!-- BARIS ATAS -->
  <div class="bg-[#0A7F81] ">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-0">
      <div class="flex items-center justify-between py-1">

        <!-- Brand (kiri) -->
        <div class="flex items-center gap-3">
          <div class="h-11 w-11 rounded-xl bg-white/10 ring-1 ring-white/15 overflow-hidden flex items-center justify-center">
            <img src="<?php echo base_url('assets/Login/logo.jpg'); ?>" class="h-9 w-9 object-contain" alt="Logo">
          </div>
          <div class="leading-tight">
            <div class="text-[11px] font-semibold tracking-[0.22em] text-[#D7FFF8] uppercase">
              Smart Office
            </div>
            <div class="text-sm font-semibold text-white/90">
              E-Booking Room
            </div>
          </div>
        </div>
        <!-- Menu tengah (desktop) -->
        <nav class="hidden md:flex items-center gap-6 text-sm text-white/85">
          <a href="<?php echo site_url('login'); ?>"
             class="inline-flex items-center gap-2 hover:text-[#D7FFF8] transition">
            <!-- icon info -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="12" r="8.5"></circle>
              <path d="M12 8.2a2.1 2.1 0 0 1 2.1 2.1c0 1.4-1.6 1.7-2.1 2.2-.3.3-.4.7-.4 1.2"></path>
              <circle cx="12" cy="16.3" r="0.9"></circle>
            </svg>
            How to Order
          </a>

          <a href="<?php echo site_url('login'); ?>"
             class="inline-flex items-center gap-2 hover:text-[#D7FFF8] transition">
            <!-- icon chat -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M5 5.5h14a1.5 1.5 0 0 1 1.5 1.5v6a1.5 1.5 0 0 1-1.5 1.5H13l-3.5 3.5V14.5H5A1.5 1.5 0 0 1 3.5 13V7A1.5 1.5 0 0 1 5 5.5z"></path>
            </svg>
            Ulasan
          </a>

          <a href="<?php echo site_url('login'); ?>"
             class="inline-flex items-center gap-1 hover:text-[#D7FFF8] transition">
            <!-- icon pin -->
            <svg class="w-7 h-7" viewBox="0 0 20 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M12 20s-5-4.3-5-8.5a5 5 0 1 1 10 0C17 15.7 12 20 12 20z"></path>
              <circle cx="12" cy="11.5" r="2"></circle>
            </svg>
            Location
          </a>

          <a href="<?php echo site_url('login'); ?>" target="_blank"
             class="inline-flex items-center gap-2 hover:text-[#D7FFF8] transition">
            <!-- icon news -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="4.5" y="5" width="15" height="14" rx="1.5"></rect>
              <path d="M8 8h5"></path>
              <path d="M8 11h8"></path>
              <path d="M8 14h6"></path>
            </svg>
            News Feed
          </a>

          <a href="<?php echo site_url('login'); ?>"
             class="inline-flex items-center gap-2 hover:text-[#D7FFF8] transition">
            <!-- icon building -->
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="5" y="5" width="6" height="14" rx="1"></rect>
              <rect x="13" y="9" width="6" height="10" rx="1"></rect>
            </svg>
            Tiga Serangkai
          </a>
        </nav>

        <!-- Tombol Login (paling kanan) -->
        <div class="relative">
                    <button
                        id="profileToggle"
                        type="button"
                        class="inline-flex items-center gap-2 px-2 py-1.5 rounded-full bg-slate-800/80 hover:bg-slate-700 transition border border-slate-700"
                    >
                        <!-- Avatar user -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             class="w-6 h-6"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <circle cx="12" cy="9" r="3.2" />
                            <path d="M6.5 18.5a5.8 5.8 0 0 1 11 0" />
                        </svg>

                        <span class="hidden sm:inline text-xs font-medium">
                            <?php echo htmlspecialchars($session_id); ?>
                        </span>

                        <!-- Chevron down -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             class="w-4 h-4"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M7 10l5 5 5-5" />
                        </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <div
                        id="profileMenu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white text-slate-800 rounded-xl shadow-lg border border-slate-100 py-2 text-sm z-30"
                    >
                        <!-- Edit Data Diri (pencil) -->
                        <a href="<?php echo site_url('edit_data/'.$session_id.'/'); ?>"
                           class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 class="w-4 h-4 text-sky-600"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M5 19.5l3.3-.7 8.4-8.4a1.5 1.5 0 0 0-2.1-2.1L6.2 16.7 5 19.5z" />
                                <path d="M14.5 7l2 2" />
                            </svg>
                            <span>Edit Data Diri</span>
                        </a>

                        <!-- Edit Foto Profil (camera) -->
                        <a href="<?php echo site_url('edit_foto/'.$session_id.'/'); ?>"
                           class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 class="w-4 h-4 text-sky-600"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M5.5 7.5h3l1-2h5l1 2h3a1.5 1.5 0 0 1 1.5 1.5v8A1.5 1.5 0 0 1 18.5 18h-13A1.5 1.5 0 0 1 4 17V9a1.5 1.5 0 0 1 1.5-1.5z" />
                                <circle cx="12" cy="13" r="3" />
                            </svg>
                            <span>Edit Foto Profil</span>
                        </a>

                        <div class="border-t border-slate-100 mt-1 pt-1">
                            <!-- Logout (arrow out) -->
                            <a href="<?php echo site_url('home/home/logout'); ?>"
                               class="flex items-center gap-2 px-3 py-2 hover:bg-red-50 text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 24 24"
                                     class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M10 5H6.5A1.5 1.5 0 0 0 5 6.5v11A1.5 1.5 0 0 0 6.5 19H10" />
                                    <path d="M14 8l4 4-4 4" />
                                    <path d="M18 12H10" />
                                </svg>
                                <span>Logout</span>
                            </a>
                        </div>
      </div>
    </div>
  </div>

  <!-- BARIS BAWAH: KARTU MENU BESAR -->
  <!-- BARIS BAWAH (NO BLUR + RAPI) -->
<div class="bg-[#0A7F81]">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-1 md:pb-2">

    <!-- pusatkan group -->
    <div class="w-full flex justify-center">
      <!-- group yang ukurannya mengikuti isi (jadi nggak nyebar) -->
      <div class="inline-flex items-center gap-2 sm:gap-3">


      <!-- HOME -->
      <a href="<?php echo site_url('home/'.$session_id.'/'); ?>"
class="group w-[290px] h-[57px] rounded-2xl
       bg-slate-900/30 ring-1 ring-white/10
       transition-all duration-200 ease-out transform
       hover:-translate-y-0.5 hover:bg-slate-900/45 hover:ring-white/20 hover:shadow-lg hover:shadow-black/20
       active:scale-[0.99]
       flex flex-col items-center justify-center text-center">

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             class="w-6 h-6 mb-0.5 text-white/90 transition
                    group-hover:text-[#D7FFF8] group-hover:scale-105"
             fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M4.5 12L12 6l7.5 6"></path>
          <path d="M6.5 11v8h11v-8"></path>
        </svg>
        <span class="text-[10px] font-semibold tracking-[0.18em] text-white/85 transition group-hover:text-[#D7FFF8]">
          HOME
        </span>
      </a>

      <!-- JADWAL -->
      <a href="<?php echo site_url('home/jadwal'); ?>"
         class="group w-full max-w-[110px] mx-auto h-[55px] sm:h-[55px] rounded-xl
                bg-slate-900/30 ring-1 ring-white/10
                transition-all duration-200 ease-out transform
                hover:-translate-y-0.5 hover:bg-slate-900/45 hover:ring-white/20 hover:shadow-lg hover:shadow-black/20
                active:translate-y-0 active:scale-[0.99]
                flex flex-col items-center justify-center text-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             class="w-6 h-6 mb-0.5 text-white/90 transition
                    group-hover:text-[#D7FFF8] group-hover:scale-105"
             fill="none" stroke="currentColor" stroke-width="1.8">
          <rect x="5" y="6.5" width="14" height="13" rx="2"></rect>
          <path d="M9 4.5v3"></path>
          <path d="M15 4.5v3"></path>
          <path d="M5 10.5h14"></path>
        </svg>
        <span class="text-[10px] font-semibold tracking-[0.18em] text-white/85 transition group-hover:text-[#D7FFF8]">
          JADWAL
        </span>
      </a>

      <!-- PEMESANAN -->
      <a href="<?php echo site_url('home/pemesanan'); ?>"
         class="group relative w-full max-w-[110px] mx-auto h-[55px] sm:h-[55px] rounded-xl
                bg-slate-900/30 ring-1 ring-white/10
                transition-all duration-200 ease-out transform
                hover:-translate-y-0.5 hover:bg-slate-900/45 hover:ring-white/20 hover:shadow-lg hover:shadow-black/20
                active:translate-y-0 active:scale-[0.99]
                flex flex-col items-center justify-center text-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             class="w-6 h-6 mb-0.5 text-white/90 transition
                    group-hover:text-[#D7FFF8] group-hover:scale-105"
             fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M8 5h8l3 3v13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"></path>
          <path d="M10 12h4"></path>
          <path d="M10 15h3"></path>
        </svg>
        <span class="text-[10px] font-semibold tracking-[0.18em] text-white/85 transition group-hover:text-[#D7FFF8]">
          PEMESANAN
        </span>

        <?php if (!empty($flag) && $flag > 0): ?>
          <span class="absolute top-1.5 right-2.5 bg-red-500 text-[9px] leading-none text-white px-1.5 py-0.5 rounded-full">
            <?php echo $flag; ?>
          </span>
        <?php endif; ?>
      </a>

      <!-- CATERING -->
      <a href="<?php echo site_url('home/view-catering'); ?>" target="_blank"
         class="group w-full max-w-[110px] mx-auto h-[55px] sm:h-[55px] rounded-xl
                bg-slate-900/30 ring-1 ring-white/10
                transition-all duration-200 ease-out transform
                hover:-translate-y-0.5 hover:bg-slate-900/45 hover:ring-white/20 hover:shadow-lg hover:shadow-black/20
                active:translate-y-0 active:scale-[0.99]
                flex flex-col items-center justify-center text-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             class="w-6 h-6 mb-0.5 text-white/90 transition
                    group-hover:text-[#D7FFF8] group-hover:scale-105"
             fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M5 19h14"></path>
          <path d="M7 15h10a5 5 0 0 0-10 0z"></path>
          <path d="M12 8v-1"></path>
          <circle cx="12" cy="6.5" r="0.8"></circle>
        </svg>
        <span class="text-[10px] font-semibold tracking-[0.18em] text-white/85 transition group-hover:text-[#D7FFF8]">
          CATERING
        </span>
      </a>

      <!-- TRANSAKSI -->
      <a href="<?php echo site_url('home/pembayaran'); ?>"
         class="group w-full max-w-[110px] mx-auto h-[55px] sm:h-[55px] rounded-xl
                bg-slate-900/30 ring-1 ring-white/10
                transition-all duration-200 ease-out transform
                hover:-translate-y-0.5 hover:bg-slate-900/45 hover:ring-white/20 hover:shadow-lg hover:shadow-black/20
                active:translate-y-0 active:scale-[0.99]
                flex flex-col items-center justify-center text-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             class="w-6 h-6 mb-0.5 text-white/90 transition
                    group-hover:text-[#D7FFF8] group-hover:scale-105"
             fill="none" stroke="currentColor" stroke-width="1.8">
          <rect x="4.5" y="8" width="15" height="10" rx="2"></rect>
          <path d="M4.5 11h15"></path>
          <path d="M8 15.5h3"></path>
        </svg>
        <span class="text-[10px] font-semibold tracking-[0.18em] text-white/85 transition group-hover:text-[#D7FFF8]">
          TRANSAKSI
        </span>
      </a>
    </div>
  </div>
</div>
</header>

    <!-- MAIN CONTENT -->
    <main class="py-6 sm:py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Judul section -->
            <section class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold text-stone-100">
                        Daftar Gedung
                    </h2>
                    <p class="mt-1 text-xs sm:text-sm text-stone-100">
                        Silakan pilih gedung yang ingin dipesan.
                    </p>
                </div>
            </section>

            <!-- GRID GEDUNG (2 kolom di layar sedang ke atas) -->
            <section>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <?php foreach ($res as $row):
                        $path = $row['PATH'];
                        $img_name = $row['IMG_NAME'];
                    ?>
                        <article class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                            <!-- GAMBAR GEDUNG -->
                            <div class="relative">
                                <img
                                    src="<?php echo $path . $img_name; ?>"
                                    alt="<?php echo htmlspecialchars($row['NAMA_GEDUNG']); ?>"
                                    class="h-48 sm:h-56 w-full object-cover"
                                >
                                <!-- Badge kapasitas -->
                                <div class="absolute bottom-2 right-2 inline-flex items-center px-2 py-1 rounded-full bg-black/60">
                                    <span class="text-[11px] font-medium text-slate-50">
                                        <?php echo $row['KAPASITAS']; ?> Orang
                                    </span>
                                </div>
                            </div>

                            <!-- ISI KARTU -->
                            <div class="flex-1 flex flex-col p-4 space-y-2">
                                <p class="hidden">
                                    ID Gedung: <?php echo $row['ID_GEDUNG']; ?>
                                </p>

                                <h3 class="text-base font-semibold text-slate-900">
                                    <?php echo $row['NAMA_GEDUNG']; ?>
                                </h3>

                                <p class="text-sm text-slate-500">
                                    Kapasitas hingga
                                    <span class="font-semibold text-slate-800">
                                        <?php echo $row['KAPASITAS']; ?> orang
                                    </span>
                                    &mdash; cocok untuk meeting, presentasi, atau acara internal.
                                </p>

                                <div class="pt-3 mt-auto flex justify-end">
                                    <a
                                        href="<?php echo site_url('home/details/'.$row['ID_GEDUNG']); ?>"
                                        class="inline-flex items-center text-sm font-medium text-sky-600 hover:text-sky-700"
                                    >
                                        DETAILS
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             class="w-4 h-4 ml-1"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.8"
                                             stroke-linecap="round"
                                             stroke-linejoin="round">
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

    <!-- (Opsional) FOOTER -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <p class="text-xs text-slate-400 text-center">
                &copy; <?= date('Y'); ?> Smart Office Tiga Serangkai &mdash; E-Booking Room.
            </p>
        </div>
    </footer>

    <!-- JS: Profile dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var profileToggle = document.getElementById('profileToggle');
            var profileMenu = document.getElementById('profileMenu');

            if (profileToggle && profileMenu) {
                profileToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function () {
                    profileMenu.classList.add('hidden');
                });

                profileMenu.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            }
        });
    </script>
</body>
</html>
