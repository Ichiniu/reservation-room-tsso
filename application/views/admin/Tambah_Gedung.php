<?php
$session_id = $this->session->userdata('username');
$this->load->helper(['text', 'form']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Gedung</title>

    <!-- Favicons -->
    <link rel="icon" href="<?= base_url('assets/home/assets/img/favicon/favicon-32x32.png') ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize (JS & upload only) -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">
</head>

<body class="bg-slate-200 min-h-screen">

    <!-- ================= SIDEBAR ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- ================= MAIN CONTENT ================= -->
    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <!-- HEADER -->
        <div class="max-w-5xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Tambah Gedung</h1>
            <p class="text-sm text-slate-500">Lengkapi data gedung dengan benar</p>
        </div>

        <!-- CARD -->
        <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-md p-6">

            <?= form_open_multipart('admin/add_gedung'); ?>

            <!-- ROW -->
            <div class="grid grid-cols-12 gap-4 items-center mb-4">
                <label class="col-span-4 text-slate-700 font-medium">Nama Gedung</label>
                <input type="text" name="nama_gedung" placeholder="The Ritz Carlton"
                    class="col-span-8 border rounded-lg px-3 py-2 focus:ring focus:ring-slate-200 outline-none">
            </div>

            <div class="grid grid-cols-12 gap-4 items-center mb-4">
                <label class="col-span-4 text-slate-700 font-medium">Kapasitas Gedung</label>
                <input type="text" name="kapasitas_gedung" placeholder="150"
                    class="col-span-4 border rounded-lg px-3 py-2 focus:ring focus:ring-slate-200 outline-none">
            </div>

            <div class="grid grid-cols-12 gap-4 items-start mb-4">
                <label class="col-span-4 text-slate-700 font-medium mt-2">Alamat Gedung</label>
                <textarea name="alamat_gedung" rows="3"
                    class="col-span-8 border rounded-lg px-3 py-2 focus:ring focus:ring-slate-200 outline-none"></textarea>
            </div>

            <div class="grid grid-cols-12 gap-4 items-start mb-4">
                <label class="col-span-4 text-slate-700 font-medium mt-2">Deskripsi Gedung</label>
                <textarea name="deskripsi_gedung" rows="3"
                    class="col-span-8 border rounded-lg px-3 py-2 focus:ring focus:ring-slate-200 outline-none"></textarea>
            </div>

            <div class="grid grid-cols-12 gap-4 items-center mb-6">
                <label class="col-span-4 text-slate-700 font-medium">Harga Sewa</label>
                <input type="text" name="harga_sewa" placeholder="650000000"
                    class="col-span-4 border rounded-lg px-3 py-2 focus:ring focus:ring-slate-200 outline-none">
            </div>

            <!-- UPLOAD -->
            <div class="grid grid-cols-12 gap-4 items-center mb-4">
                <label class="col-span-4 text-slate-700 font-medium">Gallery Gedung</label>
                <input type="file" name="img_gedung1" class="col-span-8">
            </div>

            <div class="grid grid-cols-12 gap-4 items-center mb-4">
                <label class="col-span-4"></label>
                <input type="file" name="img_gedung2" class="col-span-8">
            </div>

            <div class="grid grid-cols-12 gap-4 items-center mb-6">
                <label class="col-span-4"></label>
                <input type="file" name="img_gedung3" class="col-span-8">
            </div>

            <!-- BUTTON -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">
                    Simpan Gedung
                </button>
            </div>

            </form>
        </div>

    </main>
    <!-- ================= END MAIN CONTENT ================= -->

    <!-- JS -->
    <script src="<?= base_url('assets/home/assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/home/materialize/js/materialize.js') ?>"></script>

</body>

</html>