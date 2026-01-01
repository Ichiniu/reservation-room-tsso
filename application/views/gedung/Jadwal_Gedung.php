<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Gedung</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Scroll hanya di area tabel */
        .table-scroll {
            max-height: 420px;
            /* ubah sesuai kebutuhan (atau 60vh) */
            overflow-y: auto;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Header sticky */
        .table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Scrollbar (opsional, biar lebih rapi) */
        .table-scroll::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.35);
            /* slate-500 */
            border-radius: 999px;
        }

        .table-scroll::-webkit-scrollbar-track {
            background: rgba(226, 232, 240, 0.7);
            /* slate-200 */
            border-radius: 999px;
        }
    </style>
</head>

<body class="min-h-screen text-slate-900 bg-slate-200">

    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <div class="max-w-6xl mx-auto px-4 py-8">

        <section class="rounded-3xl bg-white border border-slate-200 shadow-xl p-6">

            <!-- HEADER -->
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-slate-900">Jadwal Penggunaan Gedung</h1>
                <p class="text-sm text-slate-600">Data jadwal penggunaan gedung</p>
            </div>

            <!-- FILTER -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

                <!-- BULAN -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Bulan</label>
                    <select id="filterBulan"
                        class="w-full rounded-xl bg-white border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <option value="">Semua Bulan</option>
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>

                <!-- TAHUN -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun</label>
                    <select id="filterTahun"
                        class="w-full rounded-xl bg-white border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <option value="">Semua Tahun</option>
                        <?php for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- RESET -->
                <div class="flex items-end">
                    <button type="button" onclick="resetFilter()"
                        class="w-full rounded-xl px-4 py-3 text-white bg-slate-900 hover:bg-slate-800 transition shadow-sm">
                        Reset
                    </button>
                </div>

            </div>

            <!-- TABLE (SCROLL HANYA DI SINI) -->
            <div class="table-scroll rounded-2xl border border-slate-200 bg-white">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-50 text-xs text-slate-700">
                            <th class="px-4 py-3 text-left border-b border-slate-200">NO</th>
                            <th class="px-4 py-3 text-left border-b border-slate-200">TANGGAL</th>
                            <th class="px-4 py-3 text-left border-b border-slate-200">JAM</th>
                            <th class="px-4 py-3 text-left border-b border-slate-200">GEDUNG</th>
                            <th class="px-4 py-3 text-left border-b border-slate-200">DESKRIPSI</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <?php if (!empty($jadwal)): ?>
                            <?php $no = 1;
                            foreach ($jadwal as $row): ?>
                                <tr class="hover:bg-slate-50 transition"
                                    data-date="<?= date('Y-m-d', strtotime($row['TANGGAL_FINAL_PEMESANAN'])) ?>">
                                    <td class="px-4 py-3"><?= $no++ ?></td>
                                    <td class="px-4 py-3">
                                        <?= date('d M Y', strtotime($row['TANGGAL_FINAL_PEMESANAN'])) ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?= $row['JAM_MULAI'] ?> - <?= $row['JAM_SELESAI'] ?>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        <?= $row['NAMA_GEDUNG'] ?>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        <?= $row['DESKRIPSI_ACARA'] ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-500">
                                    Tidak ada data jadwal.
                                </td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <!-- PREV -->
                <button id="prevBtn"
                    class="px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    Prev
                </button>

                <!-- INFO -->
                <span id="pageInfo" class="text-sm text-slate-600 text-center"></span>

                <!-- NEXT + ROWS -->
                <div class="flex items-center gap-3 justify-end">
                    <select id="rowsPerPage"
                        class="rounded-xl bg-white border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn"
                        class="px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>

            </div>

        </section>
    </div>

    <!-- ================= JAVASCRIPT ================= -->
    <script>
        const rows = Array.from(document.querySelectorAll('tbody tr[data-date]'));

        const bulanSelect = document.getElementById('filterBulan');
        const tahunSelect = document.getElementById('filterTahun');
        const rowsSelect = document.getElementById('rowsPerPage');

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageInfo = document.getElementById('pageInfo');

        let rowsPerPage = parseInt(rowsSelect.value, 10);
        let currentPage = 1;
        let filteredRows = [];

        // DEFAULT BULAN & TAHUN SEKARANG
        bulanSelect.value = String(new Date().getMonth() + 1).padStart(2, '0');
        tahunSelect.value = new Date().getFullYear();

        function applyFilter() {
            const bulan = bulanSelect.value;
            const tahun = tahunSelect.value;

            filteredRows = rows.filter(row => {
                const date = row.dataset.date; // YYYY-MM-DD
                if (!bulan && !tahun) return true;
                if (bulan && !date.includes('-' + bulan)) return false;
                if (tahun && !date.startsWith(String(tahun))) return false;
                return true;
            });

            currentPage = 1;
            render();
        }

        function resetFilter() {
            bulanSelect.value = '';
            tahunSelect.value = '';
            filteredRows = [...rows];
            currentPage = 1;
            render();
        }

        function render() {
            // hide all
            rows.forEach(r => r.style.display = 'none');

            const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            filteredRows.slice(start, end).forEach(r => r.style.display = '');

            pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }

        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                render();
            }
        };

        nextBtn.onclick = () => {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
            if (currentPage < totalPages) {
                currentPage++;
                render();
            }
        };

        bulanSelect.addEventListener('change', applyFilter);
        tahunSelect.addEventListener('change', applyFilter);

        rowsSelect.addEventListener('change', () => {
            rowsPerPage = parseInt(rowsSelect.value, 10);
            currentPage = 1;
            render();
        });

        // INIT
        applyFilter();
    </script>

</body>

</html>