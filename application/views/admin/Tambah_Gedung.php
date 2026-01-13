<?php
$session_id = $this->session->userdata('username');
$this->load->helper(['text','form']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Gedung</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed"
        href="<?= base_url('assets/home/assets/img/favicon/apple-touch-icon-152x152.png'); ?>">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?= base_url('assets/home/assets/img/favicon/mstile-144x144.png'); ?>">
    <link rel="icon" href="<?= base_url('assets/home/assets/img/favicon/favicon-32x32.png'); ?>" sizes="32x32">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-200 min-h-screen">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-5xl mx-auto">

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Tambah Gedung</h1>
                <p class="text-sm text-slate-500">Lengkapi data gedung dan unggah galeri.</p>
            </div>

            <!-- Card -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        Form tambah gedung baru
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                        Admin Panel
                    </span>
                </div>

                <div class="p-6">
                    <?= form_open_multipart('admin/add_gedung', ['class' => 'space-y-6']); ?>

                    <!-- Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Nama Gedung -->
                        <div>
                            <label for="nama_gedung" class="block text-xs font-semibold text-slate-600 mb-1">
                                Nama Gedung
                            </label>
                            <input id="nama_gedung" name="nama_gedung" type="text"
                                placeholder="Contoh: The Ritz Carlton" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                         focus:outline-none focus:ring-2 focus:ring-teal-300">
                        </div>

                        <!-- Kapasitas -->
                        <div>
                            <label for="kapasitas_gedung" class="block text-xs font-semibold text-slate-600 mb-1">
                                Kapasitas Gedung
                            </label>
                            <input id="kapasitas_gedung" name="kapasitas_gedung" type="number" min="1"
                                placeholder="Contoh: 150" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                         focus:outline-none focus:ring-2 focus:ring-teal-300">
                        </div>

                        <!-- Harga Sewa -->
                        <div>
                            <label for="harga_sewa" class="block text-xs font-semibold text-slate-600 mb-1">
                                Harga Sewa
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Rp</span>
                                <input id="harga_sewa" name="harga_sewa" type="text" placeholder="Contoh: 6500000"
                                    class="w-full pl-10 rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-teal-300">
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Masukkan angka tanpa titik/koma (opsional).</p>
                        </div>

                        <!-- (Optional) Kosong biar layout rapi -->
                        <div class="hidden md:block"></div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label for="alamat_gedung" class="block text-xs font-semibold text-slate-600 mb-1">
                                Alamat Gedung
                            </label>
                            <textarea id="alamat_gedung" name="alamat_gedung" rows="3"
                                placeholder="Tulis alamat lengkap..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                         focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
                        </div>

                        <!-- Deskripsi -->
                        <div class="md:col-span-2">
                            <label for="deskripsi_gedung" class="block text-xs font-semibold text-slate-600 mb-1">
                                Deskripsi Gedung
                            </label>
                            <textarea id="deskripsi_gedung" name="deskripsi_gedung" rows="4"
                                placeholder="Fasilitas, ruangan, catatan penting, dll..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                         focus:outline-none focus:ring-2 focus:ring-teal-300"></textarea>
                        </div>

                    </div>

                    <!-- Upload Gallery -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-800">Gallery Gedung</h3>
                            <p class="text-xs text-slate-500">Maks 3 foto (jpg/png/webp)</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Image 1 -->
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Foto 1</label>
                                <input type="file" name="img_gedung1" accept="image/*" class="block w-full text-sm text-slate-700
                           file:mr-3 file:rounded-lg file:border-0
                           file:bg-slate-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white
                           hover:file:bg-slate-900">
                            </div>

                            <!-- Image 2 -->
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Foto 2</label>
                                <input type="file" name="img_gedung2" accept="image/*" class="block w-full text-sm text-slate-700
                           file:mr-3 file:rounded-lg file:border-0
                           file:bg-slate-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white
                           hover:file:bg-slate-900">
                            </div>

                            <!-- Image 3 -->
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Foto 3</label>
                                <input type="file" name="img_gedung3" accept="image/*" class="block w-full text-sm text-slate-700
                           file:mr-3 file:rounded-lg file:border-0
                           file:bg-slate-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white
                           hover:file:bg-slate-900">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" name="submit" value="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg
                       bg-teal-700 text-white text-sm font-semibold hover:bg-teal-800">
                            Simpan & Lanjut
                        </button>

                        <a href="<?= site_url('admin/gedung'); ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg
                        bg-slate-200 text-slate-800 text-sm font-semibold hover:bg-slate-300">
                            Batal
                        </a>
                    </div>

                    <?= form_close(); ?>
                </div>
            </div>

        </div>
    </main>

    <!-- NOTE:
    Kalau sidebar kamu masih butuh JS materialize untuk collapse/sidenav,
    tambahkan kembali script berikut:
    <script src="<?= base_url('assets/home/assets/js/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/home/materialize/js/materialize.js'); ?>"></script>
  -->
</body>

</html>