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

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Materialize -->
<link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">

<style>
    table th, table td {
        text-align: center;
        vertical-align: middle;
    }
</style>
</head>

<body class="bg-gray-100 text-gray-800">

<!-- SIDEBAR -->
<?php $this->load->view('admin/components/sidebar'); ?>

<!-- MAIN -->
<main class="pt-24 pl-0 md:pl-64 px-6 pb-10">

<!-- HEADER -->
<div class="max-w-6xl mx-auto mb-6">
    <h1 class="text-2xl font-bold">Data Pemesanan Gedung</h1>
    <p class="text-sm text-gray-500">Kelola data pemesanan gedung</p>
</div>

<!-- CARD -->
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">

    <!-- TABLE -->
    <div class="overflow-x-auto">
        <table id="dataTable" class="bordered highlight w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th>ID Pemesanan</th>
                    <th>Nama User</th>
                    <th>Tanggal Pemesanan</th>
                    <th>Gedung</th>
                    <th>Status</th>
                    <th>Detail</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($pemesanan as $row): ?>
                <tr class="table-row hover:bg-gray-50">
                    <td class="px-4 py-3"><?= $row['ID_PEMESANAN']; ?></td>
                    <td class="px-4 py-3"><?= $row['USERNAME']; ?></td>
                    <td class="px-4 py-3">
                        <?= date('d F Y', strtotime($row['TANGGAL_PEMESANAN'])); ?>
                    </td>
                    <td class="px-4 py-3"><?= $row['NAMA_GEDUNG']; ?></td>

                    <!-- STATUS -->
                    <td class="px-4 py-3">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                        <?= $row['STATUS'] === 'Disetujui'
                            ? 'bg-green-100 text-green-700'
                            : ($row['STATUS'] === 'Ditolak'
                                ? 'bg-red-100 text-red-700'
                                : 'bg-yellow-100 text-yellow-700'); ?>">
                            <?= $row['STATUS']; ?>
                        </span>
                    </td>

                    <!-- DETAIL -->
                    <td class="px-4 py-3">
                        <a href="<?= site_url('admin/detail_transaksi/'.$row['ID_PEMESANAN']); ?>"
                           class="inline-flex items-center justify-center text-blue-600 hover:text-blue-800">
                            <i class="material-icons">open_in_new</i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <button id="prevBtn"
            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50">
            Prev
        </button>

        <span id="pageInfo" class="text-sm text-gray-600 text-center"></span>

        <div class="flex items-center gap-3">
            <select id="rowsPerPage"
                class="rounded-lg border px-3 py-2 text-sm">
                <option value="5">5 rows</option>
                <option value="10" selected>10 rows</option>
                <option value="25">25 rows</option>
            </select>

            <button id="nextBtn"
                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50">
                Next
            </button>
        </div>
    </div>

</div>
</main>

<!-- PAGINATION SCRIPT -->
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

    const totalPages = Math.ceil(rows.length / rowsPerPage);
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
