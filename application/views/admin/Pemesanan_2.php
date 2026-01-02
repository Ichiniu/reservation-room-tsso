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

            <!-- ================= FILTER ================= -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <input type="text" id="filterId" placeholder="Cari ID Pemesanan"
                    class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">

                <select id="filterStatus"
                    class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    <option value="">Semua Status</option>
                    <option value="SUBMITED">SUBMITED</option>
                    <option value="PROCESS">PROCESS</option>
                    <option value="PROPOSAL APPROVE">PROPOSAL APPROVE</option>
                    <option value="APPROVE & PAID">APPROVE & PAID</option>
                    <option value="REJECTED">REJECTED</option>
                </select>

                <button id="resetFilter" class="bg-gray-200 hover:bg-gray-300 rounded-lg px-4 py-2 text-sm">
                    Reset
                </button>
            </div>
            <!-- ================= END FILTER ================= -->

            <div class="overflow-x-auto max-h-[420px] overflow-y-auto relative">
                <table class="w-full text-sm border border-slate-200 rounded-lg bg-white">
                    <thead class="sticky top-0 z-20 bg-gray-100 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 text-center">ID Pemesanan</th>
                            <th class="px-4 py-3 text-center">Nama User</th>
                            <th class="px-4 py-3 text-center">Tanggal</th>
                            <th class="px-4 py-3 text-center">Gedung</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody" class="divide-y text-center">
                        <?php if (!empty($pemesanan)) : ?>
                        <?php foreach ($pemesanan as $row): ?>
                        <?php
                        $status = strtoupper(trim($row['STATUS']));
                        $badge = 'bg-gray-100 text-gray-700';

                        if ($status === 'REJECTED') $badge = 'bg-red-100 text-red-700';
                        elseif ($status === 'APPROVE & PAID') $badge = 'bg-green-100 text-green-700';
                        elseif ($status === 'PROPOSAL APPROVE') $badge = 'bg-lime-100 text-lime-700';
                        elseif ($status === 'SUBMITED') $badge = 'bg-blue-100 text-blue-700';
                        elseif ($status === 'PROCESS') $badge = 'bg-yellow-100 text-yellow-700';
                        ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 id"><?= htmlspecialchars($row['ID_PEMESANAN']); ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row['USERNAME']); ?></td>
                            <td class="px-4 py-3"><?= date('d F Y', strtotime($row['TANGGAL_PEMESANAN'])); ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row['NAMA_GEDUNG']); ?></td>
                            <td class="px-4 py-3 status">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badge; ?>">
                                    <?= htmlspecialchars($row['STATUS']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-gray-500">
                                Data pemesanan tidak ditemukan.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ================= PAGINATION ================= -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button id="prevBtn"
                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-40">Prev</button>
                <span id="pageInfo" class="text-sm text-gray-600 text-center"></span>
                <div class="flex items-center gap-3">
                    <select id="rowsPerPage" class="rounded-lg border px-3 py-2 text-sm">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>
                    <button id="nextBtn"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-40">Next</button>
                </div>
            </div>

        </div>
    </main>

    <script>
    let rows = Array.from(document.querySelectorAll(".table-row"));
    let filteredRows = [...rows];

    const rowsPerPageSelect = document.getElementById("rowsPerPage");
    const pageInfo = document.getElementById("pageInfo");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const filterId = document.getElementById("filterId");
    const filterStatus = document.getElementById("filterStatus");
    const resetFilter = document.getElementById("resetFilter");

    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);

    function renderTable() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach(row => row.style.display = "none");

        filteredRows.slice(start, end).forEach(row => {
            row.style.display = "";
        });

        const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
        pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    function applyFilter() {
        const idValue = filterId.value.toLowerCase();
        const statusValue = filterStatus.value.toLowerCase();

        filteredRows = rows.filter(row => {
            const idText = row.querySelector(".id").innerText.toLowerCase();
            const statusText = row.querySelector(".status").innerText.toLowerCase();

            return (
                idText.includes(idValue) &&
                (statusValue === "" || statusText.includes(statusValue))
            );
        });

        currentPage = 1;
        renderTable();
    }

    filterId.addEventListener("keyup", applyFilter);
    filterStatus.addEventListener("change", applyFilter);

    resetFilter.addEventListener("click", () => {
        filterId.value = "";
        filterStatus.value = "";
        filteredRows = [...rows];
        currentPage = 1;
        renderTable();
    });

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
        if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
            currentPage++;
            renderTable();
        }
    };

    renderTable();
    </script>

</body>

</html>