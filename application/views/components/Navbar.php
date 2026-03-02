<?php
$username   = (string)($this->session->userdata('username') ?? '');
$session_id = (string)($this->session->userdata('username') ?? '');
$foto_profil = (string)($this->session->userdata('foto_profil') ?? '');

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
            <div class="flex items-center gap-2">

                <!-- MOBILE: PROFILE AVATAR BUTTON (terpisah dari hamburger) -->
                <?php if ($username !== ''): ?>
                <div class="relative md:hidden">
                    <button id="mobileProfileBtn" type="button"
                        class="inline-flex items-center justify-center rounded-full border border-black/10 h-9 w-9 hover:bg-slate-100 transition overflow-hidden"
                        aria-expanded="false">
                        <?php if (!empty($foto_profil)): ?>
                            <img src="<?= base_url($foto_profil); ?>" class="h-full w-full object-cover rounded-full"
                                alt="Foto Profil">
                        <?php else: ?>
                            <i class="bi bi-person-circle text-lg text-slate-600"></i>
                        <?php endif; ?>
                    </button>

                    <!-- MOBILE PROFILE DROPDOWN -->
                    <div id="mobileProfileMenu"
                        class="hidden absolute right-0 mt-2 w-64 rounded-2xl border border-black/10 bg-white/95 backdrop-blur-xl shadow-2xl text-sm overflow-hidden z-50
                               transition-all duration-200 origin-top-right">

                        <!-- Header profil -->
                        <div class="px-4 py-3 bg-gradient-to-r from-slate-900 to-slate-800 text-white">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full border-2 border-white/30 overflow-hidden flex-shrink-0">
                                    <?php if (!empty($foto_profil)): ?>
                                        <img src="<?= base_url($foto_profil); ?>" class="h-full w-full object-cover"
                                            alt="Foto Profil">
                                    <?php else: ?>
                                        <div class="h-full w-full bg-slate-600 flex items-center justify-center">
                                            <i class="bi bi-person-fill text-white/80"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold truncate"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="text-[11px] text-white/60">Akun Saya</div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu akun -->
                        <div class="py-1">
                            <a href="<?= site_url('edit_data/' . $username); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <i class="bi bi-pencil-square text-slate-500"></i>
                                <span>Edit Data Diri</span>
                            </a>

                            <a href="<?= site_url('edit_foto/' . $username); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <i class="bi bi-camera text-slate-500"></i>
                                <span>Edit Foto Profil</span>
                            </a>

                            <button type="button" onclick="aktifkanNotif()"
                                class="w-full text-left flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <i class="bi bi-bell text-slate-500"></i>
                                <span>Aktifkan Notifikasi</span>
                            </button>
                        </div>

                        <div class="border-t border-black/5">
                            <a href="<?= site_url('home/home/logout'); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- MOBILE: LOGIN BUTTON (guest) -->
                <a href="<?= site_url('login'); ?>" class="md:hidden inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <?php endif; ?>

                <!-- MOBILE: HAMBURGER (hanya navigasi) -->
                <div class="relative md:hidden">
                    <button id="mobileMenuBtn"
                        class="inline-flex items-center justify-center rounded-lg border border-black/10 p-2 hover:bg-slate-100 transition">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <!-- MOBILE NAV DROPDOWN (overlay di atas konten) -->
                    <div id="mobileMenu"
                        class="hidden absolute right-0 mt-2 w-56 rounded-2xl border border-black/10 bg-white/95 backdrop-blur-xl shadow-2xl text-sm overflow-hidden z-50">

                        <nav class="flex flex-col py-2 font-semibold text-slate-700">
                            <a href="<?= site_url('home/' . $session_id . '/'); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <i class="bi bi-house-door text-base text-slate-500"></i> HOME
                            </a>

                            <a href="<?= site_url('home/jadwal'); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <i class="bi bi-calendar-week text-base text-slate-500"></i> JADWAL
                            </a>

                            <a id="pemesananLinkMobile" href="<?= site_url('home/pemesanan'); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <i class="bi bi-journal-text text-base text-slate-500"></i> PEMESANAN
                                <span id="notifBadgeMobile" data-count="<?= $flag; ?>"
                                    class="<?= ($flag > 0) ? '' : 'hidden'; ?> ml-auto rounded-full bg-red-500 text-[10px] text-white px-1.5 py-0.5">
                                    <?= ($flag > 0) ? $flag : ''; ?>
                                </span>
                            </a>

                            <a href="<?= site_url('home/view-catering'); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <i class="bi bi-cup-hot text-base text-slate-500"></i> CATERING
                            </a>

                            <a id="transaksiLinkMobile" href="<?= site_url('home/pembayaran'); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition">
                                <i class="bi bi-credit-card text-base text-slate-500"></i> TRANSAKSI
                                <span id="trxBadgeMobile" data-count="<?= $trx_flag; ?>"
                                    class="<?= ($trx_flag > 0) ? '' : 'hidden'; ?> ml-auto rounded-full bg-red-500 text-[10px] text-white px-1.5 py-0.5">
                                    <?= ($trx_flag > 0) ? $trx_flag : ''; ?>
                                </span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- PROFILE (DESKTOP) -->
                <?php if ($username !== ''): ?>
                <div class="relative hidden md:block">
                    <button id="profileToggle" type="button"
                        class="profile-toggle flex items-center gap-2 px-3 py-1 rounded-full bg-white hover:bg-slate-100 border border-black/10 transition"
                        aria-expanded="false">

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

                            <div class="px-4 py-0.5 flex items-center gap-2">
                                <button id="testSound"
                                    class="flex-1 flex items-center justify-center gap-1 h-5 rounded-md text-[9px] font-bold border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors shadow-sm">
                                    <i class="bi bi-volume-up-fill"></i> Test Sound
                                </button>
                                <button id="testDesktop"
                                    class="flex-1 flex items-center justify-center gap-1 h-5 rounded-md text-[9px] font-bold border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors shadow-sm">
                                    <i class="bi bi-display"></i> Test Desktop
                                </button>
                            </div>

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
                <?php else: ?>
                <!-- DESKTOP: LOGIN BUTTON (guest) -->
                <a href="<?= site_url('login'); ?>" class="hidden md:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <?php endif; ?>
            </div>

        </div>
    </div>

