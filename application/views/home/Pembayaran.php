<?php
$no = 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet">
</head>

<body class="min-h-screen flex flex-col bg-slate-200 text-black">

    <!-- NAVBAR & HEADER -->
    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <!-- MAIN CONTENT -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 py-8">

            <section class="bg-white rounded-3xl shadow-xl p-6">
                <h2 class="text-xl font-semibold mb-4">Daftar Pembayaran</h2>

                <!-- SCROLL AREA (HANYA TABEL) -->
                <div class="max-h-[420px] overflow-y-auto rounded-2xl ring-1 ring-black/5">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">ID Pemesanan</th>
                                <th class="px-4 py-3 text-left">ID Transaksi</th>
                                <th class="px-4 py-3 text-left">Atas Nama</th>
                                <th class="px-4 py-3 text-left">Tanggal Transfer</th>
                                <th class="px-4 py-3 text-left">Jumlah Transfer</th>
                                <th class="px-4 py-3 text-left">Total Tagihan</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($res)) : ?>
                            <?php foreach ($res as $row) : ?>
                            <?php
                                    $kode   = isset($row['KODE_PEMESANAN']) ? $row['KODE_PEMESANAN'] : '';
                                    $idraw  = isset($row['ID_PEMESANAN_RAW']) ? $row['ID_PEMESANAN_RAW'] : '';
                                    $idbyr  = isset($row['ID_PEMBAYARAN']) ? $row['ID_PEMBAYARAN'] : '';
                                    $atas   = isset($row['ATAS_NAMA_PENGIRIM']) ? $row['ATAS_NAMA_PENGIRIM'] : '-';
                                    $tgl    = isset($row['TANGGAL_TRANSFER']) ? $row['TANGGAL_TRANSFER'] : '';
                                    $nom    = isset($row['NOMINAL_TRANSFER']) ? (int)$row['NOMINAL_TRANSFER'] : 0;
                                    $total  = isset($row['TOTAL_TAGIHAN']) ? (int)$row['TOTAL_TAGIHAN'] : 0;
                                    $status = isset($row['STATUS_VERIF']) ? $row['STATUS_VERIF'] : '-';
                                    ?>
                            <tr class="table-row border-b hover:bg-slate-50">
                                <td class="px-4 py-3"><?php echo $no++; ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($kode . $idraw); ?></td>
                                <td class="px-4 py-3"><?php echo 'PYMT000' . htmlspecialchars($idbyr); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($atas); ?></td>

                                <!-- TANGGAL (DIFORMAT VIA JS) -->
                                <td class="px-4 py-3 date-cell" data-date="<?php echo htmlspecialchars($tgl); ?>">
                                    <?php echo htmlspecialchars($tgl); ?>
                                </td>

                                <td class="px-4 py-3"><?php echo 'Rp ' . number_format($nom); ?></td>
                                <td class="px-4 py-3"><?php echo 'Rp ' . number_format($total); ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs bg-slate-200">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else : ?>
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-slate-500">
                                    Belum ada pembayaran yang dikonfirmasi
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button id="prevBtn" class="px-4 py-2 rounded-xl border bg-white">
                        Prev
                    </button>

                    <span id="pageInfo" class="text-sm text-slate-600"></span>

                    <div class="flex gap-3">
                        <select id="rowsPerPage" class="rounded-xl border px-3 py-2">
                            <option value="5">5 rows</option>
                            <option value="10" selected>10 rows</option>
                            <option value="25">25 rows</option>
                        </select>

                        <button id="nextBtn" class="px-4 py-2 rounded-xl border bg-white">
                            Next
                        </button>
                    </div>
                </div>

            </section>
        </div>
    </main>

    <!-- FOOTER -->
    <?php $this->load->view('components/footer'); ?>

    <!-- JS -->
    <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>

    <!-- FORMAT TANGGAL + PAGINATION -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ===== FORMAT TANGGAL INDONESIA (01, 02, ...) ===== */
        const bulanIndo = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        document.querySelectorAll('.date-cell').forEach(function(el) {
            const raw = el.getAttribute('data-date');
            if (!raw) return;

            const date = new Date(raw);
            if (isNaN(date)) return;

            const tgl = String(date.getDate()).padStart(2, '0');

            el.textContent =
                tgl + ' ' +
                bulanIndo[date.getMonth()] + ' ' +
                date.getFullYear();
        });

        /* ===== PAGINATION ===== */
        const rows = Array.from(document.querySelectorAll('.table-row'));
        const rowsSelect = document.getElementById('rowsPerPage');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageInfo = document.getElementById('pageInfo');

        let rowsPerPage = parseInt(rowsSelect.value, 10);
        let currentPage = 1;

        function renderTable() {
            const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;

            rows.forEach(row => row.style.display = 'none');

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.slice(start, end).forEach(row => row.style.display = '');

            pageInfo.textContent = 'Page ' + currentPage + ' of ' + totalPages;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }

        rowsSelect.addEventListener('change', function() {
            rowsPerPage = parseInt(this.value, 10);
            currentPage = 1;
            renderTable();
        });

        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        nextBtn.addEventListener('click', function() {
            currentPage++;
            renderTable();
        });

        renderTable();
    });
    </script>

</body>

</html>