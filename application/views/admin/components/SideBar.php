<?php
$result = isset($result) ? $result : 0;
$get_transaction = isset($get_transaction) ? $get_transaction : 0;
$username = $this->session->userdata('username');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Smart Office</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- MATERIAL ICONS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- TAILWIND -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .sidebar-mini {
            width: 72px !important;
        }
        .sidebar-mini .menu-text {
            display: none;
        }
        .sidebar-mini details summary span:last-child {
            display: none;
        }
    </style>
</head>

<body class="bg-white text-gray-800">

<!-- ================= TOP NAVBAR ================= -->
<header class="fixed top-0 left-0 right-0 z-40 bg-white border-b h-16">
    <div class="flex items-center px-6 h-full gap-4">
        <button id="toggleSidebar" class="text-2xl text-gray-700">
            <span class="material-icons">menu</span>
        </button>
        <span class="font-semibold text-lg">Administrator</span>
    </div>
</header>

<!-- ================= SIDEBAR ================= -->
<aside id="sidebar"
       class="fixed top-16 left-0 z-30 w-64 h-full bg-[#f9f7f2] border-r
              transition-all duration-300">

    <nav class="px-3 py-6 space-y-1 text-sm">

        <a href="<?= site_url('admin/dashboard') ?>"
           class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white">
            <span class="material-icons">dashboard</span>
            <span class="menu-text">Home</span>
        </a>

        <a href="<?= site_url('admin/list') ?>"
           class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white">
            <span class="material-icons">people</span>
            <span class="menu-text">List User</span>
        </a>

        <a href="<?= site_url('admin/gedung') ?>"
           class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white">
            <span class="material-icons">business</span>
            <span class="menu-text">List Gedung</span>
        </a>

        <a href="<?= site_url('admin/catering') ?>"
           class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white">
            <span class="material-icons">restaurant</span>
            <span class="menu-text">Catering</span>
        </a>

        <a href="<?= site_url('admin/pemesanan2') ?>"
           class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white">
            <span class="material-icons">assignment</span>
            <span class="menu-text">List Pemesanan</span>
        </a>

        <a href="<?= site_url('admin/transaksi') ?>"
           class="flex items-center justify-between px-4 py-3 rounded hover:bg-white">
            <span class="flex items-center gap-3">
                <span class="material-icons">inbox</span>
                <span class="menu-text">Inbox</span>
            </span>
            <?php if ($result > 0): ?>
                <span class="menu-text bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                    <?= $result ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="<?= site_url('admin/pembayaran') ?>"
           class="flex items-center justify-between px-4 py-3 rounded hover:bg-white">
            <span class="flex items-center gap-3">
                <span class="material-icons">payment</span>
                <span class="menu-text">Transaksi</span>
            </span>
            <?php if ($get_transaction > 0): ?>
                <span class="menu-text bg-green-600 text-white text-xs px-2 py-0.5 rounded-full">
                    <?= $get_transaction ?>
                </span>
            <?php endif; ?>
        </a>

       <details class="group">
    <summary
        class="flex items-center justify-between px-4 py-3 cursor-pointer rounded hover:bg-white list-none">

        <div class="flex items-center gap-3">
            <span class="material-icons">bar_chart</span>
            <span class="menu-text">Rekapitulasi</span>
        </div>

        <!-- PANAH DROPDOWN -->
        <span
            class="material-icons arrow-icon transition-transform duration-300 group-open:rotate-180">
            expand_more
        </span>
    </summary>

    <div class="ml-10 mt-1 space-y-1 menu-text">
        <a href="<?= site_url('admin/rekap_aktivitas') ?>"
           class="block px-3 py-2 rounded hover:bg-white">
            Rekap Aktivitas
        </a>
        <a href="<?= site_url('admin/rekap_transaksi') ?>"
           class="block px-3 py-2 rounded hover:bg-white">
            Rekap Transaksi
        </a>
    </div>
</details>


        <hr class="my-4">

        <a href="<?= site_url('admin/log_out') ?>"
           class="flex items-center gap-3 px-4 py-3 rounded text-red-600 hover:bg-red-50">
            <span class="material-icons">logout</span>
            <span class="menu-text">Sign Out</span>
        </a>

    </nav>
</aside>

<!-- ================= CONTENT ================= -->
<main id="content" class="pt-20 ml-64 transition-all duration-300 px-6">
    <!-- ISI HALAMAN -->
</main>

<!-- ================= SCRIPT ================= -->
<script>
const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');
const toggleBtn = document.getElementById('toggleSidebar');

toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('sidebar-mini');
    content.classList.toggle('ml-64');
    content.classList.toggle('ml-[72px]');
});
</script>

</body>
</html>
