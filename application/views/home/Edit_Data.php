<?php
$this->load->helper('form');

$u = (isset($user) && is_array($user)) ? $user : [];

$username       = $u['USERNAME'] ?? '';
$nama_lengkap   = $u['NAMA_LENGKAP'] ?? '';
$email          = $u['EMAIL'] ?? '';
$alamat         = $u['ALAMAT'] ?? '';
$no_telepon     = $u['NO_TELEPON'] ?? '';
$tanggal_lahir  = $u['TANGGAL_LAHIR'] ?? '';

$perusahaan      = $u['perusahaan'] ?? '';
$nama_perusahaan = $u['nama_perusahaan'] ?? '';
$departemen      = $u['departemen'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Diri</title>

    <!-- Material Icons & Tailwind -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen">
    <!-- Wrapper: di mobile tidak "menggantung", halaman boleh scroll normal -->
    <div class="min-h-screen flex justify-center items-start px-3 py-5 sm:px-6 sm:py-10">
        <div class="w-full max-w-3xl">
            <div class="bg-white border border-slate-200 shadow-sm sm:shadow-lg rounded-2xl overflow-hidden">

                <!-- HEADER: sticky saat scroll (lebih enak di mobile) -->
                <div class="sticky top-0 z-20 bg-white/95 backdrop-blur border-b border-slate-200 px-4 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-start gap-3">
                        <span class="material-icons text-blue-600 mt-0.5">person</span>
                        <div class="flex-1">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-800 leading-tight">Edit Data Diri</h1>
                            <p class="text-xs sm:text-sm text-slate-500">Perbarui informasi akun Anda</p>
                        </div>
                    </div>
                </div>

                <!-- CONTENT: biarkan page scroll (tidak nested scroll di mobile) -->
                <div class="px-4 py-5 sm:px-6 sm:py-6 space-y-5">

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="p-3 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm">
                            <?= htmlspecialchars((string)$this->session->flashdata('error'), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="p-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm">
                            <?= htmlspecialchars((string)$this->session->flashdata('success'), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- READONLY INFO (lebih rapi, tidak bikin form "gantung") -->
                    <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="text-sm font-semibold text-slate-800">Informasi Akun</div>
                            <div class="text-xs text-slate-500">Tidak bisa diubah</div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Username</label>
                                <input type="text" readonly
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800"
                                    value="<?= htmlspecialchars((string)$username, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Perusahaan</label>
                                <input type="text" readonly
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800"
                                    value="<?= htmlspecialchars((string)$perusahaan, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Perusahaan</label>
                                <input type="text" readonly
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800"
                                    value="<?= htmlspecialchars((string)$nama_perusahaan, ENT_QUOTES, 'UTF-8'); ?>">
                                <p class="text-[11px] text-slate-500 mt-1">Hanya tampil, tidak bisa diganti</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Departemen</label>
                                <input type="text" readonly
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800"
                                    value="<?= htmlspecialchars((string)$departemen, ENT_QUOTES, 'UTF-8'); ?>">
                                <p class="text-[11px] text-slate-500 mt-1">Hanya tampil, tidak bisa diganti</p>
                            </div>
                        </div>
                    </section>

                    <?php echo form_open('edit_data'); ?>

                    <!-- EDITABLE FORM -->
                    <section class="space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-slate-500 text-base">edit</span>
                            <div class="text-sm font-semibold text-slate-800">Data Pribadi</div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                                <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required autocomplete="name"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    value="<?= htmlspecialchars((string)$nama_lengkap, ENT_QUOTES, 'UTF-8'); ?>">
                                <p id="msg_nama_lengkap" class="text-xs mt-1 hidden"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                                <input type="email" name="email" required autocomplete="email"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    value="<?= htmlspecialchars((string)$email, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat</label>
                                <textarea name="alamat" required autocomplete="street-address"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    rows="3"><?= htmlspecialchars((string)$alamat, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">No Telepon</label>
                                <input type="tel" name="no_telepon" required autocomplete="tel"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    value="<?= htmlspecialchars((string)$no_telepon, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Lahir</label>
                                <input type="date" name="dob" required
                                    class="w-full border border-slate-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    value="<?= htmlspecialchars((string)$tanggal_lahir, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>
                    </section>

                    <div class="h-px bg-slate-200"></div>

                    <!-- PASSWORD -->
                    <section class="space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-slate-500 text-base">lock</span>
                            <div class="text-sm font-semibold text-slate-800">Keamanan</div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Password Baru (opsional)</label>
                                <input type="password" name="password" autocomplete="new-password"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    placeholder="Kosongkan jika tidak ingin ubah">
                                <p class="text-[11px] text-slate-500 mt-1">Minimal 8 karakter disarankan</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_pass" autocomplete="new-password"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </section>

                    <!-- ACTIONS: di mobile enak, tombol full lebar -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3 flex items-center justify-center gap-2">
                            <span class="material-icons text-base">save</span>
                            Simpan Perubahan
                        </button>

                        <p class="text-xs text-slate-500 text-center mt-2">
                            Pastikan data sudah benar sebelum menyimpan.
                        </p>
                    </div>

                    <?php echo form_close(); ?>

                </div>
            </div>

            <!-- Footer spacing biar gak mepet bawah di mobile -->
            <div class="h-6"></div>
        </div>
    </div>

<script>
    (function () {
        const input    = document.getElementById('edit_nama_lengkap');
        const msgEl    = document.getElementById('msg_nama_lengkap');
        const CHECK_URL = '<?= site_url("registration/check_availability") ?>';
        // Nama awal user saat halaman dibuka — dipakai untuk skip cek jika tidak berubah
        const originalName = (input ? input.defaultValue : '').trim();

        if (!input) return;

        function setMsg(text, isError) {
            msgEl.textContent  = text;
            msgEl.className    = 'text-xs mt-1 ' + (isError ? 'text-red-500' : 'text-emerald-600');
            msgEl.classList.remove('hidden');
            // Border merah / normal
            if (isError) {
                input.classList.add('border-red-400', 'ring-2', 'ring-red-100');
                input.classList.remove('border-slate-200');
            } else {
                input.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
                input.classList.add('border-slate-200');
            }
        }

        function clearMsg() {
            msgEl.textContent = '';
            msgEl.classList.add('hidden');
            input.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
            input.classList.add('border-slate-200');
        }

        // Reset pesan saat user mengetik ulang
        input.addEventListener('input', clearMsg);

        input.addEventListener('blur', async function () {
            const value = this.value.trim();

            // Jika sama dengan nama awal → tidak perlu cek (tidak berubah)
            if (value === originalName || value.length < 3) {
                clearMsg();
                return;
            }

            try {
                const res  = await fetch(`${CHECK_URL}?field=nama_lengkap&value=${encodeURIComponent(value)}`);
                const data = await res.json();

                if (!data.available) {
                    setMsg('✗ Nama lengkap sudah digunakan akun lain.', true);
                } else {
                    setMsg('✓ Nama lengkap tersedia.', false);
                }
            } catch (e) {
                // Network error — abaikan, biarkan backend handle
                console.warn('cek nama_lengkap gagal:', e);
            }
        });

        // Blok submit jika field masih ditandai error
        input.closest('form').addEventListener('submit', function (e) {
            if (input.classList.contains('border-red-400')) {
                e.preventDefault();
                input.focus();
                setMsg('✗ Nama lengkap sudah digunakan akun lain. Gunakan nama yang berbeda.', true);
            }
        });
    })();
</script>
</body>
</html>