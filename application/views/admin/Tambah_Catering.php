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

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed"
        href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Tambah Catering</title>

    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize -->
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet" type="text/css">

    <style>
    /* Rapihin input Materialize di layout Tailwind */
    .input-field {
        margin-top: 14px;
    }

    .input-field label {
        position: static !important;
        transform: none !important;
    }

    .input-field input {
        margin-top: 8px;
    }

    input.validate {
        border-bottom: 1px solid #e5e7eb !important;
        /* gray-200 */
        box-shadow: none !important;
    }

    input.validate:focus {
        border-bottom: 2px solid #1d4ed8 !important;
        /* blue-700 */
        box-shadow: 0 1px 0 0 #1d4ed8 !important;
    }

    .hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
    }

    /* gray-500 */
    </style>
</head>

<body class="bg-gray-50 text-gray-900">

    <!-- ================= SIDEBAR COMPONENT ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- Overlay mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    <!-- Topbar (monochrome) -->
    <header class="fixed top-0 left-0 right-0 z-20 bg-white border-b">
        <div class="h-16 px-4 md:px-6 flex items-center gap-3">
            <button id="sidebarToggle"
                class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
                type="button" aria-label="Toggle sidebar">
                <i class="material-icons text-gray-800">menu</i>
            </button>

            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white">
                    <i class="material-icons text-gray-800">restaurant</i>
                </span>
                <div class="leading-tight">
                    <div class="font-semibold">Tambah Catering</div>
                    <div class="text-xs text-gray-500">Admin Panel</div>
                </div>
            </div>

            <div class="ml-auto text-sm text-gray-600">
                <?php echo htmlspecialchars(isset($session_id) && $session_id !== '' ? $session_id : '-', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="pt-20 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-5xl mx-auto">

            <!-- Header section -->
            <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h5 class="text-xl font-semibold text-gray-900 m-0">Form Paket Catering</h5>
                    <p class="text-sm text-gray-500 mt-1">Isi data paket catering di bawah ini.</p>
                </div>

                <a href="<?php echo site_url('admin/catering'); ?>"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                    <i class="material-icons text-base mr-2">arrow_back</i>
                    Kembali
                </a>
            </div>

            <!-- Card -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <i class="material-icons text-gray-800">edit</i>
                        <div class="font-semibold text-gray-900">Tambah Menu Catering</div>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        Tips: isi harga tanpa titik/koma (contoh: <b>125000</b>).
                    </div>
                </div>

                <div class="p-4 md:p-6">
                    <form class="row" method="post"
                        action="<?php echo site_url('admin/admin_controls/tambah_catering'); ?>">

                        <!-- Grid 2 kolom (desktop) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Nama Paket -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">local_offer</i>
                                    Nama Paket
                                </div>
                                <div class="input-field">
                                    <input placeholder="e.g Paket Hemat 1" name="nama_paket" type="text"
                                        class="validate">
                                    <div class="hint">Nama paket tampil di daftar catering.</div>
                                </div>
                            </div>

                            <!-- Harga -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">payments</i>
                                    Harga Per Porsi
                                </div>
                                <div class="input-field">
                                    <input placeholder="e.g 125000" name="harga" type="text" class="validate">
                                    <div class="hint">Masukkan angka saja (contoh: 125000).</div>
                                </div>
                            </div>

                            <!-- Menu Pembuka -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">emoji_food_beverage</i>
                                    Menu Pembuka
                                </div>
                                <div class="input-field">
                                    <input placeholder="e.g Dimsum" name="menu_pembuka" type="text" class="validate">
                                    <div class="hint">Contoh: Dimsum, Soup, Salad.</div>
                                </div>
                            </div>

                            <!-- Menu Penutup -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">icecream</i>
                                    Menu Penutup
                                </div>
                                <div class="input-field">
                                    <input placeholder="e.g Dessert" name="menu_penutup" type="text" class="validate">
                                    <div class="hint">Contoh: Dessert, Buah, Pudding.</div>
                                </div>
                            </div>

                            <!-- Menu Utama (full width) -->
                            <div class="md:col-span-2 border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">dinner_dining</i>
                                    Menu Utama
                                </div>
                                <div class="input-field">
                                    <input placeholder="e.g Nasi Lemak" name="menu_utama" type="text" class="validate">
                                    <div class="hint">Menu utama biasanya paling menonjol di paket.</div>
                                </div>
                            </div>

                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex items-center gap-3 justify-start">
                            <!-- Button utama: blue-700 -->
                            <button type="submit" name="submit" id="submit" value="Tambah Menu"
                                class="inline-flex h-10 items-center gap-2 rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white hover:bg-blue-800 active:bg-blue-900">
                                <i class="material-icons text-[18px]">add</i>
                                Tambah Menu
                            </button>

                            <!-- Button secondary -->
                            <a href="<?php echo site_url('admin/catering'); ?>"
                                class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 active:bg-gray-100">
                                <i class="material-icons text-[18px]">close</i>
                                Batal
                            </a>
                        </div>


                    </form>
                </div>
            </div>

            <div class="text-xs text-gray-500 text-center mt-6">
                © <?php echo date('Y'); ?> Smart Office • Admin Panel
            </div>

        </div>
    </main>

    <!-- JS -->
    <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/index.js"></script>

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