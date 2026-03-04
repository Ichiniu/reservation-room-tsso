<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

// User data
$u = $user ?? null;
$username = $u['USERNAME'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User — Admin Smart Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body class="bg-slate-200 min-h-screen">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="pt-24 md:pl-64 px-4 md:px-6 pb-10 transition-all duration-300">

        <!-- BREADCRUMB -->
        <nav class="mb-4 flex items-center gap-2 text-sm text-slate-500">
            <a href="<?= site_url('admin/dashboard') ?>" class="hover:text-slate-800">Dashboard</a>
            <span>/</span>
            <a href="<?= site_url('admin/list-user') ?>" class="hover:text-slate-800">Daftar User</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Edit User</span>
        </nav>

        <!-- HEADER -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Edit User</h1>
            <p class="text-sm text-slate-500">Ubah data pengguna: <strong class="text-slate-700"><?= htmlspecialchars($username) ?></strong></p>
        </div>

        <!-- CARD FORM -->
        <div class="max-w-3xl bg-white rounded-2xl shadow-md p-8">

            <form action="<?= site_url('admin/save-user/' . urlencode($username)) ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <!-- Username (read-only) -->
                    <div>
                        <label class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">USERNAME</label>
                        <input type="text" value="<?= htmlspecialchars($username) ?>" disabled
                               class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500 text-sm cursor-not-allowed">
                        <p class="mt-1 text-xs text-slate-400">Username tidak dapat diubah</p>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama_lengkap" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">NAMA LENGKAP</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                               value="<?= htmlspecialchars($u['NAMA_LENGKAP'] ?? '') ?>"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400"
                               placeholder="Nama lengkap">
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">EMAIL</label>
                        <input type="email" id="email" name="email" required
                               value="<?= htmlspecialchars($u['EMAIL'] ?? '') ?>"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400"
                               placeholder="user@gmail.com">
                    </div>

                    <!-- No Telepon -->
                    <div>
                        <label for="no_telepon" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">NO TELEPON</label>
                        <input type="text" id="no_telepon" name="no_telepon" inputmode="numeric"
                               value="<?= htmlspecialchars($u['NO_TELEPON'] ?? '') ?>"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="dob" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">TANGGAL LAHIR</label>
                        <input type="date" id="dob" name="dob"
                               value="<?= htmlspecialchars($u['TANGGAL_LAHIR'] ?? '') ?>"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400">
                    </div>

                    <!-- Perusahaan -->
                    <div class="sm:col-span-2">
                        <label for="perusahaan" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">TIPE PERUSAHAAN</label>
                        <div class="relative">
                            <select id="perusahaan" name="perusahaan"
                                    class="w-full appearance-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400">
                                <option value="">-- Pilih --</option>
                                <option value="INTERNAL" <?= ($u['perusahaan'] ?? '') === 'INTERNAL' ? 'selected' : '' ?>>INTERNAL (TS Group)</option>
                                <option value="EKSTERNAL" <?= ($u['perusahaan'] ?? '') === 'EKSTERNAL' ? 'selected' : '' ?>>EKSTERNAL</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-500">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Departemen (Internal) -->
                    <div id="wrapDepartemen" class="<?= ($u['perusahaan'] ?? '') !== 'INTERNAL' ? 'hidden' : '' ?>">
                        <label for="departemen" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">DEPARTEMEN</label>
                        <input type="text" id="departemen" name="departemen"
                               value="<?= htmlspecialchars($u['departemen'] ?? '') ?>"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400"
                               placeholder="Masukkan departemen">
                    </div>

                    <!-- Nama Perusahaan (Eksternal) -->
                    <div id="wrapNamaPerusahaan" class="<?= ($u['perusahaan'] ?? '') !== 'EKSTERNAL' ? 'hidden' : '' ?>">
                        <label for="nama_perusahaan" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">NAMA PERUSAHAAN / INSTANSI</label>
                        <input type="text" id="nama_perusahaan" name="nama_perusahaan"
                               value="<?= htmlspecialchars($u['nama_perusahaan'] ?? '') ?>"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400"
                               placeholder="Nama perusahaan/instansi">
                    </div>

                    <!-- Alamat -->
                    <div class="sm:col-span-2">
                        <label for="alamat" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">ALAMAT</label>
                        <textarea id="alamat" name="alamat" rows="3"
                                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 resize-none focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400"
                                  placeholder="Alamat lengkap"><?= htmlspecialchars($u['ALAMAT'] ?? '') ?></textarea>
                    </div>

                    <!-- Password baru (opsional) -->
                    <div>
                        <label for="password_baru" class="block text-xs font-semibold tracking-widest text-slate-600 mb-1.5">
                            PASSWORD BARU <span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_baru" name="password_baru"
                                   autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-11 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400/40 focus:border-sky-400"
                                   placeholder="Password baru (opsional)">
                            <button type="button" id="togglePw"
                                    class="absolute inset-y-0 right-2 my-auto h-9 w-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                </div><!-- /grid -->

                <!-- BUTTONS -->
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm transition shadow-sm">
                        Simpan Perubahan
                    </button>
                    <a href="<?= site_url('admin/list-user') ?>"
                       class="flex-1 py-3 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold text-center hover:bg-slate-50 transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </main>

    <script>
    /* Toggle perusahaan fields */
    const selPerusahaan = document.getElementById('perusahaan');
    const wrapDep = document.getElementById('wrapDepartemen');
    const wrapNama = document.getElementById('wrapNamaPerusahaan');

    function syncPerusahaan() {
        wrapDep.classList.toggle('hidden', selPerusahaan.value !== 'INTERNAL');
        wrapNama.classList.toggle('hidden', selPerusahaan.value !== 'EKSTERNAL');
    }
    selPerusahaan.addEventListener('change', syncPerusahaan);

    /* Toggle password visibility */
    const togglePw = document.getElementById('togglePw');
    const pwInput  = document.getElementById('password_baru');
    const toggleIcon = document.getElementById('toggleIcon');

    togglePw.addEventListener('click', () => {
        const show = pwInput.type === 'password';
        pwInput.type = show ? 'text' : 'password';
        toggleIcon.className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
    });

    /* Toast Flash */
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
        };
        const cfg = colors[type] || colors.success;
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
