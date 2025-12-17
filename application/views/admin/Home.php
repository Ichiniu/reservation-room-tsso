<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Favicons-->
        <link rel="apple-touch-icon-precomposed" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
        <meta name="msapplication-TileColor" content="#FFFFFF">
        <meta name="msapplication-TileImage" content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
        <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">
        <title>Admin Smart Office </title>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!-- Materialize core CSS -->
        <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>assets/home/style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <header>
        <nav class="top-nav">
    <!-- HAMBURGER BUTTON -->
    <a href="#" data-activates="nav-mobile" class="button-collapse menu-btn show-on-large">
        <i class="material-icons">menu</i>
    </a>
    <div class="nav-wrapper center-title">
    <span class="page-title">Administrator</span>
</div>
</nav>
<ul id="nav-mobile" class="side-nav" style="width: 24px;">
    <li class="logo"></li>

    <li class="bold">
        <a href="<?php echo site_url('admin/dashboard') ?>" class="waves-effect waves-teal">
            <i class="material-icons left">dashboard</i> Home
        </a>
    </li>

    <li class="bold">
        <a href="<?php echo site_url('admin/list') ?>" class="waves-effect waves-teal">
            <i class="material-icons left">people</i> List User
        </a>
    </li>

    <li class="bold">
        <a href="<?php echo site_url('admin/gedung') ?>" class="waves-effect waves-teal">
            <i class="material-icons left">business</i> List Gedung
        </a>
    </li>

    <li class="bold">
        <a href="<?php echo site_url('admin/catering') ?>" class="waves-effect waves-teal">
            <i class="material-icons left">restaurant_menu</i> Catering
        </a>
    </li>

    <li class="bold">
        <a href="<?php echo site_url('admin/pemesanan2') ?>" class="waves-effect waves-teal">
            <i class="material-icons left">assignment</i> List Pemesanan
        </a>
    </li>

    <li class="bold">
        <?php if($result > 0): ?>
            <a href="<?php echo site_url('admin/transaksi') ?>" class="waves-effect waves-teal">
                <i class="material-icons left">inbox</i> Inbox Pemesanan
                <span class="new badge"><?php echo $result ?></span>
            </a>
        <?php else: ?>
            <a href="<?php echo site_url('admin/transaksi') ?>" class="waves-effect waves-teal">
                <i class="material-icons left">inbox</i> Inbox Pemesanan
            </a>
        <?php endif; ?>
    </li>

    <li class="bold">
        <?php if($get_transaction > 0): ?>
            <a href="<?php echo site_url('admin/pembayaran') ?>" class="waves-effect waves-teal">
                <i class="material-icons left">payment</i> Transaksi
                <span class="new badge"><?php echo $get_transaction ?></span>
            </a>
        <?php else: ?>
            <a href="<?php echo site_url('admin/pembayaran') ?>" class="waves-effect waves-teal">
                <i class="material-icons left">payment</i> Transaksi
            </a>
        <?php endif; ?>
    </li>

    <!-- Perawatan collapsible -->
    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li class="bold">
                <a class="collapsible-header waves-effect waves-teal">
                    <i class="material-icons left">build</i> Perawatan
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a class="waves-effect waves-teal" href="<?php echo site_url('admin/pembayaran-listrik') ?>">Pembayaran Listrik</a></li>
                        <li><a class="waves-effect waves-teal" href="<?php echo site_url('admin/pembayaran-air') ?>">Pembayaran Air</a></li>
                        <li><a class="waves-effect waves-teal" href="<?php echo site_url('admin/pembayaran-kebersihan') ?>">Pembayaran Kebersihan</a></li>
                        <li><a class="waves-effect waves-teal" href="<?php echo site_url('admin/rekap_pembayaran') ?>">Rekap Pembayaran</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <!-- Rekapitulasi collapsible -->
    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li class="bold">
                <a class="collapsible-header waves-effect waves-teal">
                    <i class="material-icons left">bar_chart</i> Rekapitulasi
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a class="waves-effect waves-teal" href="<?php echo site_url('admin/rekap_aktivitas') ?>">Rekap Aktivitas</a></li>
                        <li><a class="waves-effect waves-teal" href="<?php echo site_url('admin/rekap_transaksi') ?>">Rekap Transaksi</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li class="bold">
        <a href="<?php echo site_url('admin/log_out') ?>" class="waves-effect waves-teal">
            <i class="material-icons left">exit_to_app</i> Sign Out
        </a>
    </li>
</ul>

            <center><h5>Jadwal Gedung Terbooking</h5></center>
            <div class="container">
                <div class="row">
                    <div class="col s12 m12">
                    <table class="bordered" width="100%">
                <tr>
                    <th>No</th>
                    <th>Id Pemesanan</th>
                    <th>Gedung</th>
                    <th>Username</th>
                    <th>Tanggal Acara</th>
                    <th>Status Pemesanan</th>
                    <th>Details</th>
                </tr>
                <?php $no = 1; foreach($front_data as $data): ?>
                <?php $id_gedung = $data['ID_PEMESANAN']; ?>
                <tr>
                        <td><?php echo $no++ ?></td>
                        <td><?php echo "PMSN000".$id_gedung."" ?></td>
                        <td><?php echo $data['NAMA_GEDUNG'] ?></td>
                        <td><?php echo $data['USERNAME'] ?></td>
                        <td><?php echo $data['TANGGAL_FINAL_PEMESANAN'] ?></td>
                        <td><?php echo $data['FINAL_STATUS'] ?></td>
                        <td>
                        <a href="<?php echo site_url('admin/detail_pemesanan/PMSN000'.$id_gedung.'') ?>"><i class="material-icons">open_in_new</i></a>
                        </td>
                </tr>
                <?php endforeach; ?>
            </table>
                    </div>
                </div>
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