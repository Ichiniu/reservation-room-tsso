<?php
$username = $this->session->userdata('admin_username');
if (empty($username)) {
    $username = $this->session->userdata('username');
}

/* inbox */
if (isset($result)) {
    if (is_array($result)) $result = count($result);
    elseif (is_numeric($result)) $result = (int)$result;
    else $result = 0;
} else $result = 0;

/* transaksi */
if (isset($get_transaction)) {
    if (is_array($get_transaction)) $get_transaction = count($get_transaction);
    elseif (is_numeric($get_transaction)) $get_transaction = (int)$get_transaction;
    else $get_transaction = 0;
} else $get_transaction = 0;

$current_uri = uri_string();
function is_active($uri, $current_uri)
{
    if (strpos($current_uri, $uri) !== false) return true;
    return false;
}

$jumlah_inbox = (int)$result;
$jumlah_trx   = (int)$get_transaction;
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
    <style>
    .sidebar-mini {
        width: 72px !important;
    }

    .sidebar-mini .menu-text {
        display: none;
    }

    .sidebar-mini nav a {
        justify-content: center;
    }

    .sidebar-mini .material-icons-outlined {
        margin-right: 0 !important;
    }

    .content-mini {
        margin-left: 72px !important;
    }

    .menu-active {
        background-color: #fff;
        font-weight: 600;
    }

    .badge {
        margin-left: auto;
    }

    .sidebar-mini .badge {
        position: absolute;
        top: 8px;
        right: 12px;
        width: 18px;
        height: 18px;
        font-size: 10px;
        margin-left: 0;
    }
    </style>
</head>

<body class="bg-white text-gray-800">

    <header class="fixed top-0 left-0 right-0 z-40 bg-white border-b h-16">
        <div class="flex items-center px-6 h-full gap-4">
            <span id="toggleSidebar" class="material-icons cursor-pointer select-none">menu</span>
            <span class="font-semibold text-lg">Administrator</span>

            <!-- tombol aktifkan notif -->
            <button id="enableNotifBtn"
                class="ml-3 px-3 py-1 rounded-md text-xs border border-gray-300 hover:bg-gray-50">
                Aktifkan Notifikasi
            </button>

            <!-- tombol tes -->
            <button id="testSoundBtn" class="px-3 py-1 rounded-md text-xs border border-gray-300 hover:bg-gray-50">
                Test Sound
            </button>
            <button id="testDesktopBtn" class="px-3 py-1 rounded-md text-xs border border-gray-300 hover:bg-gray-50">
                Test Desktop
            </button>

            <span class="ml-auto text-sm text-gray-500">
                <?php echo htmlspecialchars((string)$username, ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </div>
    </header>

    <aside id="sidebar" class="fixed top-16 left-0 z-30 w-64 h-full bg-[#f9f7f2] border-r transition-all duration-300">
        <nav class="px-3 py-6 space-y-1 text-sm">

            <a href="<?php echo site_url('admin/dashboard'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/dashboard',$current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">dashboard</span>
                <span class="menu-text">Home</span>
            </a>

            <a href="<?php echo site_url('admin/list'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/list',$current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">people</span>
                <span class="menu-text">List User</span>
            </a>

            <a href="<?php echo site_url('admin/gedung'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/gedung',$current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">business</span>
                <span class="menu-text">List Ruangan</span>
            </a>

            <a href="<?php echo site_url('admin/catering'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/catering',$current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">restaurant</span>
                <span class="menu-text">Catering</span>
            </a>

            <a href="<?php echo site_url('admin/pemesanan2'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/pemesanan',$current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">assignment</span>
                <span class="menu-text">List Pemesanan</span>
            </a>

            <!-- INBOX -->
            <a href="<?php echo site_url('admin/transaksi'); ?>"
                class="relative flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if ($current_uri=='admin/transaksi') echo 'menu-active'; ?>">
                <span class="material-icons-outlined">inbox</span>
                <span class="menu-text">Inbox</span>
                <span id="badge-inbox"
                    class="badge bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                    <?php if ($jumlah_inbox<=0) echo 'style="display:none"'; ?>>
                    <?php echo $jumlah_inbox; ?>
                </span>
            </a>

            <!-- TRANSAKSI -->
            <a href="<?php echo site_url('admin/pembayaran'); ?>"
                class="relative flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/pembayaran',$current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">payments</span>
                <span class="menu-text">Transaksi</span>
                <span id="badge-trx"
                    class="badge bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                    <?php if ($jumlah_trx<=0) echo 'style="display:none"'; ?>>
                    <?php echo $jumlah_trx; ?>
                </span>
            </a>

            <a href="<?php echo site_url('admin/rekap_aktivitas'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/rekap_aktivitas',$current_uri)) echo 'menu-active'; ?>">
                <span class="material-icons">history</span>
                <span class="menu-text">Rekap Aktivitas</span>
            </a>

            <a href="<?php echo site_url('admin/rekap_transaksi'); ?>"
                class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white <?php if (is_active('admin/rekap_transaksi',$current_uri)) echo 'menu-active'; ?>">
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
    /* =============== UI basic =============== */
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('sidebar-mini');
        content.classList.toggle('content-mini');
    });

    const toast = document.getElementById('toast');
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

    setInterval(pollNotif, 8000);
    </script>

</body>

</html>