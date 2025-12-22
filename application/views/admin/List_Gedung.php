<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Smart Office</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed" href="<?= base_url('assets/home/assets/img/favicon/apple-touch-icon-152x152.png') ?>">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="<?= base_url('assets/home/assets/img/favicon/mstile-144x144.png') ?>">
    <link rel="icon" href="<?= base_url('assets/home/assets/img/favicon/favicon-32x32.png') ?>" sizes="32x32">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize (UNTUK TABLE & GRID SAJA) -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/home/style.css') ?>" rel="stylesheet">
</head>

<body class="bg-gray-50">

<!-- ================= SIDEBAR COMPONENT ================= -->
<?php $this->load->view('admin/components/sidebar'); ?>
<!-- ===================================================== -->

<!-- ================= MAIN CONTENT ================= -->
<main class="pt-24 pl-0 md:pl-64 px-6">
<!-- ================= CONTENT ================= -->
<!-- <div class="container" style="margin-left:260px"> -->

    <div class="row">
        <div class="col s12">
            <a class="waves-effect waves-light btn"
               href="<?= site_url('admin/add_gedung') ?>">
                <i class="material-icons right">add</i>Tambah Gedung
            </a>
        </div>
    </div>

    <table class="bordered">
        <tr>
            <th>Nama</th>
            <th>Kapasitas</th>
            <th>Alamat</th>
            <th>Deskripsi</th>
            <th>Harga</th>
            <th colspan="2">Action</th>
        </tr>

        <?php foreach($res as $row): ?>
        <tr>
            <td><?= $row['NAMA_GEDUNG'] ?></td>
            <td><?= $row['KAPASITAS'] ?> Orang</td>
            <td><?= $row['ALAMAT'] ?></td>
            <td><?= $row['DESKRIPSI_GEDUNG'] ?></td>
            <td><?= number_format($row['HARGA_SEWA']) ?></td>
            <td><a href="<?= site_url('admin/edit/'.$row['ID_GEDUNG']) ?>">Edit</a></td>
            <td><a href="<?= site_url('admin/admin_controls/delete_gedung/'.$row['ID_GEDUNG']) ?>" onclick="return dialog()">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>

<!-- </div> -->

</main>
<!-- ================= END MAIN CONTENT ================= -->

<!-- OPTIONAL JS (Materialize untuk table saja) -->
<script src="<?= base_url('assets/home/assets/js/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/home/materialize/js/materialize.js') ?>"></script>

</body>
</body>
</html>
