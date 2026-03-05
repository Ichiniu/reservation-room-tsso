<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

function safe($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar User — Admin Smart Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <style>
        /* Dropdown */
        .action-menu { display:none; position:absolute; right:0; top:100%; z-index:50; min-width:160px; }
        .action-wrap:focus-within .action-menu,
        .action-menu.open { display:block; }
    </style>
</head>

<body class="bg-slate-100 min-h-screen">

    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 md:pl-64 px-4 md:px-6 pb-10 transition-all duration-300">

        <!-- HEADER -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Daftar User</h1>
                <p class="text-sm text-slate-500">Kelola seluruh pengguna yang terdaftar</p>
            </div>
            <!-- SEARCH INPUT -->
            <div class="relative w-full sm:w-64 cursor-text">
                <input type="text" id="searchInput" placeholder="Cari nama atau username..." 
                       class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-slate-400"></i>
                </div>
            </div>
        </div>

        <!-- CARD -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            <!-- TABLE -->
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600 w-10">No</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Username</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama Lengkap</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Email</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">No. Telepon</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Perusahaan</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Tipe</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-600 w-16">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100" id="tableBody">
                        <?php if (!empty($res)): ?>
                        <?php $no = 1; foreach ($res as $row): ?>
                        <tr class="table-row hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-500"><?= $no++ ?></td>
                            <td class="px-4 py-3 font-medium text-slate-800"><?= safe($row['USERNAME']) ?></td>
                            <td class="px-4 py-3 text-slate-700">
                                <?= !empty($row['NAMA_LENGKAP']) ? safe($row['NAMA_LENGKAP']) : '<span class="text-slate-400">-</span>' ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="max-w-[180px] block truncate" title="<?= safe($row['EMAIL']) ?>">
                                    <?= safe($row['EMAIL']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= safe($row['NO_TELEPON']) ?></td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="max-w-[140px] block truncate" title="<?= safe($row['nama_perusahaan'] ?? '') ?>">
                                    <?= !empty($row['nama_perusahaan']) ? safe($row['nama_perusahaan']) : '<span class="text-slate-400">-</span>' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <?php $tipe = strtoupper($row['perusahaan'] ?? ''); ?>
                                <?php if ($tipe === 'INTERNAL'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Internal</span>
                                <?php elseif ($tipe === 'EKSTERNAL'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-700">Eksternal</span>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- DROPDOWN AKSI -->
                            <td class="px-4 py-3 text-center">
                                <div class="relative inline-block action-wrap">
                                    <!-- Titik 3 button -->
                                    <button type="button"
                                            onclick="toggleMenu(this)"
                                            class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition focus:outline-none"
                                            aria-haspopup="true" aria-expanded="false"
                                            title="Aksi">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="5" r="1.5"/>
                                            <circle cx="12" cy="12" r="1.5"/>
                                            <circle cx="12" cy="19" r="1.5"/>
                                        </svg>
                                    </button>

                                    <!-- Dropdown -->
                                   <div class="action-menu mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
    <!-- Detail -->
    <a href="<?= site_url('admin/detail-user/' . urlencode($row['USERNAME'])) ?>"
       class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
        <i class="bi bi-eye text-indigo-500 text-base leading-none"></i>
        Detail
    </a>

    <!-- Edit -->
    <a href="<?= site_url('admin/edit-user/' . urlencode($row['USERNAME'])) ?>"
       class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
        <i class="bi bi-pencil-square text-sky-500 text-base leading-none"></i>
        Edit
    </a>

    <!-- Reset Password -->
    <button type="button"
            onclick="openResetModal('<?= addslashes($row['USERNAME']) ?>')"
            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
        <i class="bi bi-key text-amber-500 text-base leading-none"></i>
        Reset Password
    </button>

    <div class="border-t border-slate-100 my-0.5"></div>

    <!-- Hapus -->
    <button type="button"
            onclick="openDeleteModal('<?= addslashes($row['USERNAME']) ?>')"
            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
        <i class="bi bi-trash3 text-base leading-none"></i>
        Hapus
    </button>
</div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                    </svg>
                                    Data user belum tersedia
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button id="prevBtn" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm font-medium text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    ← Prev
                </button>
                <span id="pageInfo" class="text-sm text-slate-500 text-center"></span>
                <div class="flex items-center gap-2">
                    <select id="rowsPerPage" class="rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white">
                        <option value="10" selected>10 baris</option>
                        <option value="25">25 baris</option>
                        <option value="50">50 baris</option>
                    </select>
                    <button id="nextBtn" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm font-medium text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                        Next →
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- ========== MODAL HAPUS ========== -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Hapus User?</h3>
                <p class="text-sm text-slate-500 mb-5">
                    User <strong id="deleteUsername" class="text-slate-800"></strong> akan dihapus secara permanen.
                </p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    <form id="deleteForm" method="post" action="" class="flex-1">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== MODAL RESET PASSWORD ========== -->
    <div id="resetModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-11 w-11 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Reset Password</h3>
                        <p class="text-xs text-slate-500">User: <strong id="resetUsername" class="text-slate-700"></strong></p>
                    </div>
                </div>
                <form id="resetForm" method="post" action="">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">PASSWORD BARU</label>
                        <input type="password" id="resetPwInput" name="password_baru" required minlength="6"
                               placeholder="Minimal 6 karakter"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeResetModal()" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Batal</button>
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    /* ===== Search & Pagination ===== */
    const allRows = Array.from(document.querySelectorAll('.table-row'));
    let filteredRows = [...allRows];
    const rowsPerPageSel = document.getElementById('rowsPerPage');
    const pageInfo       = document.getElementById('pageInfo');
    const prevBtn        = document.getElementById('prevBtn');
    const nextBtn        = document.getElementById('nextBtn');
    const searchInput    = document.getElementById('searchInput');
    let currentPage = 1;

    function renderTable() {
        allRows.forEach(r => r.style.display = 'none');
        const rpm = parseInt(rowsPerPageSel.value);
        const start = (currentPage - 1) * rpm;
        const end   = start + rpm;
        filteredRows.forEach((r, i) => {
            if (i >= start && i < end) r.style.display = '';
        });
        const total = Math.ceil(filteredRows.length / rpm) || 1;
        pageInfo.textContent = `Halaman ${currentPage} dari ${total} · ${filteredRows.length} data`;
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === total;
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filteredRows = allRows.filter(row => {
                const username = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                const fullname = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';
                return username.includes(searchTerm) || fullname.includes(searchTerm);
            });
            currentPage = 1;
            renderTable();
        });
    }

    rowsPerPageSel.addEventListener('change', () => { currentPage = 1; renderTable(); });
    prevBtn.addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderTable(); } });
    nextBtn.addEventListener('click', () => {
        const total = Math.ceil(filteredRows.length / parseInt(rowsPerPageSel.value));
        if (currentPage < total) { currentPage++; renderTable(); }
    });
    renderTable();

    /* ===== Dropdown ===== */
    let openMenu = null;

    function toggleMenu(btn) {
        const menu = btn.nextElementSibling;
        if (openMenu && openMenu !== menu) { openMenu.classList.remove('open'); }
        menu.classList.toggle('open');
        openMenu = menu.classList.contains('open') ? menu : null;
        btn.setAttribute('aria-expanded', menu.classList.contains('open'));
    }

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-wrap')) {
            document.querySelectorAll('.action-menu.open').forEach(m => m.classList.remove('open'));
            openMenu = null;
        }
    });

    /* ===== Modal Hapus ===== */
    const deleteModal    = document.getElementById('deleteModal');
    const deleteForm     = document.getElementById('deleteForm');
    const deleteUsername = document.getElementById('deleteUsername');
    const baseDeleteUrl  = '<?= site_url("admin/delete-user/") ?>';

    function openDeleteModal(username) {
        if (openMenu) { openMenu.classList.remove('open'); openMenu = null; }
        deleteUsername.textContent = username;
        deleteForm.action = baseDeleteUrl + encodeURIComponent(username);
        deleteModal.classList.remove('hidden');
    }
    function closeDeleteModal() { deleteModal.classList.add('hidden'); }
    deleteModal.addEventListener('click', e => { if (e.target === deleteModal) closeDeleteModal(); });

    /* ===== Modal Reset Password ===== */
    const resetModal    = document.getElementById('resetModal');
    const resetForm     = document.getElementById('resetForm');
    const resetUsername = document.getElementById('resetUsername');
    const resetPwInput  = document.getElementById('resetPwInput');
    const baseResetUrl  = '<?= site_url("admin/reset-password/") ?>';

    function openResetModal(username) {
        if (openMenu) { openMenu.classList.remove('open'); openMenu = null; }
        resetUsername.textContent = username;
        resetForm.action = baseResetUrl + encodeURIComponent(username);
        resetPwInput.value = '';
        resetModal.classList.remove('hidden');
    }
    function closeResetModal() { resetModal.classList.add('hidden'); }
    resetModal.addEventListener('click', e => { if (e.target === resetModal) closeResetModal(); });

    /* ===== Toast Flash ===== */
    <?php
        $flash_msg  = $this->session->flashdata('flash_msg');
        $flash_type = $this->session->flashdata('flash_type');
        if ($flash_msg):
    ?>
    (function() {
        const msg  = <?= json_encode($flash_msg) ?>;
        const type = <?= json_encode($flash_type ?? 'info') ?>;
        const colors = {
            success : { bg: 'linear-gradient(135deg,#0A7F81,#2CC7C0)', icon: '\u2705' },
            error   : { bg: 'linear-gradient(135deg,#7f1d1d,#dc2626)',  icon: '\u274C' },
            info    : { bg: 'linear-gradient(135deg,#1e3a5f,#3b82f6)',  icon: '\u2139\uFE0F' },
        };
        const cfg = colors[type] || colors.info;
        Toastify({
            text: cfg.icon + '  ' + msg,
            duration: 4000, gravity: 'top', position: 'right', stopOnFocus: true,
            style: { background: cfg.bg, borderRadius: '12px', padding: '12px 20px', fontSize: '14px', fontWeight: '500', boxShadow: '0 8px 32px rgba(0,0,0,0.25)', fontFamily: 'ui-sans-serif, system-ui, sans-serif' },
        }).showToast();
    })();
    <?php endif; ?>
    </script>

</body>
</html>