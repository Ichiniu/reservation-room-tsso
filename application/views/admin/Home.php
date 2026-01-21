<?php
$session_id = $this->session->userdata('admin_username');

/* ===== helper format tanggal Indonesia (11 Januari 2026) ===== */
function formatTanggalIndo($tgl)
{
    if (empty($tgl)) return '-';

    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    $ts = strtotime($tgl);
    if (!$ts) return $tgl;

    $d = date('d', $ts);
    $m = (int) date('n', $ts);
    $y = date('Y', $ts);

    return $d . ' ' . $bulan[$m] . ' ' . $y;
}

/* ===== helper rupiah ===== */
function rupiah($n)
{
    if ($n === null || $n === '') return 'Rp 0';
    if (!is_numeric($n)) return (string)$n;
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
}

/* ===== helper jam HH:II (support 08:00:00 / 8 / 0800 / 08:00 - 17:00) ===== */
function _time_to_hi($t)
{
    $t = trim((string)$t);
    if ($t === '') return '';

    // 08:00:00 atau 08:00
    if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $t)) {
        return substr($t, 0, 5);
    }

    // "8" atau "08"
    if (preg_match('/^\d{1,2}$/', $t)) {
        $h = (int)$t;
        return sprintf('%02d:00', $h);
    }

    // "800" / "0800" / "1730"
    if (preg_match('/^\d{3,4}$/', $t)) {
        $t = str_pad($t, 4, '0', STR_PAD_LEFT);
        $h = substr($t, 0, 2);
        $m = substr($t, 2, 2);
        return $h . ':' . $m;
    }

    // coba parse via strtotime
    $ts = strtotime('1970-01-01 ' . $t);
    if ($ts) return date('H:i', $ts);

    return $t;
}

function formatJamHHII($jam)
{
    $jam = trim((string)$jam);
    if ($jam === '') return '-';

    // kalau range: "08:00 - 17:00" atau "08:00-17:00"
    if (strpos($jam, '-') !== false) {
        $parts = preg_split('/\s*-\s*/', $jam);
        $a = isset($parts[0]) ? _time_to_hi($parts[0]) : '';
        $b = isset($parts[1]) ? _time_to_hi($parts[1]) : '';
        if ($a !== '' && $b !== '') return $a . ' - ' . $b;
        if ($a !== '') return $a;
        if ($b !== '') return $b;
    }

    // single time
    $single = _time_to_hi($jam);
    return $single !== '' ? $single : '-';
}

/* ===== hitung ringkasan (ambil dari data yang dikirim controller) ===== */
$totalUsers  = isset($total_users) ? (int)$total_users : (isset($list_user) ? (int)count($list_user) : 0);
$totalGedung = isset($total_gedung) ? (int)$total_gedung : (isset($list_gedung) ? (int)count($list_gedung) : 0);

/* pending bookings dari inbox */
$pendingBookings = 0;
if (isset($pending_bookings)) {
    $pendingBookings = (int)$pending_bookings;
} elseif (isset($inbox) && is_array($inbox)) {
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
            if (isset($t->TOTAL)) $val = $t->TOTAL;
            elseif (isset($t->NOMINAL)) $val = $t->NOMINAL;
            elseif (isset($t->GRAND_TOTAL)) $val = $t->GRAND_TOTAL;
            elseif (isset($t->TOTAL_BAYAR)) $val = $t->TOTAL_BAYAR;
        } elseif (is_array($t)) {
            if (isset($t['TOTAL'])) $val = $t['TOTAL'];
            elseif (isset($t['NOMINAL'])) $val = $t['NOMINAL'];
            elseif (isset($t['GRAND_TOTAL'])) $val = $t['GRAND_TOTAL'];
            elseif (isset($t['TOTAL_BAYAR'])) $val = $t['TOTAL_BAYAR'];
        }
        if (is_numeric($val)) $totalRevenue += (float)$val;
    }
}

