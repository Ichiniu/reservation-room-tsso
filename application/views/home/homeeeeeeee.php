<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$user = $this->uri->segment(2);
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
        <title>Home</title>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!-- Materialize core CSS -->
        <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>assets/home/materialize/style.css" rel="stylesheet" type="text/css">
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="assets/js/html5shiv.js"></script>
            <script src="assets/js/respond.min.js"></script>
        <![endif]-->
    </head>
<header>
  <nav class="top-nav">
                <a href="#" data-activates="nav-mobile" class="button-collapse menu-btn show-on-large">
                     <i class="material-icons">menu</i>
                </a>
                <ul class="header-right right">
                    <li>
                        <a href="<?php echo site_url('edit_data/'.$user.'/'); ?>" class="btn-flat waves-effect profile-btn">
                            <i class="material-icons left">account_circle</i>
                            <?php echo $session_id; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo site_url('home/feeds'); ?>" class="btn waves-effect waves-light feeds-btn">
                            <i class="material-icons left">dynamic_feed</i>
                            Feeds Update
                        </a>
                    </li>
                </ul>
                <div class="header-center hide-on-small-only">
                    <div class="header-search">
                        <i class="material-icons search-icon">search</i>
                        <input type="text" placeholder="Search for a location">
                    </div>
                </div>  
   </nav>
</header>
<ul id="nav-mobile" class="side-nav" style="width: 2px;">
    <li class="logo"></li>
    <li class="bold">
        <a href="<?php echo site_url('home/'.$session_id.'/') ?>" class="waves-effect waves-teal">Home</a>
    </li>
    <li class="bold">
        <a href="<?php echo site_url('home/jadwal') ?>" class="waves-effect waves-teal">Jadwal Gedung</a>
    </li>
    <li class="bold">
        <a href="<?php echo site_url('home/pemesanan') ?>" class="waves-effect waves-teal">
           Pemesanan<?php if($flag > 0): ?><span class="new badge"><?php echo $flag ?></span><?php endif; ?>
        </a>
    </li>
    <li class="bold">
        <a href="<?php echo site_url('home/view-catering') ?>" class="waves-effect waves-teal" target="_blank">Menu Catering</a>
    </li>
    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li class="bold">
                <a class="collapsible-header waves-effect waves-teal">Cari Gedung</a>
                    <div class="collapsible-body">
                        <ul>
                            <li>
                                <nav> 
                                    <div class="nav-wrapper">
                                        <form method="get" action="<?php echo site_url('home/search') ?>">
                                            <div class="input-field"> 
                                                <input id="search" type="search" name="search_gedung" required placeholder="Cari Gedung"> 
                                                    <label for="search">
                                                        <i class="material-icons">search</i>
                                                    </label>                                                         
                                            </div>                                                     
                                        </form>                                                 
                                     </div>                                             
                                </nav>
                            </li>
                        </ul>
                    </div>
            </li>
        </ul>           
    </li>
<li class="bold active">
    <a class="collapsible-header waves-effect waves-teal active"><?php echo $session_id ?></a>
    <div class="collapsible-body" style="display: block;">
        <ul>
            <li>
                <a class="waves-effect waves-teal" href="<?php echo site_url('edit_data/'.$user.'/'); ?>">Edit Data Diri</a>
            </li>
            <li>
                <a class="waves-effect waves-teal" href="<?php echo site_url('home/pembayaran') ?>">Transaksi</a>
            </li>
            <li>
                <a class="waves-effect waves-teal" href="<?php echo site_url('home/home/logout'); ?>">Sign Out</a>
            </li>
        </ul>
    </div>
</li>
