<?php
$username   = $this->session->userdata('username');
$session_id = $this->session->userdata('username');
$foto_profil = $this->session->userdata('foto_profil'); // ✅ dipakai desktop + mobile

$flag     = isset($flag) ? (int)$flag : 0;         // badge PEMESANAN
$trx_flag = isset($trx_flag) ? (int)$trx_flag : 0; // badge TRANSAKSI
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<body class="bg-white text-slate-800">

    <header class="bg-white border-b border-black/5 sticky top-0 z-30">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-16">

                <!-- BRAND -->
                <div class="flex items-center gap-4">
                    <div class="h-9 w-9 rounded-lg bg-white border border-black/10 flex items-center justify-center">
                        <img src="<?= base_url('assets/login/LogoTSNew.png'); ?>" class="h-8 w-8 object-contain"
                            alt="Logo">
                    </div>
                    <div class="leading-tight">
                        <div class="text-[10px] font-semibold tracking-[0.25em] uppercase text-slate-500">Smart Office
                        </div>
                        <div class="text-sm font-semibold text-slate-800">SIRERU</div>
                    </div>
                </div>

                <!-- DESKTOP MENU -->
                <nav
                    class="hidden md:flex items-center gap-10 text-[11px] font-semibold tracking-widest text-slate-700">
                    <a href="<?= site_url('home/' . $session_id . '/'); ?>"
                        class="flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-house-door"></i> HOME
                    </a>

                    <a href="<?= site_url('home/jadwal'); ?>" class="flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-calendar-week"></i> JADWAL
                    </a>

                    <!-- PEMESANAN + BADGE -->
                    <a id="pemesananLinkDesktop" href="<?= site_url('home/pemesanan'); ?>"
                        class="relative flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-journal-text"></i> PEMESANAN
                        <span id="notifBadge" data-count="<?= $flag; ?>"
                            class="<?= ($flag > 0) ? '' : 'hidden'; ?> ml-1 rounded-full bg-red-500 text-[10px] text-white px-1.5 py-0.5">
                            <?= ($flag > 0) ? $flag : ''; ?>
                        </span>
                    </a>

                    <a href="<?= site_url('home/view-catering'); ?>"
                        class="flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-cup-hot"></i> CATERING
                    </a>

                    <!-- TRANSAKSI + BADGE -->
                    <a id="transaksiLinkDesktop" href="<?= site_url('home/pembayaran'); ?>"
                        class="relative flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-credit-card"></i> TRANSAKSI
                        <span id="trxBadge" data-count="<?= $trx_flag; ?>"
                            class="<?= ($trx_flag > 0) ? '' : 'hidden'; ?> ml-1 rounded-full bg-red-500 text-[10px] text-white px-1.5 py-0.5">
                            <?= ($trx_flag > 0) ? $trx_flag : ''; ?>
                        </span>
                    </a>
                </nav>

                <!-- RIGHT -->
                <div class="flex items-center gap-3">

                    <!-- ✅ MOBILE: PROFIL DI HEADER (foto + nama kecil) -->
                    <div class="md:hidden flex items-center gap-2 max-w-[55vw]">
                        <span class="relative inline-flex shrink-0">
                            <?php if (!empty($foto_profil)): ?>
                            <img src="<?= base_url($foto_profil); ?>"
                                class="h-9 w-9 rounded-full object-cover border border-black/10" alt="Foto Profil">
                            <?php else: ?>
                            <span
                                class="h-9 w-9 rounded-full grid place-items-center bg-slate-100 border border-black/10">
                                <i class="bi bi-person-circle text-slate-700 text-lg"></i>
                            </span>
                            <?php endif; ?>

                            <!-- indikator online -->
                            <span
                                class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        </span>

                        <div class="min-w-0">
                            <div class="text-[10px] text-slate-500 leading-none">Hi,</div>
                            <div class="text-xs font-semibold text-slate-800 truncate">
                                <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- MOBILE BUTTON -->
                    <button id="mobileMenuBtn"
                        class="md:hidden inline-flex items-center justify-center rounded-lg border border-black/10 p-2 hover:bg-slate-100 transition"
                        aria-expanded="false" aria-controls="mobileMenu">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <!-- PROFILE (DESKTOP) -->
                    <div class="relative hidden md:block">
                        <button id="profileToggle" type="button"
                            class="profile-toggle flex items-center gap-2 px-3 py-1 rounded-full bg-white hover:bg-slate-100 border border-black/10 transition"
                            aria-haspopup="menu" aria-expanded="false">

                            <span class="relative inline-flex">
                                <?php if (!empty($foto_profil)): ?>
                                <img src="<?= base_url($foto_profil); ?>" class="h-7 w-7 rounded-full object-cover"
                                    alt="Foto Profil">
                                <?php else: ?>
                                <span
                                    class="h-7 w-7 rounded-full grid place-items-center bg-slate-100 border border-black/10">
                                    <i class="bi bi-person-circle text-slate-700"></i>
                                </span>
                                <?php endif; ?>

                                <span
                                    class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                            </span>

                            <span class="text-xs font-medium text-slate-700">
                                <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <i class="bi bi-chevron-down text-xs text-slate-600"></i>
                        </button>

                        <div id="profileMenu"
                            class="profile-menu hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-md border border-black/10 text-sm overflow-hidden">
                            <a href="<?= site_url('edit_data/' . $username); ?>"
                                class="flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                <i class="bi bi-pencil-square"></i> Edit Data Diri
                            </a>

                            <a href="<?= site_url('edit_foto/' . $username); ?>"
                                class="flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                <i class="bi bi-camera"></i> Edit Foto Profil
                            </a>

                            <div class="border-t border-black/5">
                                <button type="button" onclick="aktifkanNotif()"
                                    class="w-full text-left flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                    <i class="bi bi-bell"></i> Aktifkan Notifikasi
                                </button>

                                <button id="testSound"
                                    class="ml-2 my-2 px-3 py-1 rounded-md text-xs border border-black/10 bg-slate-900 text-white hover:bg-slate-800">
                                    Test Sound
                                </button>
                                <button id="testDesktop"
                                    class="ml-2 my-2 px-3 py-1 rounded-md text-xs border border-black/10 bg-slate-900 text-white hover:bg-slate-800">
                                    Test Desktop
                                </button>

                                <div class="px-4 pb-2 text-[11px] text-slate-500 flex items-center gap-2">
                                    <span id="notifDot" class="inline-block w-2 h-2 rounded-full bg-slate-300"></span>
                                    <span id="notifStatusText">Notifikasi: belum dicek</span>
                                </div>
                            </div>

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

        <!-- MOBILE MENU -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-black/5 bg-white">
            <nav class="flex flex-col p-4 text-sm font-semibold text-slate-700 gap-3">
                <a href="<?= site_url('home'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-house-door"></i> HOME
                </a>

                <a href="<?= site_url('home/jadwal'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-calendar-week"></i> JADWAL
                </a>

                <a id="pemesananLinkMobile" href="<?= site_url('home/pemesanan'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-journal-text"></i> PEMESANAN
                    <span id="notifBadgeMobile" data-count="<?= $flag; ?>"
                        class="<?= ($flag > 0) ? '' : 'hidden'; ?> ml-1 rounded-full bg-red-500 text-[10px] text-white px-1.5 py-0.5">
                        <?= ($flag > 0) ? $flag : ''; ?>
                    </span>
                </a>

                <a href="<?= site_url('home/view-catering'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-cup-hot"></i> CATERING
                </a>

                <a id="transaksiLinkMobile" href="<?= site_url('home/pembayaran'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-credit-card"></i> TRANSAKSI
                    <span id="trxBadgeMobile" data-count="<?= $trx_flag; ?>"
                        class="<?= ($trx_flag > 0) ? '' : 'hidden'; ?> ml-1 rounded-full bg-red-500 text-[10px] text-white px-1.5 py-0.5">
                        <?= ($trx_flag > 0) ? $trx_flag : ''; ?>
                    </span>
                </a>

                <div class="border-t border-black/10 pt-3 mt-2">
                    <div class="text-xs text-slate-500 mb-2">Akun</div>

                    <a href="<?= site_url('edit_data/' . $username); ?>" class="flex items-center gap-2">
                        <i class="bi bi-pencil-square"></i> Edit Data
                    </a>

                    <a href="<?= site_url('edit_foto/' . $username); ?>" class="flex items-center gap-2">
                        <i class="bi bi-camera"></i> Edit Foto Profil
                    </a>

                    <button type="button" onclick="aktifkanNotif()"
                        class="flex items-center gap-2 text-left w-full hover:text-slate-900">
                        <i class="bi bi-bell"></i> Aktifkan Notifikasi
                    </button>

                    <a href="<?= site_url('home/home/logout'); ?>" class="flex items-center gap-2 text-red-600">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- AUDIO -->
    <audio id="notifSound" preload="auto">
        <source src="<?= base_url('assets/nada_notifikasi1.mp3'); ?>" type="audio/mpeg">
    </audio>

    <!-- SCRIPT dropdown profil + mobile menu -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // ===== MOBILE MENU TOGGLE =====
        const mobileBtn = document.getElementById("mobileMenuBtn");
        const mobileMenu = document.getElementById("mobileMenu");

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener("click", function(e) {
                e.stopPropagation();
                mobileMenu.classList.toggle("hidden");

                const expanded = !mobileMenu.classList.contains("hidden");
                mobileBtn.setAttribute("aria-expanded", expanded ? "true" : "false");
            });

            // klik link di mobile -> tutup menu
            mobileMenu.querySelectorAll("a,button").forEach(el => {
                el.addEventListener("click", () => {
                    mobileMenu.classList.add("hidden");
                    mobileBtn.setAttribute("aria-expanded", "false");
                });
            });

            // klik luar -> tutup menu
            document.addEventListener("click", function(e) {
                if (!mobileMenu.classList.contains("hidden") && !mobileMenu.contains(e.target) && !
                    mobileBtn.contains(e.target)) {
                    mobileMenu.classList.add("hidden");
                    mobileBtn.setAttribute("aria-expanded", "false");
                }
            });
        }

        // ===== PROFILE DROPDOWN (DESKTOP) =====
        const profileToggle = document.getElementById("profileToggle");
        const profileMenu = document.getElementById("profileMenu");

        function closeProfile() {
            if (!profileMenu || !profileToggle) return;
            profileMenu.classList.add("hidden");
            profileToggle.setAttribute("aria-expanded", "false");
        }

        if (profileToggle && profileMenu) {
            profileToggle.addEventListener("click", function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle("hidden");

                const expanded = !profileMenu.classList.contains("hidden");
                profileToggle.setAttribute("aria-expanded", expanded ? "true" : "false");
            });

            // klik di luar -> tutup
            document.addEventListener("click", function(e) {
                if (!profileMenu.classList.contains("hidden")) {
                    if (!profileMenu.contains(e.target) && !profileToggle.contains(e.target)) {
                        closeProfile();
                    }
                }
            });

            // ESC -> tutup
            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape") closeProfile();
            });
        }
    });
    </script>

    <!-- ====== SCRIPT NOTIF KAMU (tempel di sini) ====== -->
    <script>
    /* =========================================================
       REMINDER SCRIPT (POPUP MUNCUL BERKALI-KALI)
       tempel script notif kamu di sini
       ========================================================= */
    </script>

</body>