<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$no = 1;
$total_keseluruhan = 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>List Pembayaran</title>

    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">

</head>

<body class="bg-slate-200 min-h-screen">

    <!-- ================= SIDEBAR ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- ================= MAIN ================= -->
    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <!-- HEADER -->
        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">List Pembayaran</h1>
        </div>

        <!-- CARD -->
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">

            <div class="overflow-x-auto max-h-[420px] overflow-y-auto relative">
                <table class="w-full text-sm border border-slate-200 rounded-lg overflow-hidden">

                    <thead class="bg-slate-100 sticky top-0 z-10">
                        <tr class="text-center font-semibold text-slate-700">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Kode Transaksi</th>
                            <th class="px-4 py-3">Kode Pemesanan</th>
                            <th class="px-4 py-3">Atas Nama</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Detail</th>
                        </tr>
                    </thead>

                <tbody class="divide-y">
                <?php if(!empty($pembayaran)): ?>
                    <?php foreach($pembayaran as $row): ?>
                    <tr class="table-row hover:bg-slate-50 text-center">
                        <td class="px-4 py-3"><?= $no++ ?></td>

                        <!-- Kode Transaksi: karena tidak ada KODE_PEMBAYARAN di tabel baru -->
                        <td class="px-4 py-3 font-medium">
                        <?= 'PB' . str_pad($row['ID_PEMBAYARAN'], 6, '0', STR_PAD_LEFT); ?>
                        </td>

                        <!-- Kode Pemesanan: KODE_PEMESANAN + ID_PEMESANAN_RAW -->
                        <td class="px-4 py-3">
                        <?= $row['KODE_PEMESANAN'] . $row['ID_PEMESANAN_RAW']; ?>
                        </td>

                        <!-- Atas Nama (pengirim) -->
                        <td class="px-4 py-3">
                        <?= $row['ATAS_NAMA_PENGIRIM']; ?>
                        </td>

                        <td class="px-4 py-3 font-semibold text-green-600">
                        Rp <?= number_format($row['NOMINAL_TRANSFER'],0,',','.'); ?>
                        </td>

                        <td class="px-4 py-3">
                        <a href="<?= site_url('admin/pembayaran/details/'.$row['ID_PEMBAYARAN']) ?>"
                            class="inline-flex items-center justify-center text-blue-600 hover:text-blue-800">
                            <i class="material-icons text-base">open_in_new</i>
                        </a>
                        </td>
                        <td class="px-4 py-3">
                        <?= $row['STATUS_VERIF']; ?>
                        </td>

                    </tr>

                    <?php $total_keseluruhan += $row['NOMINAL_TRANSFER']; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                        Data pembayaran belum tersedia
                    </td>
                    </tr>
                <?php endif; ?>
                </tbody>


                    <tfoot class="bg-slate-50 font-semibold">
                        <tr class="text-center">
                            <td colspan="4" class="px-4 py-3 text-right">
                                Total Jumlah Transfer :
                            </td>
                            <td colspan="2" class="px-4 py-3 text-green-700">
                                Rp <?= number_format($total_keseluruhan,0,',','.'); ?>
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <button id="prevBtn" class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40">
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-slate-600 text-center"></span>

                <div class="flex items-center gap-3">
                    <select id="rowsPerPage" class="rounded-lg border px-3 py-2 text-sm">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn"
                        class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40">
                        Next
                    </button>
                </div>

            </div>

        </div>
    </main>

    <!-- JS -->
    <script src="<?= base_url(' assets/home/assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/home/materialize/js/materialize.js') ?>"></script>

    <script>
    const rows = document.querySelectorAll(".table-row");
    const rowsPerPageSelect = document.getElementById("rowsPerPage");
    const pageInfo = document.getElementById("pageInfo");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");

    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);

    function renderTable() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? "" : "none";
        });

        const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
        pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    rowsPerPageSelect.addEventListener("change", () => {
        rowsPerPage = parseInt(rowsPerPageSelect.value);
        currentPage = 1;
        renderTable();
    });

    prevBtn.onclick = () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    };

    nextBtn.onclick = () => {
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    };

    renderTable();
    </script>

</body>

</html>