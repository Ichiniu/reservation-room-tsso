<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

/* ===== URI ===== */
$current_uri = uri_string();

/* ===== INBOX COUNT ===== */
if (isset($res)) {
    $inbox_count = is_array($res) ? count($res) : (int)$res;
} else {
    $inbox_count = 0;
}

/* ===== DATA CATERING ===== */
$catering_data = isset($result) && is_array($result) ? $result : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Smart Office</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>


    <style>
        .sidebar-mini {
            width: 72px !important
        }

        .sidebar-mini .menu-text {
            display: none
        }

        .sidebar-mini nav a {
            justify-content: center
        }

        .sidebar-mini .material-icons-outlined {
            margin-right: 0 !important
        }

        .content-mini {
            margin-left: 72px !important
        }

        .menu-active {
            background: #fff;
            font-weight: 600
        }

        .inbox-badge {
            margin-left: auto
        }

        .sidebar-mini .inbox-badge {
            position: absolute;
            top: 8px;
            right: 12px;
            width: 18px;
            height: 18px;
            font-size: 10px
        }
    </style>
</head>

<body class="bg-slate-200">

    <!-- ================= TOPBAR ================= -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white border-b z-40">
        <div class="flex items-center h-full px-6">
            <span id="toggleSidebar" class="material-icons-outlined mr-3 cursor-pointer">menu</span>
            <span class="font-semibold text-lg">Administrator</span>
            <span class="ml-auto text-sm text-gray-500">
                <?= htmlspecialchars($session_id); ?>
            </span>
        </div>
    </header>

    <!-- ================= SIDEBAR (TIDAK DIUBAH) ================= -->
    <aside id="sidebar"
        class="fixed top-16 left-0 w-64 h-[calc(100vh-64px)] bg-[#fbf9f4] border-r transition-all duration-300">
        <nav class="px-4 py-6 text-sm space-y-1">

            <a href="<?= site_url('admin/dashboard') ?>" class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white
<?= strpos($current_uri, 'dashboard') !== false ? 'menu-active' : '' ?>">
                <span class="material-icons">dashboard</span>
                <span class="menu-text">Home</span>
            </a>

            <a href="<?= site_url('admin/list') ?>" class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white
<?= strpos($current_uri, 'list') !== false ? 'menu-active' : '' ?>">
                <span class="material-icons">people</span>
                <span class="menu-text">List User</span>
            </a>

            <a href="<?= site_url('admin/gedung') ?>" class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white
<?= strpos($current_uri, 'gedung') !== false ? 'menu-active' : '' ?>">
                <span class="material-icons">business</span>
                <span class="menu-text">List Gedung</span>
            </a>

            <a href="<?= site_url('admin/catering') ?>" class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white
