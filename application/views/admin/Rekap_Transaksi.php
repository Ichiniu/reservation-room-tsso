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
            <center><h5>Rekapitulasi Transaksi</h5>
            <input type="button" value="Filter Tanggal" name="btnFilter" id="btnFilter" onclick="unhideElement();"></center>
            <div class="container">
                <div class="row">
                    <div class="col s4 offset-s5">
                        <form action="<?php echo site_url('admin/rekap_transaksi/details') ?>">
                        <label id="labelDari" hidden><b>Dari</b></label>
                        <input type="date" name="start_date" id="start_date" hidden size="2">
                        <label id="labelSampai" hidden><b>Sampai</b></label>
                        <input type="date" name="end_date" id="end_date" hidden>
                        <center><input type="submit" value="Proses" name="btnProses" id="btnProses" hidden onclick="return btnProsesAlert();"></center>
                        </form>
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
        <script type="text/javascript">
            var startDate = document.getElementById("start_date");
            var label = document.getElementById("labelDari");
            var labelsampai = document.getElementById("labelSampai");
            var endDate = document.getElementById("end_date");
            var btnProses = document.getElementById("btnProses");
            var btnFilter = document.getElementById("btnFilter");
            function unhideElement() {
                if(startDate.hidden = true) {
                    startDate.hidden = false;
                    label.hidden = false;
                    labelsampai.hidden = false;
                    endDate.hidden = false;
                    btnProses.hidden = false
                    btnFilter.disabled = true;

                } else {
                    hideElement();
                }
            }
            function hideElement() {
                startDate.hidden = true;
                label.hidden = true;
                endDate.hidden = true;
                btnProses.hidden = true;
            } 
            function btnProsesAlert() {
                if(startDate.value == "") {
                    alert("Harap Isi Form Tanggal!");
                    return false;
                } else if(endDate.value == "") {
                    alert("Harap Isi Form Tanggal!");
                    return false;
                }
            }
        </script>
    </body>
</html>