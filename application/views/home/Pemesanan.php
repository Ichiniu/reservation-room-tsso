<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed"
        href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">
</head>

<body class="min-h-screen bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 text-slate-900">
    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 space-y-6">

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
                            <span class="material-icons text-sm">assignment</span>
                            Pemesanan
                        </div>
                        <h1 class="mt-3 text-2xl md:text-3xl font-extrabold tracking-tight">
                            Daftar Pemesanan
                        </h1>
                        <!-- <p class="mt-2 text-sm md:text-base text-slate-600">
                            Filter berdasarkan ID pemesanan dan status. Data diurutkan dari yang terbaru.
                        </p> -->
                    </div>

                    <div
                        class="hidden sm:flex items-center gap-2 text-xs text-slate-600 bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
                        <span class="material-icons text-base text-slate-500">tips_and_updates</span>
                        Gunakan pencarian cepat untuk ID “PMS…”
                    </div>
                </div>
            </section>

            <!-- MAIN CARD -->
            <section class="rounded-3xl bg-white border border-slate-200 shadow-xl p-6">

                <!-- FILTER BAR -->
                <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Filter Pemesanan</h2>
                        <p class="text-sm text-slate-500">Cari cepat dan rapikan hasil dalam beberapa klik.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full lg:w-auto">
                        <!-- Search ID -->
                        <div class="relative">
                            <label class="text-xs font-semibold text-slate-700">Cari ID</label>
                            <span
                                class="material-icons absolute left-3 top-[44px] text-slate-400 text-[18px]">search</span>
                            <input id="idFilter" type="text" placeholder="Contoh: PMS..." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-3 py-3 text-sm outline-none
                       focus:ring-4 focus:ring-sky-100 focus:border-sky-300" />
                        </div>

                        <!-- Dropdown Status -->
                        <div class="relative">
                            <label class="text-xs font-semibold text-slate-700">Status</label>
                            <span
                                class="material-icons absolute left-3 top-[44px] text-slate-400 text-[18px]">filter_alt</span>
                            <select id="statusFilter" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-3 py-3 text-sm outline-none
                       focus:ring-4 focus:ring-sky-100 focus:border-sky-300">
                                <option value="">All Status</option>
                                <option value="SUBMITED">SUBMITED</option>
                                <option value="SUBMITTED">SUBMITTED</option>
                                <option value="PROCESS">PROCESS</option>
                                <option value="PROPOSAL APPROVE">PROPOSAL APPROVE</option>
                                <option value="APPROVE & PAID">APPROVE & PAID</option>
                                <option value="REJECTED">REJECTED</option>
                                <option value="CONFIRMED">CONFIRMED</option>
                            </select>
                        </div>

                        <!-- Reset -->
                        <div>
                            <label class="text-xs font-semibold text-slate-700">Aksi</label>
                            <button id="resetBtn" type="button" class="mt-2 w-full inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold
                       bg-slate-900 text-white hover:bg-slate-800 active:scale-[0.99] transition shadow-sm">
                                Reset
                                <span class="material-icons text-[18px]">restart_alt</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TABLE WRAP (sticky header full tailwind) -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden bg-white">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200">
                                <tr class="text-xs text-slate-700">
                                    <th class="px-4 py-3 text-left font-semibold">Tanggal Pemesanan</th>
                                    <th class="px-4 py-3 text-left font-semibold">ID Pemesanan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jam Pemesanan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Paket Catering</th>
                                    <th class="px-4 py-3 text-left font-semibold">Nama Ruangan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Status Pemesanan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Detail</th>
                                </tr>
                            </thead>

                            <tbody id="tableBody" class="divide-y divide-slate-100">
                                <?php
                if (isset($res) && is_array($res)) {
                  foreach ($res as $row) {

                    // ID
                    $id_pemesanan = (isset($row['ID_PEMESANAN']) && $row['ID_PEMESANAN'] !== '') ? (string)$row['ID_PEMESANAN'] : '-';

                    // STATUS
                    $statusRaw = (isset($row['STATUS']) && $row['STATUS'] !== '') ? (string)$row['STATUS'] : '-';
                    $statusUpper = strtoupper(trim($statusRaw));

                    // TANGGAL RAW (untuk sort)
                    $tanggalRaw = (isset($row['TANGGAL_PEMESANAN']) && $row['TANGGAL_PEMESANAN'] !== '') ? (string)$row['TANGGAL_PEMESANAN'] : '';

                    // TANGGAL TAMPIL
                    $tanggalTampil = '-';
                    if ($tanggalRaw !== '') {
                      $dateObj = date_create($tanggalRaw);
                      $tanggalTampil = $dateObj ? date_format($dateObj, 'd F Y') : $tanggalRaw;
                    }

                    // JAM
                    $jamText = '-';
                    if (isset($row['JAM_PEMESANAN']) && isset($row['JAM_SELESAI'])) {
                      if ($row['JAM_PEMESANAN'] !== '' && $row['JAM_SELESAI'] !== '') {
                        $jamMulai = date('H:i', strtotime($row['JAM_PEMESANAN']));
                        $jamSelesai = date('H:i', strtotime($row['JAM_SELESAI']));
                        $jamText = $jamMulai . ' - ' . $jamSelesai . ' WIB';
                      }
                    }

                    // PAKET
                    $paket = (isset($row['NAMA_PAKET']) && $row['NAMA_PAKET'] !== '') ? (string)$row['NAMA_PAKET'] : '-';

                    // GEDUNG
                    $gedung = (isset($row['NAMA_GEDUNG']) && $row['NAMA_GEDUNG'] !== '') ? (string)$row['NAMA_GEDUNG'] : '-';

                    // BADGE
                    $badge = 'bg-slate-100 text-slate-700 border border-slate-200';
                    if ($statusUpper === 'REJECTED') $badge = 'bg-red-50 text-red-700 border border-red-200';
                    else if ($statusUpper === 'PROPOSAL APPROVE') $badge = 'bg-sky-50 text-sky-700 border border-sky-200';
                    else if ($statusUpper === 'APPROVE & PAID') $badge = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                    else if ($statusUpper === 'SUBMITED' || $statusUpper === 'SUBMITTED') $badge = 'bg-purple-50 text-purple-700 border border-purple-200';
                    else if ($statusUpper === 'PROCESS') $badge = 'bg-amber-50 text-amber-800 border border-amber-200';
                    else if ($statusUpper === 'CONFIRMED') $badge = 'bg-teal-50 text-teal-800 border border-teal-200';

                    $safeId = htmlspecialchars($id_pemesanan, ENT_QUOTES, 'UTF-8');
                    $safeStatus = htmlspecialchars($statusUpper, ENT_QUOTES, 'UTF-8');
                    $safeDate = htmlspecialchars($tanggalRaw, ENT_QUOTES, 'UTF-8');

                    $safePaket = htmlspecialchars($paket, ENT_QUOTES, 'UTF-8');
                    $safeGedung = htmlspecialchars($gedung, ENT_QUOTES, 'UTF-8');
                    $safeJam = htmlspecialchars($jamText, ENT_QUOTES, 'UTF-8');

                    $detailUrl = site_url('home/pemesanan/details/' . $id_pemesanan);
                ?>
                                <tr class="table-row hover:bg-slate-50 transition" data-id="<?php echo $safeId; ?>"
                                    data-status="<?php echo $safeStatus; ?>" data-date="<?php echo $safeDate; ?>">

                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-900"><?php echo $tanggalTampil; ?></div>
                                        <div class="mt-0.5 text-xs text-slate-500 inline-flex items-center gap-1">
                                            <span class="material-icons text-[14px]">schedule</span>
                                            Dibuat
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-2">
                                            <span
                                                class="inline-flex items-center justify-center h-8 min-w-[34px] px-2 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                                #
                                            </span>
                                            <span class="font-bold text-slate-900"><?php echo $safeId; ?></span>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-sky-50 text-sky-800 px-3 py-1 text-xs font-semibold">
                                            <span class="material-icons text-[16px]">access_time</span>
                                            <?php echo $safeJam; ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700">
                                        <div
                                            class="[display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] overflow-hidden">
                                            <?php echo $safePaket; ?>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900"><?php echo $safeGedung; ?></div>
                                        <div class="text-xs text-slate-500 mt-0.5">Ruangan</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo $badge; ?>">
                                            <?php echo $safeStatus; ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-2xl border border-slate-200 bg-white
                              hover:bg-sky-50 hover:border-sky-200 active:scale-[0.99] transition"
                                            href="<?php echo $detailUrl; ?>" title="Detail">
                                            <span class="material-icons text-slate-700">open_in_new</span>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                  }
                }
                ?>
                            </tbody>
                        </table>

                        <?php
            if (isset($rows) && (int)$rows < 1) {
              echo '<div class="p-6 text-center text-slate-600">---------- ' . $no_data . ' ----------</div>';
            }
            ?>
                    </div>
                </div>

                <!-- PAGINATION -->
                <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button id="prevBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white
                   hover:bg-slate-50 active:scale-[0.99] transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-icons text-base">chevron_left</span>
                        Prev
                    </button>

                    <div class="flex items-center gap-2">
                        <span id="resultInfo" class="text-sm text-slate-600"></span>
                        <span class="text-slate-300">•</span>
                        <span id="pageInfo" class="text-sm text-slate-600"></span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">table_rows</span>
                            <select id="rowsPerPage" class="rounded-2xl border border-slate-200 bg-white pl-10 pr-3 py-2 text-sm outline-none
                       focus:ring-4 focus:ring-sky-100 focus:border-sky-300">
                                <option value="5">5 rows</option>
                                <option value="10" selected>10 rows</option>
                                <option value="25">25 rows</option>
                            </select>
                        </div>

                        <button id="nextBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white
                     hover:bg-slate-50 active:scale-[0.99] transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Next
                            <span class="material-icons text-base">chevron_right</span>
                        </button>
                    </div>
                </div>

            </section>
        </div>
    </main>

    <?php $this->load->view('components/footer'); ?>

    <script>
    (function() {
        var tbody = document.getElementById('tableBody');
        var allRows = Array.prototype.slice.call(document.querySelectorAll('.table-row'));

        var rowsSelect = document.getElementById('rowsPerPage');
        var prevBtn = document.getElementById('prevBtn');
        var nextBtn = document.getElementById('nextBtn');
        var pageInfo = document.getElementById('pageInfo');
        var resultInfo = document.getElementById('resultInfo');

        var idFilter = document.getElementById('idFilter');
        var statusFilter = document.getElementById('statusFilter');
        var resetBtn = document.getElementById('resetBtn');

        var rowsPerPage = parseInt(rowsSelect.value, 10);
        if (!rowsPerPage || rowsPerPage < 1) rowsPerPage = 10;

        var currentPage = 1;

        // sort terbaru
        allRows.sort(function(a, b) {
            var da = new Date(a.getAttribute('data-date') || '1970-01-01');
            var db = new Date(b.getAttribute('data-date') || '1970-01-01');
            return db - da;
        });

        tbody.innerHTML = '';
        for (var i = 0; i < allRows.length; i++) tbody.appendChild(allRows[i]);

        function norm(str) {
            var s = '';
            if (str !== null && str !== undefined) s = String(str);
            return s.toUpperCase().replace(/^\s+|\s+$/g, '');
        }

        function getFilteredRows() {
            var qId = norm(idFilter.value);
            var st = norm(statusFilter.value);

            return allRows.filter(function(row) {
                var rid = norm(row.getAttribute('data-id'));
                var rstatus = norm(row.getAttribute('data-status'));

                if (qId !== '' && rid.indexOf(qId) === -1) return false;
                if (st !== '' && rstatus !== st) return false;
                return true;
            });
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

            for (var k = 0; k < allRows.length; k++) allRows[k].style.display = 'none';
            var slice = filtered.slice(start, end);
            for (var j = 0; j < slice.length; j++) slice[j].style.display = '';

            var shownFrom = (total > 0) ? (start + 1) : 0;
            var shownTo = end;
            if (shownTo > total) shownTo = total;

            resultInfo.textContent = 'Show ' + shownFrom + '-' + shownTo + ' of ' + total;
            pageInfo.textContent = 'Page ' + currentPage + ' of ' + totalPages;

            prevBtn.disabled = (currentPage === 1);
            nextBtn.disabled = (currentPage === totalPages);
        }

        function resetToFirstPage() {
            currentPage = 1;
            render();
        }

        idFilter.addEventListener('input', resetToFirstPage);
        statusFilter.addEventListener('change', resetToFirstPage);

        resetBtn.addEventListener('click', function() {
            idFilter.value = '';
            statusFilter.value = '';
            rowsSelect.value = '10';
            rowsPerPage = 10;
            currentPage = 1;
            render();
        });

        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage = currentPage - 1;
                render();
            }
        });

        nextBtn.addEventListener('click', function() {
            var filtered = getFilteredRows();
            var totalPages = Math.ceil(filtered.length / rowsPerPage);
            if (!totalPages || totalPages < 1) totalPages = 1;

            if (currentPage < totalPages) {
                currentPage = currentPage + 1;
                render();
            }
        });

        rowsSelect.addEventListener('change', function() {
            var v = parseInt(rowsSelect.value, 10);
            if (!v || v < 1) v = 10;
            rowsPerPage = v;
            currentPage = 1;
            render();
        });

        render();
    })();
    </script>

</body>

</html>