<?= strpos($current_uri, 'catering') !== false ? 'menu-active' : '' ?>">
                <span class="material-icons">restaurant</span>
                <span class="menu-text">Catering</span>
            </a>

            <a href="<?= site_url('admin/pemesanan2') ?>"
                class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white">
                <span class="material-icons">assignment</span>
                <span class="menu-text">List Pemesanan</span>
            </a>

            <a href="<?= site_url('admin/transaksi') ?>" class="relative flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= strpos($current_uri, 'transaksi') !== false ? 'menu-active' : '' ?>">
                <span class="material-icons-outlined">inbox</span>
                <span class="menu-text">Inbox</span>
                <?php if ($inbox_count > 0): ?>
                    <span
                        class="inbox-badge bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                        <?= $inbox_count ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="<?= site_url('admin/pembayaran') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
   <?= strpos($current_uri, 'pembayaran') !== false ? 'menu-active' : '' ?>">
                <span class="material-icons">payments</span>
                <span class="menu-text">Transaksi</span>
            </a>


            <a href="<?= site_url('admin/rekap_aktivitas') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
   <?= strpos($current_uri, 'rekap_aktivitas') !== false ? 'menu-active' : '' ?>">
                <span class="material-icons">history</span>
                <span class="menu-text">Rekap Aktivitas</span>
            </a>


            <a href="<?= site_url('admin/rekap_transaksi') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
   <?= strpos($current_uri, 'rekap_transaksi') !== false ? 'menu-active' : '' ?>">
                <span class="material-icons">summarize</span>
                <span class="menu-text">Rekap Transaksi</span>
            </a>


            <hr class="my-5">

            <a href="<?= site_url('admin/log_out') ?>"
                class="flex items-center gap-4 px-3 py-3 rounded text-red-600 hover:bg-red-50">
                <span class="material-icons">logout</span>
                <span class="menu-text">Sign Out</span>
            </a>

        </nav>
    </aside>

    <!-- ================= CONTENT ================= -->
    <main id="content" class="ml-64 pt-24 px-8 transition-all duration-300">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">List Catering</h2>
            <a href="<?= site_url('admin/add_catering') ?>"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                <span class="material-icons text-sm">add</span>
                Tambah Catering
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">

            <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                <table class="w-full text-sm border border-slate-200 rounded-lg bg-white">
                    <thead class="sticky top-0 bg-slate-100">
                        <tr>
                            <th class="px-4 py-3 text-center">No</th>
                            <th class="px-4 py-3">Nama Paket</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Min Pax</th>
                            <th class="px-4 py-3">Kategori Menu</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        <?php $no = 1;
                        foreach ($catering_data as $row): ?>
                            <?php
                            $menu = [];
                            if (!empty($row['MENU_JSON'])) {
                                $decoded = json_decode($row['MENU_JSON'], true);
                                if (is_array($decoded)) $menu = $decoded;
                            }
                            $cats = isset($menu['categories']) && is_array($menu['categories']) ? $menu['categories'] : [];
                            $labels = [];
                            foreach ($cats as $c) {
                                if (!empty($c['label'])) $labels[] = $c['label'];
                            }
                            $summary = empty($labels) ? '-' : implode(', ', array_slice($labels, 0, 4)) . (count($labels) > 4 ? '…' : '');
                            ?>
                            <tr class="table-row hover:bg-slate-50">
                                <td class="px-4 py-3 text-center"><?= $no++ ?></td>
                                <td class="px-4 py-3 font-medium"><?= $row['NAMA_PAKET'] ?></td>
                                <td class="px-4 py-3"><?= isset($row['JENIS']) ? str_replace('_', ' ', $row['JENIS']) : '-' ?></td>
                                <td class="px-4 py-3"><?= !empty($row['MIN_PAX']) ? (int)$row['MIN_PAX'] : '-' ?></td>
                                <td class="px-4 py-3 text-slate-700"><?= $summary ?></td>
                                <td class="px-4 py-3 font-semibold">
                                    Rp <?= number_format($row['HARGA'], 0, ',', '.') ?>
                                </td>

                                <!-- AKSI -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <!-- EDIT -->
                                        <a href="<?= site_url('admin/add_catering/' . $row['ID_CATERING']) ?>"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs bg-yellow-400 text-white rounded hover:bg-yellow-500">
                                            <span class="material-icons text-sm">edit</span>
                                            Edit
                                        </a>

                                        <!-- HAPUS -->
                                        <form action="<?= site_url('admin/delete_catering') ?>" method="post"
                                            onsubmit="return confirm('Yakin ingin menghapus data catering ini?')">
                                            <input type="hidden" name="id_catering" value="<?= $row['ID_CATERING'] ?>">
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                                <span class="material-icons text-sm">delete</span>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($catering_data)): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    Data catering belum tersedia
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>


            <!-- ===== PAGINATION ===== -->
            <div class="mt-6 flex items-center justify-between">
                <button id="prevBtn" class="px-4 py-2 bg-slate-200 rounded hover:bg-slate-300 disabled:opacity-40">
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-slate-600"></span>

                <div class="flex items-center gap-3">
                    <select id="rowsPerPage" class="border rounded px-3 py-2 text-sm">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn" class="px-4 py-2 bg-slate-200 rounded hover:bg-slate-300 disabled:opacity-40">
                        Next
                    </button>
                </div>
            </div>

        </div>
        <footer class="text-xs text-gray-500 text-center mt-6">
            © <?php echo date('Y'); ?> Smart Office • Admin Panel

        </footer>
    </main>

    <!-- ================= SCRIPT ================= -->
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        toggleBtn.onclick = () => {
            sidebar.classList.toggle('sidebar-mini');
            content.classList.toggle('content-mini')
        };

        const rows = document.querySelectorAll('.table-row');
        const rowsPerPageSelect = document.getElementById('rowsPerPage');
        const pageInfo = document.getElementById('pageInfo');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        let currentPage = 1;
        let rowsPerPage = parseInt(rowsPerPageSelect.value);

        function renderTable() {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? '' : 'none';
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
        }
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        }
        nextBtn.onclick = () => {
            if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
                currentPage++;
                renderTable();
            }
        }

        renderTable();
    </script>

</body>

</html>