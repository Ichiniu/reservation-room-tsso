<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed"
        href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Pemesanan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Materialize core CSS -->
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet">

    <style>
    .table-scroll {
        max-height: 420px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #f8fafc;
    }

    .table-scroll::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .table-scroll::-webkit-scrollbar-thumb {
        background: rgba(100, 116, 139, .35);
        border-radius: 999px;
    }

    .table-scroll::-webkit-scrollbar-track {
        background: rgba(148, 163, 184, .12);
        border-radius: 999px;
    }
    </style>
</head>

<body class="h-screen flex flex-col bg-slate-200 text-black">

    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 py-8">

            <section class="rounded-2xl bg-white border border-slate-300 ring-1 ring-slate-200 shadow-sm p-6">

                <!-- FILTER BAR -->
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Daftar Pemesanan</h2>
                        <p class="text-sm text-slate-500">Filter ID pemesanan dan status</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center w-full sm:w-auto">
                        <!-- Search ID -->
                        <div class="relative w-full sm:w-60">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                            <input id="idFilter" type="text" placeholder="Cari ID (contoh: PMS...)"
                                class="w-full rounded-xl border border-slate-300 bg-white pl-10 pr-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" />
                        </div>

                        <!-- Dropdown Status -->
                        <select id="statusFilter"
                            class="w-full sm:w-52 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                            <option value="">All Status</option>
                            <option value="SUBMITED">SUBMITED</option>
                            <option value="SUBMITTED">SUBMITTED</option>
                            <option value="PROCESS">PROCESS</option>
                            <option value="PROPOSAL APPROVE">PROPOSAL APPROVE</option>
                            <option value="APPROVE & PAID">APPROVE & PAID</option>
                            <option value="REJECTED">REJECTED</option>
                            <option value="CONFIRMED">CONFIRMED</option>
                        </select>

                        <!-- Reset -->
                        <button id="resetBtn"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-50">
                            Reset
                            <span class="material-icons text-[18px]">restart_alt</span>
                        </button>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="table-scroll rounded-xl border border-slate-300 bg-white">
                    <table class="min-w-full bordered">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Tanggal Pemesanan</th>
                                <th class="px-4 py-3 text-left">ID Pemesanan</th>
                                <th class="px-4 py-3 text-left">Jam Pemesanan</th>
                                <th class="px-4 py-3 text-left">Paket Catering</th>
                                <th class="px-4 py-3 text-left">Nama Ruangan</th>
                                <th class="px-4 py-3 text-left">Status Pemesanan</th>
                                <th class="px-4 py-3 text-left">Details</th>
                            </tr>
                        </thead>

                        <tbody id="tableBody">
                            <?php
                        if (isset($res) && is_array($res)) {
                            foreach ($res as $row) {

                                // ID
                                if (isset($row['ID_PEMESANAN']) && $row['ID_PEMESANAN'] !== '') {
                                    $id_pemesanan = (string)$row['ID_PEMESANAN'];
                                } else {
                                    $id_pemesanan = '-';
                                }

                                // STATUS
                                if (isset($row['STATUS']) && $row['STATUS'] !== '') {
                                    $statusRaw = (string)$row['STATUS'];
                                } else {
                                    $statusRaw = '-';
                                }
                                $statusUpper = strtoupper(trim($statusRaw));

                                // TANGGAL RAW (untuk sort/filter)
                                if (isset($row['TANGGAL_PEMESANAN']) && $row['TANGGAL_PEMESANAN'] !== '') {
                                    $tanggalRaw = (string)$row['TANGGAL_PEMESANAN'];
                                } else {
                                    $tanggalRaw = '';
                                }

                                // TANGGAL TAMPIL
                                $tanggalTampil = '-';
                                if ($tanggalRaw !== '') {
                                    $dateObj = date_create($tanggalRaw);
                                    if ($dateObj) {
                                        $tanggalTampil = date_format($dateObj, 'd F Y');
                                    } else {
                                        $tanggalTampil = $tanggalRaw;
                                    }
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
                                if (isset($row['NAMA_PAKET']) && $row['NAMA_PAKET'] !== '') {
                                    $paket = (string)$row['NAMA_PAKET'];
                                } else {
                                    $paket = '-';
                                }

                                // GEDUNG
                                if (isset($row['NAMA_GEDUNG']) && $row['NAMA_GEDUNG'] !== '') {
                                    $gedung = (string)$row['NAMA_GEDUNG'];
                                } else {
                                    $gedung = '-';
                                }

                                // BADGE
                                $badge = 'bg-slate-100 text-slate-700';
                                if ($statusUpper === 'REJECTED') {
                                    $badge = 'bg-red-100 text-red-700';
                                } else if ($statusUpper === 'PROPOSAL APPROVE') {
                                    $badge = 'bg-blue-100 text-blue-700';
                                } else if ($statusUpper === 'APPROVE & PAID') {
                                    $badge = 'bg-green-100 text-green-700';
                                } else if ($statusUpper === 'SUBMITED' || $statusUpper === 'SUBMITTED') {
                                    $badge = 'bg-purple-100 text-purple-700';
                                } else if ($statusUpper === 'PROCESS') {
                                    $badge = 'bg-yellow-100 text-yellow-700';
                                }

                                $safeId = htmlspecialchars($id_pemesanan, ENT_QUOTES, 'UTF-8');
                                $safeStatus = htmlspecialchars($statusUpper, ENT_QUOTES, 'UTF-8');
                                $safeDate = htmlspecialchars($tanggalRaw, ENT_QUOTES, 'UTF-8');

                                $safePaket = htmlspecialchars($paket, ENT_QUOTES, 'UTF-8');
                                $safeGedung = htmlspecialchars($gedung, ENT_QUOTES, 'UTF-8');
                                $safeJam = htmlspecialchars($jamText, ENT_QUOTES, 'UTF-8');

                                $detailUrl = site_url('home/pemesanan/details/' . $id_pemesanan);
                        ?>
                            <tr class="table-row" data-id="<?php echo $safeId; ?>"
                                data-status="<?php echo $safeStatus; ?>" data-date="<?php echo $safeDate; ?>">

                                <td class="px-4 py-3"><?php echo $tanggalTampil; ?></td>
                                <td class="px-4 py-3 font-semibold"><?php echo $safeId; ?></td>
                                <td class="px-4 py-3"><?php echo $safeJam; ?></td>
                                <td class="px-4 py-3"><?php echo $safePaket; ?></td>
                                <td class="px-4 py-3"><?php echo $safeGedung; ?></td>

                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo $badge; ?>">
                                        <?php echo $safeStatus; ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <a class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-300 hover:bg-blue-50"
                                        href="<?php echo $detailUrl; ?>" title="Detail">
                                        <i class="material-icons">open_in_new</i>
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
                        echo '<h5 class="text-center py-4">---------- ' . $no_data . ' ----------</h5>';
                    }
                    ?>
                </div>

                <!-- PAGINATION -->
                <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button id="prevBtn"
                        class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Prev
                    </button>

                    <div class="flex items-center gap-2">
                        <span id="resultInfo" class="text-sm text-slate-600"></span>
                        <span class="text-slate-300">•</span>
                        <span id="pageInfo" class="text-sm text-slate-600"></span>
                    </div>

                    <div class="flex gap-3 items-center">
                        <select id="rowsPerPage" class="rounded-xl border border-slate-300 bg-white px-3 py-2">
                            <option value="5">5 rows</option>
                            <option value="10" selected>10 rows</option>
                            <option value="25">25 rows</option>
                        </select>

                        <button id="nextBtn"
                            class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Next
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

        // SORT TERBARU dulu
        allRows.sort(function(a, b) {
            var da = new Date(a.getAttribute('data-date') || '1970-01-01');
            var db = new Date(b.getAttribute('data-date') || '1970-01-01');
            return db - da;
        });

        tbody.innerHTML = '';
        allRows.forEach(function(r) {
            tbody.appendChild(r);
        });

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

                if (qId !== '') {
                    if (rid.indexOf(qId) === -1) return false;
                }
                if (st !== '') {
                    if (rstatus !== st) return false;
                }
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

            allRows.forEach(function(r) {
                r.style.display = 'none';
            });
            filtered.slice(start, end).forEach(function(r) {
                r.style.display = '';
            });

            var shownFrom = 0;
            if (total > 0) shownFrom = start + 1;

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
            currentPage = currentPage - 1;
            render();
        });

        nextBtn.addEventListener('click', function() {
            currentPage = currentPage + 1;
            render();
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