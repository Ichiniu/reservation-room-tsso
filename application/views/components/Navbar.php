<?php
$username   = $this->session->userdata('username');
$session_id = $this->session->userdata('username');

if (isset($flag)) {
    $flag = (int)$flag;
} else {
    $flag = 0;
}
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

                    <!-- PEMESANAN -->
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

                    <a href="<?= site_url('home/pembayaran'); ?>" class="flex items-center gap-2 hover:text-slate-900">
                        <i class="bi bi-credit-card"></i> TRANSAKSI
                    </a>
                </nav>

                <!-- RIGHT -->
                <div class="flex items-center gap-3">

                    <button id="mobileMenuBtn"
                        class="md:hidden inline-flex items-center justify-center rounded-lg border border-black/10 p-2 hover:bg-slate-100 transition">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <div class="relative hidden md:block">
                        <button type="button"
                            class="profile-toggle flex items-center gap-2 px-3 py-1 rounded-full bg-white hover:bg-slate-100 border border-black/10 transition">
                            <?php $foto_profil = $this->session->userdata('foto_profil'); ?>
                            <?php if (!empty($foto_profil)): ?>
                                <img src="<?= base_url($foto_profil); ?>" class="h-7 w-7 rounded-full object-cover" alt="Foto Profil">
                            <?php else: ?>
                                <i class="bi bi-person-circle text-slate-700"></i>
                            <?php endif; ?>

                            <span class="text-xs font-medium text-slate-700">
                                <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <i class="bi bi-chevron-down text-xs text-slate-600"></i>
                        </button>

                        <div
                            class="profile-menu hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-md border border-black/10 text-sm overflow-hidden">
                            <a href="<?= site_url('edit_data/' . $username); ?>"
                                class="flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                <i class="bi bi-pencil-square"></i> Edit Data Diri
                            </a>

                            <a href="<?= site_url('edit_foto/' . $username); ?>"
                                class="flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                <i class="bi bi-camera"></i> Edit Foto Profil
                            </a>

                            <!-- ===== NOTIF MENU (NEW) ===== -->
                            <div class="border-t border-black/5">
                                <button type="button" onclick="aktifkanNotif()"
                                    class="w-full text-left flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                    <i class="bi bi-bell"></i> Aktifkan Notifikasi
                                </button>

                                <!-- status kecil biar user ngerti -->
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

                <a href="<?= site_url('home/pembayaran'); ?>" class="flex items-center gap-2">
                    <i class="bi bi-credit-card"></i> TRANSAKSI
                </a>

                <div class="border-t border-black/10 pt-3 mt-2">
                    <div class="text-xs text-slate-500 mb-2">Akun</div>

                    <a href="<?= site_url('edit_data/' . $username); ?>" class="flex items-center gap-2">
                        <i class="bi bi-pencil-square"></i> Edit Data
                    </a>

                    <!-- ===== NOTIF MOBILE (NEW) ===== -->
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

    <script>
    // =========================================================
    // 1) Fungsi simpel: user klik -> Notification.requestPermission()
    // =========================================================
    async function aktifkanNotif() {
        if (!("Notification" in window)) {
            alert("Browser kamu tidak mendukung notifikasi.");
            return;
        }

        // kalau sebelumnya diblokir user, harus buka setting browser (ikon i)
        if (Notification.permission === "denied") {
            alert(
                "Notifikasi diblokir.\n\n" +
                "Klik ikon (i) di sebelah URL → Site settings → Notifications → Allow,\n" +
                "lalu refresh halaman."
            );
            updateNotifUI(); // update label
            return;
        }

        const permission = await Notification.requestPermission(); // ✅ sesuai permintaan kamu

        if (permission === "granted") {
            new Notification("Notifikasi aktif ✅", {
                body: "Sekarang kamu akan dapat pemberitahuan saat ada update."
            });
        } else {
            alert("Notifikasi belum diizinkan. Silakan pilih 'Allow'.");
        }

        updateNotifUI(); // update label setelah klik
    }

    // =========================================================
    // 2) Helper UI status notifikasi (dot + teks)
    // =========================================================
    function updateNotifUI() {
        var dot = document.getElementById('notifDot');
        var txt = document.getElementById('notifStatusText');

        if (!dot || !txt) return;

        if (!("Notification" in window)) {
            dot.className = "inline-block w-2 h-2 rounded-full bg-slate-300";
            txt.textContent = "Notifikasi: tidak didukung";
            return;
        }

        if (Notification.permission === "granted") {
            dot.className = "inline-block w-2 h-2 rounded-full bg-emerald-500";
            txt.textContent = "Notifikasi: aktif";
        } else if (Notification.permission === "denied") {
            dot.className = "inline-block w-2 h-2 rounded-full bg-red-500";
            txt.textContent = "Notifikasi: diblokir";
        } else {
            dot.className = "inline-block w-2 h-2 rounded-full bg-amber-500";
            txt.textContent = "Notifikasi: belum diizinkan";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {

        // ================== PROFILE DROPDOWN ==================
        var profileToggle = document.querySelector('.profile-toggle');
        var profileMenu = document.querySelector('.profile-menu');

        if (profileToggle && profileMenu) {
            profileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
                updateNotifUI(); // update status saat dropdown dibuka
            });

            document.addEventListener('click', function() {
                profileMenu.classList.add('hidden');
            });
        }

        // ================== MOBILE MENU ==================
        var mobileBtn = document.getElementById('mobileMenuBtn');
        var mobileMenu = document.getElementById('mobileMenu');

        if (mobileBtn && mobileMenu) {
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
        }

        // ================== NOTIF TANPA API (saat load/refresh) ==================
        var badgeDesktop = document.getElementById('notifBadge');
        var soundEl = document.getElementById('notifSound');

        var currentCount = 0;
        if (badgeDesktop && badgeDesktop.getAttribute('data-count')) {
            currentCount = parseInt(badgeDesktop.getAttribute('data-count'), 10);
            if (isNaN(currentCount)) currentCount = 0;
        }

        var lastCountStr = localStorage.getItem('lastFlagPemesanan');
        var lastCount = 0;
        if (lastCountStr !== null) {
            lastCount = parseInt(lastCountStr, 10);
            if (isNaN(lastCount)) lastCount = 0;
        }

        // unlock audio (browser butuh interaksi user)
        var audioUnlocked = false;

        function unlockAudioOnce() {
            if (!soundEl || audioUnlocked) return;
            audioUnlocked = true;
            soundEl.play().then(function() {
                soundEl.pause();
                soundEl.currentTime = 0;
            }).catch(function() {});
        }
        document.addEventListener('click', unlockAudioOnce, {
            once: true
        });
        document.addEventListener('keydown', unlockAudioOnce, {
            once: true
        });

        function playSound() {
            if (!soundEl) return;
            soundEl.currentTime = 0;
            soundEl.play().catch(function() {});
        }

        function showDesktopNotif(title, body) {
            if (!("Notification" in window)) return;
            if (Notification.permission !== "granted") return;
            new Notification(title, {
                body: body,
                tag: "sireru-notif"
            });
        }

        // Kalau count naik dibanding kunjungan sebelumnya -> bunyi + desktop notif
        if (currentCount > lastCount) {
            showDesktopNotif("Notifikasi baru", "Ada update baru di pemesanan.");
            playSound();
        }

        // simpan untuk pembanding load berikutnya
        localStorage.setItem('lastFlagPemesanan', String(currentCount));

        // set status notif saat page load
        updateNotifUI();
    });
    </script>

</body>