<?php
$username = $this->session->userdata('admin_username');
if (empty($username)) {
    $username = $this->session->userdata('username');
}

/**
 * FIX:
 * Di halaman admin/detail_pemesanan, $result = object stdClass (detail pesanan),
 * jadi jangan pernah (int)$result.
 */

// inbox
if (isset($result)) {
    if (is_array($result)) {
        $result = count($result);
    } elseif (is_numeric($result)) {
        $result = (int)$result;
    } else {
        // kalau object (stdClass) atau tipe lain -> anggap 0
        $result = 0;
    }
} else {
    $result = 0;
}

// transaksi
if (isset($get_transaction)) {
    if (is_array($get_transaction)) {
        $get_transaction = count($get_transaction);
    } elseif (is_numeric($get_transaction)) {
        $get_transaction = (int)$get_transaction;
    } else {
        $get_transaction = 0;
    }
} else {
    $get_transaction = 0;
}

$current_uri = uri_string();

function is_active($uri, $current_uri)
{
    return strpos($current_uri, $uri) !== false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Smart Office</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ICON -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* ===== SIDEBAR ===== */
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

        /* ===== CONTENT ===== */
        .content-mini {
            margin-left: 72px !important;
        }

        /* ===== ACTIVE MENU ===== */
        .menu-active {
            background-color: #ffffff;
            font-weight: 600;
        }

        /* ===== BADGE INBOX ===== */
        .inbox-badge {
            margin-left: auto;
        }

        .sidebar-mini .inbox-badge {
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

    <!-- ================= TOPBAR ================= -->
    <header class="fixed top-0 left-0 right-0 z-40 bg-white border-b h-16">
        <div class="flex items-center px-6 h-full gap-4">
            <span id="toggleSidebar" class="material-icons cursor-pointer select-none">
                menu
            </span>
            <span class="font-semibold text-lg">Administrator</span>
            <span class="ml-auto text-sm text-gray-500">
                <?= htmlspecialchars((string)$username, ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </div>
    </header>

    <!-- ================= SIDEBAR ================= -->
    <aside id="sidebar" class="fixed top-16 left-0 z-30 w-64 h-full bg-[#f9f7f2] border-r transition-all duration-300">

        <nav class="px-3 py-6 space-y-1 text-sm">

            <a href="<?= site_url('admin/dashboard') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= is_active('admin/dashboard', $current_uri) ? 'menu-active' : '' ?>">
                <span class="material-icons">dashboard</span>
                <span class="menu-text">Home</span>
            </a>

            <a href="<?= site_url('admin/list') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= is_active('admin/list', $current_uri) ? 'menu-active' : '' ?>">
                <span class="material-icons">people</span>
                <span class="menu-text">List User</span>
            </a>

            <a href="<?= site_url('admin/gedung') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= is_active('admin/gedung', $current_uri) ? 'menu-active' : '' ?>">
                <span class="material-icons">business</span>
                <span class="menu-text">List Ruangan</span>
            </a>

            <a href="<?= site_url('admin/catering') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= is_active('admin/catering', $current_uri) ? 'menu-active' : '' ?>">
                <span class="material-icons">restaurant</span>
                <span class="menu-text">Catering</span>
            </a>

            <a href="<?= site_url('admin/pemesanan2') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= is_active('admin/pemesanan', $current_uri) ? 'menu-active' : '' ?>">
                <span class="material-icons">assignment</span>
                <span class="menu-text">List Pemesanan</span>
            </a>

            <!-- INBOX / TRANSAKSI -->
            <?php $jumlah_inbox = (int)$result; ?>
            <a href="<?= site_url('admin/transaksi') ?>" class="relative flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?php if ($current_uri == 'admin/transaksi') {
    echo 'menu-active';
} ?>">
                <span class="material-icons-outlined">inbox</span>
                <span class="menu-text">Inbox</span>

                <?php if ($jumlah_inbox > 0) { ?>
                    <span class="inbox-badge bg-red-500 text-white rounded-full
                 w-5 h-5 flex items-center justify-center text-xs">
                        <?= $jumlah_inbox ?>
                    </span>
                <?php } ?>
            </a>

            <a href="<?= site_url('admin/pembayaran') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= is_active('admin/pembayaran', $current_uri) ? 'menu-active' : '' ?>">
                <span class="material-icons">payments</span>
                <span class="menu-text">Transaksi</span>
            </a>

            <a href="<?= site_url('admin/rekap_aktivitas') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= is_active('admin/rekap_aktivitas', $current_uri) ? 'menu-active' : '' ?>">
                <span class="material-icons">history</span>
                <span class="menu-text">Rekap Aktivitas</span>
            </a>

            <a href="<?= site_url('admin/rekap_transaksi') ?>" class="flex items-center gap-3 px-4 py-3 rounded hover:bg-white
<?= is_active('admin/rekap_transaksi', $current_uri) ? 'menu-active' : '' ?>">
                <span class="material-icons">summarize</span>
                <span class="menu-text">Rekap Transaksi</span>
            </a>

            <hr class="my-4">

            <a href="<?= site_url('admin/login/log_out') ?>"
                class="flex items-center gap-3 px-4 py-3 rounded text-red-600 hover:bg-red-50">
                <span class="material-icons">logout</span>
                <span class="menu-text">Sign Out</span>
            </a>


        </nav>
    </aside>

    <!-- CONTENT PLACEHOLDER -->
    <main id="content" class=" ml-64 transition-all duration-300 px-6"></main>

    <!-- ===== SCRIPT TOGGLE ===== -->
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-mini');
            content.classList.toggle('content-mini');
        });
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