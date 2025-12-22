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
            <div class="container">
                  <div class="nav-wrapper">
                  <table class="bordered" style="width: 1000px">
                  	<tr>
                  		<th>No</th>
                  		<th>Username</th>
                  		<th>Email</th>
                  		<th>No Telepon</th>
                  		<th>Alamat User</th>
                  		<th>Tanggal Lahir</th>
                  	</tr>
                  	<tr>
                  	<?php $no = 0; foreach($res as $row): $no++; $date = date_create($row['TANGGAL_LAHIR']);?>
                  		<td><?php echo $no; ?></td>
                  		<td hidden="true"><?php echo $row['ID_GEDUNG'] ?></td>
                  		<td><?php echo $row['USERNAME']; ?></td>
                  		<td><?php echo $row['EMAIL']; ?></td>
                  		<td><?php echo $row['NO_TELEPON']; ?></td>
                  		<td><?php echo $row['ALAMAT']; ?></td>
                  		<td><?php echo date_format($date, "d F Y"); ?></td>
                  	</tr>
                  	<?php endforeach; ?>
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