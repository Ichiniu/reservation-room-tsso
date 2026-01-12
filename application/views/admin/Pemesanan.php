<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">

    <style>
        table th,
        table td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-6 pb-10">

        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold">Data Pemesanan Gedung</h1>
            <p class="text-sm text-gray-500">Kelola data pemesanan gedung</p>
        </div>

        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">

            <!-- ================= FILTER ================= -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <input type="text" id="filterId" placeholder="Cari ID Pemesanan"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm">

                <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Status</option>
                    <option value="PROPOSAL APPROVE">PROPOSAL APPROVE</option>
                    <option value="PROCESS">PROCESS</option>
                </select>

                <input type="date" id="filterTanggal" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">

                <button id="resetFilter" class="bg-gray-200 hover:bg-gray-300 rounded-lg px-4 py-2 text-sm">
                    Reset
                </button>
            </div>
            <!-- ================= END FILTER ================= -->

            <!-- ================= TABLE ================= -->
            <div class="overflow-x-auto">
                <table class="bordered highlight w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>ID Pemesanan</th>
                            <th>Nama User</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Jam Pemesanan</th>
                            <th>Gedung</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($pemesanan as $row): ?>
                            <tr class="table-row hover:bg-gray-50">
                                <td class="px-4 py-3 id"><?= $row['ID_PEMESANAN']; ?></td>

                                <td class="px-4 py-3"><?= $row['USERNAME']; ?></td>

                                <!-- tanggal -->
                                <td class="px-4 py-3 tanggal"
                                    data-date="<?= date('Y-m-d', strtotime($row['TANGGAL_PEMESANAN'])); ?>">
                                    <?= date('d F Y', strtotime($row['TANGGAL_PEMESANAN'])); ?>
                                </td>

                                <!-- jam -->
                                <td class="px-4 py-3">
                                    <?php
                                    if (!empty($row['JAM_PEMESANAN']) && !empty($row['JAM_SELESAI'])) {
                                        echo date('H:i', strtotime($row['JAM_PEMESANAN'])) . ' - ' .
                                            date('H:i', strtotime($row['JAM_SELESAI'])) . ' WIB';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>

                                <td class="px-4 py-3"><?= $row['NAMA_GEDUNG']; ?></td>

                                <td class="px-4 py-3 status">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                <?= $row['STATUS'] === 'Disetujui'
                                    ? 'bg-green-100 text-green-700'
                                    : ($row['STATUS'] === 'Ditolak'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-yellow-100 text-yellow-700'); ?>">
                                        <?= $row['STATUS']; ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <a href="<?= site_url('admin/detail_transaksi/' . $row['ID_PEMESANAN']); ?>"
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="material-icons">open_in_new</i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ================= PAGINATION ================= -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <button id="prevBtn" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50">
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-gray-600 text-center"></span>

                <div class="flex items-center gap-3">
                    <select id="rowsPerPage" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50">
                        Next
                    </button>
                </div>
            </div>

        </div>
    </main>

    <!-- ================= SCRIPT ================= -->
    <script>
        let rows = Array.from(document.querySelectorAll(".table-row"));
        let filteredRows = [...rows];

        const filterId = document.getElementById("filterId");
        const filterStatus = document.getElementById("filterStatus");
        const filterTanggal = document.getElementById("filterTanggal");
        const resetFilter = document.getElementById("resetFilter");

        const rowsPerPageSelect = document.getElementById("rowsPerPage");
        const pageInfo = document.getElementById("pageInfo");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        let currentPage = 1;
        let rowsPerPage = parseInt(rowsPerPageSelect.value);

        function renderTable() {
            rows.forEach(row => row.style.display = "none");

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            filteredRows.slice(start, end).forEach(row => {
                row.style.display = "";
            });

            const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
            pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }

        function applyFilter() {
            const idVal = filterId.value.toLowerCase();
            const statusVal = filterStatus.value.toLowerCase();
            const tanggalVal = filterTanggal.value;

            filteredRows = rows.filter(row => {
                const idText = row.querySelector(".id").innerText.toLowerCase();
                const statusText = row.querySelector(".status").innerText.toLowerCase();
                const tanggalText = row.querySelector(".tanggal").dataset.date;

                return idText.includes(idVal) &&
                    (statusVal === "" || statusText.includes(statusVal)) &&
                    (tanggalVal === "" || tanggalText === tanggalVal);
            });

            currentPage = 1;
            renderTable();
        }

        filterId.addEventListener("keyup", applyFilter);
        filterStatus.addEventListener("change", applyFilter);
        filterTanggal.addEventListener("change", applyFilter);

        resetFilter.addEventListener("click", () => {
            filterId.value = "";
            filterStatus.value = "";
            filterTanggal.value = "";
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
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        };

        renderTable();
    </script>

</body>

</html>