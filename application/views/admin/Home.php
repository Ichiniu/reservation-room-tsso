<?php
$session_id = $this->session->userdata('admin_username');

/* =======================
   BULAN (untuk label)
======================= */
$months = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

/* =======================
   AMBIL PARAM FILTER (GET)
   - Default: bulan & tahun saat ini
   - Jika user pilih "Semua": value="" => query jadi bulan= / tahun=
======================= */
$hasMonthParam = array_key_exists('bulan', $_GET);
$hasYearParam  = array_key_exists('tahun', $_GET);

$getMonth = $hasMonthParam ? trim((string)$_GET['bulan']) : null; // bisa "" untuk Semua
$getYear  = $hasYearParam  ? trim((string)$_GET['tahun'])  : null;

if ($getMonth !== null && $getMonth !== '' && !ctype_digit($getMonth)) $getMonth = '';
if ($getYear  !== null && $getYear  !== '' && !ctype_digit($getYear))  $getYear  = '';

/* kalau controller sudah set $selected_month/$selected_year, gunakan itu,
   tapi jika user mengirim GET, GET lebih diprioritaskan agar dropdown konsisten */
if ($hasMonthParam) $selected_month = $getMonth;
elseif (!isset($selected_month)) $selected_month = (string)(int)date('m');

if ($hasYearParam) $selected_year = $getYear;
elseif (!isset($selected_year)) $selected_year = (string)(int)date('Y');

/* =======================
   helper format tanggal Indonesia (11 Januari 2026)
======================= */
// Helper format_tanggal_indo sudah di-autoload

/* =======================
   helper rupiah
======================= */
function rupiah($n)
{
    if ($n === null || $n === '') return 'Rp 0';
    if (!is_numeric($n)) return (string)$n;
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
}

/* =======================
   helper jam HH:II (support 08:00:00 / 8 / 0800 / 08:00 - 17:00)
======================= */
function _time_to_hi($t)
{
    $t = trim((string)$t);
    if ($t === '') return '';

    if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $t)) {
        return substr($t, 0, 5);
    }

    if (preg_match('/^\d{1,2}$/', $t)) {
        $h = (int)$t;
        return sprintf('%02d:00', $h);
    }

    if (preg_match('/^\d{3,4}$/', $t)) {
        $t = str_pad($t, 4, '0', STR_PAD_LEFT);
        $h = substr($t, 0, 2);
        $m = substr($t, 2, 2);
        return $h . ':' . $m;
    }

    $ts = strtotime('1970-01-01 ' . $t);
    if ($ts) return date('H:i', $ts);

    return $t;
}

function formatJamHHII($jam)
{
    $jam = trim((string)$jam);
    if ($jam === '') return '-';

    if (str_contains($jam, '-')) {
        $parts = preg_split('/\s*-\s*/', $jam);
        $a = _time_to_hi($parts[0] ?? '');
        $b = _time_to_hi($parts[1] ?? '');
        if ($a !== '' && $b !== '') return $a . ' - ' . $b;
        if ($a !== '') return $a;
        if ($b !== '') return $b;
    }

    $single = _time_to_hi($jam);
    return $single !== '' ? $single : '-';
}

/* =======================
   hitung ringkasan (ambil dari data yang dikirim controller)
======================= */
$totalUsers  = (int)($total_users ?? (isset($list_user) ? count($list_user) : 0));
$totalGedung = (int)($total_gedung ?? (isset($list_gedung) ? count($list_gedung) : 0));

/* pending bookings dari inbox */
$pendingBookings = (int)($pending_bookings ?? 0);
if (!isset($pending_bookings) && isset($inbox) && is_array($inbox)) {
    foreach ($inbox as $it) {
        $status = '';
        if (is_object($it)) {
            if (isset($it->STATUS_VERIF)) $status = (string)$it->STATUS_VERIF;
            if ($status === '' && isset($it->STATUS)) $status = (string)$it->STATUS;
        } elseif (is_array($it)) {
            if (isset($it['STATUS_VERIF'])) $status = (string)$it['STATUS_VERIF'];
            if ($status === '' && isset($it['STATUS'])) $status = (string)$it['STATUS'];
        }
        $s = strtoupper(trim($status));
        if ($s === 'PENDING' || $s === 'MENUNGGU' || $s === 'WAITING') $pendingBookings++;
    }
    if ($pendingBookings === 0 && count($inbox) > 0) $pendingBookings = count($inbox);
}

