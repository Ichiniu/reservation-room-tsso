<?php
// ==========================
// Helper format tanggal Indonesia (tanpa array [] biar aman PHP lama)
// ==========================
function formatTanggalIndo($tgl)
{
    if (empty($tgl)) {
        return '-';
    }

    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );

    $ts = strtotime($tgl);
    if (!$ts) {
        return $tgl;
    }

    $d = date('d', $ts);
    $m = (int) date('n', $ts);
    $y = date('Y', $ts);

    return $d . ' ' . $bulan[$m] . ' ' . $y;
}

// pending
$pendingCount = 0;
if (isset($result) && is_array($result)) {
    $pendingCount = count($result);
}

// ==========================
// DATA CHART dari $pemesanan
// ==========================
$statusCounts = array();
$roomCounts   = array();
$dailyCounts  = array();

// 7 hari terakhir
for ($i = 6; $i >= 0; $i--) {
    $key = date('Y-m-d', strtotime('-' . $i . ' day'));
    $dailyCounts[$key] = 0;
}

if (isset($pemesanan) && is_array($pemesanan)) {
    foreach ($pemesanan as $r) {

        // status
        $status = '-';
        if (isset($r['STATUS']) && $r['STATUS'] !== '') {
            $status = strtoupper(trim((string)$r['STATUS']));
        }
        if (!isset($statusCounts[$status])) {
            $statusCounts[$status] = 0;
        }
        $statusCounts[$status]++;

        // ruangan
        $room = '-';
        if (isset($r['NAMA_GEDUNG']) && $r['NAMA_GEDUNG'] !== '') {
            $room = (string)$r['NAMA_GEDUNG'];
        }
        if (!isset($roomCounts[$room])) {
            $roomCounts[$room] = 0;
        }
        $roomCounts[$room]++;

        // daily 7 hari
        $tglRaw = '';
        if (isset($r['TANGGAL_PEMESANAN']) && $r['TANGGAL_PEMESANAN'] !== '') {
            $tglRaw = (string)$r['TANGGAL_PEMESANAN'];
        }
        if ($tglRaw !== '') {
            $key = date('Y-m-d', strtotime($tglRaw));
            if (isset($dailyCounts[$key])) {
                $dailyCounts[$key]++;
            }
        }
    }
}

// top 5 ruangan
arsort($roomCounts);
$topRooms = array_slice($roomCounts, 0, 5, true);
ksort($statusCounts);

// untuk JS
$statusLabels = array();
$statusValues = array();
foreach ($statusCounts as $k => $v) {
    $statusLabels[] = $k;
    $statusValues[] = (int)$v;
}

$dayLabels = array();
$dayValues = array();
foreach ($dailyCounts as $k => $v) {
    $dayLabels[] = $k;
    $dayValues[] = (int)$v;
}