</header>

<!-- AUDIO -->
<audio id="notifSound" preload="auto">
    <source src="<?= base_url('assets/nada_notifikasi1.mp3'); ?>" type="audio/mpeg">
</audio>
<script>
    window.BM_USERNAME = "<?= addslashes(strtolower((string)$username)) ?>";
</script>
<script>
    // ... (script kamu yang sudah ada)

    // === TEST SOUND & TEST DESKTOP (USER) ===
    const btnTestSound = document.getElementById('testSound');
    const btnTestDesktop = document.getElementById('testDesktop');

    function setNotifUI(on) {
        const dot = document.getElementById('notifDot');
        const txt = document.getElementById('notifStatusText');
        if (dot) dot.className = "inline-block w-2 h-2 rounded-full " + (on ? "bg-green-500" : "bg-red-500");
        if (txt) txt.textContent = on ? "Notifikasi: aktif" : "Notifikasi: diblokir";
    }

    if (btnTestSound) {
        btnTestSound.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            // anggap kalau test = user ingin mengaktifkan
            localStorage.setItem("bm_notif_enabled_" + window.BM_USERNAME, "1");
            setNotifUI(true);

            const audio = document.getElementById('notifSound');
            if (!audio) {
                alert("Audio notifikasi tidak ditemukan.");
                return;
            }

            try {
                audio.currentTime = 0;
                await audio.play();
            } catch (err) {
                console.log("AUDIO BLOCKED:", err);
                alert("Sound diblokir browser. Izinkan suara di site settings lalu coba lagi.");
            }
        });
    }

    if (btnTestDesktop) {
        btnTestDesktop.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (!("Notification" in window)) {
                alert("Browser tidak mendukung notifikasi desktop.");
                return;
            }

            // wajib HTTPS atau localhost
            if (!window.isSecureContext) {
                alert("Notifikasi desktop butuh HTTPS atau http://localhost.\nSekarang: " + location.origin);
                return;
            }

            let perm = Notification.permission;
            if (perm !== "granted") {
                try {
                    perm = await Notification.requestPermission();
                } catch (e) {}
            }

            if (perm !== "granted") {
                localStorage.setItem("bm_notif_enabled_" + window.BM_USERNAME, "0");
                setNotifUI(false);
                alert("Notifikasi masih diblokir. Aktifkan di setting browser.");
                return;
            }

            localStorage.setItem("bm_notif_enabled_" + window.BM_USERNAME, "1");

            setNotifUI(true);

            try {
                const n = new Notification("Booking Smarts", {
                    body: "✅ Test desktop notification berhasil",
                    silent: false
                });
                n.onclick = () => {
                    window.focus();
                    n.close();
                };

                // optional: ikut bunyi saat test desktop
                const audio = document.getElementById('notifSound');
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(() => {});
                }
            } catch (err) {
                console.log("NOTIF ERROR:", err);
                alert("Gagal menampilkan notif. Cek setting OS/Browser (DND/Focus Assist).");
            }
        });
    }
