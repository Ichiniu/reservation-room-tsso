<?php
$this->load->helper('form');

$u = (isset($user) && is_array($user)) ? $user : array();

$username       = isset($u['USERNAME']) ? $u['USERNAME'] : '';
$nama_lengkap   = isset($u['NAMA_LENGKAP']) ? $u['NAMA_LENGKAP'] : '';
$email          = isset($u['EMAIL']) ? $u['EMAIL'] : '';
$alamat         = isset($u['ALAMAT']) ? $u['ALAMAT'] : '';
$no_telepon     = isset($u['NO_TELEPON']) ? $u['NO_TELEPON'] : '';
$tanggal_lahir  = isset($u['TANGGAL_LAHIR']) ? $u['TANGGAL_LAHIR'] : '';

$perusahaan      = isset($u['perusahaan']) ? $u['perusahaan'] : '';
$nama_perusahaan = isset($u['nama_perusahaan']) ? $u['nama_perusahaan'] : '';
$departemen      = isset($u['departemen']) ? $u['departemen'] : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Data Diri</title>

    <!-- Material Icons & Tailwind -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-200 h-screen overflow-hidden flex items-center justify-center px-4">

    <div class="w-full max-w-3xl bg-white rounded-xl shadow-lg
                h-[90vh] flex flex-col border border-slate-200">

        <!-- HEADER (FIXED) -->
        <div class="p-6 border-b bg-white rounded-t-xl shrink-0">
            <h2 class="text-xl font-bold text-slate-800 mb-1 flex items-center gap-2">
                <span class="material-icons text-blue-600">person</span>
                Edit Data Diri
            </h2>
            <p class="text-sm text-slate-500">Perbarui informasi akun Anda</p>
        </div>

        <!-- CONTENT (SCROLLABLE) -->
        <div class="p-6 overflow-y-auto flex-1 space-y-6">

            <?php if ($this->session->flashdata('error')): ?>
            <div class="p-3 rounded bg-red-100 text-red-700 border border-red-300">
                <?= htmlspecialchars($this->session->flashdata('error')); ?>
            </div>
            <?php endif; ?>

            <!-- READONLY INFO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-slate-600">Username</label>
                    <input type="text" readonly class="w-full bg-slate-100 border rounded px-3 py-2"
                        value="<?= htmlspecialchars($username); ?>">
                </div>

                <div>
                    <label class="text-sm text-slate-600">Perusahaan</label>
                    <input type="text" readonly class="w-full bg-slate-100 border rounded px-3 py-2"
                        value="<?= htmlspecialchars($perusahaan); ?>">
                </div>

                <div>
                    <label class="text-sm text-slate-600">Nama Perusahaan</label>
                    <input type="text" readonly class="w-full bg-slate-100 border rounded px-3 py-2"
                        value="<?= htmlspecialchars($nama_perusahaan); ?>">
                    <p class="text-xs text-slate-500 mt-1">Hanya tampil, tidak bisa diganti</p>
                </div>

                <div>
                    <label class="text-sm text-slate-600">Departemen</label>
                    <input type="text" readonly class="w-full bg-slate-100 border rounded px-3 py-2"
                        value="<?= htmlspecialchars($departemen); ?>">
                    <p class="text-xs text-slate-500 mt-1">Hanya tampil, tidak bisa diganti</p>
                </div>
            </div>

            <?php echo form_open('edit_data'); ?>

            <!-- EDITABLE FORM -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                        value="<?= htmlspecialchars($nama_lengkap); ?>">
                </div>

                <div>
                    <label class="text-sm">Email</label>
                    <input type="email" name="email" required
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                        value="<?= htmlspecialchars($email); ?>">
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm">Alamat</label>
                    <textarea name="alamat" required
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                        rows="3"><?= htmlspecialchars($alamat); ?></textarea>
                </div>

                <div>
                    <label class="text-sm">No Telepon</label>
                    <input type="text" name="no_telepon" required
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                        value="<?= htmlspecialchars($no_telepon); ?>">
                </div>

                <div>
                    <label class="text-sm">Tanggal Lahir</label>
                    <input type="date" name="dob" required
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                        value="<?= htmlspecialchars($tanggal_lahir); ?>">
                </div>
            </div>

            <hr>

            <!-- PASSWORD -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm">Password Baru (opsional)</label>
                    <input type="password" name="password"
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                        placeholder="Kosongkan jika tidak ingin ubah password">
                </div>

                <div>
                    <label class="text-sm">Confirm Password Baru</label>
                    <input type="password" name="confirm_pass"
                        class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200"
                        placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg flex items-center justify-center gap-2">
                    <span class="material-icons">save</span>
                    Simpan Perubahan
                </button>
            </div>

            <?php echo form_close(); ?>

        </div>
    </div>

</body>

</html>