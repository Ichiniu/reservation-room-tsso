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
  <title>Pesan Gedung</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen text-white relative overflow-x-hidden
  bg-gradient-to-br from-[#0A7F81] via-[#2CC7C0] to-[#D7FFF8]
  selection:bg-black/10 selection:text-black">

  <!-- glossy highlights -->
  <div class="pointer-events-none absolute -top-40 -left-40 h-[520px] w-[520px] rounded-full bg-white/25 blur-3xl"></div>
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-white/0 via-white/12 to-white/0 opacity-70"></div>
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/25 via-black/0 to-black/10"></div>

  <!-- HEADER: welcome user style (NO BLUR) -->
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

  <!-- MAIN -->
  <main class="relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
      <section
  x-data="{
    catering: 'tidak',
    tipeJam: 'CUSTOM',
    RULES: {
      HALF_DAY_PAGI:  { start: '08:00', end: '12:00', label: 'HALF DAY (08:00 - 12:00)' },
      HALF_DAY_SIANG: { start: '13:00', end: '16:00', label: 'HALF DAY (13:00 - 16:00)' },
      FULL_DAY:       { start: '08:00', end: '17:00', label: 'FULL DAY (08:00 - 17:00)' }
    },
    get cateringEnabled(){ return this.catering === 'ya' },
    get isCustom(){ return this.tipeJam === 'CUSTOM' },
    jamMulai: '',
    jamSelesai: '',
    init(){
      this.jamMulai = '';
      this.jamSelesai = '';
    },
    applyJam(){
      if(this.isCustom) return;
      this.jamMulai = this.RULES[this.tipeJam].start;
      this.jamSelesai = this.RULES[this.tipeJam].end;
    }
  }"
  class="rounded-3xl border border-white/15 bg-white/10 shadow-2xl shadow-black/20 overflow-hidden"
