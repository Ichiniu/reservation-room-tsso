<?php
$username = $this->session->userdata('admin_username');
if (empty($username)) {
    $username = $this->session->userdata('username');
}

// Compute inbox & transaksi counts directly from model to avoid collision
// with controller-passed variables (some controllers use $result / $res
// for other data which caused incorrect badge counts).
$this->load->model('gedung/gedung_model');
$pending = $this->gedung_model->get_pending_transaction();
if (is_array($pending)) {
    $jumlah_inbox = count($pending);
} elseif (is_numeric($pending)) {
    $jumlah_inbox = (int)$pending;
} else {
    $jumlah_inbox = 0;
}

$gt = $this->gedung_model->get_unread_transaction();
$jumlah_trx = is_numeric($gt) ? (int)$gt : 0;

$current_uri = uri_string();
function is_active($uri, $current_uri)
{
    if (strpos($current_uri, $uri) !== false) return true;
    return false;
}

// ensure integers
$jumlah_inbox = (int)$jumlah_inbox;
$jumlah_trx = (int)$jumlah_trx;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Smart Office</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        #sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-closed #sidebar {
            transform: translateX(-100%);
            width: 0 !important;
            opacity: 0;
            pointer-events: none;
        }

        /* Override padding/margin on main content when sidebar is closed */
        .sidebar-closed main,
        .sidebar-closed #content,
        .sidebar-closed .md\:pl-64 {
            margin-left: 0 !important;
            padding-left: 1.5rem !important;
            /* default px-6 */
        }

        .menu-active {
            background-color: #fff;
            font-weight: 600;
            color: #2563eb !important;
        }
    </style>
</head>

