<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Office</title>
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
                            <th class="px-4 py-3 text-center">ID Pemesanan</th>
                            <th class="px-4 py-3 text-center">Nama User</th>
                            <th class="px-4 py-3 text-center">Tanggal</th>
                            <th class="px-4 py-3 text-center">Gedung</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y text-center">
                        <?php if (!empty($pemesanan)) : ?>
                            <?php foreach ($pemesanan as $row): ?>
                                <?php
                                    $status = isset($row['STATUS']) ? strtoupper(trim($row['STATUS'])) : '';

                                    // default
                                    $badge = 'bg-gray-100 text-gray-700';

                                    if ($status === 'REJECTED') {
                                        $badge = 'bg-red-100 text-red-700';
                                    } else if ($status === 'APPROVE & PAID') {
                                        $badge = 'bg-green-100 text-green-700';
                                    } else if ($status === 'PROPOSAL APPROVE') {
                                        $badge = 'bg-lime-100 text-lime-700';
                                    } else if ($status === 'SUBMITED') {
                                        $badge = 'bg-blue-100 text-blue-700';
                                    } else if ($status === 'PROCESS') {
                                        $badge = 'bg-yellow-100 text-yellow-700';
                                    }
                                ?>
                                <tr class="table-row hover:bg-gray-50">
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['ID_PEMESANAN']); ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['USERNAME']); ?></td>
                                    <td class="px-4 py-3">
                                        <?= date('d F Y', strtotime($row['TANGGAL_PEMESANAN'])); ?>
                                    </td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['NAMA_GEDUNG']); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badge; ?>">
                                            <?= htmlspecialchars($row['STATUS']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-gray-500">Data pemesanan tidak ditemukan.</td>
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
