<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Transaksi</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-900">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="pt-24 md:pl-64 px-6 pb-20" x-data="rekapTransaksi()" x-cloak>

        <!-- HEADER -->
        <div class="max-w-4xl mx-auto mb-10 text-center">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Rekapitulasi Transaksi</h1>
            <p class="text-slate-500">Analisis data keuangan dan transaksi Anda secara mendalam</p>
        </div>

        <!-- CARD FILTER -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden transform transition-all p-8">

                <form action="<?= site_url('admin/rekap_transaksi/details') ?>" method="get" @submit="return handleSubmit($event)">

                    <div class="space-y-8">
                        <!-- BARIS 1: TANGGAL RANGE -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="relative">
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tanggal Awal</label>
                                <div class="relative">
                                    <input type="text" x-model="startDisplay" readonly placeholder="Pilih tanggal mulai"
                                        @click="toggleCalendar('start')"
                                        class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-3.5 cursor-pointer focus:ring-2 focus:ring-green-500/20 transition-all text-slate-700 font-medium placeholder:text-slate-400">
                                    <input type="hidden" name="start_date" x-model="startVal">

                                    <!-- CALENDAR DROPDOWN -->
                                    <div x-show="calOpenStart" @click.outside="calOpenStart = false" x-transition
                                        class="absolute left-0 mt-3 w-[320px] bg-white rounded-3xl border border-slate-100 shadow-2xl z-50 p-5 overflow-hidden">

                                        <div class="flex items-center justify-between mb-6">
                                            <button type="button" @click="navMonth('start', -1)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-400 hover:text-green-600 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>

                                            <div class="flex items-center gap-2 font-bold text-slate-900">
                                                <!-- Month -->
                                                <div class="relative" x-data="{ open: false }">
                                                    <button type="button" @click="open = !open" class="hover:text-green-600 flex items-center gap-1">
                                                        <span x-text="months[viewMonthStart]"></span>
                                                        <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" class="absolute left-1/2 -translate-x-1/2 mt-2 w-36 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-[60] max-h-64 overflow-y-auto">
                                                        <template x-for="(m, idx) in months" :key="idx">
                                                            <button type="button" @click="viewMonthStart = idx; generateDays('start'); open = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 transition-colors"
                                                                :class="viewMonthStart === idx ? 'text-green-600 font-bold bg-green-50/50' : 'text-slate-600'" x-text="m"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                                <!-- Year -->
                                                <div class="relative" x-data="{ open: false }">
                                                    <button type="button" @click="open = !open" class="hover:text-green-600 flex items-center gap-1">
                                                        <span x-text="viewYearStart"></span>
                                                        <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" class="absolute left-1/2 -translate-x-1/2 mt-2 w-28 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-[60]">
                                                        <template x-for="y in years" :key="y">
                                                            <button type="button" @click="viewYearStart = y; generateDays('start'); open = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 transition-colors"
                                                                :class="viewYearStart == y ? 'text-green-600 font-bold bg-green-50/50' : 'text-slate-600'" x-text="y"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" @click="navMonth('start', 1)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-400 hover:text-green-600 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-7 mb-3">
                                            <template x-for="d in ['Min','Sen','Sel','Rab','Kam','Jum','Sab']">
                                                <div class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest py-1" x-text="d"></div>
                                            </template>
                                        </div>

                                        <div class="grid grid-cols-7 gap-1">
                                            <template x-for="day in daysStart" :key="day.id">
                                                <div class="aspect-square flex items-center justify-center">
                                                    <button type="button" x-show="day.date" @click="selectDate('start', day)" :disabled="!day.enabled"
                                                        class="h-9 w-9 rounded-xl text-sm transition-all flex items-center justify-center font-semibold"
                                                        :class="{
                                                            'opacity-10 cursor-not-allowed': !day.enabled && !day.isWeekend,
                                                            'text-slate-300 cursor-not-allowed': !day.enabled && day.isWeekend,
                                                            'hover:bg-green-50 text-slate-700': day.enabled && !day.selected,
                                                            'bg-slate-900 text-white shadow-lg shadow-slate-200': day.selected
                                                        }">
                                                        <span x-text="day.dayNum"></span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                                            <button type="button" @click="clearDate('start')" class="text-xs font-bold text-rose-500 hover:text-rose-600 px-2 py-1">Hapus</button>
                                            <button type="button" @click="setToday('start')" class="text-xs font-bold text-green-600 hover:text-green-700 px-2 py-1">Hari Ini</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tanggal Akhir</label>
                                <div class="relative">
                                    <input type="text" x-model="endDisplay" readonly placeholder="Pilih tanggal akhir"
                                        @click="toggleCalendar('end')"
                                        class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-3.5 cursor-pointer focus:ring-2 focus:ring-green-500/20 transition-all text-slate-700 font-medium placeholder:text-slate-400">
                                    <input type="hidden" name="end_date" x-model="endVal">

                                    <!-- CALENDAR DROPDOWN -->
                                    <div x-show="calOpenEnd" @click.outside="calOpenEnd = false" x-transition
                                        class="absolute right-0 mt-3 w-[320px] bg-white rounded-3xl border border-slate-100 shadow-2xl z-50 p-5 overflow-hidden">

                                        <div class="flex items-center justify-between mb-6">
                                            <button type="button" @click="navMonth('end', -1)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-400 hover:text-green-600 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>

                                            <div class="flex items-center gap-2 font-bold text-slate-900">
                                                <div class="relative" x-data="{ open: false }">
                                                    <button type="button" @click="open = !open" class="hover:text-green-600 flex items-center gap-1">
                                                        <span x-text="months[viewMonthEnd]"></span>
                                                        <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" class="absolute left-1/2 -translate-x-1/2 mt-2 w-36 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-[60] max-h-64 overflow-y-auto">
                                                        <template x-for="(m, idx) in months" :key="idx">
                                                            <button type="button" @click="viewMonthEnd = idx; generateDays('end'); open = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 transition-colors"
                                                                :class="viewMonthEnd === idx ? 'text-green-600 font-bold bg-green-50/50' : 'text-slate-600'" x-text="m"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="relative" x-data="{ open: false }">
                                                    <button type="button" @click="open = !open" class="hover:text-green-600 flex items-center gap-1">
                                                        <span x-text="viewYearEnd"></span>
                                                        <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" class="absolute left-1/2 -translate-x-1/2 mt-2 w-28 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-[60]">
                                                        <template x-for="y in years" :key="y">
                                                            <button type="button" @click="viewYearEnd = y; generateDays('end'); open = false"
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 transition-colors"
                                                                :class="viewYearEnd == y ? 'text-green-600 font-bold bg-green-50/50' : 'text-slate-600'" x-text="y"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" @click="navMonth('end', 1)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-400 hover:text-green-600 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-7 mb-3">
                                            <template x-for="d in ['Min','Sen','Sel','Rab','Kam','Jum','Sab']">
                                                <div class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest py-1" x-text="d"></div>
                                            </template>
                                        </div>

                                        <div class="grid grid-cols-7 gap-1">
                                            <template x-for="day in daysEnd" :key="day.id">
                                                <div class="aspect-square flex items-center justify-center">
                                                    <button type="button" x-show="day.date" @click="selectDate('end', day)" :disabled="!day.enabled"
                                                        class="h-9 w-9 rounded-xl text-sm transition-all flex items-center justify-center font-semibold"
                                                        :class="{
                                                            'opacity-10 cursor-not-allowed': !day.enabled && !day.isWeekend,
                                                            'text-slate-300 cursor-not-allowed': !day.enabled && day.isWeekend,
                                                            'hover:bg-green-50 text-slate-700': day.enabled && !day.selected,
                                                            'bg-slate-900 text-white shadow-lg shadow-slate-200': day.selected
                                                        }">
                                                        <span x-text="day.dayNum"></span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                                            <button type="button" @click="clearDate('end')" class="text-xs font-bold text-rose-500 hover:text-rose-600 px-2 py-1">Hapus</button>
                                            <button type="button" @click="setToday('end')" class="text-xs font-bold text-green-600 hover:text-green-700 px-2 py-1">Hari Ini</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BARIS 2: BULAN & TAHUN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Bulan</label>
                                <select x-model="bulan" @change="onBulanTahunChange()" class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-green-500/20 text-slate-700 font-medium">
                                    <option value="">-- Pilih Bulan --</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tahun</label>
                                <select x-model="tahun" @change="onBulanTahunChange()" class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-3.5 focus:ring-2 focus:ring-green-500/20 text-slate-700 font-medium">
                                    <option value="">-- Pilih Tahun --</option>
                                    <template x-for="y in years" :key="y">
                                        <option :value="y" x-text="y"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="bg-blue-50/50 rounded-2xl p-4 border border-blue-100/50">
                            <p class="text-[11px] text-blue-600 font-semibold leading-relaxed">
                                <span class="bg-blue-600 text-white px-1.5 py-0.5 rounded mr-1 text-[9px] uppercase tracking-tighter">Info</span>
                                Isi Tanggal Awal & Akhir <b>atau</b> pilih Bulan & Tahun. Jika Bulan & Tahun dipilih, sistem akan menyesuaikan rentang tanggal secara otomatis.
                            </p>
                        </div>

                    </div>

                    <!-- SUBMIT -->
                    <div class="mt-10">
                        <button type="submit"
                            class="w-full px-12 py-4 bg-green-600 hover:bg-green-700 text-white font-extrabold rounded-2xl shadow-xl shadow-green-100 hover:shadow-green-200 transition-all active:scale-[0.98]">
                            Mulai Proses Rekapitulasi
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>

    <script>
        function rekapTransaksi() {
            return {
                startVal: '',
                startDisplay: '',
                endVal: '',
                endDisplay: '',
                bulan: '',
                tahun: '',

                calOpenStart: false,
                viewMonthStart: new Date().getMonth(),
                viewYearStart: String(new Date().getFullYear()),
                daysStart: [],

                calOpenEnd: false,
                viewMonthEnd: new Date().getMonth(),
                viewYearEnd: String(new Date().getFullYear()),
                daysEnd: [],

                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                years: [],

                init() {
                    const cy = new Date().getFullYear();
                    for (let i = cy - 5; i <= cy + 2; i++) {
                        this.years.push(String(i));
                    }
                    this.years.sort((a, b) => b - a);
                },

                formatTgl(iso) {
                    if (!iso) return '';
                    const parts = iso.split('-');
                    const d = parseInt(parts[2], 10);
                    const m = this.months[parseInt(parts[1], 10) - 1];
                    const y = parts[0];
                    return `${d} ${m} ${y}`;
                },

                onBulanTahunChange() {
                    if (!this.bulan || !this.tahun) return;
                    const y = parseInt(this.tahun);
                    const m = parseInt(this.bulan);
                    const lastDay = new Date(y, m, 0).getDate();

                    this.startVal = y + '-' + String(m).padStart(2, '0') + '-01';
                    this.endVal = y + '-' + String(m).padStart(2, '0') + '-' + String(lastDay).padStart(2, '0');

                    this.startDisplay = this.formatTgl(this.startVal);
                    this.endDisplay = this.formatTgl(this.endVal);
                },

                onTanggalChange() {
                    if (this.startVal || this.endVal) {
                        this.bulan = '';
                        this.tahun = '';
                    }
                },

                toggleCalendar(type) {
                    if (type === 'start') {
                        this.calOpenStart = !this.calOpenStart;
                        this.calOpenEnd = false;
                        if (this.calOpenStart) {
                            if (this.startVal) {
                                const d = new Date(this.startVal);
                                this.viewMonthStart = d.getMonth();
                                this.viewYearStart = String(d.getFullYear());
                            }
                            this.generateDays('start');
                        }
                    } else {
                        this.calOpenEnd = !this.calOpenEnd;
                        this.calOpenStart = false;
                        if (this.calOpenEnd) {
                            if (this.endVal) {
                                const d = new Date(this.endVal);
                                this.viewMonthEnd = d.getMonth();
                                this.viewYearEnd = String(d.getFullYear());
                            }
                            this.generateDays('end');
                        }
                    }
                },

                navMonth(type, delta) {
                    if (type === 'start') {
                        let m = this.viewMonthStart + delta;
                        let y = parseInt(this.viewYearStart);
                        if (m < 0) {
                            m = 11;
                            y--;
                        } else if (m > 11) {
                            m = 0;
                            y++;
                        }
                        this.viewMonthStart = m;
                        this.viewYearStart = String(y);
                        this.generateDays('start');
                    } else {
                        let m = this.viewMonthEnd + delta;
                        let y = parseInt(this.viewYearEnd);
                        if (m < 0) {
                            m = 11;
                            y--;
                        } else if (m > 11) {
                            m = 0;
                            y++;
                        }
                        this.viewMonthEnd = m;
                        this.viewYearEnd = String(y);
                        this.generateDays('end');
                    }
                },

                generateDays(type) {
                    const vm = (type === 'start') ? this.viewMonthStart : this.viewMonthEnd;
                    const vy = parseInt((type === 'start') ? this.viewYearStart : this.viewYearEnd);
                    const sel = (type === 'start') ? this.startVal : this.endVal;

                    const firstDay = new Date(vy, vm, 1).getDay();
                    const daysInMonth = new Date(vy, vm + 1, 0).getDate();
                    const res = [];

                    for (let i = 0; i < firstDay; i++) {
                        res.push({
                            id: 'p' + i,
                            date: null
                        });
                    }

                    for (let d = 1; d <= daysInMonth; d++) {
                        const dt = new Date(vy, vm, d);
                        const iso = vy + '-' + String(vm + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
                        const dow = dt.getDay();
                        const isW = (dow === 0 || dow === 6);
                        res.push({
                            id: iso,
                            date: iso,
                            dayNum: d,
                            enabled: !isW,
                            isWeekend: isW,
                            selected: iso === sel
                        });
                    }
                    if (type === 'start') this.daysStart = res;
                    else this.daysEnd = res;
                },

                selectDate(type, day) {
                    if (!day.enabled) return;
                    if (type === 'start') {
                        this.startVal = day.date;
                        this.startDisplay = this.formatTgl(day.date);
                        this.calOpenStart = false;
                    } else {
                        this.endVal = day.date;
                        this.endDisplay = this.formatTgl(day.date);
                        this.calOpenEnd = false;
                    }
                    this.onTanggalChange();
                },

                clearDate(type) {
                    if (type === 'start') {
                        this.startVal = '';
                        this.startDisplay = '';
                        this.calOpenStart = false;
                    } else {
                        this.endVal = '';
                        this.endDisplay = '';
                        this.calOpenEnd = false;
                    }
                },

                setToday(type) {
                    const now = new Date();
                    const dow = now.getDay();
                    if (dow === 0 || dow === 6) {
                        alert('Hari ini adalah akhir pekan (Sabtu/Minggu), silakan pilih hari kerja.');
                        return;
                    }
                    const iso = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
                    if (type === 'start') {
                        this.startVal = iso;
                        this.startDisplay = this.formatTgl(iso);
                        this.calOpenStart = false;
                    } else {
                        this.endVal = iso;
                        this.endDisplay = this.formatTgl(iso);
                        this.calOpenEnd = false;
                    }
                    this.onTanggalChange();
                },

                handleSubmit(e) {
                    if (!this.startVal && !this.endVal && !this.bulan && !this.tahun) {
                        alert('Isi Tanggal Awal & Akhir, atau pilih Bulan & Tahun!');
                        e.preventDefault();
                        return false;
                    }

                    if (this.startVal && this.endVal && this.startVal > this.endVal) {
                        alert('Tanggal Awal tidak boleh lebih besar dari Tanggal Akhir!');
                        e.preventDefault();
                        return false;
                    }

                    return true;
                }
            };
        }
    </script>

</body>

</html>