/* revenue dari transaksi */
$totalRevenue = 0;
if (isset($total_revenue) && is_numeric($total_revenue)) {
    $totalRevenue = (float)$total_revenue;
} elseif (isset($transaksi) && is_array($transaksi)) {
    foreach ($transaksi as $t) {
        $val = null;
        if (is_object($t)) {
            $val = $t->TOTAL ?? $t->NOMINAL ?? $t->GRAND_TOTAL ?? $t->TOTAL_BAYAR ?? null;
        } elseif (is_array($t)) {
            $val = $t['TOTAL'] ?? $t['NOMINAL'] ?? $t['GRAND_TOTAL'] ?? $t['TOTAL_BAYAR'] ?? null;
        }
        if (is_numeric($val)) $totalRevenue += (float)$val;
    }
}

/* recent invoices */
$recent = $recent_invoices ?? $front_data ?? [];

/* label periode */
$selM = (string)$selected_month; // bisa ""
$selY = (string)$selected_year;  // bisa ""
$periodeLabel = 'Semua Periode';
if ($selM !== '' && $selY !== '') {
    $mInt = (int)$selM;
    $periodeLabel = (isset($months[$mInt]) ? $months[$mInt] : $selM) . ' ' . $selY;
} elseif ($selM === '' && $selY !== '') {
    $periodeLabel = 'Tahun ' . $selY;
} elseif ($selM !== '' && $selY === '') {
    $mInt = (int)$selM;
    $periodeLabel = (isset($months[$mInt]) ? $months[$mInt] : $selM) . ' • Semua Tahun';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Office</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-slate-100 min-h-screen">
    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-20 md:pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                    Welcome, <?= htmlspecialchars((string)($session_id ?? 'admin'), ENT_QUOTES, 'UTF-8'); ?>!
                </h1>
                <p class="text-sm text-slate-500 mt-1">Ringkasan data booking & transaksi.</p>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Total Users</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1"><?= (int)$totalUsers; ?></p>
                </div>
                <a href="<?= site_url('admin/list'); ?>"
                    class="mt-4 inline-flex w-full items-center justify-center px-3 py-2 rounded-xl bg-blue-600 text-white text-sm hover:bg-blue-700">
                    View Users
                </a>
            </div>


            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Pending Bookings</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1"><?= (int)$pendingBookings; ?></p>
                </div>
                <a href="<?= site_url('admin/transaksi'); ?>"
                    class="mt-4 inline-flex w-full items-center justify-center px-3 py-2 rounded-xl bg-amber-600 text-white text-sm hover:bg-amber-700">
                    Review Inbox
                </a>
            </div>

            <!-- REVENUE + FILTER (AUTO SUBMIT, TANPA TOMBOL) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <p class="text-xs font-semibold text-slate-500">Revenue</p>

                        <!-- MINI FILTER (ICON SAJA) -->
                        <form id="revFilterForm" action="<?= site_url('admin/dashboard'); ?>" method="GET"
                            class="flex items-center gap-1 bg-slate-50 p-1 rounded-lg border border-slate-200">

                            <!-- icon filter (bukan tombol) -->
                            <span class="material-icons-outlined text-[14px] text-slate-500 ml-1">filter_alt</span>

                            <select id="revMonth" name="bulan"
                                class="bg-transparent border-none text-[10px] font-bold text-slate-700 focus:ring-0 outline-none cursor-pointer p-0 px-1">
                                <!-- <option value="" <?= ($selM === '') ? 'selected' : ''; ?>>Semua</option> -->
                                <?php
                                $shortMonths = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                foreach ($shortMonths as $num => $name): ?>
                                    <option value="<?= $num; ?>" <?= ((string)$num === (string)$selM) ? 'selected' : ''; ?>>
                                        <?= $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select id="revYear" name="tahun"
                                class="bg-transparent border-none text-[10px] font-bold text-slate-700 focus:ring-0 outline-none cursor-pointer p-0 px-1">
                                <!-- <option value="" <?= ($selY === '') ? 'selected' : ''; ?>>Semua</option> -->
                                <?php for ($y = (int)date('Y'); $y >= 2023; $y--): ?>
                                    <option value="<?= $y; ?>" <?= ((string)$y === (string)$selY) ? 'selected' : ''; ?>>
                                        <?= $y; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </form>
                    </div>

                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider mb-1">
                        Periode <?= htmlspecialchars($periodeLabel, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="text-2xl font-bold text-slate-900 break-words"><?= rupiah((string)$totalRevenue); ?></p>
                </div>

                <a href="<?= site_url('admin/pembayaran'); ?>"
                    class="mt-4 inline-flex w-full items-center justify-center px-3 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 shadow-sm shadow-rose-200">
                    View Transaksi
                </a>
            </div>
        </div>

        <!-- CHART & RECENT BOOKINGS (STACKED FULL WIDTH) -->
        <div class="max-w-7xl mx-auto flex flex-col gap-6">

            <!-- CHART SECTION (Statistik Departemen) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col w-full">
                <div class="p-5 border-b border-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">Statistik Departemen</h2>
                    <p class="text-sm text-slate-500">Grafik jumlah booking per departemen (Status: Confirmed).</p>
                </div>
                <div class="p-5 flex-1 min-h-[400px]">
                    <div class="w-full h-full relative">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- RECENT BOOKINGS -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm w-full">

                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Recent Bookings</h2>
                        <p class="text-sm text-slate-500">Jadwal ruangan yang sudah ter-booking (SUBMITTED).</p>
                    </div>
                    <a href="<?= site_url('admin/pemesanan2'); ?>"
                        class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                        View All
                    </a>
                </div>

                <!-- FILTER -->
                <div class="px-5 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Invoice / ID</label>
                            <input id="filterKode" type="text" placeholder="contoh: PMSN00094 / 94"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Filter User</label>
                            <select id="filterUser"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-slate-300">
                                <option value="">Semua User</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Departemen</label>
                            <select id="filterDept"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-slate-300">
                                <option value="">Semua Departemen</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button id="resetFilter"
                                class="w-full px-4 py-2 rounded-xl bg-slate-200 text-slate-800 text-sm hover:bg-slate-300">
                                Reset
                            </button>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-slate-500">
                        * Filter berjalan otomatis saat mengetik / memilih dropdown. Pagination mengikuti hasil filter.
                    </p>
                </div>

                <!-- TABLE -->
                <div class="px-5 pb-5">
                    <div id="tableScroll"
                        class="overflow-x-auto max-h-[420px] overflow-y-auto relative border border-slate-200 rounded-xl">
                        <table class="w-full text-sm bg-white">
                            <!-- <thead class="sticky top-0 z-20 bg-slate-100 shadow-sm">
                                <tr class="border-b border-slate-200">
                                    <th class="px-4 py-3 text-center">No</th>
                                    <th class="px-4 py-3 text-center">Invoice</th>
                                    <th class="px-4 py-3 text-center">Ruangan</th>
                                    <th class="px-4 py-3 text-center">User</th>
                                    <th class="px-4 py-3 text-center">Departemen</th>
                                    <th class="px-4 py-3 text-center">Tanggal</th>
                                    <th class="px-4 py-3 text-center">Jam</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead> -->
                            <!-- TABLE -->
                            <div class="px-5 pb-5">
                                <div id="tableScroll"
                                    class="overflow-x-auto max-h-[420px] overflow-y-auto relative border border-slate-200 rounded-xl">
                                    <table class="min-w-[800px] w-full text-sm bg-white">
                                        <thead class="sticky top-0 z-20 bg-slate-100 shadow-sm">
                                            <tr class="border-b border-slate-200">
                                                <th class="px-4 py-3 text-center">No</th>
                                                <th class="px-4 py-3 text-center">Invoice</th>
                                                <th class="px-4 py-3 text-center">Ruangan</th>
                                                <th class="px-4 py-3 text-center">User</th>
                                                <th class="px-4 py-3 text-center">Departemen</th>
                                                <th class="px-4 py-3 text-center">Tanggal</th>
                                                <th class="px-4 py-3 text-center">Jam</th>
                                                <th class="px-4 py-3 text-center">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody id="tableBody">
                                            <?php if (!empty($front_data)): ?>
                                                <?php foreach ($front_data as $data): ?>
                                                    <?php
                                                    $rawId = $data->ID_PEMESANAN ?? '';
                                                    $idNum = (int)preg_replace('/\D+/', '', (string)$rawId);

                                                    $invoice = (str_starts_with((string)$rawId, 'PMSN'))
                                                        ? (string)$rawId
                                                        : ($idNum ? 'PMSN000' . $idNum : (string)$rawId);

                                                    $username   = $data->USERNAME ?? '-';
                                                    $namaGedung = $data->NAMA_GEDUNG ?? '-';

                                                    $tglRaw  = $data->TANGGAL_PEMESANAN ?? '';
                                                    $tglIndo = format_tanggal_indo($tglRaw);
                                                    $mulai   = trim((string)($data->JAM_MULAI ?? ''));
                                                    $selesai = trim((string)($data->JAM_SELESAI ?? ''));

                                                    $DEFAULT_DURASI_JAM = 2;
                                                    $jamFix = '-';

                                                    $toDot = function ($hi) {
                                                        $hi = trim((string)$hi);
                                                        if ($hi === '' || $hi === '-') return '';
                                                        return str_replace(':', '.', $hi);
                                                    };

                                                    $addHours = function ($hi, $hours) {
                                                        $hi = trim((string)$hi);
                                                        if ($hi === '') return '';
                                                        $dt = DateTime::createFromFormat('H:i', $hi);
                                                        if (!$dt) return '';
                                                        $dt->modify('+' . (int)$hours . ' hours');
                                                        return $dt->format('H:i');
                                                    };

                                                    if ($mulai !== '' && strpos($mulai, '-') !== false) {
                                                        $parts = array_map('trim', preg_split('/\s*-\s*/', $mulai, 2));
                                                        $a = _time_to_hi($parts[0] ?? '');
                                                        $b = _time_to_hi($parts[1] ?? '');
                                                        if ($a !== '' && $b === '') $b = $addHours($a, $DEFAULT_DURASI_JAM);
                                                        $a = $toDot($a);
                                                        $b = $toDot($b);
                                                        if ($a !== '' && $b !== '') $jamFix = $a . ' - ' . $b;
                                                        elseif ($a !== '') $jamFix = $a;
                                                    } else {
                                                        $a = _time_to_hi($mulai);
                                                        $b = _time_to_hi($selesai);
                                                        if ($a !== '' && $b === '') $b = $addHours($a, $DEFAULT_DURASI_JAM);
                                                        $a = $toDot($a);
                                                        $b = $toDot($b);
                                                        if ($a !== '' && $b !== '') $jamFix = $a . ' - ' . $b;
                                                        elseif ($a !== '') $jamFix = $a;
                                                    }
                                                    ?>

                                                    <tr class="table-row hover:bg-slate-50" data-idnum="<?= (int)$idNum; ?>"
                                                        data-kode="<?= htmlspecialchars($invoice, ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-user="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-dept="<?= htmlspecialchars(($data->departemen ?? '-'), ENT_QUOTES, 'UTF-8'); ?>">
                                                        <td class="px-4 py-3 text-center cell-no">1</td>

                                                        <td class="px-4 py-3 text-center font-semibold cell-kode">
                                                            <?= htmlspecialchars($invoice, ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>

                                                        <td class="px-4 py-3 text-center">
                                                            <?= htmlspecialchars($namaGedung, ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>

                                                        <td class="px-4 py-3 text-center cell-user">
                                                            <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>

                                                        <td class="px-4 py-3 text-center">
                                                            <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-bold uppercase">
                                                                <?= htmlspecialchars(!empty($data->departemen) ? (string)$data->departemen : '-', ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>

                                                        <td class="px-4 py-3 text-center">
                                                            <?= htmlspecialchars($tglIndo, ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>

                                                        <td class="px-4 py-3 text-center">
                                                            <?= htmlspecialchars((string)$jamFix, ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>

                                                        <td class="px-4 py-3 text-center">
                                                            <?php if ($idNum > 0): ?>
                                                                <a href="<?= site_url('admin/detail_pemesanan/' . $idNum); ?>"
                                                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                                    Detail
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-xs text-slate-400">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                                                        Belum ada jadwal terbooking (SUBMITTED).
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- PAGINATION -->
                                <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <button id="prevBtn"
                                        class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 disabled:opacity-40 disabled:cursor-not-allowed">
                                        Prev
                                    </button>

                                    <span id="pageInfo" class="text-sm text-slate-600 text-center"></span>

                                    <div class="flex items-center gap-3">
                                        <select id="rowsPerPage" class="rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white">
                                            <option value="5">5 rows</option>
                                            <option value="10" selected>10 rows</option>
                                            <option value="25">25 rows</option>
                                        </select>

                                        <button id="nextBtn"
                                            class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 disabled:opacity-40 disabled:cursor-not-allowed">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="p-4 flex items-center justify-between text-xs text-slate-500 border-t border-slate-200">
                                <span>© <?= date('Y'); ?> Smart Office • Admin Panel</span>
                                <span class="hidden sm:inline">Modern Dashboard</span>
                            </div> -->
                    </div>
                </div>
                

                <script>
                    // AUTO SUBMIT FILTER REVENUE (tanpa tombol)
                    (function() {
                        var f = document.getElementById('revFilterForm');
                        if (!f) return;

                        var m = document.getElementById('revMonth');
                        var y = document.getElementById('revYear');

                        function submitNow() {
                            // langsung submit saat pilih dropdown
                            f.submit();
                        }

                        if (m) m.addEventListener('change', submitNow);
                        if (y) y.addEventListener('change', submitNow);
                    })();
                </script>

                <script>
                    (function() {
                        var tbody = document.getElementById('tableBody');
                        if (!tbody) return;

                        var allRows = Array.prototype.slice.call(document.querySelectorAll('.table-row'));
                        var rowsPerPageSelect = document.getElementById('rowsPerPage');
                        var pageInfo = document.getElementById('pageInfo');
                        var prevBtn = document.getElementById('prevBtn');
                        var nextBtn = document.getElementById('nextBtn');
                        var scrollBox = document.getElementById('tableScroll');

                        var filterKode = document.getElementById('filterKode');
                        var filterUser = document.getElementById('filterUser');
                        var filterDept = document.getElementById('filterDept');
                        var resetFilterBtn = document.getElementById('resetFilter');

                        if (!allRows.length) return;

                        var currentPage = 1;
                        var rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;
                        var activeRows = allRows.slice();

                        // SORT by invoice numeric DESC (terbaru dulu)
                        function sortRowsByIdDesc() {
                            allRows.sort(function(a, b) {
                                var ida = parseInt(a.getAttribute('data-idnum') || '0', 10);
                                var idb = parseInt(b.getAttribute('data-idnum') || '0', 10);
                                return idb - ida;
                            });

                            allRows.forEach(function(r) {
                                tbody.appendChild(r);
                            });
                        }

                        function buildUserDropdown() {
                            var users = {};
                            var depts = {};
                            allRows.forEach(function(r) {
                                var u = (r.getAttribute('data-user') || '').trim();
                                if (u && u !== '-') users[u] = true;

                                var d = (r.getAttribute('data-dept') || '').trim();
                                if (d && d !== '-') depts[d] = true;
                            });

                            // User select
                            var keys = Object.keys(users).sort(function(a, b) {
                                return a.localeCompare(b);
                            });
                            filterUser.innerHTML = '<option value="">Semua User</option>';
                            keys.forEach(function(u) {
                                var opt = document.createElement('option');
                                opt.value = u;
                                opt.textContent = u;
                                filterUser.appendChild(opt);
                            });

                            // Dept select
                            var dkeys = Object.keys(depts).sort(function(a, b) {
                                return a.localeCompare(b);
                            });
                            filterDept.innerHTML = '<option value="">Semua Departemen</option>';
                            dkeys.forEach(function(d) {
                                var opt = document.createElement('option');
                                opt.value = d;
                                opt.textContent = d;
                                filterDept.appendChild(opt);
                            });
                        }

                        function normalize(s) {
                            return (s || '').toString().trim().toLowerCase().replace(/\s+/g, '');
                        }

                        function applyFilter() {
                            var kodeVal = normalize(filterKode.value);
                            var userVal = (filterUser.value || '').trim();
                            var deptVal = (filterDept.value || '').trim();

                            activeRows = allRows.filter(function(row) {
                                var kode = normalize(row.getAttribute('data-kode'));
                                var user = (row.getAttribute('data-user') || '').trim();
                                var dept = (row.getAttribute('data-dept') || '').trim();

                                var okKode = !kodeVal ? true : (kode.indexOf(kodeVal) !== -1);
                                var okUser = !userVal ? true : (user === userVal);
                                var okDept = !deptVal ? true : (dept === deptVal);

                                return okKode && okUser && okDept;
                            });

                            currentPage = 1;
                            renderTable();
                        }

                        function resetFilter() {
                            filterKode.value = '';
                            filterUser.value = '';
                            filterDept.value = '';
                            activeRows = allRows.slice();
                            currentPage = 1;
                            renderTable();
                        }

                        function renderTable() {
                            var total = activeRows.length;
                            var totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
                            if (currentPage > totalPages) currentPage = totalPages;

                            allRows.forEach(function(r) {
                                r.style.display = 'none';
                            });

                            var start = (currentPage - 1) * rowsPerPage;
                            var end = start + rowsPerPage;

                            var visibleNo = start + 1;
                            activeRows.forEach(function(row, idx) {
                                if (idx >= start && idx < end) {
                                    row.style.display = '';
                                    var cell = row.querySelector('.cell-no');
                                    if (cell) cell.textContent = visibleNo++;
                                }
                            });

                            prevBtn.disabled = (currentPage === 1);
                            nextBtn.disabled = (currentPage === totalPages);

                            var showingFrom = (total === 0) ? 0 : (start + 1);
                            var showingTo = Math.min(end, total);
                            pageInfo.textContent = 'Page ' + currentPage + ' of ' + totalPages + ' • Showing ' + showingFrom +
                                '-' + showingTo + ' of ' + total;

                            if (scrollBox) scrollBox.scrollTop = 0;
                        }

                        filterKode.addEventListener('input', applyFilter);
                        filterUser.addEventListener('change', applyFilter);
                        filterDept.addEventListener('change', applyFilter);
                        resetFilterBtn.addEventListener('click', resetFilter);

                        rowsPerPageSelect.addEventListener('change', function() {
                            rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;
                            currentPage = 1;
                            renderTable();
                        });

                        prevBtn.addEventListener('click', function() {
                            if (currentPage > 1) {
                                currentPage--;
                                renderTable();
                            }
                        });

                        nextBtn.addEventListener('click', function() {
                            var totalPages = Math.max(1, Math.ceil(activeRows.length / rowsPerPage));
                            if (currentPage < totalPages) {
                                currentPage++;
                                renderTable();
                            }
                        });

                        // init
                        sortRowsByIdDesc();
                        buildUserDropdown();
                        activeRows = allRows.slice();
                        renderTable();
                    })();
                </script>

</body>

<script>
    // Data from Controller
    const rawData = <?= json_encode($top_departments ?? []); ?>;

    // Process Data
    const labels = rawData.map(item => item.label);
    const dataValues = rawData.map(item => item.total);

    // Generate Colors (Dynamic & Repeating)
    const baseColors = [
        '#3B82F6', // Blue
        '#10B981', // Emerald
        '#F59E0B', // Amber
        '#EF4444', // Red
        '#8B5CF6', // Violet
        '#6366F1', // Indigo
        '#EC4899', // Pink
        '#14B8A6', // Teal
        '#F97316', // Orange
        '#64748B' // Slate
    ];

    // Create color array matching data length by repeating base colors
    const backgroundColors = labels.map((_, i) => baseColors[i % baseColors.length]);

    const ctx = document.getElementById('deptChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Booking',
                data: dataValues,
                backgroundColor: backgroundColors,
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 'flex',
                maxBarThickness: 40
            }]
        },
        options: {
            indexAxis: 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' Booking';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>

</html>