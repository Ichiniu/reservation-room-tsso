<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inbox Pemesanan</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed" href="<?= base_url('assets/home/assets/img/favicon/apple-touch-icon-152x152.png') ?>">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="<?= base_url('assets/home/assets/img/favicon/mstile-144x144.png') ?>">
    <link rel="icon" href="<?= base_url('assets/home/assets/img/favicon/favicon-32x32.png') ?>" sizes="32x32">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize (UNTUK TABLE & GRID SAJA) -->
</head>

<body class="bg-gray-50">

<!-- ================= SIDEBAR COMPONENT ================= -->
<?php $this->load->view('admin/components/sidebar'); ?>
<!-- ===================================================== -->

<!-- ================= MAIN CONTENT ================= -->
<main class="pt-24 pl-0 md:pl-64 px-6">
            <div class="container">
                <div class="row">
                    <table class="bordered" style="width: 1000px">
                        <tr>
                            <th>ID Pemesanan</th>
                            <th>Nama User</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Gedung</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                        <tr>
                        <?php foreach($pemesanan as $row): $id_pemesanan = $row['ID_PEMESANAN']; ?>
                            <td><?php echo $id_pemesanan ?></td>
                            <td><?php echo $row['USERNAME'] ?></td>
                            <td>
                                <?php $date = date_create($row['TANGGAL_PEMESANAN']); echo date_format($date, 'd F Y') ?>
                            </td>
                            <td><?php echo $row['NAMA_GEDUNG'] ?></td>
                            <td><?php echo $row['STATUS'] ?></td>
                            <td>
                                <a href="<?php echo site_url('admin/detail_transaksi/'.$row['ID_PEMESANAN'].'') ?>"><i class="material-icons">open_in_new</i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            </header>
            </main>
<!-- ================= END MAIN CONTENT ================= -->
</body>
</html>