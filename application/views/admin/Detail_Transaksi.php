<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$this->load->helper('form');

// PAJAK DIHILANGKAN TOTAL
$total_transaksi = (int) $hasil->TOTAL_KESELURUHAN;
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
    <title>Detail Transaksi</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }

        input.validate,
        input[type="text"] {
            border-bottom: 1px solid #e5e7eb !important;
            box-shadow: none !important;
        }

        input.validate:focus,
        input[type="text"]:focus {
            border-bottom: 2px solid #1d4ed8 !important;
            box-shadow: 0 1px 0 0 #1d4ed8 !important;
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

        .btn-blue700 {
            background: #1d4ed8 !important;
            border-radius: 12px !important;
        }

        .btn-blue700:hover {
            filter: brightness(0.95);
        }

        .link-file {
            color: #111827;
            text-decoration: underline;
        }

        .link-file:hover {
            color: #1d4ed8;
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

    <!-- Main -->
    <main class="pt-20 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-4xl mx-auto">

            <!-- Header -->
            <div class="mb-5">
                <h5 class="text-xl font-semibold text-gray-900 m-0">Ringkasan Pesanan</h5>
                <p class="text-sm text-gray-500 mt-1">Detail transaksi dan aksi approval proposal.</p>

                <div class="mt-3">
                    <a href="<?php echo site_url('admin/transaksi'); ?>"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                        <i class="material-icons text-base mr-2">arrow_back</i>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Card utama -->
            <div class="cardbox">
                <div class="cardhead">
                    <div class="font-semibold text-gray-900">ID Pemesanan: <?php echo $hasil->ID_PEMESANAN; ?></div>
                    <div class="text-sm text-gray-500 mt-1">
                        <?php $date = date_create($hasil->TANGGAL_PEMESANAN);
                        echo date_format($date, 'd F Y'); ?>
                    </div>
                    <div class="text-sm text-gray-600 mt-2">
                        Total: <b>Rp. <?php echo number_format($total_transaksi); ?></b>
                    </div>
                </div>

                <div class="cardbody">
                    <?php echo form_open('admin/detail_transaksi/' . $hasil->ID_PEMESANAN . ''); ?>

                    <!-- Detail transaksi (tabel) -->
                    <div class="cardbox" style="border-radius:12px;">
                        <div class="cardhead">
                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="material-icons text-gray-700 text-base">info</i>
                                Detail Transaksi
                            </div>
                            <div class="hint">Data pemesanan, catering, dan total biaya.</div>
                        </div>

                        <div class="cardbody" style="padding:0;">
                            <div class="overflow-x-auto">
                                <table class="bordered">
                                    <tbody>
                                        <tr>
                                            <td><b>Id Pemesan</b></td>
                                            <td>:</td>
                                            <td><b><?php echo $hasil->ID_PEMESANAN; ?></b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Username</b></td>
                                            <td>:</td>
                                            <td><?php echo $hasil->USERNAME; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Tanggal Kegiatan</b></td>
                                            <td>:</td>
                                            <td><?php $date = date_create($hasil->TANGGAL_PEMESANAN);
                                                echo date_format($date, 'd F Y'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Jam Pemesanan</b></td>
                                            <td>:</td>
                                            <td class="px-4 py-3">
                                                <?php
                                                $mulai   = isset($hasil->JAM_PEMESANAN) ? $hasil->JAM_PEMESANAN : '';
                                                $selesai = isset($hasil->JAM_SELESAI) ? $hasil->JAM_SELESAI : '';

                                                if ($mulai != '' && $selesai != '') {
                                                    echo date('H:i', strtotime($mulai)) . ' - ' . date('H:i', strtotime($selesai)) . ' WIB';
                                                } elseif ($mulai != '') {
                                                    echo date('H:i', strtotime($mulai)) . ' WIB';
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Email</b></td>
                                            <td>:</td>
                                            <td><?php echo $hasil->EMAIL; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Ruangan</b></td>
                                            <td>:</td>
                                            <td><?php echo $hasil->NAMA_GEDUNG; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Catering</b></td>
                                            <td>:</td>
                                            <td><?php echo $hasil->NAMA_PAKET; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Jumlah Porsi Catering</b></td>
                                            <td>:</td>
                                            <td><?php echo $hasil->JUMLAH_CATERING; ?> Porsi</td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Harga Catering</b></td>
                                            <td>:</td>
                                            <td>Rp. <?php echo number_format($hasil->TOTAL_HARGA); ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Harga Sewa Ruangan</b></td>
                                            <td>:</td>
                                            <td>Rp. <?php echo number_format($hasil->HARGA_SEWA); ?></td>
                                        </tr>
                                        <!-- Combined total removed per request -->

                                        <!-- PAJAK DIHAPUS -->

                                        <tr>
                                            <td><b>Total Keseluruhan (Catering + Ruangan)</b></td>
                                            <td><b>:</b></td>
                                            <td><b>Rp. <?php echo number_format($total_transaksi); ?></b></td>
                                        </tr>

                                        <tr>
                                            <td><b>Deskripsi Kegiatan</b></td>
                                            <td>:</td>
                                            <td><?php echo !empty($details) ? $details->DESKRIPSI_ACARA : '-'; ?></td>
                                        </tr>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Aksi proposal (dibawah tabel, 1 kolom) -->
                    <div class="cardbox" style="margin-top:14px;">
                        <div class="cardhead">
                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="material-icons text-gray-700 text-base">account_circle</i>
                                Keputusan Admin
                            </div>
                            <div class="hint">Remarks muncul hanya jika menolak.</div>
                        </div>
                    </div>


                    <div class="cardbody">
                        <p style="margin:0;">
                            <input class="with-gap" name="status-proposal" type="radio" id="ya" value="1"
                                onclick="return showInput();" />
                            <label for="ya">Disetujui</label>
                        </p>

                        <div style="height:10px;"></div>

                        <p style="margin:0;">
                            <input class="with-gap" name="status-proposal" type="radio" id="tidak" value="4"
                                onclick="return showInput();" />
                            <label for="tidak">Ditolak</label>
                        </p>

                        <div style="height:14px;"></div>

                        <div id="title" hidden class="text-sm font-semibold text-gray-900">Catatan (Agar User tahu kenapa ditolak)</div>
                        <div id="colon" hidden class="text-xs text-gray-500">:</div>
                        <input type="text" name="remarks" id="remarks" hidden class="validate"
                            placeholder="Masukkan alasan penolakan...">

                        <div class="hint mt-2">Pastikan pilihan sudah benar sebelum submit.</div>
                    </div>
                </div>

                <!-- Footer tombol (HANYA DI BAWAH) -->
                <div class="mt-6 pt-4 border-t border-slate-200">
                    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-end">

                        <!-- Tombol Batal -->
                        <a href="<?= site_url('admin/transaksi'); ?>" class="inline-flex items-center justify-center gap-2
              px-5 py-2.5 rounded-xl
              bg-white border border-slate-300
              text-slate-700 text-sm font-semibold
              hover:bg-slate-100 hover:border-slate-400
              transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batal
                        </a>

                        <!-- Tombol Submit -->
                        <button type="submit" name="submit" onclick="return dialog();" class="inline-flex items-center justify-center gap-2
                   px-6 py-2.5 rounded-xl
                   bg-blue-700 text-white text-sm font-semibold
                   hover:bg-blue-800
                   focus:ring-2 focus:ring-blue-300
                   transition shadow-sm">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan
                        </button>

                    </div>
                </div>


                <?php echo form_close(); ?>
            </div>
        </div>

        </div>
    </main>

    <script type="text/javascript">
        function dialog() {
            if (confirm("Lanjutkan? ")) {
                return true;
            } else {
                return false;
            }
        }

        function showInput() {
            var tolak = document.getElementById("tidak").checked;
            var terima = document.getElementById("ya").checked;
            if (tolak == true) {
                document.getElementById("title").hidden = false;
                document.getElementById("colon").hidden = false;
                document.getElementById("remarks").hidden = false;
            } else if (terima == true) {
                document.getElementById("title").hidden = true;
                document.getElementById("colon").hidden = true;
                document.getElementById("remarks").hidden = true;
            }
        }
    </script>

    <!-- Sidebar toggle -->
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

            var mq = window.matchMedia('(min-width: 768px)');
            mq.addEventListener('change', function(e) {
                if (e.matches) {
                    if (overlay) overlay.classList.add('hidden');
                    sidebar.classList.remove('-translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    closeSidebar();
                }
            });
        })();
    </script>

</body>

</html>