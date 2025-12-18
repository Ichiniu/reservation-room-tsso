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

<body class="min-h-screen text-black
  bg-slate-200">


  <!-- subtle glossy highlights -->
  <!-- <div class="pointer-events-none absolute -top-40 -left-40 h-[520px] w-[520px] rounded-full bg-white/20 blur-3xl"></div>
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-60"></div>
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/25 via-black/0 to-black/10"></div> -->


  <!-- HEADER -->
   <?php $this->load->view('components/header'); ?>
  <?php $this->load->view('components/navbar'); ?>
   <!-- HEADER -->


  <main class="relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-6">

      <?php foreach($result as $row): $harga_gedung = $row['HARGA_SEWA']; ?>

      <!-- HERO + CTA -->
      <section class="rounded-3xl border border-white/15 bg-white shadow-2xl shadow-black/20 overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col lg:flex-row gap-6 lg:items-center lg:justify-between">
          <div class="min-w-0">
            <div class="inline-flex items-center gap-2 rounded-full bg-accent-primary ring-1 ring-btn-hover px-3 py-1 text-xs text-text-subheading">
              <span class="h-2 w-2 rounded-full bg-[#D7FFF8]"></span>
              Informasi Gedung
            </div>

            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold text-text-heading tracking-wide">
              <?php echo $row['NAMA_GEDUNG']; ?>
            </h1>

            <p class="mt-2 text-sm text-slate-900 max-w-2xl text-justify hyphens-auto">
              Ruang ini menawarkan set studio yang rapi dan berkelas—perpaduan panel akustik bernuansa hangat, aksen kayu modern,
              serta dekorasi bunga yang memberi kesan premium di setiap frame. Dengan pencahayaan profesional (LED panel/soft light)
              dan area pengambilan gambar yang lapang, tempat ini siap untuk produksi konten seperti podcast, interview, video branding,
              hingga live streaming dengan tampilan visual yang clean dan elegan.
            </p>

            <div class="mt-5 flex flex-wrap gap-3">
              <div class="inline-flex items-center gap-2 rounded-2xl bg-[#14B8A6] ring-1 ring-white/10 px-4 py-3">
                <span class="text-xs text-white">Harga Sewa</span>
                <span class="text-sm font-semibold text-white">
                  <?php echo "Rp. " . number_format($harga_gedung); ?>
                </span>
              </div>

              <div class="inline-flex items-center gap-2 rounded-2xl bg-[#14B8A6]  ring-1 ring-white/10 px-4 py-3">
                <span class="text-xs text-white">Kapasitas</span>
                <span class="text-sm font-semibold text-white">
                  <?php echo $row['KAPASITAS']. " Orang"; ?>
                </span>
              </div>
            </div>
          </div>

          <div class="flex gap-3">
            <a href="<?php echo site_url('home/order-gedung/'.$id_gedung.''); ?>"
               class="relative overflow-hidden inline-flex items-center justify-center rounded-2xl px-5 py-3 font-semibold
                      text-white
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
          <div class="rounded-2xl bg-[#14B8A6] ring-1 ring-white/10 p-5">
            <h2 class="text-sm font-semibold tracking-widest text-[#D7FFF8]">ALAMAT</h2>
            <p class="mt-2 text-sm text-white leading-relaxed">
              <?php echo $row['ALAMAT']; ?>
            </p>
          </div>

          <div class="rounded-2xl bg-[#14B8A6] ring-1 ring-white/10 p-5">
            <h2 class="text-sm font-semibold tracking-widest text-[#D7FFF8]">FASITLITAS</h2>
            <p class="mt-2 text-sm text-white leading-relaxed">
              <?php echo $row['DESKRIPSI_GEDUNG']; ?>
            </p>
          </div>
        </div>
      </section>

      <?php endforeach; ?>

      <!-- GALLERY -->
      <section class="rounded-3xl border border-white/15 bg-white shadow-2xl shadow-black/20 overflow-hidden">
        <div class="p-6 sm:p-8 flex items-center justify-between gap-4">
          <div>
            <h2 class="text-xl font-semibold text-text-subheading">Gallery Gedung</h2>
            <p class="mt-1 text-sm text-text-content">Geser foto untuk melihat lebih banyak tampilan ruangan/gedung.</p>
          </div>

          <div class="hidden sm:flex items-center gap-2">
            <button id="prevBtn"
              class="rounded-xl px-3 py-2 bg-btn-primary ring-1 ring-white/10 text-white/90 hover:bg-btn-hover transition">
              Prev
            </button>
            <button id="nextBtn"
              class="rounded-xl px-3 py-2 bg-btn-primary ring-1 ring-white/10 text-white/90 hover:bg-btn-hover transition">
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

      

    </div>
    <?php $this->load->view('components/Footer'); ?>
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
