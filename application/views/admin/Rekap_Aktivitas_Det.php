<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

$no = 1;
$first_date_period  = date_create($first_period);
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
        href="<?php echo base_url('assets/home/assets/img/favicon/apple-touch-icon-152x152.png'); ?>">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url('assets/home/assets/img/favicon/mstile-144x144.png'); ?>">
    <link rel="icon" href="<?php echo base_url('assets/home/assets/img/favicon/favicon-32x32.png'); ?>" sizes="32x32">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize (UNTUK TABLE & GRID SAJA) -->
    <link href="<?php echo base_url('assets/home/materialize/css/materialize.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/home/style.css'); ?>" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- ================= SIDEBAR COMPONENT ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- ===================================================== -->

    <!-- Overlay (mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    <!-- Topbar -->
    <header class="fixed top-0 left-0 right-0 z-20 bg-white/90 backdrop-blur border-b">
        <div class="h-16 px-4 md:px-6 flex items-center gap-3">
            <button id="sidebarToggle"
                class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="font-semibold">Admin Smart Office</div>

            <div class="ml-auto text-sm text-gray-500">
                <?php echo htmlspecialchars(isset($session_id) && $session_id !== '' ? $session_id : '-', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="pt-20 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-6xl mx-auto">
            <div class="mb-4">
                <h5 class="text-xl font-semibold text-gray-800">Rekapitulasi Aktivitas</h5>
                <p class="text-sm text-gray-500 mt-1">
                    Periode:
                    <?php echo date_format($first_date_period, 'd F Y'); ?>
                    —
                    <?php echo date_format($second_date_period, 'd F Y'); ?>
                </p>
            </div>

            <div class="bg-white border rounded-xl shadow-sm">
                <div class="p-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total data: <b><?php echo (is_array($hasil) ? count($hasil) : 0); ?></b>
                    </div>

                    <a class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm"
                        href="<?php echo site_url('admin/kegiatan_download_pdf/'.$first_period.'/'.$last_period); ?>">
                        Ekspor ke PDF
                    </a>
                </div>

                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="bordered highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Gedung</th>
                                    <th>Tanggal Pemesanan</th>
                                    <th>Tanggal Approval</th>
                                    <th>Kegiatan</th>
                                    <th>Jam Kegiatan</th>
                                    <th>Nama Pemesan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($hasil) && is_array($hasil)): ?>
                                <?php foreach ($hasil as $row): ?>
                                <?php
                      $date = (!empty($row['TANGGAL_FINAL_PEMESANAN'])) ? date_create($row['TANGGAL_FINAL_PEMESANAN']) : null;
                      $date_approval = (!empty($row['TANGGAL_APPROVAL'])) ? date_create($row['TANGGAL_APPROVAL']) : null;

                      $jamMulai = null;
                      if (isset($row['JAM_MULAI']) && $row['JAM_MULAI'] !== '') {
                        $jamMulai = $row['JAM_MULAI'];
                      } elseif (isset($row['JAM_PEMESANAN']) && $row['JAM_PEMESANAN'] !== '') {
                        $jamMulai = $row['JAM_PEMESANAN'];
                      }

                      $jamSelesai = null;
                      if (isset($row['JAM_SELESAI']) && $row['JAM_SELESAI'] !== '') {
                        $jamSelesai = $row['JAM_SELESAI'];
                      }

                      $jamText = '-';
                      if (!empty($jamMulai) && !empty($jamSelesai)) {
                        $jamText = date('H:i', strtotime($jamMulai)) . ' - ' . date('H:i', strtotime($jamSelesai));
                      } elseif (!empty($jamMulai)) {
                        $jamText = date('H:i', strtotime($jamMulai));
                      }

                      $namaGedung = isset($row['NAMA_GEDUNG']) ? $row['NAMA_GEDUNG'] : '-';
                      $deskAcara  = isset($row['DESKRIPSI_ACARA']) ? $row['DESKRIPSI_ACARA'] : '-';
                      $namaLengkap= isset($row['NAMA_LENGKAP']) ? $row['NAMA_LENGKAP'] : '-';
                    ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($namaGedung, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo $date ? date_format($date, 'd M Y') : '-'; ?></td>
                                    <td><?php echo $date_approval ? date_format($date_approval, 'd M Y') : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($deskAcara, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($jamText, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($namaLengkap, ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="7" class="center-align">Tidak ada data.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-xs text-gray-500">
                        *Gunakan fitur ekspor untuk mengunduh rekap dalam format PDF.
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Materialize core JavaScript -->
    <script src="<?php echo base_url('assets/home/assets/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/home/materialize/js/materialize.js'); ?>"></script>
    <script src="<?php echo base_url('assets/home/index.js'); ?>"></script>

    <!-- Sidebar Toggle Script -->
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