<?php
$no = 1;

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function badge_class($statusUpper)
{
  $s = strtoupper(trim((string)$statusUpper));

  // Sesuaikan kalau status kamu beda
  if ($s === 'CONFIRMED' || $s === 'APPROVED') return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
  if ($s === 'PENDING' || $s === 'PROCESS')   return 'bg-amber-50 text-amber-800 border border-amber-200';
  if ($s === 'REJECTED' || $s === 'FAILED')   return 'bg-red-50 text-red-700 border border-red-200';
  if ($s === 'SUBMITED' || $s === 'SUBMITTED')return 'bg-purple-50 text-purple-700 border border-purple-200';

  return 'bg-slate-50 text-slate-700 border border-slate-200';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 text-slate-900">
    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 space-y-6">

            <!-- HERO -->
            <section
                class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 backdrop-blur shadow-sm">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-28 -right-28 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>
                    <div class="absolute -bottom-28 -left-28 h-72 w-72 rounded-full bg-emerald-200/40 blur-3xl"></div>
                </div>

                <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div>
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-semibold">
                            <span class="material-icons text-sm">payments</span>
                            Pembayaran
                        </div>

                        <h1 class="mt-3 text-2xl md:text-3xl font-extrabold tracking-tight">
                            Daftar Pembayaran
                        </h1>
                        <!-- <p class="mt-2 text-sm md:text-base text-slate-600">
                            Riwayat pembayaran yang masuk. Tanggal ditampilkan format Indonesia.
                        </p> -->
                    </div>

                    <div
                        class="hidden sm:flex items-center gap-2 text-xs text-slate-600 bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
                        <span class="material-icons text-base text-slate-500">info</span>
                        Scroll hanya pada tabel
                    </div>
                </div>
            </section>

            <!-- CARD -->
            <section class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6">

                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Data Pembayaran</h2>
                        <p class="text-sm text-slate-500">Menampilkan data pembayaran sesuai transaksi pemesanan.</p>
                    </div>

                    <div class="hidden sm:flex flex-wrap items-center gap-2 text-xs text-slate-600">
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1">
                            <span class="material-icons text-[16px] text-sky-600">verified</span>
                            Verifikasi status
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1">
                            <span class="material-icons text-[16px] text-emerald-600">sync</span>
                            Tersusun rapi
                        </span>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden bg-white">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200">
                                <tr class="text-xs text-slate-700">
                                    <th class="px-4 py-3 text-left font-semibold">No</th>
                                    <th class="px-4 py-3 text-left font-semibold">ID Pemesanan</th>
                                    <th class="px-4 py-3 text-left font-semibold">ID Transaksi</th>
                                    <th class="px-4 py-3 text-left font-semibold">Atas Nama</th>
                                    <th class="px-4 py-3 text-left font-semibold">Tanggal Transfer</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jumlah Transfer</th>
                                    <th class="px-4 py-3 text-left font-semibold">Total Tagihan</th>
                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
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
                      $badge  = badge_class($status);

                      $idPemesanan = e($kode . $idraw);
                      $idTransaksi = 'PYMT000' . e($idbyr);
                    ?>

                                <tr class="table-row hover:bg-slate-50 transition">
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center justify-center h-7 min-w-[28px] px-2 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                            <?php echo $no++; ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 font-bold text-slate-900"><?php echo $idPemesanan; ?></td>

                                    <td class="px-4 py-3 text-slate-700">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-sky-50 text-sky-800 px-3 py-1 text-xs font-semibold">
                                            <span class="material-icons text-[16px]">receipt</span>
                                            <?php echo $idTransaksi; ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700"><?php echo e($atas); ?></td>

                                    <!-- TANGGAL (diformat JS) -->
                                    <td class="px-4 py-3 text-slate-700 date-cell" data-date="<?php echo e($tgl); ?>">
                                        <?php echo e($tgl); ?>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 font-semibold">
                                        Rp <?php echo number_format($nom, 0, ',', '.'); ?>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 font-semibold">
                                        Rp <?php echo number_format($total, 0, ',', '.'); ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo $badge; ?>">
                                            <?php echo e(strtoupper(trim((string)$status))); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else : ?>
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-slate-600">
                                        <div
                                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                            <span class="material-icons text-slate-500">info</span>
                                            Belum ada pembayaran yang dikonfirmasi
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PAGINATION -->
                <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button id="prevBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white
                   hover:bg-slate-50 active:scale-[0.99] transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-icons text-base">chevron_left</span>
                        Prev
                    </button>

                    <span id="pageInfo" class="text-sm text-slate-700 font-semibold"></span>

                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">table_rows</span>
                            <select id="rowsPerPage" class="rounded-2xl border border-slate-200 bg-white pl-10 pr-3 py-2 text-sm outline-none
                       focus:ring-4 focus:ring-sky-100 focus:border-sky-300">
                                <option value="5">5 rows</option>
                                <option value="10" selected>10 rows</option>
                                <option value="25">25 rows</option>
                            </select>
                        </div>

                        <button id="nextBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white
                     hover:bg-slate-50 active:scale-[0.99] transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Next
                            <span class="material-icons text-base">chevron_right</span>
                        </button>
                    </div>
                </div>

            </section>

        </div>
    </main>

    <?php $this->load->view('components/footer'); ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // ===== format tanggal Indonesia (tanpa padStart agar aman) =====
        var bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
            'Oktober', 'November', 'Desember'
        ];

        var dateCells = document.querySelectorAll('.date-cell');
        for (var i = 0; i < dateCells.length; i++) {
            var el = dateCells[i];
            var raw = el.getAttribute('data-date');
            if (!raw) continue;

            var d = new Date(raw);
            if (isNaN(d)) continue;

            var dd = d.getDate();
            var tgl = (dd < 10 ? '0' + dd : '' + dd);

            el.textContent = tgl + ' ' + bulanIndo[d.getMonth()] + ' ' + d.getFullYear();
        }

        // ===== pagination =====
        var rows = Array.prototype.slice.call(document.querySelectorAll('.table-row'));
        var rowsSelect = document.getElementById('rowsPerPage');
        var prevBtn = document.getElementById('prevBtn');
        var nextBtn = document.getElementById('nextBtn');
        var pageInfo = document.getElementById('pageInfo');

        var rowsPerPage = parseInt(rowsSelect.value, 10);
        if (!rowsPerPage || rowsPerPage < 1) rowsPerPage = 10;

        var currentPage = 1;

        function renderTable() {
            var totalPages = Math.ceil(rows.length / rowsPerPage);
            if (!totalPages) totalPages = 1;

            for (var i = 0; i < rows.length; i++) rows[i].style.display = 'none';

            var start = (currentPage - 1) * rowsPerPage;
            var end = start + rowsPerPage;

            for (var j = start; j < end && j < rows.length; j++) {
                rows[j].style.display = '';
            }

            pageInfo.textContent = 'Page ' + currentPage + ' of ' + totalPages;
            prevBtn.disabled = (currentPage === 1);
            nextBtn.disabled = (currentPage === totalPages);
        }

        rowsSelect.addEventListener('change', function() {
            var v = parseInt(this.value, 10);
            if (!v || v < 1) v = 10;
            rowsPerPage = v;
            currentPage = 1;
            renderTable();
        });

        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage = currentPage - 1;
                renderTable();
            }
        });

        nextBtn.addEventListener('click', function() {
            var totalPages = Math.ceil(rows.length / rowsPerPage);
            if (!totalPages) totalPages = 1;

            if (currentPage < totalPages) {
                currentPage = currentPage + 1;
                renderTable();
            }
        });

        renderTable();
    });
    </script>

</body>

</html>