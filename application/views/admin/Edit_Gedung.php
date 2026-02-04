<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Favicons-->
    <link rel="apple-touch-icon-precomposed"
        href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Edit Gedung</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>


    <!-- clean, neutral form styling (use Tailwind classes on elements) -->
</head>

<body class="bg-gray-50 text-gray-900">

    <!-- ================= SIDEBAR COMPONENT ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- Overlay mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    <!-- Topbar -->
    <header class="fixed top-0 left-0 right-0 z-20 bg-white border-b border-gray-200">
        <div class="h-16 px-4 md:px-6 flex items-center gap-3">
            <button id="sidebarToggle"
                class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
                type="button" aria-label="Toggle sidebar">
                <i class="material-icons text-gray-800">menu</i>
            </button>

            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white">
                    <i class="material-icons text-gray-800">apartment</i>
                </span>
                <div class="leading-tight">
                    <div class="font-semibold">Edit Ruangan</div>
                    <div class="text-xs text-gray-500">Admin Panel</div>
                </div>
            </div>

            <div class="ml-auto text-sm text-gray-600">
                <?php echo htmlspecialchars(isset($session_id) && $session_id !== '' ? $session_id : '-', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="pt-20 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-5xl mx-auto">

            <!-- Header section -->
            <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h5 class="text-xl font-semibold text-gray-900 m-0">Form Edit Gedung</h5>
                    <p class="text-sm text-gray-500 mt-1">Ubah data gedung lalu klik <b>Simpan</b>.</p>
                </div>

                <a href="<?php echo site_url('admin/gedung'); ?>"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                    <i class="material-icons text-base mr-2">arrow_back</i>
                    Kembali
                </a>
            </div>

            <!-- Card -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <i class="material-icons text-gray-800">edit</i>
                        <div class="font-semibold text-gray-900">Data Gedung</div>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        Pastikan kapasitas & harga sewa sudah sesuai.
                    </div>
                </div>

                <?php if ($this->session->flashdata('msg_success')): ?>
                    <div class="p-4">
                        <div class="text-sm text-green-700 bg-green-50 border border-green-100 rounded p-2">
                            <?= $this->session->flashdata('msg_success') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('msg_error')): ?>
                    <div class="p-4">
                        <div class="text-sm text-red-700 bg-red-50 border border-red-100 rounded p-2">
                            <?= $this->session->flashdata('msg_error') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="p-4 md:p-6">
                    <?php foreach ($result as $row): ?>

                        <?php echo form_open_multipart('admin/edit/' . $row['ID_GEDUNG']); ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Nama Gedung -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">domain</i>
                                    Nama Gedung
                                </div>
                                <div>
                                    <input placeholder="The Ritz Carlton" value="<?php echo htmlspecialchars($row['NAMA_GEDUNG'], ENT_QUOTES, 'UTF-8') ?>"
                                        id="nama" name="nama_gedung" type="text" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    <div class="text-sm text-gray-500 mt-2">Nama resmi gedung.</div>
                                </div>
                            </div>

                            <!-- Kapasitas -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">groups</i>
                                    Kapasitas Gedung
                                </div>
                                <div>
                                    <input placeholder="150" id="kapasitas" value="<?php echo htmlspecialchars($row['KAPASITAS'], ENT_QUOTES, 'UTF-8') ?>"
                                        name="kapasitas_gedung" type="text" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    <div class="text-sm text-gray-500 mt-2">Jumlah maksimal orang.</div>
                                </div>
                            </div>

                            <!-- Harga Sewa -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">payments</i>
                                    Harga Sewa
                                </div>
                                <div>
                                    <input placeholder="650000000" value="<?php echo htmlspecialchars($row['HARGA_SEWA'], ENT_QUOTES, 'UTF-8') ?>"
                                        id="kapasitas" name="harga_sewa" type="text" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    <div class="text-sm text-gray-500 mt-2">Isi angka saja jika memungkinkan (contoh: 650000000).</div>
                                </div>
                            </div>

                            <!-- Placeholder biar grid balance -->
                            <div class="hidden md:block"></div>

                            <!-- Alamat (full width) -->
                            <div class="md:col-span-2 border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">location_on</i>
                                    Alamat Gedung
                                </div>
                                <div>
                                    <textarea name="alamat_gedung" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200"><?php echo htmlspecialchars($row['ALAMAT'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                            </div>

                            <!-- Deskripsi (full width) -->
                            <div class="md:col-span-2 border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">description</i>
                                    Deskripsi Gedung
                                </div>
                                <div>
                                    <textarea name="deskripsi_gedung" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200"><?php echo htmlspecialchars($row['DESKRIPSI_GEDUNG'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                            </div>
                            <!-- Fasilitas (full width) -->
                            <div class="md:col-span-2 border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">checklist</i>
                                    Fasilitas Ruangan
                                </div>
                                <div>
                                    <textarea name="fasilitas_gedung" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Contoh: Proyektor, AC, Sound System, WiFi"><?php echo isset($row['fasilitas']) ? htmlspecialchars($row['fasilitas'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                                    <div class="text-sm text-gray-500 mt-2">Pisahkan dengan koma atau baris baru.</div>
                                </div>
                            </div>

                            <!-- Gambar Gedung (upload & preview) -->
                            <div class="md:col-span-2 border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">photo</i>
                                    Foto Ruangan
                                </div>
                                <div>
                                    <?php if (!empty($images)): ?>
                                        <div class="flex gap-3 mt-3 flex-wrap">
                                            <?php foreach ($images as $img): ?>
                                                <div class="w-40 h-28 rounded overflow-hidden border relative">
                                                    <img src="<?= htmlspecialchars($img['PATH'] . $img['IMG_NAME'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto" class="w-full h-full object-cover">
                                                    <button type="button" data-img="<?= htmlspecialchars($img['IMG_NAME'], ENT_QUOTES, 'UTF-8') ?>" data-id="<?= (int)$row['ID_GEDUNG'] ?>" title="Hapus" class="delete-image-btn absolute top-1 right-1 bg-white/90 hover:bg-white text-red-600 rounded-full p-1 shadow z-50" style="pointer-events:auto;">
                                                        <i class="material-icons text-xs">delete</i>
                                                    </button>
                                                    <div class="absolute left-1 bottom-1 bg-white/80 px-1 rounded text-xs text-gray-700"><?= htmlspecialchars($img['IMG_NAME'], ENT_QUOTES, 'UTF-8') ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-sm text-gray-500 mt-2">Belum ada foto untuk gedung ini.</div>
                                    <?php endif; ?>

                                    <div class="mt-4">
                                        <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple class="text-sm text-gray-700">
                                        <div class="text-sm text-gray-500 mt-2">Pilih 1 atau beberapa foto (jpg/png). Upload akan menambahkan gambar baru.</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex items-center gap-3 justify-start">
                            <button type="submit" name="submit" id="submit" tabindex="10" value="Simpan"
                                class="inline-flex h-10 items-center gap-2 rounded-lg bg-slate-700 px-4 text-sm font-semibold text-white hover:bg-slate-800 active:bg-slate-900">
                                <i class="material-icons text-[18px]">save</i>
                                Simpan
                            </button>

                            <a href="<?php echo site_url('admin/gedung'); ?>"
                                class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 active:bg-gray-100">
                                <i class="material-icons text-[18px]">close</i>
                                Batal
                            </a>
                        </div>


                        <?php echo form_close(); ?>

                    <?php endforeach; ?>
                </div>
            </div>
            <footer class="text-xs text-gray-500 text-center mt-6">
                © <?php echo date('Y'); ?> Smart Office • Admin Panel

            </footer>
        </div>
    </main>

    <!-- Sidebar toggle -->
    <script>
        (function() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var btn = document.getElementById('sidebarToggle');
            if (!sidebar) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                if (overlay) overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (btn) {
                btn.addEventListener('click', function() {
                    var isClosed = sidebar.classList.contains('-translate-x-full');
                    if (isClosed) openSidebar();
                    else closeSidebar();
                });
            }

            if (overlay) overlay.addEventListener('click', closeSidebar);

            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeSidebar();
            });

            var mq = window.matchMedia('(min-width: 768px)');
            mq.addEventListener('change', function(e) {
                if (e.matches) {
                    if (overlay) overlay.classList.add('hidden');
                    sidebar.classList.remove('-translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    closeSidebar();
                }
            });
        })();
    </script>
    <!-- Hidden delete form -->
    <form id="deleteImageForm" method="post" action="<?= site_url('admin/gedung_image/delete') ?>" style="display:none;">
        <input type="hidden" name="id_gedung" value="">
        <input type="hidden" name="img_name" value="">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-image-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if (!confirm('Hapus foto ini?')) return;
                    var form = document.getElementById('deleteImageForm');
                    form.querySelector('input[name="id_gedung"]').value = this.dataset.id || '';
                    form.querySelector('input[name="img_name"]').value = this.dataset.img || '';
                    form.submit();
                });
            });
        });
    </script>
    <script>
        // Ensure submit works even if other scripts prevent default
        (function() {
            var submitBtn = document.getElementById('submit');
            if (!submitBtn) return;
            submitBtn.addEventListener('click', function(e) {
                try {
                    var f = this.form;
                    if (f) f.submit();
                } catch (err) {
                    console.error('Submit fallback error', err);
                }
            });
        })();
    </script>
</body>

</html>