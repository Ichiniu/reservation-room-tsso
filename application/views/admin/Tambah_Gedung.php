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

                        <!-- Mode Harga (User Eksternal) -->
                        <div>
                            <label for="pricing_mode" class="block text-xs font-semibold text-slate-600 mb-1">
                                Mode Harga (User Eksternal)
                            </label>
                            <select id="pricing_mode" name="pricing_mode" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                         focus:outline-none focus:ring-2 focus:ring-teal-300">
                                <option value="FLAT" selected>Flat (Harga Gedung)</option>
                                <option value="PER_PESERTA">Per Peserta (Meeting/Amphitheater)</option>
                                <option value="PODCAST_PER_JAM">Per Jam (Studio Podcast)</option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Internal tidak berubah. Setting ini untuk eksternal.</p>
                        </div>

                        <!-- Harga Sewa (Flat) -->
                        <div id="block_price_flat">
                            <label for="harga_sewa" class="block text-xs font-semibold text-slate-600 mb-1">
                                Harga Sewa (Flat)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Rp</span>
                                <input id="harga_sewa_display" name="harga_sewa_display" type="text" placeholder="Contoh: 6.500.000"
                                    class="w-full pl-10 rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-teal-300">
                                <input type="hidden" id="harga_sewa" name="harga_sewa" value="">
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Dipakai untuk mode Flat.</p>
                        </div>

                        <!-- Harga (Per Peserta) -->
                        <div id="block_price_perpeserta" class="md:col-span-2">
                            <div class="text-xs font-semibold text-slate-600 mb-2">Harga (Per Peserta)</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Halfday / peserta</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Rp</span>
                                        <input id="harga_halfday_pp_display" name="harga_halfday_pp_display" type="text" placeholder="Contoh: 30.000"
                                            class="w-full pl-10 rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                                        <input type="hidden" id="harga_halfday_pp" name="harga_halfday_pp" value="30000">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Fullday / peserta</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Rp</span>
                                        <input id="harga_fullday_pp_display" name="harga_fullday_pp_display" type="text" placeholder="Contoh: 60.000"
                                            class="w-full pl-10 rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                                        <input type="hidden" id="harga_fullday_pp" name="harga_fullday_pp" value="60000">
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Dipakai untuk Meeting Room & Amphitheater (user eksternal).</p>
                        </div>

                        <!-- Harga (Podcast Per Jam) -->
                        <div id="block_price_podcast" class="md:col-span-2">
                            <div class="text-xs font-semibold text-slate-600 mb-2">Harga (Podcast Per Jam)</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Audio Podcast / jam</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Rp</span>
                                        <input id="harga_audio_per_jam_display" name="harga_audio_per_jam_display" type="text" placeholder="Contoh: 150.000"
                                            class="w-full pl-10 rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                                        <input type="hidden" id="harga_audio_per_jam" name="harga_audio_per_jam" value="150000">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Video Streaming / jam</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Rp</span>
                                        <input id="harga_video_per_jam_display" name="harga_video_per_jam_display" type="text" placeholder="Contoh: 200.000"
                                            class="w-full pl-10 rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                                        <input type="hidden" id="harga_video_per_jam" name="harga_video_per_jam" value="200000">
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Dipakai untuk Studio Podcast (user eksternal). Per jam dibulatkan ke atas.</p>
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


</body>

</html>

<script>
    // Harga admin (Tambah Gedung) + toggle blok
    function formatRupiahInput(v) {
        v = (v === null || v === undefined) ? '' : String(v);
        v = v.replace(/[^0-9]/g, '');
        if (!v) return '';
        return v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function bindRupiahPair(displayId, hiddenId) {
        var disp = document.getElementById(displayId);
        var hidden = document.getElementById(hiddenId);
        if (!disp || !hidden) return;

        var rawInit = (hidden.value || '').toString().replace(/[^0-9]/g, '');
        disp.value = formatRupiahInput(rawInit);
        hidden.value = rawInit;

        disp.addEventListener('input', function() {
            var raw = (this.value || '').toString().replace(/[^0-9]/g, '');
            this.value = formatRupiahInput(raw);
            hidden.value = raw;
        });

        var form = disp.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                var raw = (disp.value || '').toString().replace(/[^0-9]/g, '');
                hidden.value = raw;
            });
        }
    }

    function togglePricingBlocks() {
        var pm = (document.getElementById('pricing_mode')?.value || 'FLAT').toUpperCase();
        var flat = document.getElementById('block_price_flat');
        var per = document.getElementById('block_price_perpeserta');
        var pod = document.getElementById('block_price_podcast');
        if (flat) flat.style.display = (pm === 'FLAT') ? '' : 'none';
        if (per) per.style.display = (pm === 'PER_PESERTA') ? '' : 'none';
        if (pod) pod.style.display = (pm === 'PODCAST_PER_JAM') ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        bindRupiahPair('harga_sewa_display', 'harga_sewa');
        bindRupiahPair('harga_halfday_pp_display', 'harga_halfday_pp');
        bindRupiahPair('harga_fullday_pp_display', 'harga_fullday_pp');
        bindRupiahPair('harga_audio_per_jam_display', 'harga_audio_per_jam');
        bindRupiahPair('harga_video_per_jam_display', 'harga_video_per_jam');

        var pmSel = document.getElementById('pricing_mode');
        if (pmSel) pmSel.addEventListener('change', togglePricingBlocks);
        togglePricingBlocks();
    });
</script>