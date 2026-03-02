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
    if (str_contains($current_uri, $uri)) return true;
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
        /*
           ========================================================================
           UNIFIED NOTIFICATION SYSTEM (SOUND + DESKTOP)
           ========================================================================
        */
        (function() {
            const ADMIN_KEY = "admin";
            const SITE_URL = "<?= rtrim(site_url(), '/') ?>";
            const POLL_URL = "<?= site_url('admin/admin_controls/notif_poll_v2') ?>";
            const COUNTER_URL = "<?= site_url('admin/notif_counter') ?>";

            // LocalStorage Keys
            const KEY_ENABLED = "bm_notif_enabled_" + ADMIN_KEY;
            const KEY_LAST_I = "bm_last_admin_i_id_" + ADMIN_KEY;
            const KEY_LAST_T = "bm_last_admin_t_id_" + ADMIN_KEY;

            // DOM Elements
            const badgeInbox = document.getElementById('badge-inbox');
            const badgeTrx = document.getElementById('badge-trx');
            const enableBtn = document.getElementById('enableNotifBtn');
            const notifSound = document.getElementById('notifSound');
            const toast = document.getElementById('toast');

            let sessionNotifEnabled = localStorage.getItem(KEY_ENABLED) === "1";

            // Helper: show toast
            function showToast(message, bgClass) {
                if (!toast) return;
                toast.className = "fixed bottom-5 right-5 px-4 py-3 rounded-lg shadow-lg text-white text-sm z-50 " + bgClass;
                toast.innerText = message;
                toast.style.display = "block";
                setTimeout(() => {
                    toast.style.display = "none";
                }, 4000);
            }

            // Helper: secure check
            function isSecure() {
                if (window.isSecureContext) return true;
                const host = location.hostname;
                return (host === "localhost" || host === "127.0.0.1");
            }

            // Helper: play sound
            function playSound() {
                if (!sessionNotifEnabled) return;
                try {
                    notifSound.currentTime = 0;
                    notifSound.play().catch(e => console.warn("Audio play blocked", e));
                } catch (e) {}
            }

            // Helper: Desktop Notification
            function showDesktop(title, body, tag, url) {
                if (!sessionNotifEnabled) return;
                if (!("Notification" in window) || Notification.permission !== "granted") return;
                if (!isSecure()) return;

                try {
                    const n = new Notification(title || "Booking Smarts", {
                        body: body || "",
                        tag: tag,
                        renotify: true
                    });
                    n.onclick = function() {
                        window.focus();
                        if (url) window.location.href = SITE_URL + "/" + url.replace(/^\/+/, '');
                        n.close();
                    };
                } catch (e) {
                    console.error("Desktop Notif Error:", e);
                }
            }

            // Button: Aktifkan Notifikasi
            enableBtn.onclick = async function() {
                // 1) Unlock Audio
                try {
                    notifSound.play().then(() => {
                        notifSound.pause();
                        notifSound.currentTime = 0;
                    }).catch(e => console.log("Init audio failed", e));
                } catch (e) {}

                // 2) Permission
                if (!("Notification" in window)) {
                    sessionNotifEnabled = true;
                    localStorage.setItem(KEY_ENABLED, "1");
                    showToast(" Sound aktif (Browser tidak dukung pop-up desktop)", "bg-green-600");
                    return;
                }

                if (!isSecure()) {
                    sessionNotifEnabled = true;
                    localStorage.setItem(KEY_ENABLED, "1");
                    showToast(" Sound aktif. (Desktop pop-up butuh HTTPS/localhost)", "bg-yellow-600");
                    return;
                }

                const perm = await Notification.requestPermission();
                sessionNotifEnabled = true;
                localStorage.setItem(KEY_ENABLED, "1");

                if (perm === "granted") {
                    showToast(" Notifikasi Suara & Desktop Aktif", "bg-green-600");
                    showDesktop("Notifikasi Aktif", "Anda akan menerima pemberitahuan di sini.", "init");
                    playSound();
                } else {
                    showToast(" Sound aktif (Desktop pop-up ditolak browser)", "bg-yellow-600");
                }
            };

            // Test Buttons
            document.getElementById('testSoundBtn').onclick = () => {
                sessionNotifEnabled = true;
                playSound();
                showToast("🔊 Test sound diputar", "bg-blue-600");
            };
            document.getElementById('testDesktopBtn').onclick = async () => {
                if (!isSecure()) return alert("Desktop notif butuh HTTPS atau localhost. Sekarang: " + location.origin);
                const perm = await Notification.requestPermission();
                if (perm === "granted") {
                    sessionNotifEnabled = true;
                    showDesktop("TEST Desktop", "Cek di pojok layar Anda.", "test");
                } else {
                    alert("Akses notifikasi ditolak browser (check site settings).");
                }
            };

            // POLLING 
            async function runPoll() {
                try {
                    const lastI = parseInt(localStorage.getItem(KEY_LAST_I) || "0", 10);
                    const lastT = parseInt(localStorage.getItem(KEY_LAST_T) || "0", 10);

                    const res = await fetch(POLL_URL + "?since_i=" + lastI + "&since_t=" + lastT, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (!data || !data.ok) return;

                    // Update Badges
                    if (data.counts) {
                        const bInbox = document.getElementById('badge-inbox');
                        const bTrx = document.getElementById('badge-trx');
                        if (bInbox) {
                            bInbox.innerText = data.counts.inbox;
                            bInbox.style.display = data.counts.inbox > 0 ? "flex" : "none";
                        }
                        if (bTrx) {
                            bTrx.innerText = data.counts.transaksi;
                            bTrx.style.display = data.counts.transaksi > 0 ? "flex" : "none";
                        }
                    }

                    // Handle Items
                    let maxI = lastI;
                    if (data.items && data.items.inbox) {
                        data.items.inbox.forEach(item => {
                            const id = parseInt(item.id);
                            if (id > lastI) {
                                playSound();
                                showToast("📩 " + item.title, "bg-red-500");
                                showDesktop(item.title, item.message, "i_" + id, item.url);
                                if (id > maxI) maxI = id;
                            }
                        });
                    }
                    if (maxI > lastI) localStorage.setItem(KEY_LAST_I, maxI);

                    let maxT = lastT;
                    if (data.items && data.items.transaksi) {
                        data.items.transaksi.forEach(item => {
                            const id = parseInt(item.id);
                            if (id > lastT) {
                                playSound();
                                showToast("💳 " + item.title, "bg-blue-500");
                                showDesktop(item.title, item.message, "t_" + id, item.url);
                                if (id > maxT) maxT = id;
                            }
                        });
                    }
                    if (maxT > lastT) localStorage.setItem(KEY_LAST_T, maxT);

                } catch (e) {
                    console.error("Poll Error:", e);
                }
            }

            // Start Polling
            setInterval(runPoll, 5000); // 5 detik sekali
            runPoll(); // Instant run
        })();
    </script>

</body>

</html>