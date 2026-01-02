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

<body class="min-h-screen bg-slate-200 text-slate-900 selection:bg-slate-200 selection:text-slate-900">

    <!-- HEADER / NAVBAR DARI COMPONENT -->
    <?php $this->load->view('components/header'); ?>
    <?php $this->load->view('components/navbar'); ?>

    <main class="pt-6 pb-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <section x-data="{
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
                init(){ this.jamMulai=''; this.jamSelesai=''; },
                applyJam(){
                  if(this.isCustom) return;
                  this.jamMulai = this.RULES[this.tipeJam].start;
                  this.jamSelesai = this.RULES[this.tipeJam].end;
                },
                profileOpen:false
              }" class="rounded-2xl border border-slate-300 bg-white shadow-sm overflow-hidden ring-1 ring-slate-200">

                <!-- TOP BAR -->
                <div class="p-5 sm:p-6 border-b border-slate-300 bg-slate-50 flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-slate-900">Isi Data Pesanan</h1>
                        <p class="mt-1 text-sm text-slate-600">Pastikan semua field benar terisi.</p>
                    </div>

                    <!-- Dropdown kanan (icon expand) -->
                    <div class="relative">
                        <button type="button" @click="profileOpen=!profileOpen"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                            <span class="hidden sm:inline"><?php echo htmlspecialchars($session_id); ?></span>

                            <!-- icon expand -->
                            <span class="inline-flex items-center justify-center text-slate-600">
                                <svg x-show="!profileOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                                <svg x-show="profileOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 15l-6-6-6 6" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="profileOpen" @click.outside="profileOpen=false" x-transition
                            class="absolute right-0 mt-2 w-52 rounded-xl border border-slate-300 bg-white shadow-lg overflow-hidden text-sm z-20">
                            <a href="<?php echo site_url('edit_data/' . $session_id . '/'); ?>"
                                class="flex items-center gap-2 px-4 py-3 hover:bg-slate-50 text-slate-800">
                                <span class="inline-flex items-center justify-center">
                                    <i class="material-icons text-[18px] text-blue-700">edit</i>
                                </span>
                                Edit Data Diri
                            </a>
                            <div class="border-t border-slate-200"></div>
                            <a href="<?php echo site_url('home/home/logout'); ?>"
                                class="flex items-center gap-2 px-4 py-3 hover:bg-red-50 text-red-600">
                                <span class="inline-flex items-center justify-center">
                                    <i class="material-icons text-[18px]">logout</i>
                                </span>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="p-3 mb-4 rounded bg-red-100 text-red-700">
                            <?= $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo site_url('home/order-gedung/validate/' . $id_gedung . '') ?>" method="post"
                        class="mt-2">
                        <?php
                        // token stabil per browser (akan tersimpan di cookie)
                        if (empty($_COOKIE['booking_client_id'])) {
                            $client_id = sha1(uniqid('client', true) . microtime(true));
                            setcookie('booking_client_id', $client_id, time() + (86400 * 365), "/"); // 1 tahun
                            $_COOKIE['booking_client_id'] = $client_id;
                        } else {
                            $client_id = $_COOKIE['booking_client_id'];
                        }

                        // request id spesifik untuk percobaan order saat ini
                        // kalau user back-forward, value ini tetap di form (browser preserve)
                        $request_id = sha1($client_id . '|' . uniqid('', true) . '|' . microtime(true));
                        ?>
                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">


                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                            <!-- Nama User -->
                            <div class="rounded-xl border border-slate-300 bg-slate-50 p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">NAMA
                                    USER</label>
                                <div class="mt-2 text-sm font-semibold text-slate-900"><?php echo $session_id; ?></div>
                            </div>

                            <!-- Nama Gedung -->
                            <div class="rounded-xl border border-slate-300 bg-slate-50 p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">NAMA
                                    GEDUNG</label>
                                <div class="mt-2 text-sm font-semibold text-slate-900">
                                    <?php foreach ($hasil as $gedung): ?>
                                        <?php echo $gedung['NAMA_GEDUNG']; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Tanggal -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">TANGGAL
                                    PEMESANAN</label>
                                <input type="date" name="tgl_pesan" id="tgl_pesan" required class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />
                                <p class="mt-2 text-xs text-slate-500">*Pemesanan minimal 10 hari dari tanggal hari ini*
                                </p>
                            </div>

                            <!-- Pilihan Jam -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">PILIHAN
                                    JAM</label>

                                <select name="tipe_jam" id="tipe_jam" required x-model="tipeJam" @change="applyJam()"
                                    class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40">
                                    <option value="CUSTOM">HH:MM - HH:MM (HANYA UNTUK STUDIO PODCAST)</option>
                                    <option value="HALF_DAY_PAGI">HALF DAY (08-12)</option>
                                    <option value="HALF_DAY_SIANG">HALF DAY (13-16)</option>
                                    <option value="FULL_DAY">FULL DAY</option>
                                </select>

                                <div x-show="!isCustom"
                                    class="mt-2 inline-flex items-center rounded-lg bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 border border-blue-200">
                                    <span x-text="RULES[tipeJam]?.label"></span>
                                </div>

                                <div x-show="isCustom" x-transition class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500">Jam Mulai</label>
                                        <input type="time" name="jam_pesan" id="jam_pesan" x-model="jamMulai"
                                            :required="isCustom"
                                            class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                            focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500">Jam Selesai</label>
                                        <input type="time" name="jam_selesai" id="jam_selesai" x-model="jamSelesai"
                                            :required="isCustom"
                                            class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                            focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />
                                    </div>
                                </div>

                                <template x-if="!isCustom">
                                    <div class="hidden">
                                        <input type="time" name="jam_pesan" x-model="jamMulai">
                                        <input type="time" name="jam_selesai" x-model="jamSelesai">
                                    </div>
                                </template>
                            </div>

                            <!-- Email -->
                            <div
                                class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200 lg:col-span-2">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">EMAIL</label>
                                <input type="email" name="email" value="<?php echo $email->EMAIL; ?>" required class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />
                            </div>

                            <!-- Catering -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">
                                <label
                                    class="block text-xs font-semibold tracking-widest text-slate-500">CATERING</label>

                                <div class="mt-3 flex items-center gap-3">
                                    <label
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-2 bg-slate-50 border border-slate-300 cursor-pointer hover:bg-slate-100">
                                        <input type="radio" name="radios" value="ya" class="hidden" x-model="catering">
                                        <span
                                            class="h-4 w-4 rounded-full border border-slate-400 flex items-center justify-center">
                                            <span class="h-2.5 w-2.5 rounded-full bg-blue-700"
                                                x-show="catering==='ya'"></span>
                                        </span>
                                        <span class="text-sm text-slate-900">Ya</span>
                                    </label>

                                    <label
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-2 bg-slate-50 border border-slate-300 cursor-pointer hover:bg-slate-100">
                                        <input type="radio" name="radios" value="tidak" class="hidden"
                                            x-model="catering">
                                        <span
                                            class="h-4 w-4 rounded-full border border-slate-400 flex items-center justify-center">
                                            <span class="h-2.5 w-2.5 rounded-full bg-blue-700"
                                                x-show="catering==='tidak'"></span>
                                        </span>
                                        <span class="text-sm text-slate-900">Tidak</span>
                                    </label>
                                </div>

                                <p class="mt-3 text-xs text-slate-500">*Cek harga catering per porsi pada menu Catering*
                                </p>
                            </div>

                            <!-- Paket Catering + Porsi -->
                            <!-- Paket Catering + Porsi -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200"
                                x-data="{
        selectedHarga: 0,
        selectedMinPax: 1,
        selectedNama: '',
        jumlahPorsi: '',
        onCateringChange(e){
          var opt = e.target.options[e.target.selectedIndex];
          this.selectedHarga = parseInt(opt.getAttribute('data-harga') || '0', 10);
          this.selectedMinPax = parseInt(opt.getAttribute('data-minpax') || '1', 10);
          this.selectedNama = opt.getAttribute('data-nama') || '';
          // set default jumlah ke min pax kalau kosong / kurang
          var jp = parseInt(this.jumlahPorsi || '0', 10);
          if (!jp || jp < this.selectedMinPax) this.jumlahPorsi = this.selectedMinPax;
        },
        resetCatering(){
          this.selectedHarga = 0;
          this.selectedMinPax = 1;
          this.selectedNama = '';
          this.jumlahPorsi = '';
          var sel = document.getElementById('selected_catering');
          if (sel) sel.value = '';
        },
        get subtotal(){
          var jp = parseInt(this.jumlahPorsi || '0', 10);
          if (!jp || !this.selectedHarga) return 0;
          return jp * this.selectedHarga;
        }
     }"
                                x-init="$watch('catering', v => { if(v !== 'ya') resetCatering(); })">

                                <label class="block text-xs font-semibold tracking-widest text-slate-500">PAKET CATERING</label>

                                <select id="selected_catering" name="catering"
                                    :disabled="!cateringEnabled"
                                    :required="cateringEnabled"
                                    @change="onCateringChange($event)"
                                    class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
      focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40
      disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">Pilih Paket</option>

                                    <?php
                                    // optional: grouping by JENIS biar rapi tapi tetap "semua paket"
                                    $groups = array();
                                    foreach ($res as $row) {
                                        $jenis = isset($row['JENIS']) ? $row['JENIS'] : 'LAINNYA';
                                        if (!isset($groups[$jenis])) $groups[$jenis] = array();
                                        $groups[$jenis][] = $row;
                                    }
                                    ?>

                                    <?php foreach ($groups as $jenis => $items): ?>
                                        <optgroup label="<?php echo htmlspecialchars($jenis); ?>">
                                            <?php foreach ($items as $row): ?>
                                                <?php
                                                $id   = (int)$row['ID_CATERING'];
                                                $nama = isset($row['NAMA_PAKET']) ? $row['NAMA_PAKET'] : '';
                                                $harga = isset($row['HARGA']) ? (int)$row['HARGA'] : 0;
                                                $minp = isset($row['MIN_PAX']) ? (int)$row['MIN_PAX'] : 1;
                                                if ($minp < 1) $minp = 1;
                                                ?>
                                                <option value="<?php echo $id; ?>"
                                                    data-harga="<?php echo $harga; ?>"
                                                    data-minpax="<?php echo $minp; ?>"
                                                    data-nama="<?php echo htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo htmlspecialchars($nama); ?> — Rp <?php echo number_format($harga, 0, ',', '.'); ?>/pax (min <?php echo $minp; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>

                                <label class="block mt-4 text-xs font-semibold tracking-widest text-slate-500">JUMLAH PORSI</label>
                                <input type="number"
                                    name="jumlah-porsi"
                                    id="jumlah-porsi"
                                    x-model="jumlahPorsi"
                                    :min="selectedMinPax"
                                    :disabled="!cateringEnabled"
                                    :required="cateringEnabled"
                                    placeholder="Masukkan jumlah porsi"
                                    class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400
      focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40
      disabled:opacity-50 disabled:cursor-not-allowed" />

                                <!-- Info ringkas -->
                                <div x-show="cateringEnabled && selectedHarga > 0" class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
                                    <div class="font-semibold text-slate-900" x-text="selectedNama"></div>
                                    <div class="mt-1 text-slate-700">
                                        Harga/pax: <span class="font-semibold">Rp <span x-text="selectedHarga.toLocaleString('id-ID')"></span></span>
                                        · Min pax: <span class="font-semibold" x-text="selectedMinPax"></span>
                                    </div>
                                    <div class="mt-1 text-slate-700">
                                        Estimasi total: <span class="font-semibold">Rp <span x-text="subtotal.toLocaleString('id-ID')"></span></span>
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        *Jumlah porsi minimal mengikuti Min Pax paket yang dipilih.*
                                    </div>
                                </div>

                            </div>


                        </div>

                        <!-- Warning + Submit -->
                        <div class="mt-6 rounded-xl border border-slate-300 bg-slate-50 p-5 ring-1 ring-slate-200">
                            <p class="text-sm text-slate-700">
                                *Pastikan semua field benar terisi, aksi ini tidak bisa dibatalkan.*
                            </p>

                            <div class="mt-5 flex items-center justify-end">
                                <button type="submit" name="submit" id="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-800 active:bg-blue-900">
                                    Lanjutkan
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </section>

            <!-- <p class="mt-6 text-center text-xs text-slate-500">© <?= date('Y') ?> Smart Office Tiga Serangkai</p> -->
        </div>
    </main>
    <?php $this->load->view('components/footer'); ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const btn = document.querySelector('button[type="submit"], input[type="submit"]');
            if (!form || !btn) return;

            form.addEventListener('submit', () => {
                btn.disabled = true;
                btn.style.opacity = "0.7";
            });
        });
    </script>

</body>

</html>