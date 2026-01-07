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
        .table-scroll {
            max-height: 420px;
            overflow-y: auto;
            overflow-x: auto;
        }

        .table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #f8fafc;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-200 text-slate-900">
<body class="min-h-screen bg-slate-200 text-slate-900">

    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <div class="max-w-6xl mx-auto px-4 py-8">

        <section class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6">
        <section class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6">

            <!-- HEADER -->
            <div class="mb-6">
                <h1 class="text-2xl font-semibold">Jadwal Penggunaan Gedung</h1>
                <h1 class="text-2xl font-semibold">Jadwal Penggunaan Gedung</h1>
                <p class="text-sm text-slate-600">Data jadwal penggunaan gedung</p>
            </div>

            <!-- FILTER -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="text-xs font-semibold text-slate-700">Bulan</label>
                    <select id="filterBulan" class="w-full rounded-xl border px-4 py-3">
                    <label class="text-xs font-semibold text-slate-700">Bulan</label>
                    <select id="filterBulan" class="w-full rounded-xl border px-4 py-3">
                        <option value="">Semua Bulan</option>
                        <?php
                        $bulan = [
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember'
                        ];
                        foreach ($bulan as $k => $v) {
                            echo "<option value='$k'>$v</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-700">Tahun</label>
                    <select id="filterTahun" class="w-full rounded-xl border px-4 py-3">
                    <label class="text-xs font-semibold text-slate-700">Tahun</label>
                    <select id="filterTahun" class="w-full rounded-xl border px-4 py-3">
                        <option value="">Semua Tahun</option>
                        <?php for($y=date('Y')-3;$y<=date('Y')+1;$y++): ?>
                        <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="flex items-end">
                    <button onclick="resetFilter()" class="w-full bg-slate-900 text-white rounded-xl px-4 py-3">
                    <button onclick="resetFilter()" class="w-full bg-slate-900 text-white rounded-xl px-4 py-3">
                        Reset
                    </button>
                </div>
            </div>

            <!-- INFO TOTAL -->
            <p id="totalInfo" class="text-sm text-slate-500 mb-3"></p>

            <!-- TABLE -->
            <div class="table-scroll rounded-2xl border">
            <!-- INFO TOTAL -->
            <p id="totalInfo" class="text-sm text-slate-500 mb-3"></p>

            <!-- TABLE -->
            <div class="table-scroll rounded-2xl border">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-xs text-slate-700">
                            <th class="px-4 py-3 text-left">NO</th>
                            <th class="px-4 py-3 text-left">TANGGAL</th>
                            <th class="px-4 py-3 text-left">JAM</th>
                            <th class="px-4 py-3 text-left">GEDUNG</th>
                            <th class="px-4 py-3 text-left">DESKRIPSI</th>
                        <tr class="text-xs text-slate-700">
                            <th class="px-4 py-3 text-left">NO</th>
                            <th class="px-4 py-3 text-left">TANGGAL</th>
                            <th class="px-4 py-3 text-left">JAM</th>
                            <th class="px-4 py-3 text-left">GEDUNG</th>
                            <th class="px-4 py-3 text-left">DESKRIPSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jadwal)): ?>
                            <?php $no = 1;
                            foreach ($jadwal as $row): ?>
                                <tr data-date="<?= date('Y-m-d', strtotime($row['TANGGAL_FINAL_PEMESANAN'])) ?>">
                                    <td class="px-4 py-3"><?= $no++ ?></td>
                                    <td class="px-4 py-3"><?= date('d M Y', strtotime($row['TANGGAL_FINAL_PEMESANAN'])) ?></td>
                                    <td class="px-4 py-3"><?= $row['JAM_MULAI'] . ' - ' . $row['JAM_SELESAI'] ?></td>
                                    <td class="px-4 py-3 font-semibold"><?= $row['NAMA_GEDUNG'] ?></td>
                                    <td class="px-4 py-3"><?= $row['DESKRIPSI_ACARA'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-6">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-6 flex justify-between items-center gap-4 flex-wrap">
                <button id="prevBtn" class="px-4 py-2 border rounded-xl">Prev</button>
            <div class="mt-6 flex justify-between items-center gap-4 flex-wrap">
                <button id="prevBtn" class="px-4 py-2 border rounded-xl">Prev</button>

                <span id="pageInfo" class="text-sm"></span>
                <span id="pageInfo" class="text-sm"></span>

                <div class="flex gap-3">
                    <select id="rowsPerPage" class="border rounded-xl px-3 py-2">
                <div class="flex gap-3">
                    <select id="rowsPerPage" class="border rounded-xl px-3 py-2">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn" class="px-4 py-2 border rounded-xl">Next</button>
                    <button id="nextBtn" class="px-4 py-2 border rounded-xl">Next</button>
                </div>
            </div>

        </section>
    </div>

    <script>
        const rows = Array.from(document.querySelectorAll('tbody tr[data-date]'));
        const bulanSelect = document.getElementById('filterBulan');
        const tahunSelect = document.getElementById('filterTahun');
        const rowsSelect = document.getElementById('rowsPerPage');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageInfo = document.getElementById('pageInfo');
        const totalInfo = document.getElementById('totalInfo');

        let rowsPerPage = parseInt(rowsSelect.value);
        let currentPage = 1;
        let filteredRows = [...rows];

        function render() {
            rows.forEach(r => r.style.display = 'none');

            const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            filteredRows.slice(start, end).forEach(r => r.style.display = '');

            pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;
            totalInfo.textContent = `Menampilkan ${filteredRows.length} dari ${rows.length} data`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;

            console.group("LOG JADWAL");
            console.log("Total data:", rows.length);
            console.log("Setelah filter:", filteredRows.length);
            console.log("Rows/page:", rowsPerPage);
            console.log("Total halaman:", totalPages);
            console.groupEnd();
        }

    function applyFilter() {
        const bulan = bulanSelect.value;
        const tahun = tahunSelect.value;

            filteredRows = rows.filter(r => {
                const d = r.dataset.date;
                if (bulan && !d.includes('-' + bulan)) return false;
                if (tahun && !d.startsWith(tahun)) return false;
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

        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                render();
            }
        };
        nextBtn.onclick = () => {
            currentPage++;
            render();
        };

        bulanSelect.onchange = applyFilter;
        tahunSelect.onchange = applyFilter;

        rowsSelect.onchange = () => {
            rowsPerPage = parseInt(rowsSelect.value);
            currentPage = 1;
            render();
        };

        // INIT
        render();
    </script>

</body>

</html> 10