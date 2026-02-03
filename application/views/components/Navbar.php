<?php
$username   = $this->session->userdata('username');
$session_id = $this->session->userdata('username');
$foto_profil = $this->session->userdata('foto_profil'); // ✅ dipakai desktop + mobile

$flag     = isset($flag) ? (int)$flag : 0;         // badge PEMESANAN
$trx_flag = isset($trx_flag) ? (int)$trx_flag : 0; // badge TRANSAKSI
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<header class="bg-white border-b border-black/5 sticky top-0 z-30">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-10">
        <div class="flex items-center justify-between h-16">

            <!-- BRAND -->
            <div class="flex items-center gap-4">
                <div class="h-9 w-9 rounded-lg bg-white border border-black/10 flex items-center justify-center">
                    <img src="<?= base_url('assets/login/LogoTSNew.png'); ?>" class="h-8 w-8 object-contain" alt="Logo">
                </div>
                <div class="leading-tight">
                    <div class="text-[10px] font-semibold tracking-[0.25em] uppercase text-slate-500">Smart Office</div>
                    <div class="text-sm font-semibold text-slate-800">SIRERU</div>
                </div>
            </div>

            <!-- DESKTOP MENU -->
            <nav class="hidden md:flex items-center gap-10 text-[11px] font-semibold tracking-widest text-slate-700">
                <a href="<?= site_url('home/' . $session_id . '/'); ?>" class="flex items-center gap-2 hover:text-slate-900">
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

                <a href="<?= site_url('home/view-catering'); ?>" class="flex items-center gap-2 hover:text-slate-900">
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

                    <!-- PROFILE (DESKTOP) -->
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
            <a href="<?= site_url('home/' . $session_id . '/'); ?>" class="flex items-center gap-2">
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
    (function() {
        // ===== MENU TOGGLES =====
        var mobileBtn = document.getElementById('mobileMenuBtn');
        var mobileMenu = document.getElementById('mobileMenu');

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', function() {
                if (mobileMenu.classList.contains('hidden')) mobileMenu.classList.remove('hidden');
                else mobileMenu.classList.add('hidden');
            });
        }

        var profileToggle = document.getElementById('profileToggle');
        var profileMenu = document.getElementById('profileMenu');

        function closeProfile() {
            if (profileMenu && !profileMenu.classList.contains('hidden')) {
                profileMenu.classList.add('hidden');
            }
        }

        if (profileToggle && profileMenu) {
            profileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (profileMenu.classList.contains('hidden')) profileMenu.classList.remove('hidden');
                else profileMenu.classList.add('hidden');
            });

            document.addEventListener('click', function() {
                closeProfile();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeProfile();
            });

            profileMenu.addEventListener('click', function(e) {
                // biar klik dalam menu tidak menutup sebelum link bekerja
                e.stopPropagation();
            });
        }

        // ===== NOTIF POLLING =====
        var USERNAME = "<?= addslashes(strtolower((string)$username)) ?>";
        var POLL_URL_BASE = "<?= site_url('home/home/notif_poll_v2') ?>"; // ✅ penting (folder home/home)
        var SITE_URL = "<?= rtrim(site_url(), '/') ?>";

        var KEY_LAST_P = "bm_last_user_p_id_" + USERNAME;
        var KEY_LAST_T = "bm_last_user_t_id_" + USERNAME;

            // lock untuk cegah dobel notif kalau buka banyak tab
            const LOCK_KEY = "bm_notif_lock_user_" + USERNAME;
            const TAB_ID = Date.now() + "_" + Math.random().toString(16).slice(2);

            const getNum = k => parseInt(localStorage.getItem(k) || "0", 10) || 0;
            const setNum = (k, v) => localStorage.setItem(k, String(v || 0));

            function isLeaderTab() {
                const now = Date.now();
                let lock = null;
                try {
                    lock = JSON.parse(localStorage.getItem(LOCK_KEY) || "null");
                } catch (e) {
                    lock = null;
                }

                // kalau kosong / expired 15 detik -> ambil lock
                if (!lock || (now - lock.ts) > 15000) {
                    localStorage.setItem(LOCK_KEY, JSON.stringify({
                        id: TAB_ID,
                        ts: now
                    }));
                    return true;
                }
                // kalau lock milik tab ini -> refresh ts
                if (lock.id === TAB_ID) {
                    localStorage.setItem(LOCK_KEY, JSON.stringify({
                        id: TAB_ID,
                        ts: now
                    }));
                    return true;
                }
                return false;
            }

            // ====== Badge updater (desktop + mobile) ======
            function setBadge(el, count) {
                if (!el) return;
                el.dataset.count = count;
                if (count > 0) {
                    el.classList.remove('hidden');
                    el.textContent = count;
                } else {
                    el.classList.add('hidden');
                    el.textContent = '';
                }
            }

            function updateBadges(counts) {
                const p = counts && counts.pemesanan ? parseInt(counts.pemesanan, 10) : 0;
                const t = counts && counts.transaksi ? parseInt(counts.transaksi, 10) : 0;

                setBadge(document.getElementById('notifBadge'), p);
                setBadge(document.getElementById('notifBadgeMobile'), p);

                setBadge(document.getElementById('trxBadge'), t);
                setBadge(document.getElementById('trxBadgeMobile'), t);
            }

            // ====== Notification permission via tombol ======
            window.aktifkanNotif = async function() {
                if (!("Notification" in window)) {
                    alert("Browser tidak mendukung notifikasi.");
                    return;
                }
                const perm = await Notification.requestPermission();
                const dot = document.getElementById('notifDot');
                const txt = document.getElementById('notifStatusText');

            if (perm === "granted") {
                if (dot) dot.className = "inline-block w-2 h-2 rounded-full bg-green-500";
                if (txt) txt.textContent = "Notifikasi: aktif";
                localStorage.setItem("bm_notif_enabled_" + USERNAME, "1");
            } else {
                if (dot) dot.className = "inline-block w-2 h-2 rounded-full bg-red-500";
                if (txt) txt.textContent = "Notifikasi: diblokir";
                localStorage.setItem("bm_notif_enabled_" + USERNAME, "0");
            }
        };

        function notifEnabled() {
            return localStorage.getItem("bm_notif_enabled_" + USERNAME) === "1";
        }

            // ====== Device notification ======
            function showNotif(n) {
                if (!("Notification" in window)) return;
                if (Notification.permission !== "granted") return;
                if (!notifEnabled()) return;

                const tag = "bm_" + (n.type || "x") + "_" + (n.id || "0");

                try {
                    const notif = new Notification(n.title || "Notifikasi", {
                        body: n.message || "",
                        tag: tag,
                        renotify: false,
                        silent: false
                    });

                    // optional: bunyi
                    const audio = document.getElementById('notifSound');
                    if (audio) {
                        try {
                            audio.currentTime = 0;
                            audio.play().catch(() => {});
                        } catch (e) {}
                    }

                    notif.onclick = () => {
                        window.focus();
                        if (n.url) window.location.href = SITE_URL + "/" + String(n.url).replace(/^\/+/, '');
                        notif.close();
                    };
                } catch (e) {}
            }

            function handle(list, key) {
                const last = getNum(key);
                let max = last;

                // list dari backend sudah ASC, tapi tetap aman
                (list || []).forEach(n => {
                    const id = parseInt(n.id, 10) || 0;
                    if (id > last) {
                        showNotif(n);
                        if (id > max) max = id;
                    }
                });

                if (max > last) setNum(key, max);
                return max;
            }

            async function poll() {
                if (!isLeaderTab()) return; // cegah dobel multi-tab

                const lastP = getNum(KEY_LAST_P);
                const lastT = getNum(KEY_LAST_T);

                const url = POLL_URL_BASE + "?since_p=" + encodeURIComponent(lastP) + "&since_t=" + encodeURIComponent(lastT);

                try {
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (!data || !data.ok) return;

                    // update badge dari counts
                    updateBadges(data.counts);

                    // tampilkan notif device hanya yang baru
                    const newMaxP = handle(data.items && data.items.pemesanan ? data.items.pemesanan : [], KEY_LAST_P);
                    const newMaxT = handle(data.items && data.items.transaksi ? data.items.transaksi : [], KEY_LAST_T);

                    // (opsional) kalau notif belum diaktifkan, kamu masih tetap dapat badge saja.
                } catch (e) {}
            }
            const MARK_READ_URL = "<?= site_url('api/notif/mark-read') ?>";

            async function markRead(type) {
                try {
                    const body = "type=" + encodeURIComponent(type);
                    const res = await fetch(MARK_READ_URL, {
                        method: "POST",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body
                    });
                    await res.json().catch(() => null);
                } catch (e) {}
            }

            // run
            poll();
            setInterval(poll, 8000); // 8 detik lebih stabil (nggak gampang di-throttle)

            // ===== additional event listeners =====
            document.getElementById('pemesananLinkDesktop')?.addEventListener('click', () => {
                markRead('pemesanan');
                setBadge(document.getElementById('notifBadge'), 0);
                setBadge(document.getElementById('notifBadgeMobile'), 0);
            });

            document.getElementById('pemesananLinkMobile')?.addEventListener('click', () => {
                markRead('pemesanan');
                setBadge(document.getElementById('notifBadge'), 0);
                setBadge(document.getElementById('notifBadgeMobile'), 0);
            });

            document.getElementById('transaksiLinkDesktop')?.addEventListener('click', () => {
                markRead('transaksi');
                setBadge(document.getElementById('trxBadge'), 0);
                setBadge(document.getElementById('trxBadgeMobile'), 0);
            });

            document.getElementById('transaksiLinkMobile')?.addEventListener('click', () => {
                markRead('transaksi');
                setBadge(document.getElementById('trxBadge'), 0);
                setBadge(document.getElementById('trxBadgeMobile'), 0);
            });

        })();
</script>