>

        <div class="p-6 sm:p-8">
          <div class="flex items-end justify-between gap-4">
            <div>
              <h1 class="text-2xl sm:text-3xl font-semibold text-[#034B4C] drop-shadow-sm">
                Isi Data Pesanan
              </h1>
              <p class="mt-2 text-sm text-black/70 max-w-2xl">
                Pastikan semua field benar terisi. Aksi ini tidak bisa dibatalkan.
              </p>
            </div>
          </div>

          <form action="<?php echo site_url('home/order-gedung/validate/'.$id_gedung.'')?>" method="post" class="mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

              <!-- Nama User -->
              <div class="rounded-2xl bg-white/40 ring-1 ring-black/5 p-5">
                <label class="block text-xs font-semibold tracking-widest text-black/60">NAMA USER</label>
                <div class="mt-2 text-sm font-semibold text-[#034B4C]"><?php echo $session_id; ?></div>
              </div>

              <!-- Nama Gedung (hindari variabel $res ketimpa) -->
              <div class="rounded-2xl bg-white/40 ring-1 ring-black/5 p-5">
                <label class="block text-xs font-semibold tracking-widest text-black/60">NAMA GEDUNG</label>
                <div class="mt-2 text-sm font-semibold text-[#034B4C]">
                  <?php foreach($hasil as $gedung): ?>
                    <?php echo $gedung['NAMA_GEDUNG']; ?>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Tanggal -->
              <div class="rounded-2xl bg-white/40 ring-1 ring-black/5 p-5">
                <label class="block text-xs font-semibold tracking-widest text-black/60">TANGGAL PEMESANAN</label>
                <input type="date" name="tgl_pesan" id="tgl_pesan" required
                  class="mt-2 w-full rounded-xl bg-white/70 border border-black/10 px-4 py-3
                         text-slate-900 placeholder:text-slate-400
                         focus:outline-none focus:ring-2 focus:ring-[#0A7F81]/30 focus:border-[#0A7F81]/30" />
                <p class="mt-2 text-xs text-black/60">*Pemesanan minimal 10 hari dari tanggal hari ini*</p>
              </div>

              <!-- Pilihan Jam -->
              <div class="rounded-2xl bg-white/40 ring-1 ring-black/5 p-5">
                <label class="block text-xs font-semibold tracking-widest text-black/60">PILIHAN JAM</label>
                <select name="tipe_jam" id="tipe_jam" required
                   x-model="tipeJam" @change="applyJam()">
                  <option value="CUSTOM">HH:MM - HH:MM (Input sendiri)</option>
                  <option value="HALF_DAY_PAGI">HALF DAY (08-12)</option>
                  <option value="HALF_DAY_SIANG">HALF DAY (13-16)</option>
                  <option value="FULL_DAY">FULL DAY</option>
                </select>

                <!-- info paket -->
                <div x-show="!isCustom" class="mt-2 text-xs font-semibold text-[#034B4C]">
                  <span x-text="RULES[tipeJam]?.label"></span>
                </div>

                <!-- jam custom -->
                <div x-show="isCustom" x-transition class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-semibold text-black/60">Jam Mulai</label>
                    <input type="time" name="jam_pesan" id="jam_pesan"
                           x-model="jamMulai" :required="isCustom"
                      class="mt-2 w-full rounded-xl bg-white/70 border border-black/10 px-4 py-3
                             text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0A7F81]/30 focus:border-[#0A7F81]/30"/>
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-black/60">Jam Selesai</label>
                    <input type="time" name="jam_selesai" id="jam_selesai"
                           x-model="jamSelesai" :required="isCustom"
                      class="mt-2 w-full rounded-xl bg-white/70 border border-black/10 px-4 py-3
                             text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0A7F81]/30 focus:border-[#0A7F81]/30"/>
                  </div>
                </div>

                <!-- jam preset tetap terkirim -->
                <template x-if="!isCustom">
                  <div class="hidden">
                    <input type="time" name="jam_pesan" x-model="jamMulai">
                    <input type="time" name="jam_selesai" x-model="jamSelesai">
                  </div>
                </template>
              </div>

              <!-- Email -->
              <div class="rounded-2xl bg-white/40 ring-1 ring-black/5 p-5 lg:col-span-2">
                <label class="block text-xs font-semibold tracking-widest text-black/60">EMAIL</label>
                <input type="email" name="email" value="<?php echo $email->EMAIL;?>" required
                  class="mt-2 w-full rounded-xl bg-white/70 border border-black/10 px-4 py-3
                         text-slate-900 placeholder:text-slate-400
                         focus:outline-none focus:ring-2 focus:ring-[#0A7F81]/30 focus:border-[#0A7F81]/30" />
              </div>

              <!-- Catering -->
              <div class="rounded-2xl bg-white/40 ring-1 ring-black/5 p-5">
                <label class="block text-xs font-semibold tracking-widest text-black/60">CATERING</label>

                <div class="mt-3 flex items-center gap-3">
                  <label class="inline-flex items-center gap-2 rounded-full px-3 py-2 bg-white/70 ring-1 ring-black/10 cursor-pointer hover:bg-white transition">
                    <input type="radio" name="radios" value="ya" class="hidden" x-model="catering">
                    <span class="h-4 w-4 rounded-full ring-1 ring-black/20 flex items-center justify-center">
                      <span class="h-2.5 w-2.5 rounded-full bg-[#0A7F81]" x-show="catering==='ya'"></span>
                    </span>
                    <span class="text-sm text-slate-900">Ya</span>
                  </label>

                  <label class="inline-flex items-center gap-2 rounded-full px-3 py-2 bg-white/70 ring-1 ring-black/10 cursor-pointer hover:bg-white transition">
                    <input type="radio" name="radios" value="tidak" class="hidden" x-model="catering">
                    <span class="h-4 w-4 rounded-full ring-1 ring-black/20 flex items-center justify-center">
                      <span class="h-2.5 w-2.5 rounded-full bg-[#0A7F81]" x-show="catering==='tidak'"></span>
                    </span>
                    <span class="text-sm text-slate-900">Tidak</span>
                  </label>
                </div>

                <p class="mt-3 text-xs text-black/60">*Cek harga catering per porsi pada menu Catering*</p>
              </div>

              <!-- Paket Catering + Porsi -->
              <div class="rounded-2xl bg-white/40 ring-1 ring-black/5 p-5">
                <label class="block text-xs font-semibold tracking-widest text-black/60">PAKET CATERING</label>
                <select id="selected_catering" name="catering"
                        :disabled="!cateringEnabled"
                  class="mt-2 w-full rounded-xl bg-white/70 border border-black/10 px-4 py-3
                         text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0A7F81]/30 focus:border-[#0A7F81]/30
                         disabled:opacity-50 disabled:cursor-not-allowed">
                  <option value="" disabled selected>Pilih Paket</option>
                  <?php foreach($res as $catering): ?>
                    <option value="<?php echo $catering['ID_CATERING']; ?>">
                      <?php echo $catering['NAMA_PAKET']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <label class="block mt-4 text-xs font-semibold tracking-widest text-black/60">JUMLAH PORSI</label>
                <input type="number" min="1" name="jumlah-porsi" id="jumlah-porsi"
                       :disabled="!cateringEnabled"
                       placeholder="Masukkan jumlah porsi"
                  class="mt-2 w-full rounded-xl bg-white/70 border border-black/10 px-4 py-3
                         text-slate-900 placeholder:text-slate-400
                         focus:outline-none focus:ring-2 focus:ring-[#0A7F81]/30 focus:border-[#0A7F81]/30
                         disabled:opacity-50 disabled:cursor-not-allowed" />
              </div>

            </div>

            <!-- Warning + Submit -->
            <div class="mt-6 rounded-2xl bg-white/35 ring-1 ring-black/5 p-5">
              <p class="text-sm text-black/70">
                *Pastikan semua field benar terisi, aksi ini tidak bisa dibatalkan.*
              </p>

              <div class="mt-5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-end">
                <button type="submit" name="submit" id="submit"
                  class="relative overflow-hidden inline-flex items-center justify-center rounded-xl px-6 py-3 font-semibold
                         text-white
                         bg-gradient-to-br from-[#034B4C] via-[#0A7F81] to-[#2CC7C0]
                         shadow-lg shadow-black/15
                         hover:brightness-105 active:brightness-95 transition
                         focus:outline-none focus:ring-2 focus:ring-white/30">
                  <span class="relative z-10">Lanjutkan</span>
                  <span class="pointer-events-none absolute -top-10 left-0 h-24 w-full rotate-[-10deg] bg-white/30 blur-xl"></span>
                </button>
              </div>
            </div>

          </form>
        </div>
      </section>

      <p class="mt-6 text-center text-xs text-white/55">© <?= date('Y') ?> Smart Office Tiga Serangkai</p>
    </div>
  </main>
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
