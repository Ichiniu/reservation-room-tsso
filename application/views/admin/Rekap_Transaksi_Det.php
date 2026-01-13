<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

$no    = 1;
$total = 0;

$rows = (isset($row) && is_array($row)) ? $row : array();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Transaksi Det</title>

    <link rel="apple-touch-icon-precomposed" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet" type="text/css">
</head>

<body>
    <header>
        <nav class="top-nav">
            <a href="#" data-activates="nav-mobile" class="button-collapse menu-btn show-on-large">
                <i class="material-icons">menu</i>
            </a>
            <div class="nav-wrapper center-title">
                <span class="page-title">Administrator</span>
            </div>
        </nav>

        <ul id="nav-mobile" class="side-nav" style="width: 240px;">
            <li class="logo"></li>

            <li class="bold"><a href="<?php echo site_url('admin/dashboard') ?>" class="waves-effect waves-teal">Home</a></li>
            <li class="bold"><a href="<?php echo site_url('admin/list') ?>" class="waves-effect waves-teal">List User</a></li>
            <li class="bold"><a href="<?php echo site_url('admin/gedung') ?>" class="waves-effect waves-teal">List Gedung</a></li>
            <li class="bold"><a href="<?php echo site_url('admin/catering') ?>" class="waves-effect waves-teal">Catering</a></li>
            <li class="bold"><a href="<?php echo site_url('admin/pemesanan2') ?>" class="waves-effect waves-teal">List Pemesanan</a></li>

            <li class="bold">
                <a href="<?php echo site_url('admin/transaksi') ?>" class="waves-effect waves-teal">
                    Inbox Pemesanan
                    <?php if (isset($result) && (int)$result > 0): ?>
                        <span class="new badge"><?php echo (int)$result; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="bold">
                <a href="<?php echo site_url('admin/pembayaran') ?>" class="waves-effect waves-teal">
                    Transaksi
                    <?php if (isset($get_transaction) && (int)$get_transaction > 0): ?>
                        <span class="new badge"><?php echo (int)$get_transaction; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="no-padding">
                <ul class="collapsible collapsible-accordion">
                    <li class="bold">
                        <a class="collapsible-header waves-effect waves-teal">Perawatan</a>
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

            <li class="no-padding">
                <ul class="collapsible collapsible-accordion">
                    <li class="bold">
                        <a class="collapsible-header waves-effect waves-teal">Rekapitulasi</a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a class="waves-effect waves-teal" href="<?php echo site_url('admin/rekap_aktivitas') ?>">Rekap Aktivitas</a></li>
                                <li><a class="waves-effect waves-teal" href="<?php echo site_url('admin/rekap_transaksi') ?>">Rekap Transaksi</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>

            <li class="bold"><a href="<?php echo site_url('admin/log_out') ?>" class="waves-effect waves-teal">Sign Out</a></li>
        </ul>
    </header>

    <main class="container" style="margin-top: 24px;">
        <h5 class="center-align">Rekapitulasi Transaksi</h5>
        <div class="center-align" style="margin-bottom: 16px;">
            <b>
                Periode
                <?php echo date_format(date_create($start_date), 'd/m/Y'); ?>
                -
                <?php echo date_format(date_create($end_date), 'd/m/Y'); ?>
            </b>
        </div>

        <div class="row">
            <div class="col s12">
                <table class="bordered responsive-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Pembayaran</th>
                            <th>Kode Pemesanan</th>
                            <th>Atas Nama</th>
                            <th>Bank</th>
                            <th>Tanggal Transfer</th>
                            <th>Jumlah Transfer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($rows) > 0): ?>
                            <?php foreach ($rows as $r): ?>
                                <?php
                                // fallback kolom lama/baru biar semua kepanggil
                                $atasNama = '-';
                                // === ATAS NAMA (khusus INTERNAL tampilkan: Nama + (PT + Departemen)) ===
                                $isInternal = false;
                                if (isset($r['perusahaan']) && strtoupper(trim($r['perusahaan'])) == 'INTERNAL') {
                                    $isInternal = true;
                                }

                                if ($isInternal) {
                                    $nama = (isset($r['NAMA_LENGKAP']) && $r['NAMA_LENGKAP'] != '') ? $r['NAMA_LENGKAP'] : '-';
                                    $pt   = (isset($r['nama_perusahaan']) && $r['nama_perusahaan'] != '') ? $r['nama_perusahaan'] : 'PT Tiga Serangkai Pustaka Mandiri';
                                    $dept = (isset($r['departemen']) && $r['departemen'] != '') ? $r['departemen'] : '-';

                                    $atasNamaHtml =
                                        htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') .
                                        "<br><small>" .
                                        htmlspecialchars($pt, ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($dept, ENT_QUOTES, 'UTF-8') .
                                        "</small>";
                                } else {
                                    // non internal: pakai atas nama pengirim (fallback kolom lama/baru)
                                    $atasNama = '-';
                                    if (isset($r['ATAS_NAMA_PENGIRIM']) && $r['ATAS_NAMA_PENGIRIM'] != '') $atasNama = $r['ATAS_NAMA_PENGIRIM'];
                                    else if (isset($r['ATAS_NAMA']) && $r['ATAS_NAMA'] != '') $atasNama = $r['ATAS_NAMA'];

                                    $atasNamaHtml = htmlspecialchars($atasNama, ENT_QUOTES, 'UTF-8');
                                }


                                $bank = '-';
                                if (isset($r['BANK_PENGIRIM']) && $r['BANK_PENGIRIM'] !== '') $bank = $r['BANK_PENGIRIM'];

                                $idPemesanan = '';
                                if (isset($r['ID_PEMESANAN_RAW']) && $r['ID_PEMESANAN_RAW'] !== '') $idPemesanan = $r['ID_PEMESANAN_RAW'];
                                else if (isset($r['ID_PEMESANAN']) && $r['ID_PEMESANAN'] !== '') $idPemesanan = $r['ID_PEMESANAN'];

                                $kodePemesananPrefix = isset($r['KODE_PEMESANAN']) ? $r['KODE_PEMESANAN'] : 'PMSN000';
                                $kodePemesanan = $kodePemesananPrefix . $idPemesanan;

                                $kodePembayaranPrefix = isset($r['KODE_PEMBAYARAN']) ? $r['KODE_PEMBAYARAN'] : '';
                                $idPembayaran = isset($r['ID_PEMBAYARAN']) ? $r['ID_PEMBAYARAN'] : '';
                                $kodePembayaran = $kodePembayaranPrefix . $idPembayaran;

                                $tgl = (!empty($r['TANGGAL_TRANSFER']))
                                    ? date('d/m/Y', strtotime($r['TANGGAL_TRANSFER']))
                                    : '-';

                                $nominal = isset($r['NOMINAL_TRANSFER']) ? (float)$r['NOMINAL_TRANSFER'] : 0;
                                $total  += $nominal;
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($kodePembayaran, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($kodePemesanan, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo $atasNamaHtml; ?></td>
                                    <td><?php echo htmlspecialchars($bank, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo $tgl; ?></td>
                                    <td><?php echo "Rp." . number_format($nominal, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr>
                                <td colspan="6"><b>Total Transfer:</b></td>
                                <td><b><?php echo "Rp." . number_format($total, 0, ',', '.'); ?></b></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="center-align">Tidak ada data transaksi pada periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div style="margin-top: 14px;">
                    <a href="<?php echo site_url('admin/transaksi_download_pdf/' . $start_date . '/' . $end_date); ?>">
                        Ekspor ke PDF
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/index.js"></script>

    <script>
        $(document).ready(function() {
            $(".button-collapse").sideNav({
                menuWidth: 260,
                edge: 'left',
                closeOnClick: false,
                draggable: true
            });

            $(".button-collapse").on("click", function() {
                $("body").toggleClass("nav-open");
            });

            $(document).mouseup(function(e) {
                var sb = $(".side-nav");
                if (!sb.is(e.target) && sb.has(e.target).length === 0) {
                    $("body").removeClass("nav-open");
                }
            });
        });
    </script>
</body>

</html>