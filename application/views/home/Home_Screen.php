<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$user = $this->uri->segment(2);

// info ringan (tidak mengubah logic utama, hanya untuk tampilan)
$total_gedung = is_array($res) ? count($res) : 0;
$max_kapasitas = 0;
if (!empty($res) && is_array($res)) {
    foreach ($res as $g) {
        if (isset($g['KAPASITAS']) && (int)$g['KAPASITAS'] > $max_kapasitas) {
            $max_kapasitas = (int)$g['KAPASITAS'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed"
        href="<?= base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="<?= base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?= base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Home</title>

    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex flex-col text-black bg-slate-200">

    <!-- NAVBAR -->
    <?php $this->load->view('components/navbar'); ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 py-8 sm:py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- HEADER -->
            <section class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">
                        Pilih Gedung
                    </h2>
                    <p class="mt-2 text-sm text-slate-700">
                        Silakan pilih gedung yang ingin dipesan.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?= site_url('home/gedung'); ?>"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        Lihat Semua
                        <span class="ml-2 text-slate-500">(<?= $total_gedung; ?>)</span>
                    </a>
                </div>
            </section>

            <!-- INFO CARDS (biar tidak sepi) -->
            <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs text-slate-500">Total Gedung</div>
                    <div class="mt-1 text-2xl font-semibold text-slate-900"><?= $total_gedung; ?></div>
                    <div class="mt-2 text-sm text-slate-600">Pilih sesuai kebutuhan acara kamu.</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs text-slate-500">Kapasitas Maks</div>
                    <div class="mt-1 text-2xl font-semibold text-slate-900">
                        <?= $max_kapasitas > 0 ? $max_kapasitas : '-'; ?>
                        <span class="text-sm font-medium text-slate-500">orang</span>
                    </div>
                    <div class="mt-2 text-sm text-slate-600">Cocok untuk acara besar / townhall.</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs text-slate-500">Tips Cepat</div>
                    <div class="mt-1 text-base font-semibold text-slate-900">Cek tanggal & jam</div>
                    <div class="mt-2 text-sm text-slate-600">
                        Pastikan jam pemakaian sesuai dan lihat detail sebelum booking.
                    </div>
                </div>
            </section>

            <!-- SECTION: Gedung Pilihan (3 item) -->
            <section class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-semibold text-slate-900">
                        Gedung Pilihan
                    </h3>
                    <a href="<?= site_url('home/gedung'); ?>"
                        class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                        Lihat semua →
                    </a>
                </div>

                <!-- GRID GEDUNG (3 kolom di desktop) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach (array_slice($res, 0, 3) as $row):
                        $path = $row['PATH'];
                        $img_name = $row['IMG_NAME'];
                    ?>
                    <article
                        class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">

                        <!-- GAMBAR GEDUNG -->
                        <div class="relative">
                            <img src="<?= $path . $img_name; ?>" alt="<?= htmlspecialchars($row['NAMA_GEDUNG']); ?>"
                                class="h-52 sm:h-56 w-full object-cover">

                            <!-- Badge kapasitas -->
                            <div
                                class="absolute bottom-2 right-2 inline-flex items-center px-2 py-1 rounded-full bg-black/60">
                                <span class="text-[11px] font-medium text-slate-50">
                                    <?= $row['KAPASITAS']; ?> Orang
                                </span>
                            </div>
                        </div>

                        <!-- ISI KARTU -->
                        <div class="flex-1 flex flex-col p-4 space-y-2">
                            <h3 class="text-base font-semibold text-slate-900">
                                <?= $row['NAMA_GEDUNG']; ?>
                            </h3>
                            <p class="text-sm text-slate-500">
                                Kapasitas hingga
                                <span class="font-semibold text-slate-800">
                                    <?= $row['KAPASITAS']; ?> orang
                                </span>
                                — cocok untuk meeting, presentasi, atau acara internal.
                            </p>

                            <div class="pt-3 mt-auto flex justify-end">
                                <a href="<?= site_url('home/details/'.$row['ID_GEDUNG']); ?>"
                                    class="inline-flex items-center text-sm font-semibold text-sky-700 hover:text-sky-800">
                                    DETAILS
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 ml-1"
                                        fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M8 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($res)): ?>
                <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <div class="text-base font-semibold text-slate-900">Belum ada gedung tersedia</div>
                    <div class="mt-2 text-sm text-slate-600">
                        Coba cek lagi nanti atau hubungi admin untuk menambahkan data gedung.
                    </div>
                </div>
                <?php endif; ?>
            </section>

            <!-- CTA kecil bawah (biar halaman hidup) -->
            <?php if (!empty($res) && count($res) > 3): ?>
            <section
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Mau lihat semua gedung?</div>
                    <div class="text-sm text-slate-600">Jelajahi daftar lengkap untuk menemukan yang paling cocok.</div>
                </div>
                <a href="<?= site_url('home/gedung'); ?>"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Lihat Semua Gedung
                </a>
            </section>
            <?php endif; ?>

        </div>
    </main>

    <!-- FOOTER -->
    <?php $this->load->view('components/Footer'); ?>

</body>

</html>