$roomLabels = array();
$roomValues = array();
foreach ($topRooms as $k => $v) {
    $roomLabels[] = $k;
    $roomValues[] = (int)$v;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
    #tableWrap::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    #tableWrap::-webkit-scrollbar-thumb {
        background: rgba(100, 116, 139, .35);
        border-radius: 999px;
    }

    #tableWrap::-webkit-scrollbar-track {
        background: rgba(148, 163, 184, .12);
        border-radius: 999px;
    }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">
    <div class="flex min-h-screen">

        <?php $this->load->view('admin/partials/sidebar'); ?>

        <main class="flex-1 p-6 overflow-y-auto">

            <!-- TOP BAR -->
            <div class="mb-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Dashboard Admin</h1>
                            <p class="text-slate-500">Pantau booking, user, dan aktivitas secara cepat.</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Online
                            </span>
                            <span
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                                <span class="font-semibold text-slate-700">Update:</span>
                                <span class="text-slate-600"><?php echo date('d M Y H:i'); ?> WIB</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="h-1 bg-indigo-600"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600">Total Users</p>
                                <div class="mt-2 text-3xl font-extrabold text-slate-900"><?php echo (int)$total_user; ?>
                                </div>
                            </div>
                            <div
                                class="h-11 w-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700">
                                <span class="material-icons">group</span>
                            </div>
                        </div>
                        <a href="<?php echo site_url('admin/list_user'); ?>"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white py-2.5 text-sm font-semibold hover:bg-indigo-700 transition">
                            View Users <span class="material-icons text-base">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="h-1 bg-emerald-600"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600">Total Ruang</p>
                                <div class="mt-2 text-3xl font-extrabold text-slate-900">
                                    <?php echo (int)$total_gedung; ?></div>
                            </div>
                            <div
                                class="h-11 w-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700">
                                <span class="material-icons">meeting_room</span>
                            </div>
                        </div>
                        <a href="<?php echo site_url('admin/list_gedung'); ?>"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 text-white py-2.5 text-sm font-semibold hover:bg-emerald-700 transition">
                            View Ruang <span class="material-icons text-base">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden relative">
                    <div class="h-1 bg-orange-600"></div>
                    <div class="p-5">
                        <div
                            class="absolute top-4 right-4 rounded-full bg-rose-600 text-white text-xs px-2.5 py-1 font-bold">
                            <?php echo (int)$pendingCount; ?>
                        </div>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600">Pending Bookings</p>
                                <div class="mt-2 text-3xl font-extrabold text-slate-900">
                                    <?php echo (int)$pendingCount; ?></div>
                            </div>
                            <div
                                class="h-11 w-11 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-700">
                                <span class="material-icons">pending_actions</span>
                            </div>
                        </div>
                        <a href="<?php echo site_url('admin/transaksi'); ?>"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 text-white py-2.5 text-sm font-semibold hover:bg-orange-700 transition">
                            Review <span class="material-icons text-base">assignment</span>
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="h-1 bg-rose-600"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600">Total Revenue</p>
                                <div class="mt-2 text-2xl font-extrabold text-slate-900">
                                    Rp <?php echo number_format((float)$total_transaksi, 0, ',', '.'); ?>
                                </div>
                            </div>
                            <div
                                class="h-11 w-11 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-700">
                                <span class="material-icons">payments</span>
                            </div>
                        </div>
                        <a href="<?php echo site_url('admin/rekap_transaksi'); ?>"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 text-white py-2.5 text-sm font-semibold hover:bg-rose-700 transition">
                            View <span class="material-icons text-base">receipt_long</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- ===================== GRAPHICS ===================== -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-extrabold text-slate-900">Booking per Status</h3>
                        <span class="text-xs text-slate-500">Ringkasan</span>
                    </div>
                    <div class="h-[260px]">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 xl:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-extrabold text-slate-900">Booking 7 Hari Terakhir</h3>
                        <span class="text-xs text-slate-500">Trend</span>
                    </div>
                    <div class="h-[260px]">
                        <canvas id="chartDaily"></canvas>
                    </div>
                </section>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-extrabold text-slate-900">Top Ruangan</h3>
                        <span class="text-xs text-slate-500">Top 5</span>
                    </div>
                    <div class="h-[260px]">
                        <canvas id="chartRooms"></canvas>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-extrabold text-slate-900">Insight Cepat</h3>
                        <span class="text-xs text-slate-500">Summary</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs text-slate-500">Total Status Tercatat</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900"><?php echo count($statusCounts); ?>
                            </p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs text-slate-500">Total Ruangan Tercatat</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900"><?php echo count($roomCounts); ?></p>
                        </div>
                    </div>
                </section>
            </div>
            <!-- =================== END GRAPHICS =================== -->

            <!-- 2 COLUMN TABLES -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">

                <!-- LEFT: Recent Bookings + Activities -->
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-extrabold text-slate-900">Recent Bookings</h2>
                            <span class="text-xs text-slate-500">Terbaru</span>
                        </div>

                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50">
                                    <tr class="text-left text-slate-600">
                                        <th class="px-3 py-3 font-semibold">No</th>
                                        <th class="px-3 py-3 font-semibold">ID</th>
                                        <th class="px-3 py-3 font-semibold">Ruang</th>
                                        <th class="px-3 py-3 font-semibold">Tanggal</th>
                                        <th class="px-3 py-3 font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <?php
                                    if (!empty($recent_pemesanan)) {
                                        $no = 1;
                                        foreach ($recent_pemesanan as $row) {
                                            echo '<tr class="hover:bg-slate-50">';
                                            echo '<td class="px-3 py-3 text-slate-500">' . $no . '</td>';
                                            echo '<td class="px-3 py-3 font-semibold text-slate-900">' . $row->ID_PEMESANAN . '</td>';
                                            echo '<td class="px-3 py-3 text-slate-700">' . $row->NAMA_GEDUNG . '</td>';
                                            echo '<td class="px-3 py-3 text-slate-600">' . formatTanggalIndo($row->TANGGAL_PEMESANAN) . '</td>';
                                            echo '<td class="px-3 py-3">';
                                            echo '<a href="' . site_url('admin/detail_pemesanan/' . $row->ID_PEMESANAN) . '" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 text-white px-3 py-1.5 text-xs font-semibold hover:bg-slate-800 transition">';
                                            echo 'Detail <span class="material-icons text-[16px]">open_in_new</span>';
                                            echo '</a>';
                                            echo '</td>';
                                            echo '</tr>';
                                            $no++;
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="px-3 py-10 text-center text-slate-500">Belum ada data.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-extrabold text-slate-900">User Activities</h3>
                                <span class="text-xs text-slate-500">Ringkas</span>
                            </div>

                            <ul class="space-y-3">
                                <?php
                                if (!empty($recent_pemesanan)) {
                                    foreach ($recent_pemesanan as $row) {
                                        $jam = '-';
                                        if (!empty($row->JAM)) { $jam = $row->JAM; }

                                        echo '<li class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 hover:bg-white transition">';
                                        echo '<div class="flex items-center gap-3">';
                                        echo '<div class="h-10 w-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700">';
                                        echo '<span class="material-icons text-[18px]">person</span>';
                                        echo '</div>';
                                        echo '<div>';
                                        echo '<p class="text-sm text-slate-800">';
                                        echo '<span class="font-semibold">' . $row->USERNAME . '</span> ';
                                        echo '<span class="text-slate-500">booked</span> ';
                                        echo '<span class="font-medium">' . $row->NAMA_GEDUNG . '</span>';
                                        echo '</p>';
                                        echo '<p class="text-xs text-slate-500">' . formatTanggalIndo($row->TANGGAL_PEMESANAN) . ' • ' . $jam . '</p>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '<span class="text-slate-400">›</span>';
                                        echo '</li>';
                                    }
                                } else {
                                    echo '<li class="text-sm text-slate-500">Belum ada aktivitas.</li>';
                                }
                                ?>
                            </ul>
                        </div>

                    </div>
                </section>

                <!-- RIGHT: List Pemesanan -->
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="p-5">

                        <div class="mb-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-base font-extrabold text-slate-900">List Pemesanan</h2>
                                    <p class="text-xs text-slate-500">Filter + pagination (client-side)</p>
                                </div>
                            </div>

                            <div class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="relative">
                                        <span
                                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                                        <input id="searchInput" type="text" placeholder="Cari ID / user / ruang..."
                                            class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 py-2 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                                    </div>

                                    <select id="statusFilter"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                        <option value="">All Status</option>
                                        <option value="SUBMITED">SUBMITED</option>
                                        <option value="SUBMITTED">SUBMITTED</option>
                                        <option value="PROCESS">PROCESS</option>
                                        <option value="PROPOSAL APPROVE">PROPOSAL APPROVE</option>
                                        <option value="APPROVE & PAID">APPROVE & PAID</option>
                                        <option value="REJECTED">REJECTED</option>
                                    </select>

                                    <input id="dateFilter" type="date"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                            <div id="tableWrap" class="max-h-[420px] overflow-auto">
                                <table class="w-full text-sm">
                                    <thead class="sticky top-0 z-20 bg-white border-b border-slate-200">
                                        <tr class="text-center text-slate-700">
                                            <th class="px-4 py-3 font-semibold whitespace-nowrap">ID</th>
                                            <th class="px-4 py-3 font-semibold whitespace-nowrap">User</th>
                                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Tanggal</th>
                                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Jam</th>
                                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Ruangan</th>
                                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Status</th>
                                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Detail</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tableBody" class="divide-y divide-slate-200">
                                        <?php
                                        if (!empty($pemesanan)) {
                                            foreach ($pemesanan as $row) {

                                                $id = '-';
                                                if (isset($row['ID_PEMESANAN'])) { $id = $row['ID_PEMESANAN']; }

                                                $user = '-';
                                                if (isset($row['USERNAME'])) { $user = $row['USERNAME']; }

                                                $tglRaw = '';
                                                if (isset($row['TANGGAL_PEMESANAN'])) { $tglRaw = $row['TANGGAL_PEMESANAN']; }

                                                $tglIndo = formatTanggalIndo($tglRaw);
                                                $tglForFilter = '';
                                                if (!empty($tglRaw)) { $tglForFilter = date('Y-m-d', strtotime($tglRaw)); }

                                                $gedung = '-';
                                                if (isset($row['NAMA_GEDUNG'])) { $gedung = $row['NAMA_GEDUNG']; }

                                                $status = '-';
                                                if (isset($row['STATUS'])) { $status = $row['STATUS']; }
                                                $statusUpper = strtoupper(trim((string)$status));

                                                $jamText = '-';
                                                if (!empty($row['JAM_PEMESANAN']) && !empty($row['JAM_SELESAI'])) {
                                                    $jamText = date('H:i', strtotime($row['JAM_PEMESANAN'])) . ' - ' .
                                                               date('H:i', strtotime($row['JAM_SELESAI'])) . ' WIB';
                                                }

                                                $badge = 'bg-slate-100 text-slate-700';
                                                if ($statusUpper === 'PROPOSAL APPROVE') $badge = 'bg-lime-100 text-lime-700';
                                                else if ($statusUpper === 'PROCESS') $badge = 'bg-yellow-100 text-yellow-700';
                                                else if ($statusUpper === 'REJECTED') $badge = 'bg-rose-100 text-rose-700';
                                                else if ($statusUpper === 'APPROVE & PAID') $badge = 'bg-emerald-100 text-emerald-700';
                                                else if ($statusUpper === 'SUBMITED' || $statusUpper === 'SUBMITTED') $badge = 'bg-indigo-100 text-indigo-700';

                                                $safeId = htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8');
                                                $safeUser = htmlspecialchars((string)$user, ENT_QUOTES, 'UTF-8');
                                                $safeTgl = htmlspecialchars((string)$tglIndo, ENT_QUOTES, 'UTF-8');
                                                $safeJam = htmlspecialchars((string)$jamText, ENT_QUOTES, 'UTF-8');
                                                $safeGed = htmlspecialchars((string)$gedung, ENT_QUOTES, 'UTF-8');
                                                $safeStatus = htmlspecialchars((string)$statusUpper, ENT_QUOTES, 'UTF-8');
                                                $safeDate = htmlspecialchars((string)$tglForFilter, ENT_QUOTES, 'UTF-8');

                                                $detailUrl = site_url('admin/detail_transaksi/' . $id);

                                                echo '<tr class="table-row text-center hover:bg-slate-50 odd:bg-white"';
                                                echo ' data-id="' . $safeId . '"';
                                                echo ' data-user="' . $safeUser . '"';
                                                echo ' data-gedung="' . $safeGed . '"';
                                                echo ' data-status="' . $safeStatus . '"';
                                                echo ' data-date="' . $safeDate . '">';
                                                echo '<td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">' . $safeId . '</td>';
                                                echo '<td class="px-4 py-3 text-slate-700 whitespace-nowrap">' . $safeUser . '</td>';
                                                echo '<td class="px-4 py-3 text-slate-600 whitespace-nowrap">' . $safeTgl . '</td>';
                                                echo '<td class="px-4 py-3 text-slate-600 whitespace-nowrap">' . $safeJam . '</td>';
                                                echo '<td class="px-4 py-3 text-slate-700 whitespace-nowrap">' . $safeGed . '</td>';
                                                echo '<td class="px-4 py-3 whitespace-nowrap">';
                                                echo '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ' . $badge . '">' . $safeStatus . '</span>';
                                                echo '</td>';
                                                echo '<td class="px-4 py-3 whitespace-nowrap">';
                                                echo '<a href="' . $detailUrl . '" class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white hover:bg-indigo-50 hover:border-indigo-200 text-indigo-700 transition" title="Detail">';
                                                echo '<i class="material-icons">open_in_new</i>';
                                                echo '</a>';
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">Data pemesanan tidak ditemukan.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <button id="prevBtn"
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-semibold border border-slate-200">
                                Prev
                            </button>

                            <div class="flex items-center gap-2">
                                <span id="resultInfo" class="text-sm text-slate-600"></span>
                                <span class="text-slate-300">•</span>
                                <span id="pageInfo" class="text-sm text-slate-600"></span>
                            </div>

                            <div class="flex items-center gap-3">
                                <select id="rowsPerPage"
                                    class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                    <option value="5">5 rows</option>
                                    <option value="10" selected>10 rows</option>
                                    <option value="25">25 rows</option>
                                    <option value="50">50 rows</option>
                                </select>

                                <button id="nextBtn"
                                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-semibold border border-slate-200">
                                    Next
                                </button>
                            </div>
                        </div>

                    </div>
                </section>
            </div>

            <footer class="text-xs text-slate-500 text-center mt-6">
                © <?php echo date('Y'); ?> Smart Office • Admin Panel
            </footer>

        </main>
    </div>

    <!-- =========================
         JS: Chart + Filter + Pagination
         ========================= -->
    <script>
    (function() {
        // ===== Chart Data from PHP
        var statusLabels = <?php echo json_encode($statusLabels); ?>;
        var statusValues = <?php echo json_encode($statusValues); ?>;

        var dayLabels = <?php echo json_encode($dayLabels); ?>;
        var dayValues = <?php echo json_encode($dayValues); ?>;

        var roomLabels = <?php echo json_encode($roomLabels); ?>;
        var roomValues = <?php echo json_encode($roomValues); ?>;

        // Doughnut status
        var elStatus = document.getElementById('chartStatus');
        if (elStatus) {
            new Chart(elStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    cutout: '62%'
                }
            });
        }

        // Line daily
        var elDaily = document.getElementById('chartDaily');
        if (elDaily) {
            new Chart(elDaily, {
                type: 'line',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        data: dayValues,
                        tension: 0.35,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Bar rooms
        var elRooms = document.getElementById('chartRooms');
        if (elRooms) {
            new Chart(elRooms, {
                type: 'bar',
                data: {
                    labels: roomLabels,
                    datasets: [{
                        data: roomValues,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // ===== Table Filter + Pagination (client-side)
        var tableBody = document.getElementById('tableBody');
        if (!tableBody) return;

        var rows = Array.prototype.slice.call(tableBody.querySelectorAll('tr.table-row'));
        var searchInput = document.getElementById('searchInput');
        var statusFilter = document.getElementById('statusFilter');
        var dateFilter = document.getElementById('dateFilter');

        var prevBtn = document.getElementById('prevBtn');
        var nextBtn = document.getElementById('nextBtn');
        var rowsPerPageEl = document.getElementById('rowsPerPage');
        var pageInfo = document.getElementById('pageInfo');
        var resultInfo = document.getElementById('resultInfo');

        var currentPage = 1;
        var rowsPerPage = parseInt(rowsPerPageEl.value, 10);
        if (!rowsPerPage || rowsPerPage < 1) rowsPerPage = 10;

        function norm(str) {
            var s = '';
            if (str !== null && str !== undefined) s = String(str);
            return s.toLowerCase().replace(/^\s+|\s+$/g, '');
        }

        function matchesFilter(row) {
            var q = norm(searchInput.value);
            var st = norm(statusFilter.value);
            var dt = (dateFilter.value || '').trim();

            var id = norm(row.getAttribute('data-id'));
            var user = norm(row.getAttribute('data-user'));
            var gedung = norm(row.getAttribute('data-gedung'));
            var status = norm(row.getAttribute('data-status'));
            var date = (row.getAttribute('data-date') || '').trim();

            if (q !== '') {
                var hit = (id.indexOf(q) !== -1) || (user.indexOf(q) !== -1) || (gedung.indexOf(q) !== -1);
                if (!hit) return false;
            }
            if (st !== '') {
                if (status !== st) return false;
            }
            if (dt !== '') {
                if (date !== dt) return false;
            }
            return true;
        }

        function getFilteredRows() {
            return rows.filter(matchesFilter);
        }

        function render() {
            var filtered = getFilteredRows();
            var total = filtered.length;

            var totalPages = Math.ceil(total / rowsPerPage);
            if (!totalPages || totalPages < 1) totalPages = 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            var start = (currentPage - 1) * rowsPerPage;
            var end = start + rowsPerPage;

            rows.forEach(function(r) {
                r.classList.add('hidden');
            });
            filtered.slice(start, end).forEach(function(r) {
                r.classList.remove('hidden');
            });

            var shownFrom = 0;
            if (total > 0) shownFrom = start + 1;
            var shownTo = end;
            if (shownTo > total) shownTo = total;

            resultInfo.textContent = 'Show ' + shownFrom + '-' + shownTo + ' of ' + total;
            pageInfo.textContent = 'Page ' + currentPage + ' / ' + totalPages;

            prevBtn.disabled = (currentPage <= 1);
            nextBtn.disabled = (currentPage >= totalPages);
        }

        function resetToFirstPage() {
            currentPage = 1;
            render();
        }

        if (searchInput) searchInput.addEventListener('input', resetToFirstPage);
        if (statusFilter) statusFilter.addEventListener('change', resetToFirstPage);
        if (dateFilter) dateFilter.addEventListener('change', resetToFirstPage);

        if (rowsPerPageEl) rowsPerPageEl.addEventListener('change', function() {
            var v = parseInt(rowsPerPageEl.value, 10);
            if (!v || v < 1) v = 10;
            rowsPerPage = v;
            resetToFirstPage();
        });

        if (prevBtn) prevBtn.addEventListener('click', function() {
            currentPage = currentPage - 1;
            render();
        });
        if (nextBtn) nextBtn.addEventListener('click', function() {
            currentPage = currentPage + 1;
            render();
        });

        render();
    })();
    </script>
</body>

</html>