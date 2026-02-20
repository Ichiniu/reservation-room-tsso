<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

// ✅ Extract ID_GEDUNG with multiple fallback sources
$id_gedung = 0;
if (isset($res[0]['ID_GEDUNG'])) {
    $id_gedung = (int)$res[0]['ID_GEDUNG'];
} elseif (isset($hasil[0]['ID_GEDUNG'])) {
    $id_gedung = (int)$hasil[0]['ID_GEDUNG'];
} elseif (isset($_SERVER['REQUEST_URI'])) {
    // Extract from URL as last resort: /home/order-gedung/123
    if (preg_match('/order-gedung\/(\d+)/', $_SERVER['REQUEST_URI'], $m)) {
        $id_gedung = (int)$m[1];
    }
}

// ✅ opsi pilihan jam dari controller (fallback aman)
$allowed_tipe_jam = (isset($allowed_tipe_jam) && is_array($allowed_tipe_jam))
    ? $allowed_tipe_jam
    : array('CUSTOM', 'HALF_DAY_PAGI', 'HALF_DAY_SIANG', 'FULL_DAY');

$default_tipe_jam = isset($default_tipe_jam) ? $default_tipe_jam : $allowed_tipe_jam[0];

// ✅ endpoint JSON jadwal by date (sesuaikan controller/method)
$jadwalEndpoint = site_url('home/home/jadwal_by_date/' . $id_gedung);