</script>

<script>
    (async function() {
        if (!("Notification" in window)) return;

        // permission - idealnya via tombol, tapi ini versi cepat
        if (Notification.permission === "default") {
            try {
                await Notification.requestPermission();
            } catch (e) {}
        }

        let last = 0;

        async function poll() {
            try {
                const res = await fetch("<?= site_url('api/notif/unread-count') ?>", {
                    credentials: "same-origin"
                });
                const data = await res.json();

                const count = Number(data.count || 0);
                if (Notification.permission === "granted" && count > last) {
                    new Notification("Booking Smarts", {
                        body: "Ada notifikasi baru."
                    });
                }
                last = count;
            } catch (e) {
                console.log("notif error", e);
            }
        }

        poll();
        setInterval(poll, 15000);
    })();
</script>

<script>
    (function() {
        // ========= MOBILE MENU TOGGLE =========
        const mobileBtn = document.getElementById("mobileMenuBtn");
        const mobileMenu = document.getElementById("mobileMenu");

        // ========= MOBILE PROFILE (deklarasi dulu sebelum dipakai) =========
        const mobileProfileBtn = document.getElementById("mobileProfileBtn");
        const mobileProfileMenu = document.getElementById("mobileProfileMenu");

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                const isOpen = !mobileMenu.classList.contains("hidden");
                mobileMenu.classList.toggle("hidden");
                mobileBtn.setAttribute("aria-expanded", String(!isOpen));
                // Tutup profile dropdown saat buka hamburger
                if (mobileProfileMenu && !mobileProfileMenu.classList.contains("hidden")) {
                    mobileProfileMenu.classList.add("hidden");
                }
            });

            // Klik di luar → tutup hamburger dropdown
            document.addEventListener("click", (e) => {
                if (!mobileMenu.contains(e.target) && !mobileBtn.contains(e.target)) {
                    mobileMenu.classList.add("hidden");
                    mobileBtn.setAttribute("aria-expanded", "false");
                }
            });

            // ESC → tutup hamburger dropdown
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    mobileMenu.classList.add("hidden");
                    mobileBtn.setAttribute("aria-expanded", "false");
                }
            });
        }

        // ========= MOBILE PROFILE DROPDOWN TOGGLE =========

        if (mobileProfileBtn && mobileProfileMenu) {
            mobileProfileBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                const isHidden = mobileProfileMenu.classList.contains("hidden");
                mobileProfileMenu.classList.toggle("hidden");
                mobileProfileBtn.setAttribute("aria-expanded", String(isHidden));
                // Tutup hamburger menu saat buka profile
                if (mobileMenu && !mobileMenu.classList.contains("hidden")) {
                    mobileMenu.classList.add("hidden");
                }
            });

            // Klik di luar → tutup
            document.addEventListener("click", (e) => {
                if (!mobileProfileMenu.contains(e.target) && !mobileProfileBtn.contains(e.target)) {
                    mobileProfileMenu.classList.add("hidden");
                    mobileProfileBtn.setAttribute("aria-expanded", "false");
                }
            });

            // ESC → tutup
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    mobileProfileMenu.classList.add("hidden");
                    mobileProfileBtn.setAttribute("aria-expanded", "false");
                }
            });
        }

        // ========= PROFILE DROPDOWN TOGGLE (DESKTOP) =========
        const profileToggle = document.getElementById("profileToggle");
        const profileMenu = document.getElementById("profileMenu");

        function closeProfileMenu() {
            if (!profileMenu) return;
            profileMenu.classList.add("hidden");
            if (profileToggle) profileToggle.setAttribute("aria-expanded", "false");
        }

        function toggleProfileMenu() {
            if (!profileMenu) return;
            const isHidden = profileMenu.classList.contains("hidden");
            profileMenu.classList.toggle("hidden");
            if (profileToggle) profileToggle.setAttribute("aria-expanded", String(isHidden));
        }

        if (profileToggle && profileMenu) {
            profileToggle.addEventListener("click", (e) => {
                e.stopPropagation();
                toggleProfileMenu();
            });

            // klik di luar => nutup
            document.addEventListener("click", (e) => {
                if (!profileMenu.contains(e.target) && !profileToggle.contains(e.target)) {
                    closeProfileMenu();
                }
            });

            // tombol ESC => nutup
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") closeProfileMenu();
            });

            profileMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // ====== NOTIF POLLING & BADGES ======
        var USERNAME = "<?= addslashes(strtolower((string)$username)) ?>";
        var POLL_URL_BASE = "<?= site_url('home/home/notif_poll_v2') ?>";
        var SITE_URL = "<?= rtrim(site_url(), '/') ?>";

        var KEY_LAST_P = "bm_last_user_p_id_" + USERNAME;
        var KEY_LAST_T = "bm_last_user_t_id_" + USERNAME;

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
            if (!lock || (now - lock.ts) > 15000) {
                localStorage.setItem(LOCK_KEY, JSON.stringify({
                    id: TAB_ID,
                    ts: now
                }));
                return true;
            }
            if (lock.id === TAB_ID) {
                localStorage.setItem(LOCK_KEY, JSON.stringify({
                    id: TAB_ID,
                    ts: now
                }));
                return true;
            }
            return false;
        }

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
                const audio = document.getElementById('notifSound');
                if (audio) {
                    try {
                        audio.currentTime = 0;
                        audio.play().catch(() => {});
                    } catch (e) {}
                }
                notif.onclick = () => {
                    if (n.url) window.location.href = SITE_URL + "/" + String(n.url).replace(/^\/+/, '');
                    notif.close();
                };
            } catch (e) {}
        }

        function handle(list, key) {
            const last = getNum(key);
            let max = last;
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
            if (!isLeaderTab()) return;
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
                updateBadges(data.counts);
                handle(data.items && data.items.pemesanan ? data.items.pemesanan : [], KEY_LAST_P);
                handle(data.items && data.items.transaksi ? data.items.transaksi : [], KEY_LAST_T);
            } catch (e) {}
        }

        const MARK_READ_URL = "<?= site_url('api/notif/mark-read') ?>";
        async function markRead(type) {
            try {
                const body = "type=" + encodeURIComponent(type);
                await fetch(MARK_READ_URL, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body
                }).then(r => r.json()).catch(() => null);
            } catch (e) {}
        }

        // run polling
        poll();
        setInterval(poll, 8000);

        // wire click handlers
        // Keep pemesanan badge persistent (based on STATUS='PROCESS').
        // Do not clear the badge on click so users still see pending PROCESS items.
        document.getElementById('pemesananLinkDesktop')?.addEventListener('click', () => {
            // Intentionally no markRead() nor badge clearing here.
        });
        document.getElementById('pemesananLinkMobile')?.addEventListener('click', () => {
            // Intentionally no markRead() nor badge clearing here.
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