<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Catering</title>

    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize (icon only) -->
    <link href="<?= base_url('assets/home/materialize/css/materialize.css') ?>" rel="stylesheet">

</head>

<body class="bg-slate-200 min-h-screen">

    <!-- ================= SIDEBAR ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- ================= MAIN ================= -->
    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <!-- HEADER -->
        <div class="max-w-4xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Tambah Catering</h1>
            <p class="text-sm text-slate-500">Tambahkan paket catering baru</p>
        </div>

        <!-- CARD -->
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-8">

            <form method="post" action="<?= site_url('admin/admin_controls/tambah_catering'); ?>">

                <!-- NAMA PAKET -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center mb-5">
                    <label class="text-sm font-medium text-slate-700">
                        Nama Paket
                    </label>
                    <input type="text" name="nama_paket" placeholder="e.g Paket Hemat 1" class="md:col-span-2 w-full border rounded-lg px-4 py-2
                  focus:outline-none focus:ring-2 focus:ring-slate-200">
                </div>

                <!-- MENU PEMBUKA -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center mb-5">
                    <label class="text-sm font-medium text-slate-700">
                        Menu Pembuka
                    </label>
                    <input type="text" name="menu_pembuka" placeholder="e.g Dimsum" class="md:col-span-2 w-full border rounded-lg px-4 py-2
                  focus:outline-none focus:ring-2 focus:ring-slate-200">
                </div>

                <!-- MENU UTAMA -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center mb-5">
                    <label class="text-sm font-medium text-slate-700">
                        Menu Utama
                    </label>
                    <input type="text" name="menu_utama" placeholder="e.g Nasi Lemak" class="md:col-span-2 w-full border rounded-lg px-4 py-2
                  focus:outline-none focus:ring-2 focus:ring-slate-200">
                </div>

                <!-- MENU PENUTUP -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center mb-5">
                    <label class="text-sm font-medium text-slate-700">
                        Menu Penutup
                    </label>
                    <input type="text" name="menu_penutup" placeholder="e.g Dessert" class="md:col-span-2 w-full border rounded-lg px-4 py-2
                  focus:outline-none focus:ring-2 focus:ring-slate-200">
                </div>

                <!-- HARGA -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center mb-8">
                    <label class="text-sm font-medium text-slate-700">
                        Harga / Porsi
                    </label>
                    <input type="text" name="harga" placeholder="e.g 125000" class="md:col-span-1 w-full border rounded-lg px-4 py-2
                  focus:outline-none focus:ring-2 focus:ring-slate-200">
                </div>

                <!-- BUTTON -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2
                   px-6 py-2.5 rounded-lg
                   bg-blue-600 text-white text-sm font-medium
                   hover:bg-blue-700 transition">
                        <!-- <span class="material-icons text-sm">add</span> -->
                        Tambah Menu
                    </button>
                </div>

            </form>

        </div>
    </main>

    <!-- JS -->
    <script src="<?= base_url('assets/home/assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/home/materialize/js/materialize.js') ?>"></script>

</body>

</html>