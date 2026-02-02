<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pembayaran</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- (opsional) kalau sidebar kamu masih pakai materialize -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">
</head>

<body class="bg-slate-200 min-h-screen">
    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <!-- HEADER (samakan seperti page lain) -->
        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">List Pembayaran</h1>
            <p class="text-sm text-slate-500">Kelola dan pantau data pembayaran</p>
        </div>

        <!-- CARD -->
        <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200">

            <!-- Card header -->
            <div
                class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="text-sm text-slate-600">
                    Data pembayaran ditampilkan berdasarkan filter yang dipilih.
                </div>
            </div>

            <div class="p-5">

                <!-- ================= FILTER (samakan style) ================= -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Cari (Kode Transaksi / Pemesanan / Nama)
                        </label>
                        <input id="filterText" type="text" placeholder="contoh: PB000123 / PMSN00090 / nama"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select id="filterStatus"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                            <option value="">Semua Status</option>
                            <option value="PENDING">PENDING</option>
                            <option value="APPROVED">APPROVED</option>
                            <option value="REJECTED">REJECTED</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button id="resetFilter"
                            class="w-full px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-sm text-slate-800">
                            Reset
                        </button>
                    </div>
                </div>

                <div class="text-xs text-slate-500 mb-4">
                    * Filter berjalan otomatis saat mengetik / memilih status. Pagination mengikuti hasil filter.
                </div>

                <!-- ================= TABLE (scroll hanya tabel) ================= -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div id="tableScroll" class="max-h-[420px] overflow-auto">
                        <?php if (!empty($notifs_admin_trx)): ?>
                            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <div class="font-semibold mb-2">Notifikasi</div>
                                <ul class="list-disc pl-5">
                                    <?php foreach ($notifs_admin_trx as $n): ?>
                                        <li>
                                            <span class="font-semibold"><?= htmlspecialchars($n['title']) ?></span>
                                            - <?= htmlspecialchars($n['message']) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <table class="w-full text-sm bg-white">
                            <thead class="sticky top-0 z-10 bg-slate-100 border-b border-slate-200">
                                <tr class="text-center font-semibold text-slate-700">
                                    <th class="px-4 py-3 w-[70px]">No</th>
                                    <th class="px-4 py-3 w-[160px]">Kode Transaksi</th>
                                    <th class="px-4 py-3 w-[180px]">Kode Pemesanan</th>
                                    <th class="px-4 py-3">Atas Nama</th>
                                    <th class="px-4 py-3 w-[150px] text-right">Jumlah</th>
                                    <th class="px-4 py-3 w-[130px]">Status</th>
                                    <th class="px-4 py-3 w-[90px]">Detail</th>
                                </tr>
                            </thead>

                            <tbody id="tableBody" class="divide-y divide-slate-200">
                                <?php if (!empty($pembayaran)): ?>
                                    <?php foreach ($pembayaran as $row): ?>
                                        <?php
                                        $kodeTransaksi = 'PB' . str_pad($row['ID_PEMBAYARAN'], 6, '0', STR_PAD_LEFT);
                                        $kodePemesanan = $row['KODE_PEMESANAN'] . $row['ID_PEMESANAN_RAW'];
                                        $nama = !empty($row['NAMA_LENGKAP']) ? $row['NAMA_LENGKAP'] : $row['ATAS_NAMA_PENGIRIM'];
                                        $status = strtoupper(trim($row['STATUS_VERIF']));
                                        $nominal = (float)$row['NOMINAL_TRANSFER'];

                                        // badge status
                                        $badge = 'bg-slate-100 text-slate-700';
                                        if ($status === 'APPROVED') $badge = 'bg-emerald-100 text-emerald-700';
                                        else if ($status === 'REJECTED') $badge = 'bg-red-100 text-red-700';
                                        else if ($status === 'PENDING') $badge = 'bg-yellow-100 text-yellow-800';
                                        ?>
                                        <tr class="table-row text-center hover:bg-slate-50"
                                            data-text="<?= htmlspecialchars(strtolower($kodeTransaksi . ' ' . $kodePemesanan . ' ' . $nama), ENT_QUOTES, 'UTF-8'); ?>"
                                            data-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-nominal="<?= htmlspecialchars($nominal, ENT_QUOTES, 'UTF-8'); ?>">

                                            <td class="px-4 py-3 cell-no"></td>

                                            <td class="px-4 py-3 font-medium text-slate-800">
                                                <?= htmlspecialchars($kodeTransaksi, ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td class="px-4 py-3 text-slate-700">
                                                <?= htmlspecialchars($kodePemesanan, ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td class="px-4 py-3 text-slate-700">
                                                <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td class="px-4 py-3 text-right font-semibold text-green-700">
                                                Rp <?= number_format($nominal, 0, ',', '.'); ?>
                                            </td>

                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $badge; ?>">
                                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                <a href="<?= site_url('admin/admin_controls/read_transaction/' . $row['ID_PEMBAYARAN']) ?>"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-blue-50 text-blue-600 hover:text-blue-800">
                                                    <i class="material-icons text-base">open_in_new</i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                            Data pembayaran belum tersedia
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SUMMARY TOTAL (samakan seperti page lain) -->
                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-slate-700">
                        <span class="font-semibold">Total (sesuai filter):</span>
                        <span id="totalAmount" class="font-semibold text-green-700"></span>
                    </div>
                </div>

                <!-- ================= PAGINATION (samakan style) ================= -->
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
                <!-- ================= END PAGINATION ================= -->

            </div>
        </div>
        <!-- /CARD -->

    </main>

    <script>
        (function() {
            const allRows = Array.from(document.querySelectorAll(".table-row"));
            let filteredRows = [...allRows];

            const filterText = document.getElementById("filterText");
            const filterStatus = document.getElementById("filterStatus");
            const resetBtn = document.getElementById("resetFilter");

            const rowsPerPageSelect = document.getElementById("rowsPerPage");
            const pageInfo = document.getElementById("pageInfo");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const totalAmount = document.getElementById("totalAmount");
            const scrollBox = document.getElementById("tableScroll");

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;

            function applyFilter() {
                const text = (filterText.value || "").toLowerCase().trim();
                const status = (filterStatus.value || "").toUpperCase().trim();

                filteredRows = allRows.filter(r => {
                    const okText = (r.dataset.text || "").includes(text);
                    const okStatus = !status || (r.dataset.status || "") === status;
                    return okText && okStatus;
                });

                currentPage = 1;
                render();
            }

            function render() {
                // hide all
                allRows.forEach(r => r.style.display = "none");

                const totalRows = filteredRows.length;
                const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));
                if (currentPage > totalPages) currentPage = totalPages;

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                // show current page
                filteredRows.forEach((r, i) => {
                    if (i >= start && i < end) r.style.display = "";
                });

                // renumber No for visible
                let no = start + 1;
                filteredRows.forEach((r, i) => {
                    if (i >= start && i < end) {
                        const cell = r.querySelector(".cell-no");
                        if (cell) cell.textContent = no++;
                    }
                });

                // update total (sesuai filter)
                let total = 0;
                filteredRows.forEach(r => {
                    total += parseFloat(r.dataset.nominal || "0");
                });
                totalAmount.textContent = "Rp " + total.toLocaleString("id-ID");

                // page info
                const showingFrom = totalRows === 0 ? 0 : start + 1;
                const showingTo = Math.min(end, totalRows);
                pageInfo.textContent =
                    `Page ${currentPage} of ${totalPages} • Showing ${showingFrom}-${showingTo} of ${totalRows}`;

                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= totalPages;

                if (scrollBox) scrollBox.scrollTop = 0;
            }

            // events
            filterText.addEventListener("input", applyFilter);
            filterStatus.addEventListener("change", applyFilter);

            resetBtn.addEventListener("click", function() {
                filterText.value = "";
                filterStatus.value = "";
                applyFilter();
            });

            rowsPerPageSelect.addEventListener("change", function() {
                rowsPerPage = parseInt(this.value, 10) || 10;
                currentPage = 1;
                render();
            });

            prevBtn.addEventListener("click", function() {
                if (currentPage > 1) {
                    currentPage--;
                    render();
                }
            });

            nextBtn.addEventListener("click", function() {
                const totalPages = Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));
                if (currentPage < totalPages) {
                    currentPage++;
                    render();
                }
            });

            // init
            applyFilter(); // biar total + nomor langsung valid
        })();
    </script>

</body>

</html>