<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

/* ===== helper format tanggal Indonesia: 11 Januari 2026 ===== */
// Helper format_tanggal_indo sudah di-autoload
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan</title>
    <link rel="icon" href="<?= base_url('assets/home/assets/img/favicon/favicon-32x32.png') ?>" sizes="32x32">

</head>

<body class="bg-gray-100 text-gray-800">
    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold">Data Pemesanan Ruangan</h1>
            <p class="text-sm text-gray-500">Daftar seluruh pemesanan Ruangan</p>
        </div>

        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">

            <!-- ================= FILTER (AUTO) ================= -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 items-end">
                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari ID Pemesanan</label>
                    <input type="text" id="filterId" placeholder="Cari ID Pemesanan"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select id="filterStatus"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                        <option value="">Semua Status</option>
                        <option value="SUBMITED">SUBMITED</option>
                        <option value="PROCESS">PROCESS</option>
                        <option value="PROPOSAL APPROVE">PROPOSAL APPROVE</option>
                        <option value="APPROVE & PAID">APPROVE & PAID</option>
                        <option value="REJECTED">REJECTED</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex gap-2">
                    <button id="resetFilter" class="w-full bg-gray-200 hover:bg-gray-300 rounded-lg px-4 py-2 text-sm">
                        Reset
                    </button>
                </div>
            </div>

            <div class="text-xs text-gray-500 mb-4">
                * Filter berjalan otomatis saat mengetik / memilih status. Pagination mengikuti hasil filter.
            </div>
            <!-- ================= END FILTER ================= -->

            <div id="tableScroll"
                class="overflow-x-auto max-h-[420px] overflow-y-auto relative border border-slate-200 rounded-lg">
                <table class="min-w-[700px] w-full text-sm bg-white">
                    <thead class="sticky top-0 z-20 bg-gray-100 shadow-sm border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-center w-[70px]">No</th>
                            <th class="px-4 py-3 text-center w-[170px]">ID Pemesanan</th>
                            <th class="px-4 py-3 text-center">Nama Lengkap</th>
                            <th class="px-4 py-3 text-center">Tanggal</th>
                            <th class="px-4 py-3 text-center">Ruangan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody" class="divide-y text-center">
                        <?php if (!empty($pemesanan)) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($pemesanan as $row): ?>
                                <?php
                                $statusUpper = isset($row['STATUS']) ? strtoupper(trim($row['STATUS'])) : '';
                                $badge = 'bg-gray-100 text-gray-700';

                                if ($statusUpper === 'REJECTED') $badge = 'bg-red-100 text-red-700';
                                else if ($statusUpper === 'APPROVE & PAID') $badge = 'bg-green-100 text-green-700';
                                else if ($statusUpper === 'APPROVE') $badge = 'bg-lime-100 text-lime-700';
                                else if ($statusUpper === 'SUBMITED') $badge = 'bg-blue-100 text-blue-700';
                                else if ($statusUpper === 'PROCESS') $badge = 'bg-yellow-100 text-yellow-700';

                                $idPemesanan = isset($row['ID_PEMESANAN']) ? $row['ID_PEMESANAN'] : '-';
                                $tanggalRaw  = isset($row['TANGGAL_PEMESANAN']) ? $row['TANGGAL_PEMESANAN'] : '';
                                $namaGedung  = isset($row['NAMA_GEDUNG']) ? $row['NAMA_GEDUNG'] : '-';
                                $statusText  = isset($row['STATUS']) ? $row['STATUS'] : '-';
                                $usernameRow = isset($row['USERNAME']) ? $row['USERNAME'] : '-';

                                $namaLengkap = !empty($row['USER_NAMA_LENGKAP']) ? $row['USER_NAMA_LENGKAP'] : $usernameRow;
                                $namaPT      = !empty($row['USER_NAMA_PERUSAHAAN']) ? $row['USER_NAMA_PERUSAHAAN'] : '-';
                                $departemen  = !empty($row['USER_DEPARTEMEN']) ? $row['USER_DEPARTEMEN'] : '';
                                $jenis       = !empty($row['USER_JENIS']) ? strtoupper(trim($row['USER_JENIS'])) : '';

                                if ($jenis === 'INTERNAL') {
                                    if ($namaPT === '-' || $namaPT === '') $namaPT = 'PT Tiga Serangkai Pustaka Mandiri';
                                }

                                $tanggalTampil = format_tanggal_indo($tanggalRaw);

                                // untuk filter
                                $dataId = htmlspecialchars((string)$idPemesanan, ENT_QUOTES, 'UTF-8');
                                $dataStatus = htmlspecialchars($statusUpper, ENT_QUOTES, 'UTF-8');

                                // untuk sorting frontend (angka saja)
                                $idNum = (int) preg_replace('/\D+/', '', (string)$idPemesanan);
                                ?>
                                <tr class="table-row hover:bg-gray-50" data-id="<?= $dataId; ?>"
                                    data-status="<?= $dataStatus; ?>" data-idnum="<?= (int)$idNum; ?>">

                                    <td class="px-4 py-3 cell-no"><?= $no++; ?></td>

                                    <td class="px-4 py-3 font-medium">
                                        <?= htmlspecialchars((string)$idPemesanan, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td class="px-4 py-3 text-left">
                                        <div class="font-semibold text-slate-800">
                                            <?= htmlspecialchars((string)$namaLengkap, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                        <div class="text-xs text-slate-500 leading-snug">
                                            <?= htmlspecialchars((string)$namaPT, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php if (!empty($departemen)): ?>
                                                <br><?= htmlspecialchars((string)$departemen, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars((string)$tanggalTampil, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars((string)$namaGedung, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badge; ?>">
                                            <?= htmlspecialchars((string)$statusText, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-gray-500">Data pemesanan tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ================= PAGINATION ================= -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button id="prevBtn"
                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-40 disabled:cursor-not-allowed">
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-gray-600 text-center"></span>

                <div class="flex items-center gap-3">
                    <select id="rowsPerPage" class="rounded-lg border px-3 py-2 text-sm bg-white">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-40 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
            <!-- ================= END PAGINATION ================= -->

        </div>
    </main>

    <script>
        (function() {
            const tbody = document.getElementById("tableBody");
            let allRows = Array.from(document.querySelectorAll(".table-row"));

            const filterId = document.getElementById("filterId");
            const filterStatus = document.getElementById("filterStatus");
            const resetBtn = document.getElementById("resetFilter");

            const rowsPerPageSelect = document.getElementById("rowsPerPage");
            const pageInfo = document.getElementById("pageInfo");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const scrollBox = document.getElementById("tableScroll");

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;
            let activeRows = [];

            function norm(s) {
                return (s || "").toString().trim().toLowerCase().replace(/\s+/g, "");
            }

            // ===== SORT FRONTEND: ID TERKECIL (ASC) =====
            function sortRowsByIdAsc() {
                allRows.sort((a, b) => {
                    const ida = parseInt(a.dataset.idnum || "0", 10);
                    const idb = parseInt(b.dataset.idnum || "0", 10);
                    return ida - idb;
                });

                // re-append biar DOM ikut urutan sorting
                allRows.forEach(r => tbody.appendChild(r));
            }

            function applyFilter() {
                const qId = norm(filterId.value);
                const qStatus = (filterStatus.value || "").trim(); // uppercase

                activeRows = allRows.filter(row => {
                    const rid = norm(row.dataset.id);
                    const rst = (row.dataset.status || "").trim();

                    const okId = !qId ? true : rid.includes(qId);
                    const okSt = !qStatus ? true : rst === qStatus;

                    return okId && okSt;
                });

                currentPage = 1;
                render();
            }

            function resetFilter() {
                filterId.value = "";
                filterStatus.value = "";
                activeRows = [...allRows];
                currentPage = 1;
                render();
            }

            function render() {
                allRows.forEach(r => r.style.display = "none");

                const total = activeRows.length;
                const totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
                if (currentPage > totalPages) currentPage = totalPages;

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                activeRows.forEach((r, idx) => {
                    r.style.display = (idx >= start && idx < end) ? "" : "none";
                });

                // renumber No (ikut urutan ASC)
                let n = start + 1;
                activeRows.forEach((r, idx) => {
                    if (idx >= start && idx < end) {
                        const cell = r.querySelector(".cell-no");
                        if (cell) cell.textContent = n++;
                    }
                });

                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= totalPages;

                const showingFrom = total === 0 ? 0 : start + 1;
                const showingTo = Math.min(end, total);
                pageInfo.textContent =
                    `Page ${currentPage} of ${totalPages} • Showing ${showingFrom}-${showingTo} of ${total}`;

                if (scrollBox) scrollBox.scrollTop = 0;
            }

            // AUTO filter events
            filterId.addEventListener("input", applyFilter);
            filterStatus.addEventListener("change", applyFilter);
            resetBtn.addEventListener("click", resetFilter);

            rowsPerPageSelect.addEventListener("change", () => {
                rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;
                currentPage = 1;
                render();
            });

            prevBtn.addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    render();
                }
            });

            nextBtn.addEventListener("click", () => {
                const totalPages = Math.max(1, Math.ceil(activeRows.length / rowsPerPage));
                if (currentPage < totalPages) {
                    currentPage++;
                    render();
                }
            });

            // init
            sortRowsByIdAsc(); // <--- PENTING: sort dulu
            activeRows = [...allRows];
            render();
        })();
    </script>

</body>

</html>