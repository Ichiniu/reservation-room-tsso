<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

$no    = 1;
$total = 0;

$rows = $row ?? [];

/* ===== Format tanggal Indonesia ===== */
// Helper formatTanggalIndo sudah di-autoload (tanggal_helper)
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Transaksi Det</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .scroll-stable {
            scrollbar-gutter: stable;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <!-- SIDEBAR COMPONENT -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-xl font-semibold mb-2">Rekapitulasi Transaksi</h1>

            <div class="text-sm text-slate-600 mb-5">
                <span class="font-semibold text-slate-800">Periode:</span>
                <?= format_tanggal_indo($start_date); ?>
                <span class="mx-2"> - </span>
                <?= format_tanggal_indo($end_date); ?>
            </div>

            <!-- CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div
                    class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="text-sm text-slate-600">
                        Data transaksi berdasarkan periode yang dipilih.
                    </div>

                    <a class="text-sm font-medium text-teal-700 hover:text-teal-800 underline"
                        href="<?= site_url('admin/transaksi_download_pdf/' . $start_date . '/' . $end_date); ?>">
                        Ekspor ke PDF
                    </a>
                </div>

                <div class="p-5">
                    <!-- TABLE WRAP: yang scroll hanya tabel -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div id="tableScroll" class="max-h-[420px] overflow-auto scroll-stable">
                            <table id="rekapTable" class="min-w-[1100px] w-full text-sm">
                                <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200">
                                    <tr class="text-left">
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[70px] text-center">No</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[170px]">Kode Pembayaran
                                        </th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[170px]">Kode Pemesanan</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[260px]">Atas Nama</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[140px]">Bank</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[180px]">Tanggal Transfer
                                        </th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[160px] text-right">Jumlah
                                            Transfer</th>
                                    </tr>
                                </thead>

                                <tbody id="rekapBody" class="divide-y divide-slate-200">
                                    <?php if (count($rows) > 0): ?>
                                        <?php foreach ($rows as $r): ?>
                                            <?php
                                            // === ATAS NAMA (INTERNAL: Nama + (PT - Departemen)) ===
                                            $isInternal = (strtoupper(trim((string)(isset($r['perusahaan']) ? $r['perusahaan'] : ''))) === 'INTERNAL');

                                            if ($isInternal) {
                                                $nama = (!empty($r['NAMA_LENGKAP'])) ? $r['NAMA_LENGKAP'] : '-';
                                                $pt   = (!empty($r['nama_perusahaan'])) ? $r['nama_perusahaan'] : 'PT Tiga Serangkai Pustaka Mandiri';
                                                $dept = (!empty($r['departemen'])) ? $r['departemen'] : '-';

                                                $atasNamaHtml =
                                                    htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') .
                                                    "<div class='text-xs text-slate-500 mt-0.5'>" .
                                                    htmlspecialchars($pt, ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($dept, ENT_QUOTES, 'UTF-8') .
                                                    "</div>";
                                            } else {
                                                $atasNama = $r['ATAS_NAMA_PENGIRIM'] ?? $r['ATAS_NAMA'] ?? '-';

                                                $atasNamaHtml = htmlspecialchars($atasNama, ENT_QUOTES, 'UTF-8');
                                            }

                                            $bank = !empty($r['BANK_PENGIRIM']) ? $r['BANK_PENGIRIM'] : '-';

                                            $idPemesanan = $r['ID_PEMESANAN_RAW'] ?? $r['ID_PEMESANAN'] ?? '';

                                            $kodePemesananPrefix = $r['KODE_PEMESANAN'] ?? 'PMSN000';
                                            $kodePemesanan = $kodePemesananPrefix . $idPemesanan;

                                            $kodePembayaranPrefix = $r['KODE_PEMBAYARAN'] ?? 'PB0000';
                                            $idPembayaran = $r['ID_PEMBAYARAN'] ?? '';
                                            $kodePembayaran = $kodePembayaranPrefix . $idPembayaran;

                                            // Tanggal Transfer versi Indonesia
                                            $tglIndo = !empty($r['TANGGAL_TRANSFER']) ? format_tanggal_indo($r['TANGGAL_TRANSFER']) : '-';

                                            $nominal = (float)($r['NOMINAL_TRANSFER'] ?? 0);
                                            $total  += $nominal;
                                            ?>
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-3 text-center w-[70px]"><?= $no++; ?></td>
                                                <td class="px-4 py-3 w-[170px]">
                                                    <?= htmlspecialchars($kodePembayaran, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="px-4 py-3 w-[170px]">
                                                    <?= htmlspecialchars($kodePemesanan, ENT_QUOTES, 'UTF-8'); ?></td>

                                                <td class="px-4 py-3 w-[260px] whitespace-normal break-words">
                                                    <?= $atasNamaHtml; ?>
                                                </td>

                                                <td class="px-4 py-3 w-[140px]">
                                                    <?= htmlspecialchars($bank, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="px-4 py-3 w-[180px]"><?= $tglIndo; ?></td>
                                                <td class="px-4 py-3 w-[160px] text-right">
                                                    <?= "Rp." . number_format($nominal, 0, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                                                Tidak ada data transaksi pada periode ini.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SUMMARY TOTAL -->
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-sm text-slate-700">
                            <span class="font-semibold">Total Transfer:</span>
                            <span class="font-semibold"><?= "Rp." . number_format($total, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <button id="prevBtn"
                            class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40 disabled:cursor-not-allowed">
                            Prev
                        </button>

                        <span id="pageInfo" class="text-sm text-slate-600 text-center"></span>

                        <div class="flex items-center gap-3">
                            <select id="rowsPerPage" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
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
            </div>
            <!-- /CARD -->
        </div>
    </main>

    <script>
        (function() {
            const tbody = document.getElementById('rekapBody');
            const scrollBox = document.getElementById('tableScroll');
            if (!tbody) return;

            let rows = Array.from(tbody.querySelectorAll('tr'));
            const onlyEmptyRow = rows.length === 1 && rows[0].innerText.toLowerCase().includes('tidak ada data');
            if (onlyEmptyRow) return;

            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const pageInfo = document.getElementById('pageInfo');
            const rowsPerPageSelect = document.getElementById('rowsPerPage');

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;

            function totalPages() {
                return Math.max(1, Math.ceil(rows.length / rowsPerPage));
            }

            function render() {
                const tp = totalPages();
                if (currentPage > tp) currentPage = tp;

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, idx) => {
                    row.style.display = (idx >= start && idx < end) ? '' : 'none';
                });

                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= tp;

                const totalRows = rows.length;
                const showingFrom = totalRows === 0 ? 0 : start + 1;
                const showingTo = Math.min(end, totalRows);

                pageInfo.textContent =
                    `Page ${currentPage} of ${tp} • Showing ${showingFrom}-${showingTo} of ${totalRows}`;

                // renumber No
                let visibleNo = start + 1;
                rows.forEach((row, idx) => {
                    if (idx >= start && idx < end) {
                        const firstCell = row.querySelector('td');
                        if (firstCell) firstCell.textContent = visibleNo++;
                    }
                });

                if (scrollBox) scrollBox.scrollTop = 0;
            }

            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    render();
                }
            });

            nextBtn.addEventListener('click', function() {
                if (currentPage < totalPages()) {
                    currentPage++;
                    render();
                }
            });

            rowsPerPageSelect.addEventListener('change', function() {
                rowsPerPage = parseInt(this.value, 10) || 10;
                currentPage = 1;
                render();
            });

            render();
        })();
    </script>

</body>

</html>