$is_internal_view = !empty($is_internal);
$pricing_mode_view = isset($pricing_mode) ? (string)$pricing_mode : 'FLAT';
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

                    <form id="orderFormEl" action="<?php echo site_url('home/home/order/' . (int)$id_gedung); ?>"
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

                            <!-- ✅ TANGGAL (FIX BUTTON CALENDAR) -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">
                                    TANGGAL PEMESANAN
                                </label>

                                <!-- input date asli (untuk submit) dibuat 1px dan tidak menutup UI -->
                                <input type="date" name="tgl_pesan" id="tgl_pesan" x-ref="tglDate"
                                    min="<?php echo htmlspecialchars($min_pesan, ENT_QUOTES, 'UTF-8'); ?>" required
                                    @change="setTanggalDisplay($event.target.value); onDatePicked($event.target.value)"
                                    class="absolute w-px h-px opacity-0 -z-10" />

                                <!-- input display + tombol kalender -->
                                <div class="mt-2 relative">
                                    <input type="text" id="tgl_pesan_display" x-model="tglDisplay" readonly
                                        placeholder="Pilih tanggal" @click="toggleCalendar()"
                                        class="w-full rounded-xl bg-white border border-slate-300 px-4 py-3 pr-12 text-slate-900 cursor-pointer
                                        focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />

                                    <button type="button" @click="toggleCalendar()" class="absolute right-3 top-1/2 -translate-y-1/2 inline-flex items-center justify-center
                                        h-9 w-9 rounded-lg hover:bg-slate-100 text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </button>

                                    <!-- DROPDOWN KALENDER -->
                                    <div x-show="calOpen" @click.outside="calOpen=false" x-transition
                                        class="absolute left-0 mt-2 w-[320px] bg-white rounded-2xl border border-slate-200 shadow-xl z-30 p-4">

                                        <!-- Header: Navigasi + Month/Year -->
                                        <div class="flex items-center justify-between mb-4">
                                            <button type="button" @click="prevMonth()" class="p-2 hover:bg-slate-100 rounded-lg text-slate-600">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="15 18 9 12 15 6"></polyline>
                                                </svg>
                                            </button>

                                            <div class="flex items-center gap-3 font-medium text-slate-900">
                                                <!-- Month Selector (Custom Dropdown) -->
                                                <div class="relative" x-data="{ mOpen: false }">
                                                    <button type="button" @click="mOpen = !mOpen"
                                                        class="flex items-center gap-1 pl-2 pr-1 py-1 hover:text-blue-600 transition-colors">
                                                        <span x-text="months[viewMonth]"></span>
                                                        <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    <div x-show="mOpen" @click.outside="mOpen = false" x-transition
                                                        class="absolute left-0 mt-1 w-32 bg-white border border-slate-200 rounded-xl shadow-lg z-40 py-1 max-h-60 overflow-y-auto">
                                                        <template x-for="(m, idx) in months" :key="idx">
                                                            <button type="button"
                                                                @click="viewMonth = idx; generateDays(); mOpen = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 transition-colors"
                                                                :class="viewMonth === idx ? 'text-blue-600 font-semibold' : 'text-slate-700'">
                                                                <span x-text="m"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <!-- Year Selector (Custom Dropdown) -->
                                                <div class="relative" x-data="{ yOpen: false }">
                                                    <button type="button" @click="yOpen = !yOpen"
                                                        class="flex items-center gap-1 pl-2 pr-1 py-1 hover:text-blue-600 transition-colors">
                                                        <span x-text="viewYear"></span>
                                                        <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    <div x-show="yOpen" @click.outside="yOpen = false" x-transition
                                                        class="absolute left-0 mt-1 w-32 bg-white border border-slate-200 rounded-xl shadow-lg z-40 py-1 max-h-60 overflow-y-auto">
                                                        <template x-for="y in years" :key="y">
                                                            <button type="button"
                                                                @click="viewYear = y; generateDays(); yOpen = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 transition-colors"
                                                                :class="viewYear === y ? 'text-blue-600 font-semibold' : 'text-slate-700'">
                                                                <span x-text="y"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" @click="nextMonth()" class="p-2 hover:bg-slate-100 rounded-lg text-slate-600">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Days Header -->
                                        <div class="grid grid-cols-7 mb-2">
                                            <template x-for="d in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                                                <div class="text-center text-sm text-slate-400 py-1" x-text="d"></div>
                                            </template>
                                        </div>

                                        <!-- Days Grid -->
                                        <div class="grid grid-cols-7 gap-y-1">
                                            <template x-for="day in days" :key="day.id">
                                                <div class="relative h-10 w-full flex items-center justify-center">
                                                    <button type="button"
                                                        x-show="day.date"
                                                        @click="selectDate(day)"
                                                        :disabled="!day.enabled"
                                                        class="h-9 w-9 rounded-xl flex items-center justify-center text-sm transition-all relative overflow-hidden"
                                                        :class="{
                                                            'cursor-not-allowed opacity-20': !day.enabled,
                                                            'hover:bg-slate-50 text-slate-900': day.enabled && !day.selected,
                                                            'bg-[#1a1a1a] text-white font-semibold': day.selected,
                                                            'text-slate-400': !day.enabled && day.isWeekend
                                                        }">
                                                        <span x-text="day.dayNum"></span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- ✅ baris "📅 13 Februari 2026" DIHILANGKAN -->

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
                                                <div class="text-sm text-slate-600 text-right font-semibold truncate"
                                                    x-text="'(' + it.DESKRIPSI_ACARA + ')'"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- ✅ PILIHAN JAM -->
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

                                <template x-if="!isCustom">
                                    <div class="hidden">
                                        <input type="time" name="jam_pesan" x-model="jamMulai">
                                        <input type="time" name="jam_selesai" x-model="jamSelesai">
                                    </div>
                                </template>

                                <!-- MODAL WHEEL -->
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
                                                <div
                                                    class="pointer-events-none absolute top-0 left-0 right-0 h-16 bg-gradient-to-b from-white via-white/70 to-transparent">
                                                </div>
                                                <div
                                                    class="pointer-events-none absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white via-white/70 to-transparent">
                                                </div>
                                                <div
                                                    class="pointer-events-none absolute left-0 right-0 top-1/2 -translate-y-1/2 h-12 border-y border-slate-300/80">
                                                </div>

                                                <div class="grid grid-cols-[1fr_auto_1fr] items-center">
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
                            </div>

                            <!-- Podcast -->
                            <div x-show="showPodcast" x-transition
                                class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">JENIS
                                    PODCAST</label>
                                <select name="podcast_type" x-model="podcastType" :required="showPodcast" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40">
                                    <option value="">Pilih Jenis</option>
                                    <option value="AUDIO">Audio Podcast</option>
                                    <option value="VIDEO">Video Streaming</option>
                                </select>
                                <p class="mt-2 text-xs text-slate-500">
                                    *Khusus user eksternal untuk Studio Podcast (tarif per jam sesuai jenis).*
                                </p>
                            </div>

                            <div x-show="extraError" x-transition
                                class="lg:col-span-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                                <span x-text="extraError"></span>
                            </div>

                            <!-- EMAIL -->
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200"
                                :class="showPeserta ? '' : 'lg:col-span-2'">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">EMAIL</label>
                                <input type="email" name="email"
                                    value="<?php echo htmlspecialchars($email->EMAIL, ENT_QUOTES, 'UTF-8'); ?>" required
                                    class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />
                            </div>

                            <!-- TOTAL PESERTA -->
                            <div x-show="showPeserta" x-transition
                                class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">
                                <label class="block text-xs font-semibold tracking-widest text-slate-500">TOTAL
                                    PESERTA</label>
                                <input type="number" name="total_peserta" x-model="totalPeserta" min="1" step="1"
                                    :required="showPeserta" placeholder="Masukkan jumlah peserta" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40" />
                                <p class="mt-2 text-xs text-slate-500">
                                    *Untuk Meeting Room &amp; Amphitheater. (Harga terhitung 0 untuk user Internal).*
                                </p>
                            </div>

                            <!-- Catering -->
                            <?php
                            $has_active_catering = (isset($catering_list) && is_array($catering_list) && count($catering_list) > 0);
                            $_phone = isset($catering_phone) ? $catering_phone : '089649261851';
                            ?>
                            <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200 <?= !$has_active_catering ? 'lg:col-span-2' : '' ?>">
                                <label
                                    class="block text-xs font-semibold tracking-widest text-slate-500">CATERING</label>

                                <?php if ($has_active_catering): ?>
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
                                <?php else: ?>
                                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                        <div class="flex items-start gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-semibold text-amber-900">Untuk sementara catering belum tersedia</p>
                                                <p class="mt-1 text-xs text-amber-700">Apabila menghendaki layanan catering, bisa hubungi nomor berikut:</p>
                                                <a href="https://wa.me/62<?= preg_replace('/^0/', '', $_phone) ?>" target="_blank"
                                                    class="inline-flex items-center gap-1.5 mt-2 text-xs font-semibold text-green-700 hover:text-green-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                    </svg>
                                                    <?= htmlspecialchars($_phone) ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="radios" value="tidak">
                                <?php endif; ?>
                            </div>

                            <!-- Paket Catering + Jumlah Porsi -->
                            <?php if ($has_active_catering): ?>
                                <div class="rounded-xl border border-slate-300 bg-white p-5 ring-1 ring-slate-200">
                                    <label class="block text-xs font-semibold tracking-widest text-slate-500">PAKET
                                        CATERING</label>

                                    <select id="catering" name="catering" :disabled="!cateringEnabled"
                                        :required="cateringEnabled" x-model="cateringId" @change="onCateringChange($event)"
                                        class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40
                                    disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">Pilih Paket</option>
                                        <?php if (isset($catering_list) && is_array($catering_list)): ?>
                                            <?php foreach ($catering_list as $r): ?>
                                                <option value="<?php echo $r['ID_CATERING']; ?>"
                                                    data-harga="<?php echo (float)$r['HARGA']; ?>"
                                                    data-nama="<?php echo htmlspecialchars($r['NAMA_PAKET']); ?>">
                                                    <?php echo htmlspecialchars($r['NAMA_PAKET']); ?> - Rp <?php echo number_format((float)$r['HARGA'], 0, ',', '.'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>

                                    <label class="block mt-4 text-xs font-semibold tracking-widest text-slate-500">JUMLAH
                                        PORSI</label>
                                    <input type="number" name="jumlah-porsi" id="jumlah-porsi" x-model="jumlahPorsi"
                                        min="1" :disabled="!cateringEnabled" :required="cateringEnabled"
                                        placeholder="Masukkan jumlah porsi" class="mt-2 w-full rounded-xl bg-white border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400
                                    focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40
                                    disabled:opacity-50 disabled:cursor-not-allowed" />

                                    <!-- Preview Total Catering -->
                                    <div x-show="cateringEnabled && cateringHarga > 0 && jumlahPorsi > 0"
                                        class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm">
                                        <div class="font-semibold text-blue-900" x-text="cateringNama"></div>

                                        <div class="mt-2 text-blue-800">
                                            Harga/pax:
                                            <span class="font-semibold">Rp <span
                                                    x-text="cateringHarga.toLocaleString('id-ID')"></span></span>
                                        </div>

                                        <div class="mt-1 text-blue-800">
                                            Jumlah Porsi: <span class="font-semibold" x-text="jumlahPorsi"></span>
                                        </div>

                                        <div class="mt-2 pt-2 border-t border-blue-300 text-blue-900 font-semibold">
                                            Estimasi Total:
                                            <span class="text-lg">Rp <span
                                                    x-text="totalCatering.toLocaleString('id-ID')"></span></span>
                                        </div>

                                        <div class="mt-1 text-xs text-blue-600">
                                            *Estimasi = harga per pax × jumlah porsi*
                                        </div>
                                    </div>

                                    <p class="mt-3 text-xs text-slate-500">*Pilih paket catering dan masukkan jumlah porsi yang dibutuhkan*</p>

                                    <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                        <p class="text-xs font-semibold text-slate-700">NB : Untuk Spesifikasi Pilihan menu bisa hubungi admin.</p>
                                        <a href="https://wa.me/62<?= preg_replace('/^0/', '', $_phone) ?>" target="_blank"
                                            class="inline-flex items-center gap-1 mt-1 text-xs font-semibold text-green-700 hover:text-green-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                            </svg>
                                            <?= htmlspecialchars($_phone) ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Warning + Submit -->
                        <div class="mt-6 rounded-xl border border-slate-300 bg-slate-50 p-5 ring-1 ring-slate-200">
                            <p class="text-sm text-slate-700">
                                *Pastikan semua field benar terisi, aksi ini tidak bisa dibatalkan.*
                            </p>

                            <div class="mt-5 flex items-center justify-end">
                                <button type="submit" id="submit"
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
        function orderForm() {
            return {
                // ==== state umum ====
                isInternal: <?php echo json_encode(!empty($is_internal_view)); ?>,
                pricingMode: <?php echo json_encode($pricing_mode_view); ?>,
                totalPeserta: '',
                podcastType: '',
                extraError: '',
                catering: 'tidak',
                tipeJam: <?php echo json_encode($default_tipe_jam); ?>,
                allowedTipeJam: <?php echo json_encode(array_values($allowed_tipe_jam)); ?>,
                defaultTipeJam: <?php echo json_encode($default_tipe_jam); ?>,

                // ✅ display tanggal Indonesia
                tglDisplay: '',

                // ==== CALENDAR STATE ====
                calOpen: false,
                viewMonth: new Date().getMonth(),
                viewYear: String(new Date().getFullYear()),
                days: [],
                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                years: [
                    String(new Date().getFullYear()),
                    String(new Date().getFullYear() + 1),
                    String(new Date().getFullYear() + 2),
                    String(new Date().getFullYear() + 3),
                    String(new Date().getFullYear() + 4)
                ],
                minDateStr: <?php echo json_encode($min_pesan); ?>,

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

                // ==== WHEEL ====
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
                cateringId: '',
                cateringHarga: 0,
                cateringNama: '',

                // ==== paket gedung state (deprecated) ====
                paketGedungHarga: 0,
                paketGedungNama: '',
                minimalOrder: '',

                get cateringEnabled() {
                    return this.catering === 'ya';
                },
                get isCustom() {
                    return this.tipeJam === 'CUSTOM';
                },
                get showPeserta() {
                    // Tampilkan jika mode per peserta, atau jika internal, atau jika mode FLAT (kecuali podcast)
                    if (this.pricingMode === 'PER_PESERTA') return true;
                    if (this.isInternal) return true;
                    if (this.pricingMode === 'FLAT' && this.pricingMode !== 'PODCAST_PER_JAM') return true;
                    return false;
                },
                get showPodcast() {
                    return this.pricingMode === 'PODCAST_PER_JAM';
                },
                get totalPaketGedung() {
                    const harga = parseInt(this.paketGedungHarga || 0, 10);
                    const minimal = parseInt(this.minimalOrder || 0, 10);
                    return harga * minimal;
                },
                get totalCatering() {
                    const harga = parseInt(this.cateringHarga || 0, 10);
                    const porsi = parseInt(this.jumlahPorsi || 0, 10);
                    return harga * porsi;
                },

                // ✅ format tanggal Indonesia
                formatTanggalIndonesia(dateStr) {
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                        'Oktober', 'November', 'Desember'
                    ];
                    const parts = String(dateStr || '').split('-');
                    if (parts.length !== 3) return '';
                    const y = parts[0];
                    const m = parseInt(parts[1], 10) - 1;
                    const d = parseInt(parts[2], 10);
                    if (isNaN(d) || isNaN(m) || m < 0 || m > 11) return '';
                    return d + ' ' + months[m] + ' ' + y;
                },

                setTanggalDisplay(dateStr) {
                    this.tglDisplay = dateStr ? this.formatTanggalIndonesia(dateStr) : '';
                    const el = this.$refs.tglDate;
                    if (el && el.value !== dateStr) el.value = dateStr || '';
                },

                // ==== CALENDAR LOGIC ====
                toggleCalendar() {
                    this.calOpen = !this.calOpen;
                    if (this.calOpen) {
                        // sync view with current selection or today
                        const current = this.$refs.tglDate.value;
                        if (current) {
                            const d = new Date(current);
                            this.viewMonth = d.getMonth();
                            this.viewYear = d.getFullYear();
                        } else {
                            const d = new Date();
                            this.viewMonth = d.getMonth();
                            this.viewYear = d.getFullYear();
                        }
                        this.generateDays();
                    }
                },

                prevMonth() {
                    if (this.viewMonth === 0) {
                        this.viewMonth = 11;
                        this.viewYear--;
                    } else {
                        this.viewMonth--;
                    }
                    this.generateDays();
                },

                nextMonth() {
                    if (this.viewMonth === 11) {
                        this.viewMonth = 0;
                        this.viewYear++;
                    } else {
                        this.viewMonth++;
                    }
                    this.generateDays();
                },

                generateDays() {
                    this.days = [];
                    const firstDay = new Date(this.viewYear, this.viewMonth, 1).getDay(); // 0(Su) - 6(Sa)
                    const daysInMonth = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();

                    const minD = this.minDateStr ? new Date(this.minDateStr) : null;
                    if (minD) minD.setHours(0, 0, 0, 0);

                    const selectedStr = this.$refs.tglDate.value;

                    // padding
                    for (let i = 0; i < firstDay; i++) {
                        this.days.push({
                            id: 'p' + i,
                            date: null,
                            dayNum: '',
                            enabled: false
                        });
                    }

                    for (let d = 1; d <= daysInMonth; d++) {
                        const dateObj = new Date(this.viewYear, this.viewMonth, d);
                        const dateIso = this.viewYear + '-' + String(this.viewMonth + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');

                        const dayOfWeek = dateObj.getDay();
                        const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);

                        let enabled = true;
                        if (isWeekend) enabled = false;
                        if (minD && dateObj < minD) enabled = false;

                        this.days.push({
                            id: dateIso,
                            date: dateIso,
                            dayNum: d,
                            enabled: enabled,
                            isWeekend: isWeekend,
                            selected: dateIso === selectedStr
                        });
                    }
                },

                selectDate(day) {
                    if (!day.enabled) return;
                    this.setTanggalDisplay(day.date);
                    this.onDatePicked(day.date);
                    this.calOpen = false;
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

                    // autofill date -> set display + fetch jadwal (kalau ada value)
                    this.$nextTick(() => {
                        const tgl = this.$refs.tglDate || document.getElementById('tgl_pesan');
                        if (tgl && tgl.value) {
                            this.setTanggalDisplay(tgl.value);
                            this.onDatePicked(tgl.value);
                        }
                    });
                },

                // ✅ submit handler
                handleSubmit(e) {
                    if (!this.validateCustomTime()) {
                        e.preventDefault();
                        return;
                    }

                    this.extraError = '';

                    if (this.showPeserta) {
                        const n = parseInt(this.totalPeserta || '0', 10);
                        if (!n || n < 1) {
                            this.extraError = 'Total peserta wajib diisi (minimal 1).';
                            e.preventDefault();
                            return;
                        }
                        if (this.tipeJam === 'CUSTOM') {
                            this.extraError =
                                'Untuk ruangan ini (eksternal), tipe jam tidak boleh CUSTOM. Pilih HALF/FULL DAY.';
                            e.preventDefault();
                            return;
                        }
                    }

                    if (this.showPodcast) {
                        if (!this.podcastType) {
                            this.extraError = 'Pilih jenis podcast (Audio atau Video).';
                            e.preventDefault();
                            return;
                        }
                        if (this.tipeJam !== 'CUSTOM') {
                            this.extraError = 'Studio Podcast (eksternal) wajib memilih tipe jam CUSTOM (per jam).';
                            e.preventDefault();
                            return;
                        }
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
                    while (node && node !== el && !(node.dataset && node.dataset.value)) node = node.parentNode;
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

                // ===== catering =====
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

                onPaketGedungChange(e) {
                    const opt = e.target.options[e.target.selectedIndex];
                    this.paketGedungHarga = parseInt(opt.getAttribute('data-harga') || '0', 10);
                    this.paketGedungNama = opt.text || '';
                },
                onCateringChange(e) {
                    const opt = e.target.options[e.target.selectedIndex];
                    this.cateringHarga = parseInt(opt.getAttribute('data-harga') || '0', 10);
                    this.cateringNama = opt.getAttribute('data-nama') || '';
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