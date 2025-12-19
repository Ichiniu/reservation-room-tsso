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

    <div class="bg-white rounded-xl shadow-sm p-6">

        <h5 class="text-center text-lg font-semibold text-gray-800 mb-6">
            Jadwal Gedung Terbooking
        </h5>

        <div class="overflow-x-auto">
            <table class="bordered highlight responsive-table" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Id Pemesanan</th>
                        <th>Gedung</th>
                        <th>Username</th>
                        <th>Tanggal Acara</th>
                        <th>Jam</th>
                        <th>Status Pemesanan</th>
                        <th>Detail</th>
                    </tr>
                </thead>

                <tbody>
                <?php $no = 1; foreach($front_data as $data): ?>
                    <?php $id_gedung = $data['ID_PEMESANAN']; ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>PMSN000<?= $id_gedung ?></td>
                        <td><?= $data['NAMA_GEDUNG'] ?></td>
                        <td><?= $data['USERNAME'] ?></td>
                        <td><?= $data['TANGGAL_FINAL_PEMESANAN'] ?></td>
                        <td><?= $data['JAM_PEMESANAN'].' - '.$data['JAM_SELESAI']; ?></td>
                        <td><?= $data['FINAL_STATUS'] ?></td>
                        <td>
                            <a href="<?= site_url('admin/detail_pemesanan/PMSN000'.$id_gedung) ?>"
                               class="text-blue-600 hover:text-blue-800">
                                Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</main>
<!-- ================= END MAIN CONTENT ================= -->

<!-- OPTIONAL JS (Materialize untuk table saja) -->
<script src="<?= base_url('assets/home/assets/js/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/home/materialize/js/materialize.js') ?>"></script>

</body>
</html>
