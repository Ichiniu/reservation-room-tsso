<?php
$username = $this->session->userdata('username');
$session_id = $this->session->userdata('username');
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<body class="bg-white text-slate-800">

    <!-- ================= NAVBAR ================= -->
    <header class="bg-white border-b border-black/5 sticky top-0 z-30">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-16">

                <!-- ===== LEFT: BRAND ===== -->
                <div class="flex items-center gap-4">
                    <div class="h-9 w-9 rounded-lg bg-white border border-black/10 flex items-center justify-center">
                        <img src="<?= base_url('assets/login/LogoTSNew.png'); ?>" class="h-8 w-8 object-contain"
                            alt="Logo">
                    </div>

                    <div class="leading-tight">
                        <div class="text-[10px] font-semibold tracking-[0.25em] uppercase text-slate-500">
                            Smart Office
                        </div>
                        <div class="text-sm font-semibold text-slate-800">
                            SIRERU
                        </div>
                    </div>
                </div>

                <!-- ===== DESKTOP MENU ===== -->
                <nav
                    class="hidden md:flex items-center gap-10 text-[11px] font-semibold tracking-widest text-slate-700">


                    <a href="<?= site_url('home/'.$session_id.'/'); ?>"
                        class="flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-house-door"></i> HOME
                    </a>

                    <a href="<?= site_url('home/jadwal'); ?>" class="flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-calendar-week"></i> JADWAL
                    </a>

                    <a href="<?= site_url('home/pemesanan'); ?>"
                        class="relative flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-journal-text"></i> PEMESANAN
                        <?php if (!empty($flag) && $flag > 0): ?>
                        <span class="ml-1 rounded-full bg-red-500 text-[10px] text-white px-1.5 py-0.5">
                            <?= $flag; ?>
                        </span>
                        <?php endif; ?>
                    </a>

                    <a href="<?= site_url('home/view-catering'); ?>"
                        class="flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-cup-hot"></i> CATERING
                    </a>

                    <a href="<?= site_url('home/pembayaran'); ?>" class="flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-credit-card"></i> TRANSAKSI
                    </a>
                </nav>

                <!-- ===== RIGHT ===== -->
                <div class="flex items-center gap-3">

                    <!-- ===== MOBILE HAMBURGER ===== -->
                    <button id="mobileMenuBtn" class="md:hidden inline-flex items-center justify-center
                 rounded-lg border border-black/10
                 p-2 hover:bg-slate-100 transition">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <!-- ===== PROFILE ===== -->
                    <div class="relative hidden md:block">
                        <button type="button" class="profile-toggle flex items-center gap-2 px-3 py-2 rounded-full
                   bg-white hover:bg-slate-100 border border-black/10 transition">

                            <i class="bi bi-person-circle text-slate-700"></i>
                            <span class="text-xs font-medium text-slate-700">
                                <?= htmlspecialchars($username); ?>
                            </span>
                            <i class="bi bi-chevron-down text-xs text-slate-600"></i>
                        </button>

                        <!-- PROFILE DROPDOWN -->
                        <div class="profile-menu hidden absolute right-0 mt-2 w-48
                      bg-white rounded-xl shadow-md border border-black/10 text-sm">
                            <a href="<?= site_url('edit_data/'.$username); ?>"
                                class="flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                <i class="bi bi-pencil-square"></i> Edit Data Diri
                            </a>
                            <a href="<?= site_url('edit_foto/'.$username); ?>"
                                class="flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                <i class="bi bi-camera"></i> Edit Foto Profil
                            </a>
                            <div class="border-t border-black/5">
                                <a href="<?= site_url('home/home/logout'); ?>"
                                    class="flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ===== MOBILE MENU PANEL ===== -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-black/5 bg-white">
            <nav class="flex flex-col p-4 text-sm font-semibold text-slate-700 gap-3">

                <a href="<?= site_url('home'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-house-door"></i> HOME
                </a>

                <a href="<?= site_url('home/jadwal'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-calendar-week"></i> JADWAL
                </a>

                <a href="<?= site_url('home/pemesanan'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-journal-text"></i> PEMESANAN
                </a>

                <a href="<?= site_url('home/view-catering'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-cup-hot"></i> CATERING
                </a>

                <a href="<?= site_url('home/pembayaran'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-credit-card"></i> TRANSAKSI
                </a>

                <div class="border-t border-black/10 pt-3 mt-2">
                    <div class="text-xs text-slate-500 mb-2">Akun</div>
                    <a href="<?= site_url('edit_data/'.$username); ?>" class="flex items-center gap-2">
                        <i class="bi bi-pencil-square"></i> Edit Data
                    </a>
                    <a href="<?= site_url('home/home/logout'); ?>" class="flex items-center gap-2 text-red-600">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- ================= SCRIPT ================= -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // PROFILE DROPDOWN
        const profileToggle = document.querySelector('.profile-toggle');
        const profileMenu = document.querySelector('.profile-menu');

        if (profileToggle) {
            profileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function() {
                profileMenu.classList.add('hidden');
            });
        }

        // MOBILE MENU
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
        });

        mobileMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });

    });
    </script>

</body>