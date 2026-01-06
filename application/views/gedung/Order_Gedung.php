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

    <?php $this->load->view('components/header'); ?>
    <?php $this->load->view('components/navbar'); ?>

    <main class="pt-6 pb-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <section x-data="orderForm()" x-init="init()"
                class="rounded-2xl border border-slate-300 bg-white shadow-sm overflow-hidden ring-1 ring-slate-200">

                <!-- TOP BAR -->
                <div class="p-5 sm:p-6 border-b border-slate-300 bg-slate-50 flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-slate-900">Isi Data Pesanan</h1>
                        <p class="mt-1 text-sm text-slate-600">Pastikan semua field benar terisi.</p>
                    </div>

                    <!-- Dropdown kanan -->
                    <div class="relative">
                        <button type="button" @click="profileOpen=!profileOpen"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                            <span
                                class="hidden sm:inline"><?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?></span>
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
                                Edit Data Diri
                            </a>
                            <div class="border-t border-slate-200"></div>
                            <a href="<?php echo site_url('home/home/logout'); ?>"
                                class="flex items-center gap-2 px-4 py-3 hover:bg-red-50 text-red-600">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="p-3 mb-4 rounded bg-red-100 text-red-700">
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo site_url('home/order-gedung/validate/' . $id_gedung); ?>" method="post"
                        class="mt-2">
                        <?php
                        // token stabil per browser
                        if (empty($_COOKIE['booking_client_id'])) {
                            $client_id = sha1(uniqid('client', true) . microtime(true));
                            setcookie('booking_client_id', $client_id, time() + (86400 * 365), "/");
                            $_COOKIE['booking_client_id'] = $client_id;
                        } else {
                            $client_id = $_COOKIE['booking_client_id'];
                        }
                        $request_id = sha1($client_id . '|' . uniqid('', true) . '|' . microtime(true));
                        ?>
                        <input type="hidden" name="request_id"
                            value="<?php echo htmlspecialchars($request_id, ENT_QUOTES, 'UTF-8'); ?>">

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                            <!-- Nama User -->
                            <div class="rounded-xl border border-slate-300 bg-slate-50 p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">NAMA
                                    USER</label>
                                <div class="mt-2 text-sm font-semibold text-slate-900">
                                    <?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>

                            <!-- Nama Gedung -->
                            <div class="rounded-xl border border-slate-300 bg-slate-50 p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">NAMA
                                    RUANGAN</label>
                                <div class="mt-2 text-sm font-semibold text-slate-900">
                                    <?php foreach ($hasil as $gedung): ?>
                                        <?php echo htmlspecialchars($gedung['NAMA_GEDUNG'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <!-- Tanggal -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">
                                    TANGGAL PEMESANAN
                                </label>

                                <input type="date" name="tgl_pesan" id="tgl_pesan"
                                    min="<?= htmlspecialchars($min_pesan, ENT_QUOTES, 'UTF-8'); ?>" required class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
               focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />

                                <!-- ✅ HASIL FORMAT TANGGAL INDONESIA -->
                                <div id="tgl-format-id" class="mt-2 text-sm font-semibold text-slate-700 hidden">
                                    📅 <span id="tgl-text"></span>
                                </div>

                                <small class="mt-2 block text-xs text-slate-500">
                                    *<?= htmlspecialchars($min_text, ENT_QUOTES, 'UTF-8'); ?>*
                                </small>
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
                                    <span x-text="RULES[tipeJam] ? RULES[tipeJam].label : ''"></span>
                                </div>

                                <div x-show="isCustom" x-transition class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500">Jam Mulai</label>
                                        <input type="time" name="jam_pesan" id="jam_pesan" x-model="jamMulai"
                                            :required="isCustom" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                             focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500">Jam Selesai</label>
                                        <input type="time" name="jam_selesai" id="jam_selesai" x-model="jamSelesai"
                                            :required="isCustom" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                             focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />
                                    </div>
                                </div>

                                <!-- biar tetap terkirim saat non-custom -->
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
                                <input type="email" name="email"
                                    value="<?php echo htmlspecialchars($email->EMAIL, ENT_QUOTES, 'UTF-8'); ?>" required
                                    class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
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

                            <!-- Paket Catering + Porsi + Input Kategori -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">

                                <label class="block text-xs font-semibold tracking-widest text-slate-500">PAKET
                                    CATERING</label>

                                <select id="selected_catering" name="catering" :disabled="!cateringEnabled"
                                    :required="cateringEnabled" @change="onCateringChange($event)" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                         focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40
                         disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">Pilih Paket</option>

                                    <?php
                                    // grouping by JENIS biar rapi
                                    $groups = array();
                                    foreach ($res as $row) {
                                        $jenis = isset($row['JENIS']) ? $row['JENIS'] : 'LAINNYA';
                                        if (!isset($groups[$jenis])) $groups[$jenis] = array();
                                        $groups[$jenis][] = $row;
                                    }
                                    ?>

                                    <?php foreach ($groups as $jenis => $items): ?>
                                        <optgroup label="<?php echo htmlspecialchars($jenis, ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php foreach ($items as $row): ?>
                                                <?php
                                                $id      = (int)$row['ID_CATERING'];
                                                $nama    = isset($row['NAMA_PAKET']) ? $row['NAMA_PAKET'] : '';
                                                $harga   = isset($row['HARGA']) ? (int)$row['HARGA'] : 0;
                                                $minp    = isset($row['MIN_PAX']) ? (int)$row['MIN_PAX'] : 1;
                                                if ($minp < 1) $minp = 1;

                                                $menujson = isset($row['MENU_JSON']) ? trim($row['MENU_JSON']) : '';
                                                // amanin buat attribute (hapus newline)
                                                $menujson_attr = str_replace(array("\r", "\n"), ' ', $menujson);
                                                ?>
                                                <option value="<?php echo $id; ?>" data-harga="<?php echo $harga; ?>"
                                                    data-minpax="<?php echo $minp; ?>"
                                                    data-nama="<?php echo htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-menujson="<?php echo htmlspecialchars($menujson_attr, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>
                                                    — Rp <?php echo number_format($harga, 0, ',', '.'); ?>/pax (min
                                                    <?php echo $minp; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>

                                <label class="block mt-4 text-xs font-semibold tracking-widest text-slate-500">JUMLAH
                                    PORSI</label>
                                <input type="number" name="jumlah-porsi" id="jumlah-porsi" x-model="jumlahPorsi"
                                    :min="selectedMinPax" :disabled="!cateringEnabled" :required="cateringEnabled"
                                    placeholder="Masukkan jumlah porsi" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400
                         focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40
                         disabled:opacity-50 disabled:cursor-not-allowed" />

                                <!-- Kategori Menu -->
                                <div x-show="cateringEnabled && categories.length" x-transition
                                    class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="text-sm font-semibold text-slate-900 mb-2">
                                        Isi Pilihan Menu per Kategori
                                    </div>

                                    <template x-for="cat in categories" :key="cat.key">
                                        <div class="mb-4 last:mb-0" x-show="!cat.exclude">
                                            <div class="flex items-baseline justify-between gap-3">
                                                <div class="font-semibold text-slate-800" x-text="cat.label"></div>
                                                <div class="text-xs text-slate-500" x-text="cat.noteText"></div>
                                            </div>

                                            <textarea class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400
                               focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40"
                                                rows="2" :name="'menu_input[' + cat.key + ']'"
                                                :placeholder="cat.placeholder"></textarea>

                                            <div class="mt-1 text-xs text-slate-500">Isi pilihan kamu (mis: “Nasi Putih,
                                                Mie Goreng Jawa”).</div>
                                        </div>
                                    </template>

                                    <!-- Add-on -->
                                    <div x-show="addons.length" class="mt-5 pt-4 border-t border-slate-200">
                                        <div class="text-sm font-semibold text-slate-900 mb-2">Add-on (Opsional)</div>

                                        <template x-for="a in addons" :key="a.key">
                                            <div class="mb-4 last:mb-0">
                                                <label
                                                    class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                                                    <input type="checkbox" class="h-4 w-4" x-model="a.enabled"
                                                        :name="'addon_enabled['+a.key+']'" value="1">
                                                    <span x-text="a.label"></span>
                                                    <span class="text-xs font-normal text-slate-500" x-show="a.price">
                                                        (+Rp <span x-text="a.price.toLocaleString('id-ID')"></span>/pax)
                                                    </span>
                                                </label>

                                                <div class="text-xs text-slate-500 mt-1" x-text="a.note"></div>

                                                <textarea x-show="a.enabled" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400
                                 focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40"
                                                    rows="2" :name="'addon_input[' + a.key + ']'"
                                                    :placeholder="a.placeholder"></textarea>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Info ringkas -->
                                <div x-show="cateringEnabled && selectedHarga > 0"
                                    class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
                                    <div class="font-semibold text-slate-900" x-text="selectedNama"></div>

                                    <div class="mt-1 text-slate-700">
                                        Harga/pax:
                                        <span class="font-semibold">Rp <span
                                                x-text="selectedHarga.toLocaleString('id-ID')"></span></span>
                                        · Min pax: <span class="font-semibold" x-text="selectedMinPax"></span>
                                    </div>

                                    <div class="mt-1 text-slate-700">
                                        Estimasi total:
                                        <span class="font-semibold">Rp <span
                                                x-text="grandTotal.toLocaleString('id-ID')"></span></span>
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        *Estimasi = (harga * porsi) + add-on (jika dicentang, dihitung per pax).*
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Warning + Submit -->
                        <div class="mt-6 rounded-xl border border-slate-300 bg-slate-50 p-5 ring-1 ring-slate-200">
                            <p class="text-sm text-slate-700">*Pastikan semua field benar terisi, aksi ini tidak bisa
                                dibatalkan.*</p>

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
        </div>
    </main>

    <?php $this->load->view('components/footer'); ?>

    <script>
        // FORMAT TANGGAL INDONESIA
        document.addEventListener('DOMContentLoaded', function() {
            const tglInput = document.getElementById('tgl_pesan');
            const tglFormatId = document.getElementById('tgl-format-id');
            const tglText = document.getElementById('tgl-text');

            function formatTanggalIndonesia(dateStr) {
                const months = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                const parts = dateStr.split('-');
                if (parts.length !== 3) return '';
                const year = parts[0];
                const month = parseInt(parts[1], 10) - 1;
                const day = parseInt(parts[2], 10);
                return day + ' ' + months[month] + ' ' + year;
            }

            tglInput.addEventListener('change', function() {
                const formatted = formatTanggalIndonesia(tglInput.value);
                if (formatted) {
                    tglText.textContent = formatted;
                    tglFormatId.classList.remove('hidden');
                } else {
                    tglFormatId.classList.add('hidden');
                }
            });

            // inisialisasi jika sudah ada value
            if (tglInput.value) {
                const formatted = formatTanggalIndonesia(tglInput.value);
                if (formatted) {
                    tglText.textContent = formatted;
                    tglFormatId.classList.remove('hidden');
                }
            }
        });

        // disable tombol saat submit
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.querySelector('form');
            var btn = document.querySelector('button[type="submit"], input[type="submit"]');
            if (!form || !btn) return;
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.style.opacity = "0.7";
            });
        });

        // ALPINE COMPONENT
        function orderForm() {
            return {
                // ==== state umum ====
                catering: 'tidak',
                tipeJam: 'CUSTOM',
                RULES: {
                    HALF_DAY_PAGI: {
                        start: '08:00',
                        end: '12:00',
                        label: 'HALF DAY (08:00 - 12:00)'
                    },
                    HALF_DAY_SIANG: {
                        start: '13:00',
                        end: '16:00',
                        label: 'HALF DAY (13:00 - 16:00)'
                    },
                    FULL_DAY: {
                        start: '08:00',
                        end: '17:00',
                        label: 'FULL DAY (08:00 - 17:00)'
                    }
                },
                jamMulai: '',
                jamSelesai: '',
                profileOpen: false,

                // ==== catering state ====
                categories: [],
                addons: [],
                selectedHarga: 0,
                selectedMinPax: 1,
                selectedNama: '',
                jumlahPorsi: '',

                get cateringEnabled() {
                    return this.catering === 'ya';
                },
                get isCustom() {
                    return this.tipeJam === 'CUSTOM';
                },

                init: function() {
                    this.jamMulai = '';
                    this.jamSelesai = '';

                    var self = this;
                    this.$watch('catering', function(v) {
                        if (v !== 'ya') self.resetCatering();
                    });
                },

                applyJam: function() {
                    if (this.isCustom) return;
                    this.jamMulai = this.RULES[this.tipeJam].start;
                    this.jamSelesai = this.RULES[this.tipeJam].end;
                },

                resetCatering: function() {
                    this.selectedHarga = 0;
                    this.selectedMinPax = 1;
                    this.selectedNama = '';
                    this.jumlahPorsi = '';
                    this.categories = [];
                    this.addons = [];
                    var sel = document.getElementById('selected_catering');
                    if (sel) sel.value = '';
                },

                onCateringChange: function(e) {
                    var opt = e.target.options[e.target.selectedIndex];

                    this.selectedHarga = parseInt(opt.getAttribute('data-harga') || '0', 10);
                    this.selectedMinPax = parseInt(opt.getAttribute('data-minpax') || '1', 10);
                    this.selectedNama = opt.getAttribute('data-nama') || '';

                    var raw = opt.getAttribute('data-menujson') || '';
                    this.parseMenuJson(raw);

                    var jp = parseInt(this.jumlahPorsi || '0', 10);
                    if (!jp || jp < this.selectedMinPax) this.jumlahPorsi = this.selectedMinPax;
                },

                parseMenuJson: function(raw) {
                    this.categories = [];
                    this.addons = [];
                    if (!raw) return;

                    var obj = null;
                    try {
                        obj = JSON.parse(raw);
                    } catch (e) {
                        obj = null;
                    }
                    if (!obj) return;

                    // categories
                    if (obj.categories && Array.isArray(obj.categories)) {
                        for (var i = 0; i < obj.categories.length; i++) {
                            var c = obj.categories[i] || {};
                            var key = c.key || ('cat_' + i);
                            var label = c.label || ('Kategori ' + (i + 1));
                            var pick = parseInt(c.pick || 0, 10);
                            var note = c.note || '';
                            var items = (c.items && Array.isArray(c.items)) ? c.items : [];

                            // sembunyikan yg "tidak termasuk" + kosong
                            var exclude = false;
                            if (items.length === 0 && note && note.toLowerCase().indexOf('tidak termasuk') !== -1)
                                exclude = true;

                            var noteText = '';
                            if (pick > 0) noteText = 'Bebas memilih ' + pick + ' macam';
                            if (note) noteText = noteText ? (noteText + ' • ' + note) : note;

                            var example = items.length ? items.slice(0, 2).join(', ') : 'Tulis pilihan kamu di sini';

                            var placeholder = '';
                            if (pick > 0) placeholder = 'Pilih maksimal ' + pick + ' (contoh: ' + example + ')';
                            else if (note) placeholder = note + ' (contoh: ' + example + ')';
                            else placeholder = 'Contoh: ' + example;

                            this.categories.push({
                                key: key,
                                label: label,
                                pick: pick,
                                note: note,
                                noteText: noteText,
                                items: items,
                                example: example,
                                placeholder: placeholder,
                                exclude: exclude
                            });
                        }
                    }

                    // addons
                    if (obj.addons && Array.isArray(obj.addons)) {
                        for (var j = 0; j < obj.addons.length; j++) {
                            var a = obj.addons[j] || {};
                            var akey = a.key || ('addon_' + j);
                            var alabel = a.label || ('Add-on ' + (j + 1));
                            var apick = parseInt(a.pick || 0, 10);
                            var aprice = parseInt(a.price || 0, 10);
                            var anote = a.note || '';
                            var aitems = (a.items && Array.isArray(a.items)) ? a.items : [];

                            var aexample = aitems.length ? aitems.slice(0, 2).join(', ') : 'Tulis pilihan add-on';
                            var aplaceholder = '';
                            if (apick > 0) aplaceholder = 'Pilih maksimal ' + apick + ' (contoh: ' + aexample + ')';
                            else if (anote) aplaceholder = anote + ' (contoh: ' + aexample + ')';
                            else aplaceholder = 'Contoh: ' + aexample;

                            this.addons.push({
                                key: akey,
                                label: alabel,
                                pick: apick,
                                price: aprice,
                                note: anote,
                                items: aitems,
                                example: aexample,
                                placeholder: aplaceholder,
                                enabled: false
                            });
                        }
                    }
                },

                get subtotal() {
                    var jp = parseInt(this.jumlahPorsi || '0', 10);
                    if (!jp || !this.selectedHarga) return 0;
                    return jp * this.selectedHarga;
                },

                get addonsTotal() {
                    // dihitung per pax
                    var jp = parseInt(this.jumlahPorsi || '0', 10);
                    if (!jp) jp = 0;
                    var total = 0;
                    for (var i = 0; i < this.addons.length; i++) {
                        if (this.addons[i].enabled) total += (parseInt(this.addons[i].price || 0, 10) * jp);
                    }
                    return total;
                },

                get grandTotal() {
                    return this.subtotal + this.addonsTotal;
                }
            }
        }
    </script>
</body>

</html>