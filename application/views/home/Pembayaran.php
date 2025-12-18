<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$user = $this->uri->segment(2);
$no = 1;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
          <script src="https://cdn.tailwindcss.com"></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Favicons-->
        <link rel="apple-touch-icon-precomposed" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
        <meta name="msapplication-TileColor" content="#FFFFFF">
        <meta name="msapplication-TileImage" content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
        <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">
        <title>Transaksi</title>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!-- Materialize core CSS -->
        <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="assets/js/html5shiv.js"></script>
            <script src="assets/js/respond.min.js"></script>
        <![endif]-->
        <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet" type="text/css">
    </head>
     <body class="min-h-screen text-black
  bg-slate-200">
      <?php $this->load->view('components/navbar'); ?>
<?php $this->load->view('components/header'); ?>
            
            
           
<div class="max-w-7xl mx-auto px-4 py-8">

<section class="rounded-3xl bg-white ring-white/15 shadow-xl p-6">
    <div class="overflow-x-auto rounded-2xl ring-1 bg-card-bg  ring-white/15">
      <table class="min-w-full">
                        <table class="bordered">
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">ID Pemesanan</th>
                            <th class="px-4 py-3 text-left">ID Transaksi</th>
                            <th class="px-4 py-3 text-left">Atas Nama</th>
                            <th class="px-4 py-3 text-left">Tanggal Transfer</th>
                            <th class="px-4 py-3 text-left">Jumlah Transfer</th>
                            <th class="px-4 py-3 text-left">Total Harga</th>
                            <th class="px-4 py-3 text-left">Terhutang</th>
                            <?php foreach($res as $row): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <?php echo $no++; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php echo $row['KODE_PEMESANAN'].$row['ID_PEMESANAN']; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php echo $row['KODE_PEMBAYARAN'].$row['ID_PEMBAYARAN']; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php echo $row['ATAS_NAMA']; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php echo $row['TANGGAL_TRANSFER']; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php echo "Rp.".number_format($row['NOMINAL_TRANSFER']); ?>
                                </td>
                                <td>
                                    <?php echo "Rp.".number_format($row['TOTAL']); ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php echo "Rp.".number_format($row['TOTAL']-$row['NOMINAL_TRANSFER']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                </div>
            </div>
</section>

</div>


        <main class="">
</main>
        <!-- Materialize core JavaScript -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
        <script src="<?php echo base_url(); ?>assets/home/index.js"></script>
        
    </body>
    
</html>
