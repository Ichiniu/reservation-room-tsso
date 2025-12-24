<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$no = 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Favicons-->
    <link rel="apple-touch-icon-precomposed"
        href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Detail Pembayaran</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize core CSS -->
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet" type="text/css">

    <style>
    .cardbox {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
    }

    .cardhead {
        padding: 16px 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .cardbody {
        padding: 16px 18px;
    }

    table.bordered td,
    table.bordered th {
        padding: 10px 12px;
    }

    table.bordered tr {
        border-bottom: 1px solid #e5e7eb;
    }

    table.bordered {
        margin: 0;
    }

    .hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
    }

    .img-proof {
        width: 100%;
        max-width: 520px;
        height: auto;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        display: block;
    }

    .label-strong {
        font-weight: 700;
        color: #111827;
    }

    .btn-blue700 {
        background: #1d4ed8 !important;
        border-radius: 12px !important;
    }

    .btn-blue700:hover {
        filter: brightness(.95);
    }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">

    <!-- ================= SIDEBAR COMPONENT ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- Overlay mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    <!-- Topbar -->
    <header class="fixed top-0 left-0 right-0 z-20 bg-white border-b border-gray-200">
        <div class="h-16 px-4 md:px-6 flex items-center gap-3">
            <button id="sidebarToggle"
                class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
                type="button" aria-label="Toggle sidebar">
                <i class="material-icons text-gray-800">menu</i>
            </button>

            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white">
                    <i class="material-icons text-gray-800">paid</i>
                </span>
                <div class="leading-tight">
                    <div class="font-semibold">Detail Pembayaran</div>
                    <div class="text-xs text-gray-500">Transaksi</div>
                </div>
            </div>

            <div class="ml-auto text-sm text-gray-600">
                <?php echo htmlspecialchars(isset($session_id) && $session_id !== '' ? $session_id : '-', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
    </header>

    <!-- Main (1 kolom kebawah) -->
    <main class="pt-20 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-4xl mx-auto">

            <!-- Heading -->
            <div class="mb-5">
                <h5 class="text-xl font-semibold text-gray-900 m-0">Detail Pembayaran</h5>
                <p class="text-sm text-gray-500 mt-1">Informasi transaksi pembayaran dan bukti transfer.</p>

                <div class="mt-3">
                    <a href="<?php echo site_url('admin/pembayaran'); ?>"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                        <i class="material-icons text-base mr-2">arrow_back</i>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Card -->
            <div class="cardbox">
                <div class="cardhead">
                    <div class="font-semibold text-gray-900">Ringkasan Transaksi</div>
                    <div class="hint">Pastikan nominal & bukti transfer sesuai.</div>
                </div>

                <div class="cardbody" style="padding:0;">
                    <div class="overflow-x-auto">
                        <table class="bordered">
                            <tbody>
                                <tr>
                                    <td class="label-strong">ID Transaksi</td>
                                    <td>:</td>
                                    <td><?php echo $details->KODE_PEMBAYARAN.$details->ID_PEMBAYARAN; ?></td>
                                </tr>
                                <tr>
                                    <td class="label-strong">ID Pemesanan</td>
                                    <td>:</td>
                                    <td><?php echo $details->KODE_PEMESANAN.$details->ID_PEMESANAN; ?></td>
                                </tr>
                                <tr>
                                    <td class="label-strong">Atas Nama</td>
                                    <td>:</td>
                                    <td><?php echo $details->ATAS_NAMA; ?></td>
                                </tr>
                                <tr>
                                    <td class="label-strong">Tanggal Pembayaran</td>
                                    <td>:</td>
                                    <td><?php echo $details->TANGGAL_TRANSFER; ?></td>
                                </tr>
                                <tr>
                                    <td class="label-strong">Nominal Pembayaran</td>
                                    <td>:</td>
                                    <td><?php echo "Rp.".number_format($details->NOMINAL_TRANSFER); ?></td>
                                </tr>
                                <tr>
                                    <td class="label-strong">Total Keseluruhan</td>
                                    <td>:</td>
                                    <td><?php echo "Rp.".number_format($details->TOTAL); ?></td>
                                </tr>
                                <tr>
                                    <td class="label-strong">Total Terhutang</td>
                                    <td>:</td>
                                    <td><?php echo "Rp.".number_format($details->TOTAL-$details->NOMINAL_TRANSFER); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-strong">Bank Pengirim</td>
                                    <td>:</td>
                                    <td><?php echo $details->BANK_PENGIRIM; ?></td>
                                </tr>
                                <tr>
                                    <td class="label-strong">Bukti Transfer</td>
                                    <td>:</td>
                                    <td>
                                        <img class="img-proof" src="<?php echo $details->PATH.$details->IMG_NAME; ?>"
                                            alt="Bukti Transfer">
                                        <div class="hint">Klik kanan → Open image in new tab (opsional).</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer actions -->
                    <div class="cardbody" style="border-top:1px solid #e5e7eb;">
                        <a href="<?php echo site_url('admin/pembayaran'); ?>"
                            class="btn waves-effect waves-light btn-blue700">
                            <i class="material-icons left">arrow_back</i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-xs text-gray-500 text-center mt-6">
                © <?php echo date('Y'); ?> Smart Office • Admin Panel
            </div>

        </div>
    </main>

    <!-- Materialize core JavaScript -->
    <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/index.js"></script>

    <!-- Sidebar toggle (optional) -->
    <script>
    (function() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        var btn = document.getElementById('sidebarToggle');
        if (!sidebar) return;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        if (btn) {
            btn.addEventListener('click', function() {
                var isClosed = sidebar.classList.contains('-translate-x-full');
                if (isClosed) openSidebar();
                else closeSidebar();
            });
        }
        if (overlay) overlay.addEventListener('click', closeSidebar);

        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });
    })();
    </script>

</body>

</html>