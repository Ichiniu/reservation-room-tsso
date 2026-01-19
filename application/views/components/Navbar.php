<?php
$username   = $this->session->userdata('username');
$session_id = $this->session->userdata('username');

$flag     = isset($flag) ? (int)$flag : 0;        // badge PEMESANAN
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
                    <button id="mobileMenuBtn"
                        class="md:hidden inline-flex items-center justify-center rounded-lg border border-black/10 p-2 hover:bg-slate-100 transition">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <div class="relative hidden md:block">
                        <button type="button"
                            class="profile-toggle flex items-center gap-2 px-3 py-1 rounded-full bg-white hover:bg-slate-100 border border-black/10 transition">
                            <?php $foto_profil = $this->session->userdata('foto_profil'); ?>
                            <?php if (!empty($foto_profil)): ?>
                            <img src="<?= base_url($foto_profil); ?>" class="h-7 w-7 rounded-full object-cover"
                                alt="Foto Profil">
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

                            <div class="border-t border-black/5">
                                <button type="button" onclick="aktifkanNotif()"
                                    class="w-full text-left flex items-center gap-2 px-4 py-2 hover:bg-slate-100">
                                    <i class="bi bi-bell"></i> Aktifkan Notifikasi
                                </button>
                                <button id="testSound"
                                    class="ml-2 px-3 py-1 rounded-md text-xs border bg-slate-950 border-gray-300 hover:bg-gray-50">
                                    Test Sound
                                </button>
                                <button id="testDesktop"
                                    class="ml-2 px-3 py-1 rounded-md text-xs border bg-slate-950 border-gray-300 hover:bg-gray-50">
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
    /* =========================================================
   FULL SCRIPT NOTIF PEMESANAN + TRANSAKSI (POPUP TERPISAH)
   - Badge update realtime (polling)
   - Popup notif pemesanan + transaksi masing-masing sendiri
   - Isi popup: jumlah + list ID (dari backend notif_poll)
   - Suara notif (optional)
========================================================= */

    /* =========================
       1) Permission button
    ========================= */
    async function aktifkanNotif() {
        if (!("Notification" in window)) {
            alert("Browser kamu tidak mendukung notifikasi.");
            return;
        }

        if (Notification.permission === "denied") {
            alert(
                "Notifikasi diblokir.\n\n" +
                "Klik ikon (i) di sebelah URL → Site settings → Notifications → Allow,\n" +
                "lalu refresh halaman."
            );
            updateNotifUI();
            return;
        }

        try {
            const permission = await Notification.requestPermission();
            if (permission === "granted") {
                try {
                    new Notification("Notifikasi aktif ✅", {
                        body: "Sekarang kamu akan dapat pemberitahuan saat ada update."
                    });
                } catch (e) {}
                localStorage.setItem("notifJustEnabled", "1");
            } else {
                alert("Notifikasi belum diizinkan. Silakan pilih 'Allow'.");
            }
        } catch (e) {
            console.log("[notif] requestPermission error", e);
        }

        updateNotifUI();
    }

    /* =========================
       2) UI status (dot + text)
    ========================= */
    function updateNotifUI() {
        var dot = document.getElementById("notifDot");
        var txt = document.getElementById("notifStatusText");
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

    /* =========================
       3) Badge helper
    ========================= */
    function setBadge(el, count) {
        if (!el) return;
        count = parseInt(count || 0, 10);
        if (isNaN(count)) count = 0;

        el.dataset.count = String(count);

        if (count > 0) {
            el.classList.remove("hidden");
            el.textContent = String(count);
        } else {
            el.classList.add("hidden");
            el.textContent = "";
        }
    }

    /* =========================
       4) Format list ID untuk popup
       - pemesanan: PMSN000 + pad
       - transaksi: TRX- + id (atau ganti sesuai format kamu)
    ========================= */
    function formatIds(ids, prefix, padLen) {
        if (!ids || !Array.isArray(ids) || ids.length === 0) return "-";

        var shown = ids.slice(0, 3).map(function(x) {
            x = parseInt(x, 10);
            if (isNaN(x)) return prefix + String(x);
            if (padLen && padLen > 0) {
                return prefix + String(x).padStart(padLen, "0");
            }
            return prefix + String(x);
        });

        var more = (ids.length > 3) ? (" +" + (ids.length - 3) + " lainnya") : "";
        return shown.join(", ") + more;
    }

    /* =========================
       5) Desktop Notification helper (lebih aman)
       - minta izin jika default
       - tag beda: pemesanan / transaksi (biar gak saling timpa)
    ========================= */
    async function showDesktopNotif(title, body, tag) {
        if (!("Notification" in window)) return false;

        // Kalau permission default, wajib minta izin via user gesture.
        // Jadi kalau masih default, kita jangan spam: coba request,
        // tapi bisa gagal kalau bukan karena klik user.
        if (Notification.permission === "default") {
            try {
                await Notification.requestPermission();
            } catch (e) {}
        }

        if (Notification.permission !== "granted") {
            console.log("[notif] permission bukan granted:", Notification.permission);
            return false;
        }

        try {
            new Notification(title, {
                body: body,
                tag: tag || ("sireru-" + Date.now())
            });
            return true;
        } catch (e) {
            console.log("[notif] gagal create notification:", e);
            return false;
        }
    }

    /* =========================
       6) Cooldown popup (biar gak spam)
       - Simpan last popup time per jenis
    ========================= */
    function shouldPopup(key, cooldownMs) {
        cooldownMs = cooldownMs || 15000; // 15 detik
        var now = Date.now();
        var last = parseInt(localStorage.getItem(key) || "0", 10);
        if (isNaN(last)) last = 0;
        if (now - last < cooldownMs) return false;
        localStorage.setItem(key, String(now));
        return true;
    }

    /* =========================
       7) (Opsional) Mark transaksi dibaca
       - Kalau kamu punya route: home/trx_mark_all_read
       - Kalau belum ada, boleh hapus fungsi ini
    ========================= */
    function markAllTrxRead() {
        fetch("<?= site_url('home/trx_mark_all_read'); ?>", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                credentials: "same-origin"
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(j) {
                if (!j || !j.ok) return;
                setBadge(document.getElementById("trxBadge"), 0);
                setBadge(document.getElementById("trxBadgeMobile"), 0);
                localStorage.setItem("lastFlagTransaksi", "0");
            })
            .catch(function() {});
    }

    /* =========================
       8) Main
    ========================= */
    document.addEventListener("DOMContentLoaded", function() {

        /* ===== PROFILE DROPDOWN ===== */
        var profileToggle = document.querySelector(".profile-toggle");
        var profileMenu = document.querySelector(".profile-menu");
        if (profileToggle && profileMenu) {
            profileToggle.addEventListener("click", function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle("hidden");
                updateNotifUI();
            });
            document.addEventListener("click", function() {
                profileMenu.classList.add("hidden");
            });
        }

        /* ===== MOBILE MENU ===== */
        var mobileBtn = document.getElementById("mobileMenuBtn");
        var mobileMenu = document.getElementById("mobileMenu");
        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener("click", function(e) {
                e.stopPropagation();
                mobileMenu.classList.toggle("hidden");
            });
            document.addEventListener("click", function() {
                mobileMenu.classList.add("hidden");
            });
            mobileMenu.addEventListener("click", function(e) {
                e.stopPropagation();
            });
        }

        /* ===== ELEMENTS ===== */
        var soundEl = document.getElementById("notifSound");

        var badgePDesktop = document.getElementById("notifBadge");
        var badgePMobile = document.getElementById("notifBadgeMobile");

        var badgeTDesktop = document.getElementById("trxBadge");
        var badgeTMobile = document.getElementById("trxBadgeMobile");

        /* ===== UNLOCK AUDIO ===== */
        var audioUnlocked = false;

        function unlockAudioOnce() {
            if (!soundEl || audioUnlocked) return;
            audioUnlocked = true;
            soundEl.play().then(function() {
                soundEl.pause();
                soundEl.currentTime = 0;
            }).catch(function() {});
        }
        document.addEventListener("click", unlockAudioOnce, {
            once: true
        });
        document.addEventListener("keydown", unlockAudioOnce, {
            once: true
        });

        function playSound() {
            if (!soundEl) return;
            soundEl.currentTime = 0;
            soundEl.play().catch(function() {});
        }

        /* ===== INIT COUNTS FROM HTML ===== */
        var curP = parseInt((badgePDesktop && badgePDesktop.dataset.count) ? badgePDesktop.dataset.count : "0",
            10);
        if (isNaN(curP)) curP = 0;

        var curT = parseInt((badgeTDesktop && badgeTDesktop.dataset.count) ? badgeTDesktop.dataset.count : "0",
            10);
        if (isNaN(curT)) curT = 0;

        var lastP = parseInt(localStorage.getItem("lastFlagPemesanan") || "0", 10);
        if (isNaN(lastP)) lastP = 0;

        var lastT = parseInt(localStorage.getItem("lastFlagTransaksi") || "0", 10);
        if (isNaN(lastT)) lastT = 0;

        var justEnabled = localStorage.getItem("notifJustEnabled") === "1";

        /* ===== POPUP ON LOAD (jika count naik) ===== */
        // Note: kalau permission masih default, popup bisa tidak muncul sampai user klik allow.
        if (curP > lastP) {
            showDesktopNotif(
                "Notifikasi pemesanan",
                "Ada update pemesanan (" + curP + ").",
                "pemesanan"
            ).then(function(ok) {
                if (ok) playSound();
            });
        }

        if (curT > lastT) {
            showDesktopNotif(
                "Notifikasi transaksi",
                "Ada update transaksi (" + curT + ").",
                "transaksi"
            ).then(function(ok) {
                if (ok) playSound();
            });
        }

        if (justEnabled) {
            // setelah user allow, kasih popup jika ada notifikasi existing
            if (curP > 0) showDesktopNotif("Notifikasi pemesanan", "Kamu punya update pemesanan (" + curP +
                ").", "pemesanan");
            if (curT > 0) showDesktopNotif("Notifikasi transaksi", "Kamu punya update transaksi (" + curT +
                ").", "transaksi");
            localStorage.removeItem("notifJustEnabled");
        }

        localStorage.setItem("lastFlagPemesanan", String(curP));
        localStorage.setItem("lastFlagTransaksi", String(curT));

        /* ===== POLL CONFIG ===== */
        var pollIntervalMs = 5000;
        var cooldownMs = 15000; // supaya popup tidak spam

        /* ===== REALTIME POLL ===== */
        async function pollNotif() {
            try {
                const res = await fetch("<?= site_url('home/notif_poll') ?>", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    credentials: "same-origin"
                });
                if (!res.ok) return;

                const data = await res.json();
                if (!data || !data.ok) return;

                var newP = parseInt(data.flag || 0, 10);
                if (isNaN(newP)) newP = 0;

                var newT = parseInt(data.trx_flag || 0, 10);
                if (isNaN(newT)) newT = 0;

                // list id dari backend
                var pemIds = data.pemesanan_ids || [];
                var trxIds = data.trx_ids || [];

                // update badge
                setBadge(badgePDesktop, newP);
                setBadge(badgePMobile, newP);
                setBadge(badgeTDesktop, newT);
                setBadge(badgeTMobile, newT);

                // isi popup masing-masing (dengan ID)
                var pemText =
                    "Kamu punya update pemesanan (" + newP + ").\n" +
                    "ID: " + formatIds(pemIds, "PMSN000", 3); // padLen 3 -> 001/123 (ubah kalau perlu)

                var trxText =
                    "Kamu punya update transaksi (" + newT + ").\n" +
                    "ID: " + formatIds(trxIds, "TRX-", 0);

                // POPUP PEMESANAN (sendiri)
                // Trigger kalau naik ATAU kamu mau tiap polling selama masih >0 (pilih salah satu):
                // A) naik saja: if (newP > curP)
                // B) selama masih ada unread: if (newP > 0)
                // Aku set: naik saja (lebih aman tidak spam)
                if (newP > curP && shouldPopup("popup_pemesanan", cooldownMs)) {
                    var okP = await showDesktopNotif("Notifikasi pemesanan", pemText, "pemesanan");
                    if (okP) playSound();
                }

                // POPUP TRANSAKSI (sendiri)
                if (newT > curT && shouldPopup("popup_transaksi", cooldownMs)) {
                    var okT = await showDesktopNotif("Notifikasi transaksi", trxText, "transaksi");
                    if (okT) playSound();
                }

                // geser current
                curP = newP;
                curT = newT;

                localStorage.setItem("lastFlagPemesanan", String(curP));
                localStorage.setItem("lastFlagTransaksi", String(curT));

                // DEBUG
                // console.log("[poll]", { newP, newT, pemIds, trxIds, perm: Notification.permission });

            } catch (e) {
                // console.log(e);
            }
        }

        pollNotif();
        setInterval(pollNotif, pollIntervalMs);

        updateNotifUI();
    });
    </script>


</body>