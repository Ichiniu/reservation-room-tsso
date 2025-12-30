<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$this->load->helper('form');
$id_pemesanan = substr($hasil->ID_PEMESANAN, 7);
$tax = 0.1 * $hasil->HARGA_SEWA;
$total_stl_pajak = $hasil->TOTAL_KESELURUHAN + $tax;
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

    <title>Detail Transaksi</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize -->
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet" type="text/css">

    <style>
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

    .hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
    }

    .link-action {
        color: #1d4ed8;
        /* blue-700 */
        text-decoration: underline;
        font-weight: 600;
    }

    .link-action:hover {
        filter: brightness(.9);
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
                    <i class="material-icons text-gray-800">receipt_long</i>
                </span>
                <div class="leading-tight">
                    <div class="font-semibold">Detail Transaksi</div>
                    <div class="text-xs text-gray-500">Administrator</div>
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

            <!-- Title -->
            <div class="mb-5">
                <h5 class="text-xl font-semibold text-gray-900 m-0">Detail Transaksi</h5>
                <p class="text-sm text-gray-500 mt-1">Informasi pemesanan dan rincian biaya.</p>
                <div class="hint">Semua informasi ditampilkan 1 kolom (kebawah).</div>
            </div>

            <!-- Card -->
            <div class="cardbox">
                <div class="cardhead">
                    <div class="font-semibold text-gray-900">ID Pemesanan: <?php echo $hasil->ID_PEMESANAN; ?></div>
                    <div class="text-sm text-gray-500 mt-1">
                        Total (incl. pajak): <b>Rp. <?php echo number_format($total_stl_pajak) ?></b>
                    </div>
                </div>

                <div class="cardbody" style="padding:0;">
                    <div class="overflow-x-auto">
                        <table class="bordered">
                            <tbody>
                                <tr>
                                    <td><b>ID PEMESANAN</b></td>
                                    <td>:</td>
                                    <td><b><?php echo $hasil->ID_PEMESANAN; ?></b></td>
                                </tr>
                                <tr>
                                    <td><b>USERNAME</b></td>
                                    <td>:</td>
                                    <td><?php echo $hasil->USERNAME; ?></td>
                                </tr>
                                <tr>
                                    <td><b>TANGGAL PEMESANAN</b></td>
                                    <td>:</td>
                                    <td><?php $date = date_create($hasil->TANGGAL_PEMESANAN); echo date_format($date, 'd F Y') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>JAM PEMESANAN</b></td>
                                    <td>:</td>
                                    <td><?php echo $hasil->JAM_PEMESANAN.' - '.$hasil->JAM_SELESAI; ?></td>
                                </tr>
                                <tr>
                                    <td><b>EMAIL</b></td>
                                    <td>:</td>
                                    <td><?php echo $hasil->EMAIL; ?></td>
                                </tr>
                                <tr>
                                    <td><b>GEDUNG</b></td>
                                    <td>:</td>
                                    <td><?php echo $hasil->NAMA_GEDUNG; ?></td>
                                </tr>
                                <tr>
                                    <td><b>CATERING</b></td>
                                    <td>:</td>
                                    <td><?php echo $hasil->NAMA_PAKET; ?></td>
                                </tr>
                                <tr>
                                    <td><b>JUMLAH PORSI CATERING</b></td>
                                    <td>:</td>
                                    <td><?php echo $hasil->JUMLAH_CATERING; ?> Porsi</td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL HARGA CATERING</b></td>
                                    <td>:</td>
                                    <td>Rp. <?php echo number_format($hasil->TOTAL_HARGA); ?></td>
                                </tr>
                                <tr>
                                    <td><b>HARGA GEDUNG</b></td>
                                    <td>:</td>
                                    <td>Rp. <?php echo number_format($hasil->HARGA_SEWA); ?></td>
                                </tr>
                                <tr>
                                    <td><b>PAJAK 10%</b></td>
                                    <td><b>:</b></td>
                                    <td>Rp. <?php echo number_format($tax) ?></td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL HARGA GEDUNG +CATERING</b></td>
                                    <td>:</td>
                                    <td>Rp. <?php echo number_format($hasil->TOTAL_KESELURUHAN); ?></td>
                                </tr>
                                <tr>
                                    <td><b>TOTAL KESELURUHAN (CATERING + GEDUNG + PAJAK)</b></td>
                                    <td><b>:</b></td>
                                    <td>Rp. <?php echo number_format($total_stl_pajak) ?></td>
                                </tr>
                                <tr>
                                    <td><b>DESKRIPSI PEMESANAN</b></td>
                                    <td>:</td>
                                    <td><?php echo $hasil->DESKRIPSI_ACARA; ?></td>
                                </tr>
                                <tr>
                                    <td><b>AKSI</b></td>
                                    <td>:</td>
                                    <td>
                                        <a class="link-action"
                                            href="<?php echo site_url('admin/admin_controls/delete_jadwal/'.$id_pemesanan.'')?>"
                                            onclick="return dialog();">
                                            Hapus Acara
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="cardbody border-t border-blue-900 flex items-center">
                        <a href="<?php echo site_url('admin/transaksi'); ?>"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-700 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800 active:bg-blue-900">
                            <i class="material-icons text-base">arrow_back</i>
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

    <script type="text/javascript">
    function dialog() {
        if (confirm("Yakin Hapus Jadwal?")) {
            return true;
        } else {
            return false;
        }
    }
    </script>

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