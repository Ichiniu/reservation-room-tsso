<?php
$session_id = $this->session->userdata('admin_username');


$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Office</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/home/style.css') ?>" rel="stylesheet">
</head>

<body class="bg-slate-200 min-h-screen flex flex-col">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="flex-1 pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Jadwal Gedung Terbooking</h1>
            <p class="text-sm text-slate-500">Daftar pemesanan gedung</p>
        </div>

        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">

            <div class="overflow-x-auto max-h-[420px] overflow-y-auto relative">
                <table class="w-full text-sm border border-slate-200 rounded-lg bg-white">
                    <thead class="sticky top-0 z-20 bg-slate-100 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 text-center">No</th>
                            <th class="px-4 py-3 text-center">ID</th>
                            <th class="px-4 py-3 text-center">Gedung</th>
                            <th class="px-4 py-3 text-center">User</th>
                            <th class="px-4 py-3 text-center">Tanggal</th>
                            <th class="px-4 py-3 text-center">Jam</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        <?php if (!empty($front_data)): ?>
                            <?php $no = 1;
                            foreach ($front_data as $data): ?>
                                <?php
                                // ID dari V_PEMESANAN bisa "94" atau "PMSN00094"
                                $rawId = $data->ID_PEMESANAN;

                                // tampilan ID (jangan dobel PMSN000)
                                $displayId = (stripos((string)$rawId, 'PMSN') === 0) ? $rawId : ('PMSN000' . $rawId);

                                // link ID: kirim angka saja biar pasti cocok ke detail
                                $linkId = (int) preg_replace('/\D+/', '', (string)$rawId); // jadi 94

                                // jam sudah dari query: alias JAM (HH:ii)
                                $jam_mulai = !empty($data->JAM) ? $data->JAM : '-';
                                ?>

                                <tr class="table-row hover:bg-slate-50">
                                    <td class="px-4 py-3 text-center"><?= $no++ ?></td>

                                    <td class="px-4 py-3 text-center font-semibold">
                                        <?= $displayId ?>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <?= $data->NAMA_GEDUNG ?>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <?= $data->USERNAME ?>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <?= $data->TANGGAL_PEMESANAN ?>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <?= $jam_mulai ?>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <a href="<?= site_url('admin/detail_pemesanan/' . $linkId) ?>"
                                            class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
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

    <footer class="mt-auto text-xs text-gray-500 text-center py-4">
        © <?php echo date('Y'); ?> Smart Office • Admin Panel
    </footer>

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
            const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        };

        renderTable();
    </script>

</body>

</html>