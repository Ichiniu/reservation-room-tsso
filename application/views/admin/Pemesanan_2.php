<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan</title>
    <link rel="icon" href="<?= base_url('assets/home/assets/img/favicon/favicon-32x32.png') ?>" sizes="32x32">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800">
    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-6 pb-10">

        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold">Data Pemesanan Gedung</h1>
            <p class="text-sm text-gray-500">Daftar seluruh pemesanan gedung</p>
        </div>

        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">
            <div class="overflow-x-auto max-h-[420px] overflow-y-auto relative">
                <table id="dataTable" class="w-full text-sm border border-slate-200 rounded-lg bg-white">
                    <thead class="sticky top-0 z-20 bg-gray-100 shadow-sm">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3 text-center">ID Pemesanan</th>
                            <th class="px-4 py-3 text-center">Nama Lengkap</th>
                            <th class="px-4 py-3 text-center">Tanggal</th>
                            <th class="px-4 py-3 text-center">Gedung</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    <tbody class="divide-y text-center">
                        <?php if (!empty($pemesanan)) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($pemesanan as $row): ?>
                                <?php
                                // STATUS BADGE
                                $statusUpper = isset($row['STATUS']) ? strtoupper(trim($row['STATUS'])) : '';
                                $badge = 'bg-gray-100 text-gray-700';

                                if ($statusUpper === 'REJECTED') {
                                    $badge = 'bg-red-100 text-red-700';
                                } else if ($statusUpper === 'APPROVE & PAID') {
                                    $badge = 'bg-green-100 text-green-700';
                                } else if ($statusUpper === 'PROPOSAL APPROVE') {
                                    $badge = 'bg-lime-100 text-lime-700';
                                } else if ($statusUpper === 'SUBMITED') {
                                    $badge = 'bg-blue-100 text-blue-700';
                                } else if ($statusUpper === 'PROCESS') {
                                    $badge = 'bg-yellow-100 text-yellow-700';
                                }

                                // AMANKAN DATA (PHP 5.x)
                                $idPemesanan   = isset($row['ID_PEMESANAN']) ? $row['ID_PEMESANAN'] : '-';
                                $tanggalRaw    = isset($row['TANGGAL_PEMESANAN']) ? $row['TANGGAL_PEMESANAN'] : '';
                                $namaGedung    = isset($row['NAMA_GEDUNG']) ? $row['NAMA_GEDUNG'] : '-';
                                $statusText    = isset($row['STATUS']) ? $row['STATUS'] : '-';
                                $usernameRow   = isset($row['USERNAME']) ? $row['USERNAME'] : '-';

                                // NAMA seperti pembayaran (Nama Lengkap + PT + Departemen)
                                $namaLengkap   = !empty($row['USER_NAMA_LENGKAP']) ? $row['USER_NAMA_LENGKAP'] : $usernameRow;
                                $namaPT        = !empty($row['USER_NAMA_PERUSAHAAN']) ? $row['USER_NAMA_PERUSAHAAN'] : '-';
                                $departemen    = !empty($row['USER_DEPARTEMEN']) ? $row['USER_DEPARTEMEN'] : '';
                                $jenis         = !empty($row['USER_JENIS']) ? strtoupper(trim($row['USER_JENIS'])) : '';

                                // sama seperti pembayaran: internal tapi PT kosong -> fallback PT
                                if ($jenis === 'INTERNAL') {
                                    if ($namaPT === '-' || $namaPT === '') {
                                        $namaPT = 'PT Tiga Serangkai Pustaka Mandiri';
                                    }
                                }

                                // format tanggal
                                $tanggalTampil = '-';
                                if (!empty($tanggalRaw)) {
                                    $tanggalTampil = date('d F Y', strtotime($tanggalRaw));
                                }
                                ?>

                                <tr class="table-row hover:bg-gray-50">
                                    <td class="px-4 py-3"><?= $no++; ?></td>

                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars((string)$idPemesanan, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td class="px-4 py-3 text-left">
                                        <div class="font-semibold text-slate-800">
                                            <?= htmlspecialchars((string)$namaLengkap, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                        <div class="text-xs text-slate-500 leading-snug">
                                            <?= htmlspecialchars((string)$namaPT, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php if (!empty($departemen)): ?>
                                                <br><?= htmlspecialchars((string)$departemen, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3"><?= htmlspecialchars((string)$tanggalTampil, ENT_QUOTES, 'UTF-8'); ?></td>

                                    <td class="px-4 py-3"><?= htmlspecialchars((string)$namaGedung, ENT_QUOTES, 'UTF-8'); ?></td>

                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badge; ?>">
                                            <?= htmlspecialchars((string)$statusText, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-gray-500">Data pemesanan tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button id="prevBtn" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-40">
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-gray-600 text-center"></span>

                <div class="flex items-center gap-3">
                    <select id="rowsPerPage" class="rounded-lg border px-3 py-2 text-sm">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-40">
                        Next
                    </button>
                </div>
            </div>

        </div>
    </main>

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
                row.style.display = index >= start && index < end ? "" : "none";
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
            if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
                currentPage++;
                renderTable();
            }
        };

        renderTable();
    </script>

</body>

</html>