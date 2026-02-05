<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);

// ✅ opsi pilihan jam dari controller (fallback aman)
$allowed_tipe_jam = (isset($allowed_tipe_jam) && is_array($allowed_tipe_jam))
    ? $allowed_tipe_jam
    : array('CUSTOM', 'HALF_DAY_PAGI', 'HALF_DAY_SIANG', 'FULL_DAY');

$default_tipe_jam = isset($default_tipe_jam) ? $default_tipe_jam : $allowed_tipe_jam[0];

// ✅ endpoint JSON jadwal by date (sesuaikan controller/method)
$jadwalEndpoint = site_url('home/home/jadwal_by_date/' . $id_gedung);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pesan Ruangan</title>

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

                    <!-- ✅ jangan pakai .prevent agar submit normal -->
                    <form id="orderFormEl" action="<?php echo site_url('home/order-gedung/validate/' . $id_gedung); ?>"
                        method="post" class="mt-2" @submit="handleSubmit($event)">

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
                                    <?php echo htmlspecialchars($session_id, ENT_QUOTES, 'UTF-8'); ?>
                                </div>
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
                                    min="<?php echo htmlspecialchars($min_pesan, ENT_QUOTES, 'UTF-8'); ?>" required
                                    @change="onDatePicked($event.target.value)" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />

                                <!-- ✅ HASIL FORMAT TANGGAL INDONESIA -->
                                <div id="tgl-format-id" class="mt-2 text-sm font-semibold text-slate-700 hidden">
                                    📅 <span id="tgl-text"></span>
                                </div>

                                <small class="mt-2 block text-xs text-slate-500">
                                    *<?php echo htmlspecialchars($min_text, ENT_QUOTES, 'UTF-8'); ?>*
                                </small>

                                <!-- ✅ BOX JADWAL -->
                                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="font-semibold mb-2">Jadwal pada tanggal ini</div>

                                    <template x-if="jadwalLoading">
                                        <div class="text-sm text-slate-500">Memuat...</div>
                                    </template>

                                    <template x-if="jadwalError">
                                        <div class="text-sm text-red-600" x-text="jadwalError"></div>
                                    </template>

                                    <template x-if="!jadwalLoading && !jadwalError && jadwalItems.length === 0">
                                        <div class="text-sm text-slate-500">Belum ada jadwal.</div>
                                    </template>

                                    <template x-for="it in jadwalItems" :key="it.ID_PEMESANAN">
                                        <div class="py-2 border-t border-slate-200 first:border-t-0">
                                            <div class="flex items-center gap-3">
                                                <div class="text-sm font-bold whitespace-nowrap"
                                                    x-text="it.JAM_MULAI + ' - ' + it.JAM_SELESAI"></div>

                                                <div class="text-ms text-slate-600 text-right font-semibold truncate"
                                                    x-text="'(' + it.DESKRIPSI_ACARA + ')'"></div>
                                            </div>

                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- ✅ PILIHAN JAM (WHEEL dari script lama) -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200 relative">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">PILIHAN
                                    JAM</label>

                                <select name="tipe_jam" id="tipe_jam" required x-model="tipeJam" @change="applyJam()"
                                    class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40">
                                    <?php
                                    $labels_tipe_jam = array(
                                        'CUSTOM'         => 'HH:MM - HH:MM (PER JAM)',
                                        'HALF_DAY_PAGI'  => 'HALF DAY (08-12)',
                                        'HALF_DAY_SIANG' => 'HALF DAY (13-16)',
                                        'FULL_DAY'       => 'FULL DAY',
                                    );
                                    ?>
                                    <?php foreach ($allowed_tipe_jam as $opt): ?>
                                    <option value="<?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars(isset($labels_tipe_jam[$opt]) ? $labels_tipe_jam[$opt] : $opt, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                                <div x-show="!isCustom"
                                    class="mt-2 inline-flex items-center rounded-lg bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 border border-blue-200">
                                    <span x-text="RULES[tipeJam] ? RULES[tipeJam].label : ''"></span>
                                </div>

                                <!-- CUSTOM (WHEEL PICKER UI) -->
                                <div x-show="isCustom" x-transition class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500">Jam Mulai</label>
                                        <button type="button" @click="openWheel('start')" class="mt-2 w-full text-left rounded-xl bg-white border border-slate-300 px-4 py-3
                                            text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40
                                            flex items-center justify-between">
                                            <span class="font-semibold"
                                                x-text="jamMulai ? jamMulai : 'Pilih jam mulai'"></span>
                                            <span class="text-slate-500 text-sm">🕒</span>
                                        </button>
                                        <input type="hidden" name="jam_pesan" :value="jamMulai">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500">Jam Selesai</label>
                                        <button type="button" @click="openWheel('end')" class="mt-2 w-full text-left rounded-xl bg-white border border-slate-300 px-4 py-3
                                            text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40
                                            flex items-center justify-between">
                                            <span class="font-semibold"
                                                x-text="jamSelesai ? jamSelesai : 'Pilih jam selesai'"></span>
                                            <span class="text-slate-500 text-sm">🕒</span>
                                        </button>
                                        <input type="hidden" name="jam_selesai" :value="jamSelesai">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <div class="mt-1 text-xs text-slate-500">Format 24 jam (contoh: 11:02). Tanpa
                                            AM/PM.</div>
                                        <div x-show="customError" class="mt-2 text-sm text-red-600 font-semibold"
                                            x-text="customError"></div>
                                    </div>
                                </div>

                                <!-- fallback non-custom (tetap kirim time input tersembunyi) -->
                                <template x-if="!isCustom">
                                    <div class="hidden">
                                        <input type="time" name="jam_pesan" x-model="jamMulai">
                                        <input type="time" name="jam_selesai" x-model="jamSelesai">
                                    </div>
                                </template>

                                <!-- MODAL WHEEL (FULL Tailwind) -->
                                <div x-show="wheelOpen" x-transition
                                    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
                                    <div class="absolute inset-0 bg-black/40" @click="closeWheel()"></div>

                                    <div
                                        class="relative w-full sm:max-w-md bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                                        <div
                                            class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                            <div class="text-sm font-semibold text-slate-900">
                                                Pilih Jam
                                                <span class="text-slate-500 font-normal"
                                                    x-text="wheelTarget==='start' ? '(Mulai)' : '(Selesai)'"></span>
                                            </div>
                                            <button type="button" class="text-slate-500 hover:text-slate-900"
                                                @click="closeWheel()">✕</button>
                                        </div>

                                        <div class="p-5">
                                            <div
                                                class="relative rounded-2xl border border-slate-200 bg-white overflow-hidden">
                                                <!-- fade -->
                                                <div
                                                    class="pointer-events-none absolute top-0 left-0 right-0 h-16 bg-gradient-to-b from-white via-white/70 to-transparent">
                                                </div>
                                                <div
                                                    class="pointer-events-none absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white via-white/70 to-transparent">
                                                </div>

                                                <!-- highlight center -->
                                                <div
                                                    class="pointer-events-none absolute left-0 right-0 top-1/2 -translate-y-1/2 h-12 border-y border-slate-300/80">
                                                </div>

                                                <div class="grid grid-cols-[1fr_auto_1fr] items-center">
                                                    <!-- hours -->
                                                    <div class="relative">
                                                        <div x-ref="wheelHours" @scroll.passive="onWheelScroll('hours')"
                                                            class="h-60 overflow-y-auto [scroll-snap-type:y_mandatory] [scrollbar-width:none]"
                                                            style="padding-top: var(--wheel-pad, 100px); padding-bottom: var(--wheel-pad, 100px); -webkit-overflow-scrolling: touch;">
                                                            <template x-for="h in hours" :key="'h'+h">
                                                                <div class="h-10 flex items-center justify-center text-[22px] font-semibold select-none [scroll-snap-align:center]"
                                                                    :class="selectedHour===h ? 'text-slate-900' : 'text-slate-900/35'"
                                                                    :data-value="h" x-text="h"></div>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div class="px-3 text-3xl font-bold text-slate-900">:</div>

                                                    <!-- minutes -->
                                                    <div class="relative">
                                                        <div x-ref="wheelMinutes"
                                                            @scroll.passive="onWheelScroll('minutes')"
                                                            class="h-60 overflow-y-auto [scroll-snap-type:y_mandatory] [scrollbar-width:none]"
                                                            style="padding-top: var(--wheel-pad, 100px); padding-bottom: var(--wheel-pad, 100px); -webkit-overflow-scrolling: touch;">
                                                            <template x-for="m in minutes" :key="'m'+m">
                                                                <div class="h-10 flex items-center justify-center text-[22px] font-semibold select-none [scroll-snap-align:center]"
                                                                    :class="selectedMinute===m ? 'text-slate-900' : 'text-slate-900/35'"
                                                                    :data-value="m" x-text="m"></div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="px-5 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                                            <div class="text-sm font-semibold text-slate-700">
                                                Dipilih: <span class="text-slate-900"
                                                    x-text="selectedHour + ':' + selectedMinute"></span>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="button" @click="closeWheel()"
                                                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-50">
                                                    Batal
                                                </button>
                                                <button type="button" @click="applyWheel()"
                                                    class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                                                    Pakai
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END MODAL -->
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
                                    <div class="text-sm font-semibold text-slate-900 mb-2">Isi Pilihan Menu per Kategori
                                    </div>

                                    <template x-for="cat in categories" :key="cat.key">
                                        <div class="mb-4 last:mb-0" x-show="!cat.exclude">
                                            <div class="flex items-baseline justify-between gap-3">
                                                <div class="font-semibold text-slate-800" x-text="cat.label"></div>
                                                <div class="text-xs text-slate-500" x-text="cat.noteText"></div>
                                            </div>

                                            <textarea
                                                class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400
                                                focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40"
                                                rows="2" :name="'menu_input[' + cat.key + ']'"
                                                :placeholder="cat.placeholder"></textarea>

                                            <div class="mt-1 text-xs text-slate-500">
                                                Isi pilihan kamu (mis: “Nasi Putih, Mie Goreng Jawa”).
                                            </div>
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

                                                <textarea x-show="a.enabled"
                                                    class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400
                                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" rows="2"
                                                    :name="'addon_input[' + a.key + ']'"
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
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                'September', 'Oktober', 'November', 'Desember'
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

        if (tglInput.value) {
            const formatted = formatTanggalIndonesia(tglInput.value);
            if (formatted) {
                tglText.textContent = formatted;
                tglFormatId.classList.remove('hidden');
            }
        }
    });

    function orderForm() {
        return {
            // ==== state umum ====
            catering: 'tidak',
            tipeJam: <?php echo json_encode($default_tipe_jam); ?>,
            allowedTipeJam: <?php echo json_encode(array_values($allowed_tipe_jam)); ?>,
            defaultTipeJam: <?php echo json_encode($default_tipe_jam); ?>,
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
            customError: '',

            // ==== jadwal by date ====
            jadwalLoading: false,
            jadwalError: '',
            jadwalItems: [],

            // ==== WHEEL (anti-loncat) ====
            wheelOpen: false,
            wheelTarget: 'start',
            hours: [],
            minutes: [],
            selectedHour: '08',
            selectedMinute: '00',
            itemHeight: 40,
            _scrollRAF: null,
            _scrollTimer: null,

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

            init() {
                // build hours 00-23
                this.hours = [];
                for (let h = 0; h < 24; h++) this.hours.push(String(h).padStart(2, '0'));

                // build minutes 00-59
                this.minutes = [];
                for (let m = 0; m < 60; m++) this.minutes.push(String(m).padStart(2, '0'));

                // guard tipeJam
                if (this.allowedTipeJam && this.allowedTipeJam.indexOf(this.tipeJam) === -1) {
                    this.tipeJam = this.defaultTipeJam;
                }

                // set jam default jika bukan CUSTOM
                if (!this.isCustom) this.applyJam();

                this.$watch('catering', (v) => {
                    if (v !== 'ya') this.resetCatering();
                });

                this.$watch('tipeJam', () => {
                    this.customError = '';
                    if (!this.isCustom) this.applyJam();
                });

                // autofill date -> fetch jadwal (kalau ada value)
                this.$nextTick(() => {
                    const tgl = document.getElementById('tgl_pesan');
                    if (tgl && tgl.value) this.onDatePicked(tgl.value);
                });
            },

            // ✅ submit handler
            handleSubmit(e) {
                if (!this.validateCustomTime()) {
                    e.preventDefault();
                    return;
                }
                const btn = document.getElementById('submit');
                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                }
            },

            timeToMinutes(t) {
                if (!t || !t.includes(':')) return -1;
                const p = t.split(':');
                const hh = parseInt(p[0], 10);
                const mm = parseInt(p[1], 10);
                if (isNaN(hh) || isNaN(mm)) return -1;
                return (hh * 60) + mm;
            },

            validateCustomTime() {
                this.customError = '';
                if (!this.isCustom) return true;

                if (!this.jamMulai || !this.jamSelesai) {
                    this.customError = 'Jam mulai dan jam selesai wajib diisi.';
                    return false;
                }

                const a = this.timeToMinutes(this.jamMulai);
                const b = this.timeToMinutes(this.jamSelesai);

                if (a < 0 || b < 0) {
                    this.customError = 'Format jam tidak valid. Gunakan HH:MM (24 jam).';
                    return false;
                }
                if (b <= a) {
                    this.customError = 'Jam selesai harus lebih besar dari jam mulai.';
                    return false;
                }
                return true;
            },

            applyJam() {
                if (this.allowedTipeJam && this.allowedTipeJam.indexOf(this.tipeJam) === -1) {
                    this.tipeJam = this.defaultTipeJam;
                }
                if (this.isCustom) return;

                const rule = this.RULES[this.tipeJam];
                if (!rule) return;

                this.jamMulai = rule.start;
                this.jamSelesai = rule.end;
            },

            // ✅ fetch jadwal by date
            async onDatePicked(dateStr) {
                this.jadwalError = '';
                this.jadwalItems = [];
                if (!dateStr) return;

                this.jadwalLoading = true;

                try {
                    const base = <?php echo json_encode($jadwalEndpoint); ?>;
                    const url = base + '?date=' + encodeURIComponent(dateStr);

                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const rawText = await res.text();

                    let json = null;
                    try {
                        json = JSON.parse(rawText);
                    } catch (e) {
                        json = null;
                    }

                    if (!res.ok) {
                        this.jadwalError = (json && json.message) ? json.message : ('Error ' + res.status);
                        return;
                    }
                    if (!json || !json.ok) {
                        this.jadwalError = (json && json.message) ? json.message : 'Gagal mengambil jadwal.';
                        return;
                    }
                    this.jadwalItems = Array.isArray(json.data) ? json.data : [];
                } catch (e) {
                    this.jadwalError = 'Terjadi error saat mengambil jadwal.';
                } finally {
                    this.jadwalLoading = false;
                }
            },

            // ===== WHEEL helpers =====
            _setWheelPad(el) {
                if (!el) return;
                const pad = Math.max(0, Math.round((el.clientHeight - this.itemHeight) / 2));
                el.style.setProperty('--wheel-pad', pad + 'px');
            },

            _scrollToValue(el, val, smooth = true) {
                if (!el) return;
                const item = el.querySelector(`[data-value="${val}"]`);
                if (!item) return;
                item.scrollIntoView({
                    block: 'center',
                    behavior: smooth ? 'smooth' : 'auto'
                });
            },

            _syncFromCenter(type) {
                const el = (type === 'hours') ? this.$refs.wheelHours : this.$refs.wheelMinutes;
                if (!el) return;

                const r = el.getBoundingClientRect();
                const cx = Math.round(r.left + r.width / 2);
                const cy = Math.round(r.top + r.height / 2);

                let node = document.elementFromPoint(cx, cy);
                while (node && node !== el && !(node.dataset && node.dataset.value)) {
                    node = node.parentNode;
                }
                if (!node || !(node.dataset && node.dataset.value)) return;

                const v = node.dataset.value;
                if (type === 'hours') this.selectedHour = v;
                else this.selectedMinute = v;
            },

            openWheel(target) {
                this.customError = '';
                this.wheelTarget = target || 'start';

                const current = (this.wheelTarget === 'start') ? this.jamMulai : this.jamSelesai;
                if (current && current.includes(':')) {
                    const p = current.split(':');
                    this.selectedHour = String(p[0] || '08').padStart(2, '0');
                    this.selectedMinute = String(p[1] || '00').padStart(2, '0');
                } else {
                    this.selectedHour = '08';
                    this.selectedMinute = '00';
                }

                this.wheelOpen = true;

                this.$nextTick(() => {
                    this._setWheelPad(this.$refs.wheelHours);
                    this._setWheelPad(this.$refs.wheelMinutes);

                    this._scrollToValue(this.$refs.wheelHours, this.selectedHour, false);
                    this._scrollToValue(this.$refs.wheelMinutes, this.selectedMinute, false);

                    this._syncFromCenter('hours');
                    this._syncFromCenter('minutes');
                });
            },

            closeWheel() {
                this.wheelOpen = false;
            },

            onWheelScroll(type) {
                if (this._scrollRAF) cancelAnimationFrame(this._scrollRAF);
                this._scrollRAF = requestAnimationFrame(() => this._syncFromCenter(type));

                clearTimeout(this._scrollTimer);
                this._scrollTimer = setTimeout(() => {
                    const el = (type === 'hours') ? this.$refs.wheelHours : this.$refs.wheelMinutes;
                    const v = (type === 'hours') ? this.selectedHour : this.selectedMinute;
                    this._scrollToValue(el, v, true);
                }, 120);
            },

            applyWheel() {
                const value = this.selectedHour + ':' + this.selectedMinute;
                if (this.wheelTarget === 'start') this.jamMulai = value;
                else this.jamSelesai = value;
                this.wheelOpen = false;
            },

            // ===== catering (tetap) =====
            resetCatering() {
                this.selectedHarga = 0;
                this.selectedMinPax = 1;
                this.selectedNama = '';
                this.jumlahPorsi = '';
                this.categories = [];
                this.addons = [];
                const sel = document.getElementById('selected_catering');
                if (sel) sel.value = '';
            },

            onCateringChange(e) {
                const opt = e.target.options[e.target.selectedIndex];
                this.selectedHarga = parseInt(opt.getAttribute('data-harga') || '0', 10);
                this.selectedMinPax = parseInt(opt.getAttribute('data-minpax') || '1', 10);
                this.selectedNama = opt.getAttribute('data-nama') || '';

                const raw = opt.getAttribute('data-menujson') || '';
                this.parseMenuJson(raw);

                const jp = parseInt(this.jumlahPorsi || '0', 10);
                if (!jp || jp < this.selectedMinPax) this.jumlahPorsi = this.selectedMinPax;
            },

            parseMenuJson(raw) {
                this.categories = [];
                this.addons = [];
                if (!raw) return;

                let obj = null;
                try {
                    obj = JSON.parse(raw);
                } catch (e) {
                    obj = null;
                }
                if (!obj) return;

                if (obj.categories && Array.isArray(obj.categories)) {
                    for (let i = 0; i < obj.categories.length; i++) {
                        const c = obj.categories[i] || {};
                        const key = c.key || ('cat_' + i);
                        const label = c.label || ('Kategori ' + (i + 1));
                        const pick = parseInt(c.pick || 0, 10);
                        const note = c.note || '';
                        const items = (c.items && Array.isArray(c.items)) ? c.items : [];

                        let exclude = false;
                        if (items.length === 0 && note && note.toLowerCase().includes('tidak termasuk')) exclude = true;

                        let noteText = '';
                        if (pick > 0) noteText = 'Bebas memilih ' + pick + ' macam';
                        if (note) noteText = noteText ? (noteText + ' • ' + note) : note;

                        const example = items.length ? items.slice(0, 2).join(', ') : 'Tulis pilihan kamu di sini';

                        let placeholder = '';
                        if (pick > 0) placeholder = 'Pilih maksimal ' + pick + ' (contoh: ' + example + ')';
                        else if (note) placeholder = note + ' (contoh: ' + example + ')';
                        else placeholder = 'Contoh: ' + example;

                        this.categories.push({
                            key,
                            label,
                            pick,
                            note,
                            noteText,
                            items,
                            example,
                            placeholder,
                            exclude
                        });
                    }
                }

                if (obj.addons && Array.isArray(obj.addons)) {
                    for (let j = 0; j < obj.addons.length; j++) {
                        const a = obj.addons[j] || {};
                        const key = a.key || ('addon_' + j);
                        const label = a.label || ('Add-on ' + (j + 1));
                        const pick = parseInt(a.pick || 0, 10);
                        const price = parseInt(a.price || 0, 10);
                        const note = a.note || '';
                        const items = (a.items && Array.isArray(a.items)) ? a.items : [];

                        const example = items.length ? items.slice(0, 2).join(', ') : 'Tulis pilihan add-on';

                        let placeholder = '';
                        if (pick > 0) placeholder = 'Pilih maksimal ' + pick + ' (contoh: ' + example + ')';
                        else if (note) placeholder = note + ' (contoh: ' + example + ')';
                        else placeholder = 'Contoh: ' + example;

                        this.addons.push({
                            key,
                            label,
                            pick,
                            price,
                            note,
                            items,
                            example,
                            placeholder,
                            enabled: false
                        });
                    }
                }
            },

            get subtotal() {
                const jp = parseInt(this.jumlahPorsi || '0', 10);
                if (!jp || !this.selectedHarga) return 0;
                return jp * this.selectedHarga;
            },

            get addonsTotal() {
                let jp = parseInt(this.jumlahPorsi || '0', 10);
                if (!jp) jp = 0;
                let total = 0;
                for (let i = 0; i < this.addons.length; i++) {
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