<?php
$session_id = $this->session->userdata('admin_username');
$this->load->helper('text');

/* ===== helper format tanggal Indonesia (11 Januari 2026) ===== */
function formatTanggalIndo($tgl)
{
    if (empty($tgl)) return '-';

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

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
    <title>Admin Smart Office</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- (Opsional) kalau sidebar kamu masih pakai materialize -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/home/style.css') ?>" rel="stylesheet">
</head>

<body class="bg-slate-200 min-h-screen flex flex-col">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="flex-1 pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Jadwal Ruangan Terbooking</h1>
            <p class="text-sm text-slate-500">Daftar Pemesanan Ruangan</p>
        </div>

        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">

            <!-- FILTER (langsung jalan, tanpa tombol filter) -->
            <div class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Kode Pemesanan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kode Pemesanan</label>
                        <input id="filterKode" type="text" placeholder="contoh: PMSN00094 / 94"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300" />
                    </div>

                    <!-- Dropdown User -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">User</label>
                        <select id="filterUser"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <option value="">Semua User</option>
                        </select>
                    </div>

                    <!-- Tombol Reset -->
                    <div class="flex items-end">
                        <button id="resetFilter"
                            class="w-full px-4 py-2 rounded-lg bg-slate-200 text-slate-800 text-sm hover:bg-slate-300">
                            Reset
                        </button>
                    </div>
                </div>

                <div class="mt-3 text-xs text-slate-500">
                    * Filter berjalan otomatis saat mengetik / memilih dropdown. Pagination mengikuti hasil filter.
                </div>
            </div>

            <!-- TABLE -->
            <div id="tableScroll"
                class="overflow-x-auto max-h-[420px] overflow-y-auto relative border border-slate-200 rounded-lg">
                <table class="w-full text-sm bg-white">
                    <thead class="sticky top-0 z-20 bg-slate-100 shadow-sm">
                        <tr class="border-b border-slate-200">
                            <th class="px-4 py-3 text-center">No</th>
                            <th class="px-4 py-3 text-center">ID</th>
                            <th class="px-4 py-3 text-center">Ruangan</th>
                            <th class="px-4 py-3 text-center">User</th>
                            <th class="px-4 py-3 text-center">Tanggal</th>
                            <th class="px-4 py-3 text-center">Jam</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        <?php if (!empty($front_data)): ?>
                        <?php $no = 1; foreach ($front_data as $data): ?>
                        <?php
                            $rawId = $data->ID_PEMESANAN;
                            $displayId = (stripos((string)$rawId, 'PMSN') === 0) ? $rawId : ('PMSN000' . $rawId);
                            $linkId = (int) preg_replace('/\D+/', '', (string)$rawId);

                            $jam_mulai = !empty($data->JAM) ? $data->JAM : '-';
                            $username  = !empty($data->USERNAME) ? $data->USERNAME : '-';
                            $namaGedung = !empty($data->NAMA_GEDUNG) ? $data->NAMA_GEDUNG : '-';

                            // tanggal -> format Indonesia
                            $tglRaw = !empty($data->TANGGAL_PEMESANAN) ? $data->TANGGAL_PEMESANAN : '';
                            $tglIndo = formatTanggalIndo($tglRaw);
                        ?>
                        <tr class="table-row hover:bg-slate-50"
                            data-kode="<?= htmlspecialchars($displayId, ENT_QUOTES, 'UTF-8'); ?>"
                            data-user="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
                            <td class="px-4 py-3 text-center cell-no"><?= $no++; ?></td>

                            <td class="px-4 py-3 text-center font-semibold cell-kode">
                                <?= htmlspecialchars($displayId, ENT_QUOTES, 'UTF-8'); ?>
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
                                <?= htmlspecialchars($jam_mulai, ENT_QUOTES, 'UTF-8'); ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <a href="<?= site_url('admin/detail_pemesanan/' . $linkId) ?>"
                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                                Belum ada jadwal terbooking (SUBMITED).
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button id="prevBtn"
                    class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40 disabled:cursor-not-allowed">
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-slate-600 text-center"></span>

                <div class="flex items-center gap-3">
                    <select id="rowsPerPage" class="rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn"
                        class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>

        </div>
    </main>

    <footer class="mt-auto text-xs text-gray-500 text-center py-4">
        © <?= date('Y'); ?> Smart Office • Admin Panel
    </footer>

    <script>
    (function() {
        const allRows = Array.from(document.querySelectorAll(".table-row"));
        const rowsPerPageSelect = document.getElementById("rowsPerPage");
        const pageInfo = document.getElementById("pageInfo");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const scrollBox = document.getElementById("tableScroll");

        // filter inputs
        const filterKode = document.getElementById("filterKode");
        const filterUser = document.getElementById("filterUser");
        const resetFilterBtn = document.getElementById("resetFilter");

        let currentPage = 1;
        let rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;
        let activeRows = [...allRows];

        function buildUserDropdown() {
            const users = new Set();
            allRows.forEach(r => {
                const u = (r.dataset.user || "").trim();
                if (u) users.add(u);
            });

            const sorted = Array.from(users).sort((a, b) => a.localeCompare(b));
            filterUser.innerHTML = '<option value="">Semua User</option>';
            sorted.forEach(u => {
                const opt = document.createElement("option");
                opt.value = u;
                opt.textContent = u;
                filterUser.appendChild(opt);
            });
        }

        function normalizeKode(s) {
            return (s || "").toString().trim().toLowerCase().replace(/\s+/g, "");
        }

        function applyFilterAuto() {
            const kodeVal = normalizeKode(filterKode.value);
            const userVal = (filterUser.value || "").trim();

            activeRows = allRows.filter(row => {
                const kode = normalizeKode(row.dataset.kode);
                const user = (row.dataset.user || "").trim();
                const okKode = !kodeVal ? true : kode.includes(kodeVal);
                const okUser = !userVal ? true : user === userVal;
                return okKode && okUser;
            });

            currentPage = 1;
            renderTable();
        }

        function resetFilter() {
            filterKode.value = "";
            filterUser.value = "";
            activeRows = [...allRows];
            currentPage = 1;
            renderTable();
        }

        function renderTable() {
            const total = activeRows.length;
            const totalPages = Math.max(1, Math.ceil(total / rowsPerPage));

            if (currentPage > totalPages) currentPage = totalPages;

            allRows.forEach(r => r.style.display = "none");

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            activeRows.forEach((row, idx) => {
                row.style.display = (idx >= start && idx < end) ? "" : "none";
            });

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;

            const showingFrom = total === 0 ? 0 : start + 1;
            const showingTo = Math.min(end, total);

            pageInfo.textContent =
                `Page ${currentPage} of ${totalPages} • Showing ${showingFrom}-${showingTo} of ${total}`;

            // renumber No sesuai halaman
            let n = start + 1;
            activeRows.forEach((row, idx) => {
                if (idx >= start && idx < end) {
                    const cell = row.querySelector(".cell-no");
                    if (cell) cell.textContent = n++;
                }
            });

            if (scrollBox) scrollBox.scrollTop = 0;
        }

        // events (AUTO)
        filterKode.addEventListener("input", applyFilterAuto);
        filterUser.addEventListener("change", applyFilterAuto);

        resetFilterBtn.addEventListener("click", resetFilter);

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
            const totalPages = Math.max(1, Math.ceil(activeRows.length / rowsPerPage));
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });

        // init
        buildUserDropdown();
        activeRows = [...allRows];
        renderTable();
    })();
    </script>

</body>

</html>