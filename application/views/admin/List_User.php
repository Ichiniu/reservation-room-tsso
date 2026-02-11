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

</head>

<body class="bg-slate-200 min-h-screen">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="pt-24 md:pl-64 px-4 md:px-6 pb-10 transition-all duration-300">

        <!-- HEADER -->
        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Daftar User</h1>
            <p class="text-sm text-slate-500">Data pengguna yang terdaftar</p>
        </div>

        <!-- CARD -->
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">

            <div class="overflow-x-auto max-h-[420px] overflow-y-auto relative">
                <table class="w-full text-sm border border-slate-200 rounded-lg bg-white">
                    <thead class="sticky top-0 z-20 bg-slate-100 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 text-center">No</th>
                            <th class="px-4 py-3 text-center">Username</th>
                            <th class="px-4 py-3 text-center">Nama Lengkap</th>
                            <th class="px-4 py-3 text-center">Email</th>
                            <th class="px-4 py-3 text-center">No Telepon</th>
                            <th class="px-4 py-3 text-center">Nama Perusahaan</th>
                            <th class="px-4 py-3 text-center">Departemen</th>
                            <th class="px-4 py-3 text-center">Alamat</th>
                            <th class="px-10 py-3 text-center">Tanggal Lahir</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody" class="divide-y">
                        <?php $no = 1;
                        foreach ($res as $row):
                            $date = date_create($row['TANGGAL_LAHIR']);
                        ?>
                            <tr class="table-row hover:bg-slate-50">
                                <td class="px-4 py-3 text-center"><?= $no++ ?></td>
                                <td class="px-4 py-3 text-center font-medium"><?= $row['USERNAME']; ?></td>
                                <td class="px-4 py-3 text-center"><?= !empty($row['NAMA_LENGKAP']) ? $row['NAMA_LENGKAP'] : '-'; ?></td>
                                <td class="px-4 py-3 text-center"><?= $row['EMAIL']; ?></td>
                                <td class="px-4 py-3 text-center"><?= $row['NO_TELEPON']; ?></td>
                                <td class="px-4 py-3 text-center"><?= !empty($row['nama_perusahaan']) ? $row['nama_perusahaan'] : '-'; ?></td>
                                <td class="px-4 py-3 text-center"><?= !empty($row['departemen']) ? $row['departemen'] : '-'; ?></td>
                                <td class="px-4 py-3 text-center"><?= $row['ALAMAT']; ?></td>
                                <td class="px-4 py-3 text-center">
                                    <?= format_tanggal_indo($row['TANGGAL_LAHIR']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($res)): ?>
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-slate-500">
                                    Data user belum tersedia
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <!-- PREV -->
                <button id="prevBtn" class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40">
                    Prev
                </button>

                <!-- INFO -->
                <span id="pageInfo" class="text-sm text-slate-600 text-center"></span>

                <!-- NEXT + ROWS -->
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