/* recent invoices */
$recent = array();
if (isset($recent_invoices) && is_array($recent_invoices)) {
    $recent = $recent_invoices;
} elseif (isset($front_data) && is_array($front_data)) {
    $recent = $front_data;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Office</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/home/style.css') ?>" rel="stylesheet">
</head>

<body class="bg-slate-100 min-h-screen">
    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-20 md:pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                    Welcome, <?= htmlspecialchars(($session_id ? $session_id : 'admin'), ENT_QUOTES, 'UTF-8'); ?>!
                </h1>
                <p class="text-sm text-slate-500 mt-1">Ringkasan data booking & transaksi.</p>
            </div>


        </div>

        <!-- STAT CARDS -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-500">Total Users</p>
                <p class="text-3xl font-bold text-slate-900 mt-1"><?= (int)$totalUsers; ?></p>
                <a href="<?= site_url('admin/list'); ?>"
                    class="mt-4 inline-flex w-full items-center justify-center px-3 py-2 rounded-xl bg-blue-600 text-white text-sm hover:bg-blue-700">
                    View Users
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-500">Total Ruangan</p>
                <p class="text-3xl font-bold text-slate-900 mt-1"><?= (int)$totalGedung; ?></p>
                <a href="<?= site_url('admin/gedung'); ?>"
                    class="mt-4 inline-flex w-full items-center justify-center px-3 py-2 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                    View Ruangan
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-500">Pending Bookings</p>
                <p class="text-3xl font-bold text-slate-900 mt-1"><?= (int)$pendingBookings; ?></p>
                <a href="<?= site_url('admin/transaksi'); ?>"
                    class="mt-4 inline-flex w-full items-center justify-center px-3 py-2 rounded-xl bg-amber-600 text-white text-sm hover:bg-amber-700">
                    Review Inbox
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-500">Total Revenue</p>
                <p class="text-2xl md:text-3xl font-bold text-slate-900 mt-1"><?= rupiah($totalRevenue); ?></p>
                <a href="<?= site_url('admin/pembayaran'); ?>"
                    class="mt-4 inline-flex w-full items-center justify-center px-3 py-2 rounded-xl bg-rose-600 text-white text-sm hover:bg-rose-700">
                    View Transaksi
                </a>
            </div>
        </div>

        <!-- RECENT BOOKINGS (INVOICE TERBARU) -->
        <!-- RECENT BOOKINGS (JADWAL TERBOOKING - SUBMITTED) -->
        <div class="max-w-7xl mx-auto bg-white rounded-2xl border border-slate-200 shadow-sm">

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
                        <thead class="sticky top-0 z-20 bg-slate-100 shadow-sm">
                            <tr class="border-b border-slate-200">
                                <th class="px-4 py-3 text-center">No</th>
                                <th class="px-4 py-3 text-center">Invoice</th>
                                <th class="px-4 py-3 text-center">Ruangan</th>
                                <th class="px-4 py-3 text-center">User</th>
                                <th class="px-4 py-3 text-center">Tanggal</th>
                                <th class="px-4 py-3 text-center">Jam</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="tableBody">
                            <?php if (!empty($front_data)): ?>
                            <?php foreach ($front_data as $data): ?>
                            <?php
                                    // AMAN: jangan panggil properti sebagai function
                                    $rawId = isset($data->ID_PEMESANAN) ? $data->ID_PEMESANAN : '';
                                    $idNum = (int)preg_replace('/\D+/', '', (string)$rawId);

                                    // invoice PMSN000xx
                                    $invoice = (stripos((string)$rawId, 'PMSN') === 0) ? (string)$rawId : ($idNum ? 'PMSN000' . $idNum : (string)$rawId);

                                    $username   = isset($data->USERNAME) ? $data->USERNAME : '-';
                                    $namaGedung = isset($data->NAMA_GEDUNG) ? $data->NAMA_GEDUNG : '-';

                                    $tglRaw  = isset($data->TANGGAL_PEMESANAN) ? $data->TANGGAL_PEMESANAN : '';
                                    $tglIndo = formatTanggalIndo($tglRaw);
                                    $mulai   = isset($data->JAM_MULAI) ? trim((string)$data->JAM_MULAI) : '';
                                    $selesai = isset($data->JAM_SELESAI) ? trim((string)$data->JAM_SELESAI) : '';

                                    $DEFAULT_DURASI_JAM = 2; // <-- ubah kalau mau default lain
                                    $jamFix = '-';

                                    $toDot = function ($hi) {
                                        $hi = trim((string)$hi);
                                        if ($hi === '' || $hi === '-') return '';
                                        return str_replace(':', '.', $hi);
                                    };

                                    $addHours = function ($hi, $hours) {
                                        // $hi format "HH:II"
                                        $hi = trim((string)$hi);
                                        if ($hi === '') return '';
                                        $dt = DateTime::createFromFormat('H:i', $hi);
                                        if (!$dt) return '';
                                        $dt->modify('+' . (int)$hours . ' hours');
                                        return $dt->format('H:i');
                                    };

                                    if ($mulai !== '' && strpos($mulai, '-') !== false) {
                                        // Kalau JAM_MULAI sudah berupa range: "08:00 - 10:00" atau "08.00 - 10.00"
                                        $parts = array_map('trim', preg_split('/\s*-\s*/', $mulai, 2));
                                        $a = isset($parts[0]) ? _time_to_hi($parts[0]) : '';
                                        $b = isset($parts[1]) ? _time_to_hi($parts[1]) : '';

                                        if ($a !== '' && $b === '') $b = $addHours($a, $DEFAULT_DURASI_JAM);

                                        $a = $toDot($a);
                                        $b = $toDot($b);

                                        if ($a !== '' && $b !== '') $jamFix = $a . ' - ' . $b;
                                        elseif ($a !== '') $jamFix = $a; // fallback terakhir
                                    } else {
                                        // Kalau JAM_MULAI & JAM_SELESAI terpisah
                                        $a = _time_to_hi($mulai);
                                        $b = _time_to_hi($selesai);

                                        if ($a !== '' && $b === '') $b = $addHours($a, $DEFAULT_DURASI_JAM);

                                        $a = $toDot($a);
                                        $b = $toDot($b);

                                        if ($a !== '' && $b !== '') $jamFix = $a . ' - ' . $b;
                                        elseif ($a !== '') $jamFix = $a; // fallback terakhir
                                    }
                                    ?>

                            <tr class="table-row hover:bg-slate-50" data-idnum="<?= (int)$idNum; ?>"
                                data-kode="<?= htmlspecialchars($invoice, ENT_QUOTES, 'UTF-8'); ?>"
                                data-user="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
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
                                    <?= htmlspecialchars($tglIndo, ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <?= htmlspecialchars($jamFix, ENT_QUOTES, 'UTF-8'); ?>
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

            <div class="p-4 flex items-center justify-between text-xs text-slate-500 border-t border-slate-200">
                <span>© <?= date('Y'); ?> Smart Office • Admin Panel</span>
                <span class="hidden sm:inline">Modern Dashboard</span>
            </div>
        </div>

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

                // urutkan DOM sesuai sort
                allRows.forEach(function(r) {
                    tbody.appendChild(r);
                });
            }

            function buildUserDropdown() {
                var users = {};
                allRows.forEach(function(r) {
                    var u = (r.getAttribute('data-user') || '').trim();
                    if (u && u !== '-') users[u] = true;
                });

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
            }

            function normalize(s) {
                return (s || '').toString().trim().toLowerCase().replace(/\s+/g, '');
            }

            function applyFilter() {
                var kodeVal = normalize(filterKode.value); // bisa PMSN00094 atau 94
                var userVal = (filterUser.value || '').trim();

                activeRows = allRows.filter(function(row) {
                    var kode = normalize(row.getAttribute('data-kode'));
                    var user = (row.getAttribute('data-user') || '').trim();

                    var okKode = !kodeVal ? true : (kode.indexOf(kodeVal) !== -1);
                    var okUser = !userVal ? true : (user === userVal);

                    return okKode && okUser;
                });

                currentPage = 1;
                renderTable();
            }

            function resetFilter() {
                filterKode.value = '';
                filterUser.value = '';
                activeRows = allRows.slice();
                currentPage = 1;
                renderTable();
            }

            function renderTable() {
                var total = activeRows.length;
                var totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
                if (currentPage > totalPages) currentPage = totalPages;

                // hide all
                allRows.forEach(function(r) {
                    r.style.display = 'none';
                });

                var start = (currentPage - 1) * rowsPerPage;
                var end = start + rowsPerPage;

                // show active in page
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

            // events
            filterKode.addEventListener('input', applyFilter);
            filterUser.addEventListener('change', applyFilter);
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

</html>