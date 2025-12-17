<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detail Gedung</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen text-white relative overflow-x-hidden
  bg-gradient-to-br from-[#0A7F81] via-[#2CC7C0] to-[#D7FFF8]
  selection:bg-black/10 selection:text-black">


  <!-- subtle glossy highlights -->
  <div class="pointer-events-none absolute -top-40 -left-40 h-[520px] w-[520px] rounded-full bg-white/20 blur-3xl"></div>
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-60"></div>
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/25 via-black/0 to-black/10"></div>


  <!-- HEADER (simple, no blur) -->
  <header class="sticky top-0 z-30 bg-[#0A7F81] border-b border-white/10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl bg-white/10 ring-1 ring-white/15 overflow-hidden flex items-center justify-center">
          <img src="<?php echo base_url('assets/Login/logo.jpg'); ?>" class="h-9 w-9 object-contain" alt="Logo">
        </div>
        <div class="leading-tight">
          <div class="text-[11px] font-semibold tracking-[0.22em] text-[#D7FFF8] uppercase">Smart Office</div>
          <div class="text-sm font-semibold text-white/90">Detail Gedung</div>
        </div>
      </div>
      <nav class="hidden md:flex items-center gap-2 text-sm text-white/85">
          <a href="<?php echo site_url('home/'.$session_id.'/'); ?>"
             class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl
                    hover:bg-white/10 hover:ring-1 hover:ring-white/15 hover:text-[#D7FFF8] transition">
            <svg class="w-5 h-5 opacity-90 group-hover:opacity-100 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M4.5 12L12 6l7.5 6"></path><path d="M6.5 11v8h11v-8"></path>
            </svg>
            Home
          </a>

          <a href="<?php echo site_url('home/jadwal'); ?>"
             class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl
                    hover:bg-white/10 hover:ring-1 hover:ring-white/15 hover:text-[#D7FFF8] transition">
            <svg class="w-5 h-5 opacity-90 group-hover:opacity-100 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="5" y="6.5" width="14" height="13" rx="2"></rect><path d="M9 4.5v3"></path><path d="M15 4.5v3"></path><path d="M5 10.5h14"></path>
            </svg>
            Jadwal
          </a>

          <a href="<?php echo site_url('home/pemesanan'); ?>"
             class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl
                    hover:bg-white/10 hover:ring-1 hover:ring-white/15 hover:text-[#D7FFF8] transition">
            <svg class="w-5 h-5 opacity-90 group-hover:opacity-100 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M8 5h8l3 3v13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"></path><path d="M10 12h4"></path><path d="M10 15h3"></path>
            </svg>
            Pemesanan
            <?php if (!empty($flag) && $flag > 0): ?>
              <span class="ml-1 rounded-full bg-red-500 text-[10px] leading-none text-white px-1.5 py-0.5"><?php echo $flag; ?></span>
            <?php endif; ?>
          </a>

          <a href="<?php echo site_url('home/view-catering'); ?>" target="_blank"
             class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl
                    hover:bg-white/10 hover:ring-1 hover:ring-white/15 hover:text-[#D7FFF8] transition">
            <svg class="w-5 h-5 opacity-90 group-hover:opacity-100 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M5 19h14"></path><path d="M7 15h10a5 5 0 0 0-10 0z"></path><path d="M12 8v-1"></path><circle cx="12" cy="6.5" r="0.8"></circle>
            </svg>
            Catering
          </a>

          <a href="<?php echo site_url('home/pembayaran'); ?>"
             class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl
                    hover:bg-white/10 hover:ring-1 hover:ring-white/15 hover:text-[#D7FFF8] transition">
            <svg class="w-5 h-5 opacity-90 group-hover:opacity-100 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="4.5" y="8" width="15" height="10" rx="2"></rect><path d="M4.5 11h15"></path><path d="M8 15.5h3"></path>
            </svg>
            Transaksi
          </a>
        </nav>
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
    </div>
  </header>

  <main class="relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-6">

      <?php foreach($result as $row): $harga_gedung = $row['HARGA_SEWA']; ?>

      <!-- HERO + CTA -->
      <section class="rounded-3xl border border-white/15 bg-white/10 shadow-2xl shadow-black/20 overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col lg:flex-row gap-6 lg:items-center lg:justify-between">
          <div class="min-w-0">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 ring-1 ring-white/15 px-3 py-1 text-xs text-white/85">
              <span class="h-2 w-2 rounded-full bg-[#D7FFF8]"></span>
              Informasi Gedung
            </div>

            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold text-[#D7FFF8] tracking-wide">
              <?php echo $row['NAMA_GEDUNG']; ?>
            </h1>

            <p class="mt-2 text-sm text-white/75 max-w-2xl text-justify hyphens-auto">
              Ruang ini menawarkan set studio yang rapi dan berkelas—perpaduan panel akustik bernuansa hangat, aksen kayu modern,
              serta dekorasi bunga yang memberi kesan premium di setiap frame. Dengan pencahayaan profesional (LED panel/soft light)
              dan area pengambilan gambar yang lapang, tempat ini siap untuk produksi konten seperti podcast, interview, video branding,
              hingga live streaming dengan tampilan visual yang clean dan elegan.
            </p>

            <div class="mt-5 flex flex-wrap gap-3">
              <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-900/25 ring-1 ring-white/10 px-4 py-3">
                <span class="text-xs text-white/70">Harga Sewa</span>
                <span class="text-sm font-semibold text-[#D7FFF8]">
                  <?php echo "Rp. " . number_format($harga_gedung); ?>
                </span>
              </div>

              <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-900/25 ring-1 ring-white/10 px-4 py-3">
                <span class="text-xs text-white/70">Kapasitas</span>
                <span class="text-sm font-semibold text-[#D7FFF8]">
                  <?php echo $row['KAPASITAS']. " Orang"; ?>
                </span>
              </div>
            </div>
          </div>

          <div class="flex gap-3">
            <a href="<?php echo site_url('home/order-gedung/'.$id_gedung.''); ?>"
               class="relative overflow-hidden inline-flex items-center justify-center rounded-2xl px-5 py-3 font-semibold
                      text-[#071A1A]
                      bg-gradient-to-br from-[#D7FFF8] via-[#2CC7C0] to-[#0A7F81]
                      shadow-lg shadow-black/25
                      hover:brightness-105 active:brightness-95 transition
                      focus:outline-none focus:ring-2 focus:ring-white/30">
              <span class="relative z-10">Ajukan Pesanan</span>
              <span class="pointer-events-none absolute -top-10 left-0 h-24 w-full rotate-[-10deg] bg-white/30 blur-xl"></span>
            </a>
          </div>
        </div>

        <!-- subtle divider -->
        <div class="h-px bg-gradient-to-r from-white/0 via-white/20 to-white/0"></div>

        <!-- DETAILS GRID -->
        <div class="p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div class="rounded-2xl bg-slate-900/25 ring-1 ring-white/10 p-5">
            <h2 class="text-sm font-semibold tracking-widest text-[#D7FFF8]">ALAMAT</h2>
            <p class="mt-2 text-sm text-white/80 leading-relaxed">
              <?php echo $row['ALAMAT']; ?>
            </p>
          </div>

          <div class="rounded-2xl bg-slate-900/25 ring-1 ring-white/10 p-5">
            <h2 class="text-sm font-semibold tracking-widest text-[#D7FFF8]">FASITLITAS</h2>
            <p class="mt-2 text-sm text-white/80 leading-relaxed">
              <?php echo $row['DESKRIPSI_GEDUNG']; ?>
            </p>
          </div>
        </div>
      </section>

      <?php endforeach; ?>

      <!-- GALLERY -->
      <section class="rounded-3xl border border-white/15 bg-white/10 shadow-2xl shadow-black/20 overflow-hidden">
        <div class="p-6 sm:p-8 flex items-center justify-between gap-4">
          <div>
            <h2 class="text-xl font-semibold text-[#D7FFF8]">Gallery Gedung</h2>
            <p class="mt-1 text-sm text-white/75">Geser foto untuk melihat lebih banyak tampilan ruangan/gedung.</p>
          </div>

          <div class="hidden sm:flex items-center gap-2">
            <button id="prevBtn"
              class="rounded-xl px-3 py-2 bg-slate-900/25 ring-1 ring-white/10 text-white/90 hover:bg-slate-900/35 transition">
              Prev
            </button>
            <button id="nextBtn"
              class="rounded-xl px-3 py-2 bg-slate-900/25 ring-1 ring-white/10 text-white/90 hover:bg-slate-900/35 transition">
              Next
            </button>
          </div>
        </div>

        <div class="px-6 sm:px-8 pb-8">
          <div class="relative rounded-2xl overflow-hidden ring-1 ring-white/10 bg-slate-900/20">
            <!-- slider -->
            <div id="sliderTrack" class="flex transition-transform duration-500 ease-out">
              <?php foreach($gallery as $images):
                $path = $images['PATH']; $img = $images['IMG_NAME']; ?>
                <div class="min-w-full">
                  <img
                    src="<?php echo $path . $img; ?>"
                    alt="Gallery"
                    class="h-[260px] sm:h-[360px] w-full object-cover"
                    loading="lazy"
                  />
                </div>
              <?php endforeach; ?>
            </div>

            <!-- overlay controls (mobile) -->
            <button id="prevBtnMobile"
              class="sm:hidden absolute left-3 top-1/2 -translate-y-1/2 rounded-full h-10 w-10
                     bg-black/35 ring-1 ring-white/15 text-white hover:bg-black/45 transition">
              ‹
            </button>
            <button id="nextBtnMobile"
              class="sm:hidden absolute right-3 top-1/2 -translate-y-1/2 rounded-full h-10 w-10
                     bg-black/35 ring-1 ring-white/15 text-white hover:bg-black/45 transition">
              ›
            </button>
          </div>

          <?php if (empty($gallery)): ?>
            <p class="mt-4 text-sm text-white/70">Belum ada foto untuk gedung ini.</p>
          <?php endif; ?>
        </div>
      </section>

      <footer class="pt-2 pb-6">
        <p class="text-center text-xs text-white/55">
          © <?= date('Y'); ?> Smart Office Tiga Serangkai
        </p>
      </footer>

    </div>
  </main>

  <script>
    // simple slider (no dependencies)
    (function () {
      const track = document.getElementById('sliderTrack');
      if (!track) return;

      const slides = track.children.length;
      if (slides <= 1) return;

      let index = 0;

      function go(to) {
        index = (to + slides) % slides;
        track.style.transform = `translateX(${-index * 100}%)`;
      }

      const prev = document.getElementById('prevBtn');
      const next = document.getElementById('nextBtn');
      const prevM = document.getElementById('prevBtnMobile');
      const nextM = document.getElementById('nextBtnMobile');

      prev && prev.addEventListener('click', () => go(index - 1));
      next && next.addEventListener('click', () => go(index + 1));
      prevM && prevM.addEventListener('click', () => go(index - 1));
      nextM && nextM.addEventListener('click', () => go(index + 1));

      // swipe support (mobile)
      let startX = null;
      track.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, {passive:true});
      track.addEventListener('touchend', (e) => {
        if (startX === null) return;
        const endX = e.changedTouches[0].clientX;
        const dx = endX - startX;
        if (Math.abs(dx) > 40) go(index + (dx < 0 ? 1 : -1));
        startX = null;
      });
    })();
  </script>
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