<body class="bg-white text-gray-800">

    <header class="fixed top-0 left-0 right-0 z-40 bg-white border-b h-16" x-data>
        <div class="flex items-center px-4 sm:px-6 h-full gap-2 sm:gap-4">
            <button @click="$store.sidebar.toggle()" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                <span class="material-icons text-gray-600 block">menu</span>
            </button>
            <span class="font-bold text-slate-800 text-base sm:text-lg tracking-tight">Administrator</span>

            <!-- tombol aktifkan notif -->
            <button id="enableNotifBtn"
                class="hidden sm:inline-flex ml-3 px-3 py-1 rounded-md text-xs border border-gray-300 hover:bg-gray-50">
                Aktifkan Notifikasi
            </button>

            <!-- tombol tes -->
            <button id="testSoundBtn" class="hidden md:inline-flex px-3 py-1 rounded-md text-xs border border-gray-300 hover:bg-gray-50">
                Test Sound
            </button>
            <button id="testDesktopBtn" class="hidden md:inline-flex px-3 py-1 rounded-md text-xs border border-gray-300 hover:bg-gray-50">
                Test Desktop
            </button>

            <span class="ml-auto text-xs sm:text-sm text-gray-500 truncate max-w-[120px] sm:max-w-none">
                <?php echo htmlspecialchars((string)$username, ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </div>
    </header>

    <!-- Mobile backdrop overlay -->
    <div x-data
        x-show="$store.sidebar.open"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$store.sidebar.toggle()"
        class="fixed inset-0 bg-black/40 z-20 md:hidden"
        style="top:64px;"></div>

    <aside id="sidebar"
        :class="$store.sidebar.open ? 'w-64' : 'w-0 -translate-x-full opacity-0'"
        class="fixed top-16 left-0 z-30 h-full bg-[#f9f7f2] border-r transition-all duration-300 overflow-y-auto">
        <nav class="px-3 py-6 space-y-1 text-sm">

            <a href="<?php echo site_url('admin/dashboard'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/dashboard', $current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">dashboard</span>
                <span class="menu-text">Home</span>
            </a>

            <a href="<?php echo site_url('admin/list'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/list', $current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">people</span>
                <span class="menu-text">List User</span>
            </a>

            <a href="<?php echo site_url('admin/gedung'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/gedung', $current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">business</span>
                <span class="menu-text">List Ruangan</span>
            </a>

            <a href="<?php echo site_url('admin/catering'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/catering', $current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">restaurant</span>
                <span class="menu-text">Catering</span>
            </a>

            <a href="<?php echo site_url('admin/pemesanan2'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/pemesanan', $current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">assignment</span>
                <span class="menu-text">List Pemesanan</span>
            </a>

            <!-- INBOX -->
            <a href="<?php echo site_url('admin/transaksi'); ?>"
                class="relative flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if ($current_uri == 'admin/transaksi') echo 'menu-active'; ?>">
                <span class="material-icons-outlined">inbox</span>
                <span class="menu-text">Inbox</span>
                <span id="badge-inbox"
                    class="badge ml-auto bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                    <?php if ($jumlah_inbox <= 0) echo 'style="display:none"'; ?>>
                    <?php echo $jumlah_inbox; ?>
                </span>
            </a>

            <!-- TRANSAKSI -->
            <a href="<?php echo site_url('admin/pembayaran'); ?>"
                class="relative flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/pembayaran', $current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">payments</span>
                <span class="menu-text">Transaksi</span>
                <span id="badge-trx"
                    class="badge ml-auto bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                    <?php if ($jumlah_trx <= 0) echo 'style="display:none"'; ?>>
                    <?php echo $jumlah_trx; ?>
                </span>
            </a>

            <a href="<?php echo site_url('admin/rekap_aktivitas'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/rekap_aktivitas', $current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">history</span>
                <span class="menu-text">Rekap Aktivitas</span>
            </a>

            <a href="<?php echo site_url('admin/rekap_transaksi'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/rekap_transaksi', $current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">summarize</span>
                <span class="menu-text">Rekap Transaksi</span>
            </a>

            <hr class="my-4">

            <a href="<?php echo site_url('admin/login/log_out'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded text-red-600 hover:bg-red-50">
                <span class="material-icons">logout</span>
                <span class="menu-text">Sign Out</span>
            </a>

        </nav>
    </aside>

    <main id="content" class="ml-64 transition-all duration-300 px-6"></main>

    <div id="toast" class="fixed bottom-5 right-5 px-4 py-3 rounded-lg shadow-lg text-white text-sm z-50"
        style="display:none;"></div>

    <!-- AUDIO -->
    <audio id="notifSound" preload="auto">
        <source src="<?php echo base_url('assets/nada_notifikasi1.mp3'); ?>" type="audio/mpeg">
    </audio>

    <script>
        document.addEventListener('alpine:init', () => {
            const isMobile = () => window.innerWidth < 768;
            Alpine.store('sidebar', {
                open: isMobile() ? false : localStorage.getItem('sidebar-open') !== 'false',
                toggle() {
                    this.open = !this.open;
                    if (!isMobile()) localStorage.setItem('sidebar-open', this.open);
                    this.updateBodyClass();
                },
                updateBodyClass() {
                    if (this.open) {
                        document.body.classList.remove('sidebar-closed');
                        if (isMobile()) document.body.style.overflow = 'hidden';
                    } else {
                        document.body.classList.add('sidebar-closed');
                        document.body.style.overflow = '';
                    }
                }
            });
            // Initial call
            Alpine.store('sidebar').updateBodyClass();
            // Close sidebar on resize to desktop if it was mobile-opened
            window.addEventListener('resize', () => {
                if (!isMobile() && !Alpine.store('sidebar').open) {
                    // Restore desktop default from localStorage
                    if (localStorage.getItem('sidebar-open') !== 'false') {
                        Alpine.store('sidebar').open = true;
                        Alpine.store('sidebar').updateBodyClass();
                    }
                }
                if (!isMobile()) document.body.style.overflow = '';
            });
        });
    </script>

    <script>
        /* =============== UI basic =============== */
        const badgeInbox = document.getElementById('badge-inbox');
        const badgeTrx = document.getElementById('badge-trx');

        function showToast(message, bgClass) {
            toast.className = "fixed bottom-5 right-5 px-4 py-3 rounded-lg shadow-lg text-white text-sm z-50 " + bgClass;
            toast.innerText = message;
            toast.style.display = "block";
            setTimeout(function() {
                toast.style.display = "none";
            }, 3500);
        }

        function setBadge(el, count) {
            if (!el) return;
            if (count > 0) {
                el.innerText = count;
                el.style.display = "flex";
            } else {
                el.style.display = "none";
            }
        }

        /* =============== IMPORTANT: HTTPS check (desktop notif usually requires secure context) =============== */
        /*
          Browser modern biasanya butuh secure context untuk Notification:
          - OK: https://... atau http://localhost
          - NOT OK: http://192.168.x.x atau http://nama-host (dianggap tidak secure)
        */
        function isSecureForNotif() {
            if (window.isSecureContext) return true;
            // fallback check
            const host = location.hostname;
            if (host === "localhost" || host === "127.0.0.1") return true;
            return false;
        }

        /* =============== AUDIO =============== */
        const notifSound = document.getElementById('notifSound');

        function playNotifSound() {
            if (!notifEnabled) return;

            try {
                notifSound.currentTime = 0;
                const p = notifSound.play();
                if (p && p.catch) {
                    p.catch(function(err) {
                        console.log("AUDIO BLOCKED:", err);
                        showToast("🔇 Sound diblokir browser (cek Console F12)", "bg-yellow-600");
                    });
                }
            } catch (e) {
                console.log("AUDIO ERROR:", e);
            }
        }

        /* =============== DESKTOP NOTIF =============== */
        function showDesktopNotif(title, body, tag) {
            if (!notifEnabled) return;

            if (!("Notification" in window)) {
                console.log("Notification API not supported");
                return;
            }
            if (!isSecureForNotif()) {
                console.log("Not secure context. Need https or localhost. Current:", location.origin);
                showToast("⚠️ Desktop notif butuh HTTPS / localhost", "bg-yellow-600");
                return;
            }
            if (Notification.permission !== "granted") {
                console.log("Notification permission:", Notification.permission);
                return;
            }

            try {
                const n = new Notification(title, {
                    body: body,
                    tag: tag,
                    renotify: true
                });
                n.onclick = function() {
                    try {
                        window.focus();
                    } catch (e) {}
                    n.close();
                };
                setTimeout(function() {
                    n.close();
                }, 6000);
            } catch (e) {
                console.log("NOTIF ERROR:", e);
            }
        }

        /* =============== ENABLE + TEST BUTTONS =============== */
        const enableBtn = document.getElementById('enableNotifBtn');
        const testSoundBtn = document.getElementById('testSoundBtn');
        const testDesktopBtn = document.getElementById('testDesktopBtn');

        let notifEnabled = false;

        enableBtn.addEventListener('click', function() {
            // 1) unlock audio
            try {
                const p = notifSound.play();
                if (p && p.then) {
                    p.then(function() {
                        notifSound.pause();
                        notifSound.currentTime = 0;
                        console.log("Audio unlocked");
                    }).catch(function(err) {
                        console.log("Audio unlock failed:", err);
                    });
                }
            } catch (e) {}

            // 2) request notif permission
            if (!("Notification" in window)) {
                notifEnabled = true; // sound only
                showToast("⚠️ Browser tidak dukung desktop notif. Sound aktif.", "bg-yellow-600");
                return;
            }

            if (!isSecureForNotif()) {
                notifEnabled = true; // sound only
                showToast("⚠️ Desktop notif butuh HTTPS/localhost. Sound aktif.", "bg-yellow-600");
                return;
            }

            Notification.requestPermission().then(function(permission) {
                console.log("Notification permission result:", permission);
                notifEnabled = true; // minimal sound aktif
                if (permission === "granted") {
                    showToast("✅ Notifikasi aktif (sound + desktop)", "bg-green-600");
                    showDesktopNotif("Notifikasi aktif", "Desktop notification sudah aktif.", "enabled");
                    playNotifSound();
                } else {
                    showToast("⚠️ Desktop notif ditolak. Sound saja.", "bg-yellow-600");
                }
            });
        });

        testSoundBtn.addEventListener('click', function() {
            notifEnabled = true;
            playNotifSound();
            showToast("🔊 Test sound diputar (cek volume PC)", "bg-blue-600");
        });

        testDesktopBtn.addEventListener('click', function() {
            notifEnabled = true;

            if (!("Notification" in window)) {
                alert("Browser tidak support Notification API");
                return;
            }
            if (!isSecureForNotif()) {
                alert("Desktop notif butuh HTTPS atau http://localhost\nSekarang: " + location.origin);
                return;
            }
            alert("Permission sekarang: " + Notification.permission + "\nOrigin: " + location.origin);

            if (Notification.permission !== "granted") {
                Notification.requestPermission().then(function(p) {
                    alert("Hasil request: " + p);
                    if (p === "granted") {
                        showDesktopNotif("TEST Desktop", "Kalau ini tidak muncul, OS/Browser memblokir.",
                            "test");
                    }
                });
                return;
            }
            showDesktopNotif("TEST Desktop", "Kalau ini tidak muncul, OS/Browser memblokir.", "test");
        });

        /* =============== POLLING =============== */
        let lastInbox = <?php echo (int)$jumlah_inbox; ?>;
        let lastTrx = <?php echo (int)$jumlah_trx; ?>;

        function pollNotif() {
            fetch("<?php echo site_url('admin/notif_counter'); ?>", {
                    cache: "no-store"
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(d) {
                    if (!d || d.ok !== true) return;

                    // update badge dulu (biar kelihatan jalan)
                    setBadge(badgeInbox, d.inbox);
                    setBadge(badgeTrx, d.transaksi);

                    // INBOX
                    if (d.inbox > lastInbox) {
                        const plus = d.inbox - lastInbox;
                        playNotifSound();
                        showToast("📩 Inbox baru: +" + plus + " (total " + d.inbox + ")", "bg-red-500");
                        showDesktopNotif("Notifikasi inbox", "Ada inbox baru (+" + plus + "). Total: " + d.inbox + ".",
                            "inbox");
                    }

                    // TRANSAKSI
                    if (d.transaksi > lastTrx) {
                        const plus2 = d.transaksi - lastTrx;
                        playNotifSound();
                        showToast("💳 Transaksi baru: +" + plus2 + " (total " + d.transaksi + ")", "bg-blue-500");
                        showDesktopNotif("Notifikasi transaksi", "Ada update transaksi (" + d.transaksi + ").",
                            "transaksi");
                    }

                    lastInbox = d.inbox;
                    lastTrx = d.transaksi;
                })
                .catch(function(err) {
                    console.log("poll error:", err);
                });
        }

        setInterval(pollNotif, 2000);
    </script>
    <script>
        (function() {
            // === kunci admin (samakan dengan username di tabel notifications) ===
            var ADMIN_KEY = "admin";

            var POLL_URL = "<?= site_url('admin/admin_controls/notif_poll_v2') ?>";
            var SITE_URL = "<?= rtrim(site_url(), '/') ?>";

            // localStorage keys
            var KEY_LAST_I = "bm_last_admin_i_id_" + ADMIN_KEY;
            var KEY_LAST_T = "bm_last_admin_t_id_" + ADMIN_KEY;
            var KEY_ENABLED = "bm_notif_enabled_" + ADMIN_KEY;

            function getNum(k) {
                return parseInt(localStorage.getItem(k) || "0", 10) || 0;
            }

            function setNum(k, v) {
                localStorage.setItem(k, String(v || 0));
            }

            function notifEnabled() {
                return localStorage.getItem(KEY_ENABLED) === "1";
            }

            // optional: kalau kamu punya <audio id="notifSoundAdmin">
            function playSound() {
                var audio = document.getElementById('notifSoundAdmin') || document.getElementById('notifSound');
                if (!audio) return;
                try {
                    audio.currentTime = 0;
                    audio.play().catch(function() {});
                } catch (e) {}
            }

            function showNotif(n) {
                if (!("Notification" in window)) return;
                if (Notification.permission !== "granted") return;
                if (!notifEnabled()) return;

                try {
                    var notif = new Notification(n.title || "Booking Smarts", {
                        body: n.message || "",
                        silent: false,
                        tag: "bm_admin_" + (n.type || "x") + "_" + (n.id || "0")
                    });

                    playSound();

                    notif.onclick = function() {
                        window.focus();
                        if (n.url) window.location.href = SITE_URL + "/" + String(n.url).replace(/^\/+/, '');
                        notif.close();
                    };
                } catch (e) {}
            }

            function handle(list, key) {
                var last = getNum(key);
                var max = last;
                if (list && list.length) {
                    for (var i = 0; i < list.length; i++) {
                        var n = list[i];
                        var id = parseInt(n.id, 10) || 0;
                        if (id > last) {
                            showNotif(n);
                            if (id > max) max = id;
                        }
                    }
                }
                if (max > last) setNum(key, max);
            }

            async function poll() {
                try {
                    var lastI = getNum(KEY_LAST_I);
                    var lastT = getNum(KEY_LAST_T);

                    var url = POLL_URL + "?since_i=" + encodeURIComponent(lastI) + "&since_t=" + encodeURIComponent(lastT);

                    var res = await fetch(url, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        credentials: "same-origin"
                    });
                    var data = await res.json();
                    if (!data || !data.ok) return;

                    // update badge (kalau kamu punya id badge)
                    // contoh:
                    // updateBadgesAdmin(data.counts);

                    handle(data.items && data.items.inbox ? data.items.inbox : [], KEY_LAST_I);
                    handle(data.items && data.items.transaksi ? data.items.transaksi : [], KEY_LAST_T);

                } catch (e) {
                    console.log("admin poll error", e);
                }
            }

            // tombol enable (kalau ada)
            window.aktifkanNotifAdmin = async function() {
                if (!("Notification" in window)) return alert("Browser tidak mendukung notifikasi.");
                if (!window.isSecureContext) return alert("Notifikasi butuh HTTPS atau localhost.");

                var perm = Notification.permission;
                if (perm !== "granted") {
                    try {
                        perm = await Notification.requestPermission();
                    } catch (e) {}
                }
                if (perm === "granted") {
                    localStorage.setItem(KEY_ENABLED, "1");
                    alert("Notifikasi admin aktif.");
                } else {
                    localStorage.setItem(KEY_ENABLED, "0");
                    alert("Notifikasi diblokir.");
                }
            };

            // jalan
            poll();
            setInterval(poll, 8000);
        })();
    </script>

    <script>
        (function() {
            const POLL_URL = "<?= base_url('admin/admin_controls/notif_poll_v2') ?>";
            const KEY_LAST_INBOX = "bm_last_admin_inbox_id";
            const KEY_LAST_TRX = "bm_last_admin_trx_id";
            const LOCK_KEY = "bm_notif_lock_admin";
            const TAB_ID = Date.now() + "_" + Math.random().toString(16).slice(2);

            function getNum(k) {
                return parseInt(localStorage.getItem(k) || "0", 10) || 0;
            }

            function setNum(k, v) {
                localStorage.setItem(k, String(v || 0));
            }

            function isLeader() {
                const now = Date.now();
                const raw = localStorage.getItem(LOCK_KEY);
                let lock = null;
                try {
                    lock = raw ? JSON.parse(raw) : null;
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

            function ensurePermission() {
                if (!("Notification" in window)) return;
                if (Notification.permission === "default") {
                    window.addEventListener("click", function req() {
                        Notification.requestPermission();
                        window.removeEventListener("click", req);
                    }, {
                        once: true
                    });
                }
            }

            function showDeviceNotif(n) {
                if (!("Notification" in window)) return;
                if (Notification.permission !== "granted") return;
                const tag = "bm_admin_" + n.type + "_" + n.id;

                try {
                    const notif = new Notification(n.title || "Notifikasi Admin", {
                        body: n.message || "",
                        tag: tag,
                        renotify: false,
                        silent: false
                    });

                    notif.onclick = function() {
                        window.focus();
                        if (n.url) window.location.href = "<?= base_url() ?>" + n.url.replace(/^\/+/, '');
                        notif.close();
                    };
                } catch (e) {}
            }

            function handleList(list, keyLast) {
                const lastId = getNum(keyLast);
                const sorted = (list || []).slice().sort((a, b) => (a.id || 0) - (b.id || 0));
                let maxId = lastId;

                sorted.forEach(n => {
                    const id = parseInt(n.id, 10) || 0;
                    if (id > lastId) {
                        showDeviceNotif(n);
                        if (id > maxId) maxId = id;
                    }
                });

                if (maxId > lastId) setNum(keyLast, maxId);
            }

            async function poll() {
                if (!isLeader()) return;
                try {
                    const res = await fetch(POLL_URL, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (!data || !data.ok) return;

                    handleList(data.items?.inbox, KEY_LAST_INBOX);
                    handleList(data.items?.transaksi, KEY_LAST_TRX);
                } catch (e) {}
            }

            ensurePermission();
            poll();
            setInterval(poll, 8000);
        })();
    </script>

</body>

</html>