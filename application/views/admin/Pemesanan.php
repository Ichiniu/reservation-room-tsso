<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

/* ===== helper format tanggal Indonesia: 11 Januari 2026 ===== */
function formatTanggalIndo($tgl)
{
    if (empty($tgl)) return '-';

    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );

    $ts = strtotime($tgl);
    if (!$ts) return $tgl;

    $d = date('d', $ts);
    $m = (int) date('n', $ts);
    $y = date('Y', $ts);

    return $d . ' ' . $bulan[$m] . ' ' . $y;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize (kalau icon masih dipakai) -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800">
    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-6 pb-10">

        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold">Data Pemesanan Ruangan</h1>
            <p class="text-sm text-gray-500">Kelola data pemesanan Ruangan</p>
        </div>

        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6">

            <!-- ================= FILTER (AUTO) ================= -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari ID Pemesanan</label>
                    <input type="text" id="filterId" placeholder="Cari ID Pemesanan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select id="filterStatus"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <option value="">Semua Status</option>
                        <option value="PROPOSAL APPROVE">PROPOSAL APPROVE</option>
                        <option value="PROCESS">PROCESS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal</label>
                    <input type="date" id="filterTanggal"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                </div>

                <!-- ✅ SORT BY BUTTON (seperti gambar) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Urutkan</label>

                    <div class="relative inline-block w-full">
                        <button id="sortBtn" type="button"
                            class="w-full flex items-center justify-between gap-2 border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-300">
                            <span class="flex items-center gap-2">
                                <!-- icon filter (funnel) -->
                                <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M3 5H21L14 13V19L10 21V13L3 5Z" stroke="currentColor" stroke-width="1.6"
                                        stroke-linejoin="round" />
                                </svg>
                                <span class="font-medium">Sort By</span>
                            </span>

                            <!-- chevron down -->
                            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div id="sortMenu"
                            class="hidden absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                            <button type="button" data-sort="new"
                                class="sortItem w-full flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-50">
                                <span>Terbaru</span>
                                <span class="check hidden text-teal-600">
                                    <!-- check icon -->
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </button>

                            <button type="button" data-sort="old"
                                class="sortItem w-full flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-50">
                                <span>Terlama</span>
                                <span class="check hidden text-teal-600">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <button id="resetFilter" class="w-full bg-gray-200 hover:bg-gray-300 rounded-lg px-4 py-2 text-sm">
                        Reset
                    </button>
                </div>
            </div>

            <div class="text-xs text-gray-500 mb-4">
                * Filter berjalan otomatis saat mengetik / memilih status / memilih tanggal. Pagination mengikuti hasil
                filter.
            </div>
            <!-- ================= END FILTER ================= -->

            <!-- ================= TABLE (scroll hanya tabel) ================= -->
            <div id="tableScroll" class="border border-slate-200 rounded-lg overflow-hidden">
                <div class="max-h-[420px] overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 z-20 bg-gray-100 border-b border-slate-200">
                            <tr class="text-center text-slate-700">
                                <th class="px-4 py-3 font-semibold">ID Pemesanan</th>
                                <th class="px-4 py-3 font-semibold">Nama User</th>
                                <th class="px-4 py-3 font-semibold">Tanggal Pemesanan</th>
                                <th class="px-4 py-3 font-semibold">Jam Pemesanan</th>
                                <th class="px-4 py-3 font-semibold">Ruangan</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Detail</th>
                            </tr>
                        </thead>

                        <tbody id="tableBody" class="divide-y divide-slate-200">
                            <?php if (!empty($pemesanan)) : ?>
                            <?php foreach ($pemesanan as $row): ?>
                            <?php
                                        $id = isset($row['ID_PEMESANAN']) ? $row['ID_PEMESANAN'] : '-';
                                        $user = isset($row['USERNAME']) ? $row['USERNAME'] : '-';
                                        $tglRaw = isset($row['TANGGAL_PEMESANAN']) ? $row['TANGGAL_PEMESANAN'] : '';
                                        $tglIndo = formatTanggalIndo($tglRaw);

                                        // buat filter tanggal (yyyy-mm-dd)
                                        $tglForFilter = !empty($tglRaw) ? date('Y-m-d', strtotime($tglRaw)) : '';

                                        $gedung = isset($row['NAMA_GEDUNG']) ? $row['NAMA_GEDUNG'] : '-';
                                        $status = isset($row['STATUS']) ? $row['STATUS'] : '-';
                                        $statusUpper = strtoupper(trim((string)$status));

                                        // jam
                                        $jamText = '-';
                                        if (!empty($row['JAM_PEMESANAN']) && !empty($row['JAM_SELESAI'])) {
                                            $jamText = date('H:i', strtotime($row['JAM_PEMESANAN'])) . ' - ' . date('H:i', strtotime($row['JAM_SELESAI'])) . ' WIB';
                                        }

                                        // badge status
                                        $badge = 'bg-slate-100 text-slate-700';
                                        if ($statusUpper === 'PROPOSAL APPROVE') $badge = 'bg-lime-100 text-lime-700';
                                        else if ($statusUpper === 'PROCESS') $badge = 'bg-yellow-100 text-yellow-700';
                                        else if ($statusUpper === 'REJECTED') $badge = 'bg-red-100 text-red-700';
                                        else if ($statusUpper === 'APPROVE & PAID') $badge = 'bg-emerald-100 text-emerald-700';
                                        else if ($statusUpper === 'SUBMITED' || $statusUpper === 'SUBMITTED') $badge = 'bg-blue-100 text-blue-700';
                                    ?>

                            <tr class="table-row hover:bg-gray-50 text-center"
                                data-id="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>"
                                data-status="<?= htmlspecialchars((string)$statusUpper, ENT_QUOTES, 'UTF-8'); ?>"
                                data-date="<?= htmlspecialchars((string)$tglForFilter, ENT_QUOTES, 'UTF-8'); ?>">
                                <td class="px-4 py-3 id font-medium text-slate-800">
                                    <?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td class="px-4 py-3 text-slate-700">
                                    <?= htmlspecialchars((string)$user, ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td class="px-4 py-3 tanggal text-slate-700">
                                    <?= htmlspecialchars((string)$tglIndo, ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td class="px-4 py-3 text-slate-700">
                                    <?= htmlspecialchars((string)$jamText, ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td class="px-4 py-3 text-slate-700">
                                    <?= htmlspecialchars((string)$gedung, ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td class="px-4 py-3 status">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $badge; ?>">
                                        <?= htmlspecialchars((string)$statusUpper, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <a href="<?= site_url('admin/detail_transaksi/' . $row['ID_PEMESANAN']); ?>"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-blue-50 text-blue-600">
                                        <span class="text-xs font-semibold">Detail</span>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Data pemesanan tidak ditemukan.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- ================= END TABLE ================= -->

            <!-- ================= PAGINATION ================= -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <button id="prevBtn"
                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-gray-600 text-center"></span>

                <div class="flex items-center gap-3">
                    <select id="rowsPerPage" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
            <!-- ================= END PAGINATION ================= -->

        </div>
    </main>

    <!-- ================= SCRIPT (AUTO FILTER + SORT BUTTON + PAGINATION) ================= -->
    <script>
    (function() {
        const rows = Array.from(document.querySelectorAll(".table-row"));
        let filteredRows = [...rows];

        const filterId = document.getElementById("filterId");
        const filterStatus = document.getElementById("filterStatus");
        const filterTanggal = document.getElementById("filterTanggal");
        const resetFilter = document.getElementById("resetFilter");

        const rowsPerPageSelect = document.getElementById("rowsPerPage");
        const pageInfo = document.getElementById("pageInfo");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        // Sort UI
        const sortBtn = document.getElementById("sortBtn");
        const sortMenu = document.getElementById("sortMenu");
        const sortItems = Array.from(document.querySelectorAll(".sortItem"));

        let currentPage = 1;
        let rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;

        // default sort: terbaru
        let sortMode = "new"; // "new" | "old"

        function norm(s) {
            return (s || "").toString().trim().toLowerCase().replace(/\s+/g, "");
        }

        function setSortCheck() {
            sortItems.forEach(btn => {
                const check = btn.querySelector(".check");
                if (!check) return;
                if (btn.dataset.sort === sortMode) check.classList.remove("hidden");
                else check.classList.add("hidden");
            });
        }

        function sortFilteredRows() {
            filteredRows.sort((a, b) => {
                const da = (a.dataset.date || "").trim(); // yyyy-mm-dd
                const db = (b.dataset.date || "").trim();

                // sort tanggal dulu
                if (da !== db) {
                    return sortMode === "new" ? db.localeCompare(da) : da.localeCompare(db);
                }

                // tie breaker by ID (angka)
                const ia = parseInt(a.dataset.id || "0", 10) || 0;
                const ib = parseInt(b.dataset.id || "0", 10) || 0;

                return sortMode === "new" ? (ib - ia) : (ia - ib);
            });
        }

        function renderTable() {
            rows.forEach(row => row.style.display = "none");

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            filteredRows.slice(start, end).forEach(row => {
                row.style.display = "";
            });

            const totalPages = Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));
            pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }

        function applyFilter() {
            const idVal = norm(filterId.value);
            const statusVal = (filterStatus.value || "").trim().toUpperCase();
            const tanggalVal = filterTanggal.value; // yyyy-mm-dd

            filteredRows = rows.filter(row => {
                const idText = norm(row.dataset.id);
                const statusText = (row.dataset.status || "").trim().toUpperCase();
                const tanggalText = (row.dataset.date || "").trim();

                const okId = !idVal ? true : idText.includes(idVal);
                const okStatus = !statusVal ? true : statusText === statusVal;
                const okTgl = !tanggalVal ? true : tanggalText === tanggalVal;

                return okId && okStatus && okTgl;
            });

            sortFilteredRows();
            currentPage = 1;
            renderTable();
        }

        // ===== SORT DROPDOWN BEHAVIOR =====
        function openSortMenu() {
            if (!sortMenu) return;
            sortMenu.classList.remove("hidden");
        }

        function closeSortMenu() {
            if (!sortMenu) return;
            sortMenu.classList.add("hidden");
        }

        if (sortBtn && sortMenu) {
            sortBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                sortMenu.classList.toggle("hidden");
            });

            // klik di luar => tutup
            document.addEventListener("click", () => closeSortMenu());

            // ESC => tutup
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") closeSortMenu();
            });
        }

        // klik item sort
        sortItems.forEach(btn => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                sortMode = btn.dataset.sort || "new";
                setSortCheck();
                closeSortMenu();
                sortFilteredRows();
                currentPage = 1;
                renderTable();
            });
        });

        // ===== events filter =====
        filterId.addEventListener("input", applyFilter);
        filterStatus.addEventListener("change", applyFilter);
        filterTanggal.addEventListener("change", applyFilter);

        resetFilter.addEventListener("click", () => {
            filterId.value = "";
            filterStatus.value = "";
            filterTanggal.value = "";

            sortMode = "new";
            setSortCheck();

            filteredRows = [...rows];
            sortFilteredRows();
            currentPage = 1;
            renderTable();
        });

        rowsPerPageSelect.addEventListener("change", () => {
            rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;
            currentPage = 1;
            renderTable();
        });

        prevBtn.addEventListener("click", () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        nextBtn.addEventListener("click", () => {
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });

        // init
        setSortCheck();
        sortFilteredRows();
        renderTable();
    })();
    </script>
</body>

</html>