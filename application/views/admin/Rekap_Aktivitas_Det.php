<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$no = 1;
$first_date_period = date_create($first_period);
$second_date_period = date_create($last_period);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Smart Office</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed"
        href="<?= base_url('assets/home/assets/img/favicon/apple-touch-icon-152x152.png') ?>">
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
        <center>
            <h5>Rekapitulasi Aktivitas</h5>
            <div class="container">
                <div class="row">
                    <div class="col s12 m12">
                        <table class="bordered">
                            <tr>
                                <th>No</th>
                                <th>Nama Gedung</th>
                                <th>Tanggal Pemesanan</th>
                                <th>Tanggal Approval</th>
                                <th>Kegiatan</th>
                                <th>Jam Kegiatan</th>
                                <th>Nama Pemesan</th>
                            </tr>
                            <?php foreach($hasil as $row): ?>
                            <?php $date = date_create($row['TANGGAL_FINAL_PEMESANAN']) ?>
                            <?php $date_approval = date_create($row['TANGGAL_APPROVAL']) ?>
                            <?php
                                $jamMulai = null;
                                if (isset($row['JAM_MULAI']) && $row['JAM_MULAI'] != '') {
                                    $jamMulai = $row['JAM_MULAI'];
                                } elseif (isset($row['JAM_PEMESANAN']) && $row['JAM_PEMESANAN'] != '') {
                                    $jamMulai = $row['JAM_PEMESANAN'];
                                }

                                $jamSelesai = null;
                                if (isset($row['JAM_SELESAI']) && $row['JAM_SELESAI'] != '') {
                                    $jamSelesai = $row['JAM_SELESAI'];
                                }

                                $jamText = '-';
                                if (!empty($jamMulai) && !empty($jamSelesai)) {
                                    $jamText = date('H:i', strtotime($jamMulai)) . ' - ' . date('H:i', strtotime($jamSelesai));
                                } elseif (!empty($jamMulai)) {
                                    $jamText = date('H:i', strtotime($jamMulai));
                                }
                                ?>
                            <tr>
                                <td><?php echo $no++?></td>
                                <td><?php echo $row['NAMA_GEDUNG']?></td>
                                <td><?php echo date_format($date, 'd M Y')?></td>
                                <td><?php echo date_format($date_approval, 'd M Y')?></td>
                                <td><?php echo $row['DESKRIPSI_ACARA']?></td>                                
                                <td><?php echo $jamText; ?></td>
                                <td><?php echo $row['NAMA_LENGKAP']?></td>
                            </tr>
                            <?php endforeach;?>
                        </table>
                        <table style="display: inline-block;">
                            <tr>
                                <td><b>Periode:</b></td>
                                <td><?php echo date_format($first_date_period, 'd F Y') ?></td>
                                <td><b>To</b></td>
                                <td><?php echo date_format($second_date_period, 'd F Y') ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <a
                                        href="<?php echo site_url('admin/kegiatan_download_pdf/'.$first_period.'/'.$last_period.'') ?>">Ekspor
                                        ke PDF</a>
                                </td>
                            </tr>
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
            <script type="text/javascript">
            var startDate = document.getElementById("start_date");
            var label = document.getElementById("labelDari");
            var labelsampai = document.getElementById("labelSampai");
            var endDate = document.getElementById("end_date");
            var btnProses = document.getElementById("btnProses");
            var btnFilter = document.getElementById("btnFilter");

            function unhideElement() {
                if (startDate.hidden = true) {
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
                if (startDate.value == "") {
                    alert("Harap Isi Form Tanggal!");
                    return false;
                } else if (endDate.value == "") {
                    alert("Harap Isi Form Tanggal!");
                    return false;
                }
            }
            </script>
            <script>
            $(document).ready(function() {

                $(".button-collapse").sideNav({
                    menuWidth: 260,
                    edge: 'left',
                    closeOnClick: false,
                    draggable: true
                });

                // OPEN / CLOSE SIDEBAR + SHIFT CONTENT
                $(".button-collapse").on("click", function() {
                    $("body").toggleClass("nav-open");
                });

                // CLOSE JIKA KLIK LUAR SIDEBAR
                $(document).mouseup(function(e) {
                    let sb = $(".side-nav");
                    if (!sb.is(e.target) && sb.has(e.target).length === 0) {
                        $("body").removeClass("nav-open");
                    }
                });

            });
            </script>
</body>

</html>