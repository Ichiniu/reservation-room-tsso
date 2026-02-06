<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$this->load->helper('pricing');
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
    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <!-- HEADER -->
        <div class="max-w-6xl mx-auto mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Data Ruangan</h1>
                <p class="text-sm text-slate-500">Daftar Ruangan yang tersedia</p>
            </div>

            <a href="<?= site_url('admin/add_gedung') ?>" class="inline-flex items-center gap-2 px-4 py-2
              bg-blue-600 text-white text-sm font-medium
              rounded-lg hover:bg-blue-700 transition">
                Tambah Ruangan
            </a>
        </div>

        <!-- CARD -->
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">

            <div class="overflow-x-auto max-h-[420px] overflow-y-auto relative">
                <table class="w-full text-sm border border-slate-200 rounded-lg bg-white">
                    <thead class="sticky top-0 z-20 bg-slate-100 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 text-center">Nama</th>
                            <th class="px-4 py-3 text-center">Kapasitas</th>
                            <th class="px-4 py-3">Alamat</th>
                            <th class="px-4 py-3 text-center">Deskripsi</th>
                            <th class="px-4 py-3 text-center">Fasilitas</th>
                            <th class="px-14 py-3">Harga</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>


                    <tbody class="divide-y">
                        <?php foreach ($res as $row): ?>
                            <tr class="table-row hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-medium"><?= $row['NAMA_GEDUNG'] ?></td>
                                <td class="px-4 py-3"><?= $row['KAPASITAS'] ?> Orang</td>
                                <td class="px-4 py-3"><?= $row['ALAMAT'] ?></td>
                                <td class="px-4 py-3"><?= $row['DESKRIPSI_GEDUNG'] ?></td>

                                <td class="px-4 py-3">
                                    <?= !empty($row['fasilitas']) ? nl2br(htmlspecialchars($row['fasilitas'])) : '-' ?>
                                </td>

                                <td class="px-4 py-3 font-semibold">
                                    <?php
                                    $pm = bs_detect_pricing_mode($row['NAMA_GEDUNG'], isset($row['PRICING_MODE']) ? $row['PRICING_MODE'] : '');
                                    if ($pm === 'PER_PESERTA') {
                                        $half = isset($row['HARGA_HALF_DAY_PP']) ? (int)$row['HARGA_HALF_DAY_PP'] : 30000;
                                        $full = isset($row['HARGA_FULL_DAY_PP']) ? (int)$row['HARGA_FULL_DAY_PP'] : 60000;
                                        echo '<div class="text-xs text-slate-500">Halfday</div><div>Rp ' . number_format($half, 0, ',', '.') . ' /org</div>';
                                        echo '<div class="mt-1 text-xs text-slate-500">Fullday</div><div>Rp ' . number_format($full, 0, ',', '.') . ' /org</div>';
                                    } elseif ($pm === 'PODCAST_PER_JAM') {
                                        $audio = isset($row['HARGA_AUDIO_PER_JAM']) ? (int)$row['HARGA_AUDIO_PER_JAM'] : 150000;
                                        $video = isset($row['HARGA_VIDEO_PER_JAM']) ? (int)$row['HARGA_VIDEO_PER_JAM'] : 200000;
                                        echo '<div class="text-xs text-slate-500">Audio</div><div>Rp ' . number_format($audio, 0, ',', '.') . ' /jam</div>';
                                        echo '<div class="mt-1 text-xs text-slate-500">Video</div><div>Rp ' . number_format($video, 0, ',', '.') . ' /jam</div>';
                                    } else {
                                        echo 'Rp ' . number_format((int)$row['HARGA_SEWA'], 0, ',', '.');
                                    }
                                    ?>
                                </td>
                                <td class="px-4 py-3 ">
                                    <div class="flex justify-center gap-2">
                                        <a href="<?= site_url('admin/edit/' . $row['ID_GEDUNG']) ?>"
                                            class="px-3 py-1.5 text-xs bg-yellow-400 rounded hover:bg-yellow-500">
                                            Edit
                                        </a>
                                        <a href="<?= site_url('admin/admin_controls/delete_gedung/' . $row['ID_GEDUNG']) ?>"
                                            onclick="return confirm('Yakin ingin menghapus ruangan ini?')"
                                            class="px-3 py-1.5 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                        <?php if (empty($res)): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                                    Data ruangan belum tersedia
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
        <footer class="text-xs text-gray-500 text-center mt-6 stacked">
            © <?php echo date('Y'); ?> Smart Office • Admin Panel

        </footer>
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

        rowsPerPageSelect.onchange = () => {
            rowsPerPage = parseInt(rowsPerPageSelect.value);
            currentPage = 1;
            renderTable();
        };

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