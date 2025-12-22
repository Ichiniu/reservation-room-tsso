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

<!-- ICON -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<link href="<?= base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet">

<style>
/* ===== SIDEBAR ===== */
.sidebar-mini {
    width: 72px !important;
}
.sidebar-mini .menu-text {
    display: none;
}
.sidebar-mini nav a {
    justify-content: center;
}
.sidebar-mini .material-icons-outlined {
    margin-right: 0 !important;
}

/* ===== CONTENT ===== */
.content-mini {
    margin-left: 72px !important;
}

/* ===== ACTIVE MENU ===== */
.menu-active {
    background-color: #ffffff;
    font-weight: 600;
}

/* ===== BADGE INBOX ===== */
.inbox-badge {
    margin-left: auto;
}
.sidebar-mini .inbox-badge {
    position: absolute;
    top: 8px;
    right: 12px;
    width: 18px;
    height: 18px;
    font-size: 10px;
    margin-left: 0;
}
</style>
</head>

<body class="bg-gray-50">

<!-- ===== TOPBAR ===== -->
<header class="fixed top-0 left-0 right-0 h-16 bg-white border-b z-40">
    <div class="flex items-center h-full px-6">
        <span id="toggleSidebar"
              class="material-icons-outlined mr-3 cursor-pointer select-none">
            menu
        </span>
        <span class="font-semibold text-lg">Administrator</span>
        <span class="ml-auto text-sm text-gray-500">
            <?= htmlspecialchars($session_id); ?>
        </span>
    </div>
</header>

<!-- ===== SIDEBAR ===== -->
<aside id="sidebar"
class="fixed top-16 left-0 w-64 h-[calc(100vh-64px)] bg-[#fbf9f4] border-r transition-all duration-300">

<nav class="px-4 py-6 text-sm space-y-1">

<a href="<?= site_url('admin/dashboard') ?>"
class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white
<?= strpos($current_uri,'dashboard')!==false?'menu-active':'' ?>">
<span class="material-icons">dashboard</span>
<span class="menu-text">Home</span>
</a>

<a href="<?= site_url('admin/list') ?>"
class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white
<?= strpos($current_uri,'list')!==false?'menu-active':'' ?>">
<span class="material-icons">people</span>
<span class="menu-text">List User</span>
</a>

<a href="<?= site_url('admin/gedung') ?>"
class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white
<?= strpos($current_uri,'gedung')!==false?'menu-active':'' ?>">
<span class="material-icons">business</span>
<span class="menu-text">List Gedung</span>
</a>

<a href="<?= site_url('admin/catering') ?>"
class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white
<?= strpos($current_uri,'catering')!==false?'menu-active':'' ?>">
<span class="material-icons">restaurant</span>
<span class="menu-text">Catering</span>
</a>

<a href="<?= site_url('admin/pemesanan2') ?>"
class="flex items-center gap-4 px-3 py-3 rounded hover:bg-white">
<span class="material-icons">assignment</span>
<span class="menu-text">List Pemesanan</span>
</a>

<!-- ===== INBOX ===== -->
<a href="<?= site_url('admin/transaksi') ?>"
class="relative flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= strpos($current_uri,'transaksi')!==false?'menu-active':'' ?>">
<span class="material-icons-outlined">inbox</span>
<span class="menu-text">Inbox</span>

<?php if ($inbox_count > 0): ?>
 <span class="inbox-badge bg-red-500 text-white rounded-full
                 w-5 h-5 flex items-center justify-center text-xs">
<?= $inbox_count ?>
</span>
<?php endif; ?>
</a>

<!-- ===== TRANSAKSI ===== -->
<details class="group">
<summary class="flex items-center justify-between px-3 py-3 cursor-pointer rounded hover:bg-white list-none">
<span class="flex items-center gap-4">
<span class="material-icons">payment</span>
<span class="menu-text">Transaksi</span>
</span>
<span class="material-icons-outlined transition-transform group-open:rotate-180">
expand_more
</span>
</summary>

<div class="ml-10 mt-1 space-y-1">
<a href="<?= site_url('admin/rekap_aktivitas') ?>" class="block px-3 py-2 rounded hover:bg-white">
Rekap Aktivitas
</a>
<a href="<?= site_url('admin/rekap_transaksi') ?>" class="block px-3 py-2 rounded hover:bg-white">
Rekap Transaksi
</a>
</div>
</details>

<hr class="my-5">

<a href="<?= site_url('admin/log_out') ?>"
class="flex items-center gap-4 px-3 py-3 rounded text-red-600 hover:bg-red-50">
<span class="material-icons">logout</span>
<span class="menu-text">Sign Out</span>
</a>

</nav>
</aside>

<!-- ===== CONTENT ===== -->
<main id="content" class="ml-64 pt-24 px-8 transition-all duration-300">

<div class="bg-white rounded-xl shadow-sm p-6">
<h5 class="font-semibold text-center mb-6">List Catering</h5>

<table class="bordered">
<thead>
<tr>
<th>No</th>
<th>Nama Paket</th>
<th>Menu Pembuka</th>
<th>Menu Utama</th>
<th>Menu Penutup</th>
<th>Harga</th>
</tr>
</thead>
<tbody>
<?php if (!empty($result)): ?>
<?php $no = 1; foreach ($result as $row): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= isset($row['ID_CATERING']) ? $row['ID_CATERING'] : '-' ?></td>
    <td><?= isset($row['NAMA_PAKET']) ? $row['NAMA_PAKET'] : '-' ?></td>
    <td><?= isset($row['MENU_PEMBUKA']) ? $row['MENU_PEMBUKA'] : '-' ?></td>
    <td><?= isset($row['MENU_UTAMA']) ? $row['MENU_UTAMA'] : '-' ?></td>
    <td><?= isset($row['MENU_PENUTUP']) ? $row['MENU_PENUTUP'] : '-' ?></td>
    <td><?= isset($row['HARGA']) ? $row['HARGA'] : '-' ?></td>
    <td>
        <a href="#" class="text-blue-600">Detail</a>
    </td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
    <td colspan="8" class="center-align">Data tidak tersedia</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

</main>

<script>
const toggleBtn = document.getElementById('toggleSidebar');
const sidebar   = document.getElementById('sidebar');
const content   = document.getElementById('content');

toggleBtn.onclick = () => {
    sidebar.classList.toggle('sidebar-mini');
    content.classList.toggle('content-mini');
};
</script>

</body>
</html>
