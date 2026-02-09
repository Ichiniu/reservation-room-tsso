<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Ruangan</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 text-slate-900">
    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

        <!-- HERO -->
        <section
            class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 backdrop-blur shadow-sm">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-28 -right-28 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>
                <div class="absolute -bottom-28 -left-28 h-72 w-72 rounded-full bg-indigo-200/40 blur-3xl"></div>
            </div>

            <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-semibold">
                        <span class="material-icons text-sm">event</span>
                        Jadwal Ruangan
                    </div>

                    <h1 class="mt-3 text-2xl md:text-3xl font-extrabold tracking-tight">
                        Jadwal Penggunaan Ruangan
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="hidden sm:flex items-center gap-2 text-xs text-slate-600 bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
                        <span class="material-icons text-base text-slate-500">info</span>
                        <span>Data realtime dari sistem</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- CARD CONTENT -->
        <section class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6">

            <!-- FILTER (✅ tambah filter ruangan) -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-5">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700">Bulan</label>
                    <div class="relative">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">date_range</span>
                        <select id="filterBulan" class="w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-4 py-3 text-sm outline-none
                            focus:ring-4 focus:ring-sky-100 focus:border-sky-300">
                            <option value="">Semua Bulan</option>
                            <?php
                                $bulan = array(
                                    '01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April',
                                    '05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus',
                                    '09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember'
                                );
                                foreach ($bulan as $k => $v) echo "<option value='$k'>$v</option>";
                            ?>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700">Tahun</label>
                    <div class="relative">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">calendar_today</span>
                        <select id="filterTahun" class="w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-4 py-3 text-sm outline-none
                            focus:ring-4 focus:ring-sky-100 focus:border-sky-300">
                            <option value="">Semua Tahun</option>
                            <?php for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <!-- ✅ FILTER RUANGAN -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700">Ruangan</label>
                    <div class="relative">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">meeting_room</span>
                        <select id="filterRuangan" class="w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-4 py-3 text-sm outline-none
                            focus:ring-4 focus:ring-sky-100 focus:border-sky-300">
                            <option value="">Semua Ruangan</option>
                            <!-- option akan diisi via JS dari data tabel -->
                        </select>
                    </div>
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="resetFilter()" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold
                        bg-slate-900 text-white hover:bg-slate-800 active:scale-[0.99] transition shadow-sm">
                        <span class="material-icons text-base">restart_alt</span>
                        Reset
                    </button>
                </div>
            </div>

            <!-- INFO TOTAL -->
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p id="totalInfo" class="text-sm text-slate-600"></p>

                <div class="flex items-center gap-2 text-xs text-slate-600">
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1">
                        <span class="material-icons text-[16px] text-emerald-600">check_circle</span>
                        Jadwal aktif
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1">
                        <span class="material-icons text-[16px] text-sky-600">tune</span>
                        Filter & pagination
                    </span>
                </div>
            </div>

            <!-- TABLE WRAP -->
            <div class="rounded-2xl border border-slate-200 overflow-hidden">
                <div class="max-h-[420px] overflow-auto">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200">
                            <tr class="text-xs text-slate-700">
                                <th class="px-4 py-3 text-center font-semibold">NO</th>
                                <th class="px-4 py-3 text-center font-semibold">TANGGAL</th>
                                <th class="px-4 py-3 text-center font-semibold">JAM</th>
                                <th class="px-4 py-3 text-center font-semibold">RUANGAN</th>
                                <th class="px-4 py-3 text-center font-semibold">DESKRIPSI</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $today = date('Y-m-d');
                            $no = 1;

                            if (!empty($jadwal)):

                                $jadwal_filtered = array_values(array_filter($jadwal, function ($row) use ($today) {
                                    $tgl = date('Y-m-d', strtotime($row['TANGGAL_FINAL_PEMESANAN']));
                                    return $tgl >= $today;
                                }));

                                if (!empty($jadwal_filtered)):
                                    foreach ($jadwal_filtered as $row):
                                        $tglFinal = date('Y-m-d', strtotime($row['TANGGAL_FINAL_PEMESANAN']));
                                        $ruang = isset($row['NAMA_GEDUNG']) ? $row['NAMA_GEDUNG'] : '';
                            ?>
                            <!-- ✅ tambahkan data-room untuk filter ruangan -->
                            <tr data-date="<?= $tglFinal ?>"
                                data-room="<?= htmlspecialchars($ruang, ENT_QUOTES, 'UTF-8') ?>"
                                class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center text-center justify-center h-7 min-w-[28px] px-2 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                        <?= $no++ ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="font-semibold text-slate-900">
                                        <?= date('d M Y', strtotime($row['TANGGAL_FINAL_PEMESANAN'])) ?>
                                    </div>
                                    <div class="text-xs text-slate-500 flex justify-center items-center gap-1 mt-0.5">
                                        <span class="material-icons text-[14px]">event_available</span>
                                        Jadwal
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-700 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-sky-50 text-sky-800 px-3 py-1 text-xs font-semibold">
                                        <span class="material-icons text-[16px]">schedule</span>
                                        <?= $row['JAM_MULAI'] . ' - ' . $row['JAM_SELESAI'] ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="font-bold text-slate-900"><?= $row['NAMA_GEDUNG'] ?></div>
                                    <div class="text-xs text-slate-500 mt-0.5">Ruangan</div>
                                </td>

                                <td class="px-4 py-3 text-slate-700 text-center">
                                    <div
                                        class="[display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] overflow-hidden">
                                        <?= $row['DESKRIPSI_ACARA'] ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                    endforeach;
                                else:
                            ?>
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-600">
                                    <div
                                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <span class="material-icons text-slate-500">info</span>
                                        Tidak ada jadwal dari hari ini ke depan
                                    </div>
                                </td>
                            </tr>
                            <?php
                                endif;

                            else:
                            ?>
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-600">
                                    <div
                                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <span class="material-icons text-slate-500">info</span>
                                        Tidak ada data
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PAGINATION -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button id="prevBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white
                    hover:bg-slate-50 active:scale-[0.99] transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-icons text-base">chevron_left</span>
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-slate-700 font-semibold"></span>

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">table_rows</span>
                        <select id="rowsPerPage" class="border border-slate-200 bg-white rounded-2xl pl-10 pr-3 py-2 text-sm outline-none
                            focus:ring-4 focus:ring-sky-100 focus:border-sky-300">
                            <option value="5">5 rows</option>
                            <option value="10" selected>10 rows</option>
                            <option value="25">25 rows</option>
                        </select>
                    </div>

                    <button id="nextBtn"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white
                        hover:bg-slate-50 active:scale-[0.99] transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                        <span class="material-icons text-base">chevron_right</span>
                    </button>
                </div>
            </div>

        </section>
    </div>

    <script>
    var rows = Array.prototype.slice.call(document.querySelectorAll('tbody tr[data-date]'));
    var bulanSelect = document.getElementById('filterBulan');
    var tahunSelect = document.getElementById('filterTahun');
    var ruanganSelect = document.getElementById('filterRuangan'); // ✅
    var rowsSelect = document.getElementById('rowsPerPage');
    var prevBtn = document.getElementById('prevBtn');
    var nextBtn = document.getElementById('nextBtn');
    var pageInfo = document.getElementById('pageInfo');
    var totalInfo = document.getElementById('totalInfo');

    var rowsPerPage = parseInt(rowsSelect.value, 10);
    var currentPage = 1;
    var filteredRows = rows.slice();

    // ✅ isi dropdown ruangan dari data tabel (unik, diurutkan)
    function initRuanganOptions() {
        var set = {};
        rows.forEach(function(r) {
            var room = (r.getAttribute('data-room') || '').trim();
            if (room) set[room] = true;
        });

        var rooms = Object.keys(set).sort(function(a, b) {
            return a.localeCompare(b, 'id');
        });

        // bersihin option selain default
        while (ruanganSelect.options.length > 1) ruanganSelect.remove(1);

        rooms.forEach(function(room) {
            var opt = document.createElement('option');
            opt.value = room;
            opt.textContent = room;
            ruanganSelect.appendChild(opt);
        });
    }

    function render() {
        for (var i = 0; i < rows.length; i++) rows[i].style.display = 'none';

        var totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (!totalPages) totalPages = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        var start = (currentPage - 1) * rowsPerPage;
        var end = start + rowsPerPage;

        for (var j = start; j < end && j < filteredRows.length; j++) {
            filteredRows[j].style.display = '';
        }

        pageInfo.textContent = 'Halaman ' + currentPage + ' dari ' + totalPages;
        totalInfo.textContent = 'Menampilkan ' + filteredRows.length + ' dari ' + rows.length + ' data';

        prevBtn.disabled = (currentPage === 1);
        nextBtn.disabled = (currentPage === totalPages);
    }

    function applyFilter() {
        var bulan = bulanSelect.value;
        var tahun = tahunSelect.value;
        var ruangan = ruanganSelect.value; // ✅

        filteredRows = rows.filter(function(r) {
            var d = r.getAttribute('data-date');
            var room = (r.getAttribute('data-room') || '');

            if (bulan && d.indexOf('-' + bulan) === -1) return false;
            if (tahun && d.indexOf(tahun) !== 0) return false;
            if (ruangan && room !== ruangan) return false; // ✅ filter ruangan
            return true;
        });

        currentPage = 1;
        render();
    }

    function resetFilter() {
        bulanSelect.value = '';
        tahunSelect.value = '';
        ruanganSelect.value = ''; // ✅
        filteredRows = rows.slice();
        currentPage = 1;
        render();
    }

    prevBtn.onclick = function() {
        if (currentPage > 1) {
            currentPage--;
            render();
        }
    };

    nextBtn.onclick = function() {
        var totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (!totalPages) totalPages = 1;

        if (currentPage < totalPages) {
            currentPage++;
            render();
        }
    };

    bulanSelect.onchange = applyFilter;
    tahunSelect.onchange = applyFilter;
    ruanganSelect.onchange = applyFilter; // ✅

    rowsSelect.onchange = function() {
        rowsPerPage = parseInt(rowsSelect.value, 10);
        currentPage = 1;
        render();
    };

    // init
    initRuanganOptions();
    render();
    </script>

</body>

</html>