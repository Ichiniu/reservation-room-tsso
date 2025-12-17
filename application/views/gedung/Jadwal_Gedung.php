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
        <title>Jadwal Gedung</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body>
        <header class="sticky top-0 z-30 bg-[#0A7F81] border-b border-white/10">
    <!-- baris atas -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
      <div class="flex items-center justify-between gap-4">

        <!-- Brand -->
        <div class="flex items-center gap-3">
          <div class="h-11 w-11 rounded-xl bg-white/10 ring-1 ring-white/15 overflow-hidden flex items-center justify-center">
            <img src="<?php echo base_url('assets/Login/logo.jpg'); ?>" class="h-9 w-9 object-contain" alt="Logo">
          </div>
          <div class="leading-tight">
            <div class="text-[11px] font-semibold tracking-[0.22em] text-[#D7FFF8] uppercase">Smart Office</div>
            <div class="text-sm font-semibold text-white/90">E-Booking Room</div>
          </div>
        </div>

        <!-- menu tengah (desktop) -->
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

        <!-- pill akun (ikon + username) -->
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
  </header>
              <div class="relative">
  <!-- Background glossy lebih cerah untuk area konten -->
  <div class="pointer-events-none absolute inset-0 -z-10
              bg-gradient-to-br from-[#0A7F81] via-[#2CC7C0] to-[#D7FFF8]"></div>
  <div class="pointer-events-none absolute -top-40 -left-40 -z-10 h-[520px] w-[520px] rounded-full bg-white/25 blur-3xl"></div>
  <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-t from-black/25 via-black/0 to-black/10"></div>

  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <?php 
      $akhir_bulan = strtotime('last day of this month', time());
      $second_date = date('d M Y', $akhir_bulan);
      $first_date  = date('d M Y', time());
    ?>

    <section x-data="{open:false}" class="rounded-3xl border border-white/15 bg-white/10 shadow-2xl shadow-black/20 overflow-hidden">
      <div class="p-6 sm:p-8">
        <!-- Header card -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-semibold text-[#D7FFF8]">
              Jadwal Penggunaan Gedung
            </h2>
            <p class="mt-2 text-sm text-white/75">
              Periode:
              <span class="font-semibold text-[#D7FFF8]">
                <?php echo $first_date; ?> - <?php echo $second_date; ?>
              </span>
            </p>
          </div>

          <div class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-[#D7FFF8] ring-1 ring-white/15">
              Total: <?php echo is_array($jadwal) ? count($jadwal) : 0; ?>
            </span>

            <button type="button" @click="open=!open"
              class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold
                     bg-white/10 ring-1 ring-white/15 text-[#D7FFF8]
                     hover:bg-white/15 transition">
              <span x-text="open ? 'Tutup Filter' : 'Filter Tanggal'"></span>
            </button>
          </div>
        </div>

        <!-- Filter (tetap route sama, tetap GET) -->
        <div x-show="open" x-transition
             class="mt-6 rounded-2xl bg-slate-900/20 ring-1 ring-white/15 p-4 sm:p-5">
          <form method="get" action="<?php echo site_url('home/jadwal-periode') ?>" onsubmit="return btnProsesAlert();">
            <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto_1fr_auto] gap-3 items-end">
              <div>
                <label class="block text-xs font-semibold tracking-widest text-white/70 mb-1">Mulai</label>
                <input type="date" name="start_date" id="start_date"
                  class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
                         focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" />
              </div>

              <div class="text-center text-xs font-semibold tracking-widest text-white/70 pb-3">
                Sampai
              </div>

              <div>
                <label class="block text-xs font-semibold tracking-widest text-white/70 mb-1">Selesai</label>
                <input type="date" name="end_date" id="end_date"
                  class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
                         focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" />
              </div>

              <div>
                <button type="submit" name="btnProses" id="btnProses"
                  class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold
                         text-[#071A1A]
                         bg-gradient-to-br from-[#D7FFF8] via-[#2CC7C0] to-[#0A7F81]
                         shadow-lg shadow-black/25 hover:brightness-105 active:brightness-95 transition">
                  Proses
                </button>
              </div>
            </div>

            <p class="mt-3 text-xs text-white/60">
              Pilih tanggal mulai & selesai, lalu klik <span class="font-semibold text-[#D7FFF8]">Proses</span>.
            </p>
          </form>
        </div>

        <!-- Table -->
        <div class="mt-6 rounded-2xl ring-1 ring-white/15 bg-slate-900/20 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full text-left">
              <thead class="bg-white/10">
                <tr class="text-xs tracking-widest text-white/80">
                  <th class="px-4 py-3 font-semibold">NO</th>
                  <th class="px-4 py-3 font-semibold">TANGGAL ACARA</th>
                  <th class="px-4 py-3 font-semibold">JAM</th>
                  <th class="px-4 py-3 font-semibold">NAMA GEDUNG</th>
                  <th class="px-4 py-3 font-semibold">DESKRIPSI ACARA</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-white/10">
                <?php if (!empty($jadwal)): ?>
                  <?php $no = 1; foreach($jadwal as $row): ?>
                    <?php $tanggal = date_create($row['TANGGAL_FINAL_PEMESANAN']); ?>
                    <tr class="text-sm text-white/85 hover:bg-white/5 transition">
                      <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/10 text-xs font-semibold text-[#D7FFF8]">
                          <?php echo $no++; ?>
                        </span>
                      </td>

                      <td class="px-4 py-3 whitespace-nowrap">
                        <?php echo date_format($tanggal, 'd M Y'); ?>
                      </td>

                      <td class="px-4 py-3 whitespace-nowrap">
  <?php
    $mulai   = isset($row['JAM_MULAI']) ? $row['JAM_MULAI'] : '';
    $selesai = isset($row['JAM_SELESAI']) ? $row['JAM_SELESAI'] : '';
    $tipe    = isset($row['TIPE_JAM']) ? $row['TIPE_JAM'] : 'CUSTOM';

    if ($tipe == 'HALF_DAY') {
      echo "HALF DAY ($mulai - $selesai)";
    } elseif ($tipe == 'FULL_DAY') {
      echo "FULL DAY ($mulai - $selesai)";
    } else {
      if (!empty($mulai) && !empty($selesai)) {
        echo $mulai . " - " . $selesai;
      } elseif (!empty($mulai)) {
        echo $mulai;
      } else {
        echo "-";
      }
    }
  ?>
</td>




                      <td class="px-4 py-3">
                        <div class="font-semibold text-[#D7FFF8]"><?php echo $row['NAMA_GEDUNG']; ?></div>
                      </td>

                      <td class="px-4 py-3">
                        <div class="text-white/80 leading-relaxed"><?php echo $row['DESKRIPSI_ACARA']; ?></div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="px-4 py-10 text-center">
                      <div class="text-sm text-white/70">Belum ada jadwal pada periode ini.</div>
                      <div class="mt-2 text-xs text-white/55">Coba gunakan filter tanggal untuk melihat periode lain.</div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <p class="mt-4 text-xs text-white/55">
          Di layar HP, tabel bisa digeser ke samping.
        </p>
      </div>
    </section>
  </div>
</div>
<script>
    function btnProsesAlert() {
  var startDate = document.getElementById("start_date");
  var endDate = document.getElementById("end_date");

  if (!startDate.value) { alert("Harap Isi Form Tanggal!"); return false; }
  if (!endDate.value) { alert("Harap Isi Form Tanggal!"); return false; }
  return true;
}
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
        
        <script type="text/javascript">
            var startDate = document.getElementById("start_date");
            var label = document.getElementById("labelSampai");
            var endDate = document.getElementById("end_date");
            var btnProses = document.getElementById("btnProses");
            var btnFilter = document.getElementById("btnFilter");
            function unhideElement() {
                if(startDate.hidden = true) {
                    startDate.hidden = false;
                    label.hidden = false;
                    endDate.hidden = false;
                    btnProses.hidden = false
                    btnFilter.disabled = true;

                } else {
                    hideElement();
                }
            }
            function hideElement() {
                startDate.hidden = true;
                label.hidden = true;
                endDate.hidden = true;
                btnProses.hidden = true;
            } 
            function btnProsesAlert() {
                if(startDate.value == "") {
                    alert("Harap Isi Form Tanggal!");
                    return false;
                } else if(endDate.value == "") {
                    alert("Harap Isi Form Tanggal!");
                    return false;
                }
            }
        </script>
         <script>
$(document).ready(function(){

    $(".button-collapse").sideNav({
        menuWidth: 260,
        edge: 'left',
        closeOnClick: false,
        draggable: true
    });

    // OPEN / CLOSE SIDEBAR + SHIFT CONTENT
    $(".button-collapse").on("click", function () {
        $("body").toggleClass("nav-open");
    });

    // CLOSE JIKA KLIK LUAR SIDEBAR
    $(document).mouseup(function(e){
        let sb = $(".side-nav");
        if (!sb.is(e.target) && sb.has(e.target).length === 0) {
            $("body").removeClass("nav-open");
        }
    });

});
</script>
    </body>
</html>