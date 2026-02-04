<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

$no = 1;

$first_date_period  = !empty($first_period) ? date_create($first_period) : null;
$second_date_period = !empty($last_period) ? date_create($last_period) : null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Office</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
    /* biar scroll bar tidak bikin header/body “geser” di beberapa browser */
    .scroll-stable {
        scrollbar-gutter: stable;
    }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-6 pb-10">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-xl font-semibold mb-4">Rekapitulasi Aktivitas</h1>

            <!-- CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
                <!-- HEADER -->
                <div
                    class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-800">Periode:</span>
                        <?php if ($first_date_period && $second_date_period): ?>
                        <?= date_format($first_date_period, 'd F Y'); ?>
                        <span class="mx-2">—</span>
                        <?= date_format($second_date_period, 'd F Y'); ?>
                        <?php else: ?>
                        -
                        <?php endif; ?>
                    </div>

                    <a class="text-sm font-medium text-teal-700 hover:text-teal-800 underline"
                        href="<?= site_url('admin/kegiatan_download_pdf/' . $first_period . '/' . $last_period); ?>">
                        Ekspor ke PDF
                    </a>
                </div>

                <div class="p-5">
                    <!-- TABLE WRAP: HANYA INI YANG SCROLL -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="max-h-[420px] overflow-auto scroll-stable">
                            <table id="rekapTable" class="min-w-[980px] w-full text-sm">
                                <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200">
                                    <tr class="text-left">
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[70px]">No</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[180px]">Nama Gedung</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[160px]">Tanggal Pemesanan
                                        </th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[160px]">Tanggal Approval
                                        </th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[240px]">Kegiatan</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[140px]">Jam Kegiatan</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[200px]">Nama Pemesan</th>
                                    </tr>
                                </thead>

                                <tbody id="rekapBody" class="divide-y divide-slate-200">
                                    <?php if (!empty($hasil)): ?>
                                    <?php foreach ($hasil as $row): ?>
                                    <?php
                        $date = !empty($row['TANGGAL_FINAL_PEMESANAN']) ? date_create($row['TANGGAL_FINAL_PEMESANAN']) : null;
                        $date_approval = !empty($row['TANGGAL_APPROVAL']) ? date_create($row['TANGGAL_APPROVAL']) : null;

                        $jamMulai = !empty($row['JAM_MULAI']) ? $row['JAM_MULAI'] : (!empty($row['JAM_PEMESANAN']) ? $row['JAM_PEMESANAN'] : null);
                        $jamSelesai = !empty($row['JAM_SELESAI']) ? $row['JAM_SELESAI'] : null;

                        $jamText = '-';
                        if (!empty($jamMulai) && !empty($jamSelesai)) {
                          $jamText = date('H:i', strtotime($jamMulai)) . ' - ' . date('H:i', strtotime($jamSelesai));
                        } elseif (!empty($jamMulai)) {
                          $jamText = date('H:i', strtotime($jamMulai));
                        }
                      ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-center w-[70px]"><?= $no++; ?></td>
                                        <td class="px-4 py-3 w-[180px]">
                                            <?= !empty($row['NAMA_GEDUNG']) ? $row['NAMA_GEDUNG'] : '-'; ?></td>
                                        <td class="px-4 py-3 w-[160px]">
                                            <?= $date ? date_format($date, 'd M Y') : '-'; ?></td>
                                        <td class="px-4 py-3 w-[160px]">
                                            <?= $date_approval ? date_format($date_approval, 'd M Y') : '-'; ?></td>

                                        <!-- Kegiatan boleh wrap -->
                                        <td class="px-4 py-3 w-[240px] whitespace-normal break-words">
                                            <?= !empty($row['DESKRIPSI_ACARA']) ? $row['DESKRIPSI_ACARA'] : '-'; ?>
                                        </td>

                                        <td class="px-4 py-3 w-[140px]"><?= $jamText; ?></td>
                                        <td class="px-4 py-3 w-[200px]">
                                            <?= !empty($row['NAMA_LENGKAP']) ? $row['NAMA_LENGKAP'] : '-'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-slate-500">Tidak ada data.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
    // Pagination client-side + reset scroll tabel saat pindah halaman
    (function() {
        const tbody = document.getElementById('rekapBody');
        const scrollBox = document.querySelector('.max-h-\\[420px\\]');
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

            // nomor ulang sesuai urutan global
            let visibleNo = start + 1;
            rows.forEach((row, idx) => {
                if (idx >= start && idx < end) {
                    const firstCell = row.querySelector('td');
                    if (firstCell) firstCell.textContent = visibleNo++;
                }
            });

            // balik ke atas tabel saat pindah halaman
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