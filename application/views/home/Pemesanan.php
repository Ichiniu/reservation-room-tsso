<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);
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
        <title>Pemesanan</title>
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
                 <tr>
                     <th class="px-4 py-3 text-left">Tanggal Pemesanan</th>
                     <th class="px-4 py-3 text-left">ID Pemesanan</th>
                     <th class="px-4 py-3 text-left">Jam Pemesanan</th>
                     <th class="px-4 py-3 text-left">Paket Catering</th>
                     <th class="px-4 py-3 text-left">Nama Gedung</th>
                     <th class="px-4 py-3 text-left">Status Pemesanan</th>
                     <th class="px-4 py-3 text-left">Details</th>
                 </tr>   
                 <!--<?php //else: ?> -->
                 <tr>
                 <?php foreach($res as $row): 
                 $id_pemesanan = $row['ID_PEMESANAN'];?>
                     <td class="px-4 py-3">
                         <?php echo $id_pemesanan; ?>
                     </td>
                     <td class="px-4 py-3">
                         <?php $date = date_create($row['TANGGAL_PEMESANAN']); echo date_format($date, 'd F Y') ?>
                     </td>
                     <td class="px-4 py-3">
         <?php if (!empty($row->JAM_PEMESANAN)): ?>
             <?= date('H:i', strtotime($row->JAM_PEMESANAN)); ?> WIB
         <?php else: ?>
             -
         <?php endif; ?>
     </td>
                     <td class="px-4 py-3">
                         <?php echo $row['NAMA_PAKET']; ?>
                     </td class="px-4 py-3">
                     <td>
                         <?php echo $row['NAMA_GEDUNG']; ?>
                     </td>
                     <td class="px-4 py-3">
                     <?php
                         $status = $row['STATUS'];
                         if($status == "DITOLAK") {
                             echo '<font color="red">'.$status.'</font>';
                         } else if($status == "DISETUJUI") {
                             echo '<font color="blue">'.$status.'</font>';
                         } else if($status == "PROSES") {
                             echo '<font color="black">'.$status.'</font>';
                         } else {
                             echo $status;
                         }
                     ?>
                     </td>
                     <td class="px-4 py-3">
                         <a href="<?php echo site_url('home/pemesanan/details/'.$id_pemesanan.''); ?>"><i class="material-icons">open_in_new</i></a>
                     </td>
                 </tr>
                 <?php endforeach; ?>
             </table>
             <?php if($rows < 1): ?>
                 <h5><center>-------------
                     <?php echo $no_data; ?>-----------
                 </center></h5>
             <?php endif; ?>
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
         <script>
$(document).ready(function(){

    $(".button-collapse").sideNav({
        menuWidth: 260,
        edge: 'left',
        closeOnClick: false,
        draggable: true
    });

    // OPEN / CLOSE SIDEBAR + SHIFT CONTENT
    $(".button-collapse").on("click", function () {
        $("body").toggleClass("nav-open");
    });

    // CLOSE JIKA KLIK LUAR SIDEBAR
    $(document).mouseup(function(e){
        let sb = $(".side-nav");
        if (!sb.is(e.target) && sb.has(e.target).length === 0) {
            $("body").removeClass("nav-open");
        }
    });

});
</script>
        </body>
</html>