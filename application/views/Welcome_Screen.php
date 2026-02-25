<?php
// Welcome screen (public)
$this->load->helper(['text', 'url']);

$total = count((array)($res ?? []));
function e($v)
{
    return html_escape((string)$v);
}

/* =========================================================================
    PENGATURAN FASILITAS PER RUANGAN (EDIT DI SINI)
   -------------------------------------------------------------------------
   - Key array = ID_GEDUNG (ID ruangan di DB)
   - Isi = list badge icon + label (Material Icons)
   ========================================================================= */
$FACILITY_BY_ROOM_ID = [
    // RUANG 1 (ID_GEDUNG = 1) -> Meeting Room
    1 => [
        ['icon' => 'tv',        'label' => 'TV LED'],
        ['icon' => 'wifi',      'label' => 'WiFi'],
        ['icon' => 'ac_unit',   'label' => 'AC'],
        ['icon' => 'volume_up', 'label' => 'Sound'],
    ],

    // RUANG 2 (ID_GEDUNG = 2) -> Amphitheater
    2 => [
        ['icon' => 'present_to_all', 'label' => 'Proyektor'],
        ['icon' => 'wifi',           'label' => 'WiFi'],
        ['icon' => 'ac_unit',        'label' => 'AC'],
        ['icon' => 'volume_up',      'label' => 'Sound'],
    ],

    // RUANG 3 (ID_GEDUNG = 3) -> Studio Podcast
    3 => [
        ['icon' => 'mic',        'label' => 'Mic'],
        ['icon' => 'headphones', 'label' => 'Headset'],
        ['icon' => 'graphic_eq', 'label' => 'Audio'],
        ['icon' => 'videocam',   'label' => 'Kamera'],
    ],
];

/* =========================================================================
    PENGATURAN DESKRIPSI PER RUANGAN (EDIT DI SINI)
   -------------------------------------------------------------------------
   - Ini yang mengganti teks:
     "Ruangan nyaman dengan penataan modern..."
   ========================================================================= */
$DESC_BY_ROOM_ID = [
    1 => "Meeting Room nyaman untuk rapat, presentasi, dan diskusi internal. Tata ruang rapi, suasana fokus, siap dipakai kegiatan resmi.",
    2 => "Amphitheater luas untuk seminar, pelatihan, and acara skala besar. Visibilitas bagus, audio jelas, cocok untuk event formal maupun publik.",
    3 => "Studio Podcast untuk rekaman audio/video, talkshow, dan konten kreatif. Setup mendukung produksi konten dengan kualitas suara lebih rapi.",
];

/* =========================================================================
    PENGATURAN TAGLINE OVERLAY (EDIT DI SINI)
   -------------------------------------------------------------------------
   - Ini yang mengganti teks kecil di overlay bawah nama ruangan.
   ========================================================================= */
$TAGLINE_BY_ROOM_ID = [
    1 => "Cocok untuk rapat, presentasi, dan diskusi tim.",
    2 => "Ideal untuk seminar, pelatihan, dan event skala besar.",
    3 => "Untuk rekaman podcast, talkshow, dan konten kreatif.",
];

/* =========================================================================
   (OPSIONAL) Fallback fasilitas kalau ada ruangan baru ID 4,5,6 dst.
   ========================================================================= */
$FACILITY_POOL_FALLBACK = [
    ['icon' => 'wifi',        'label' => 'WiFi'],
    ['icon' => 'ac_unit',     'label' => 'AC'],
    ['icon' => 'volume_up',   'label' => 'Sound'],
    ['icon' => 'present_to_all', 'label' => 'Proyektor'],
    ['icon' => 'tv',          'label' => 'TV'],
    ['icon' => 'mic',         'label' => 'Mic'],
    ['icon' => 'draw',        'label' => 'Whiteboard'],
    ['icon' => 'power',       'label' => 'Power'],
];

/* ===== Shuffle deterministik untuk fallback (biar stabil) ===== */
if (!function_exists('seeded_shuffle')) {
    function seeded_shuffle($items, $seed)
    {
        $items = array_values((array)$items);
        $n = count($items);

        $state = (int)$seed;
        if ($state <= 0) $state = 1234567;

        for ($i = $n - 1; $i > 0; $i--) {
            $state = ($state * 1103515245 + 12345) & 0x7fffffff;
            $j = $state % ($i + 1);
            $tmp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $tmp;
        }
        return $items;
    }
}

/* ===== Ambil fasilitas untuk ruangan (mapping -> fallback) ===== */
if (!function_exists('get_facilities_for_room')) {
    function get_facilities_for_room($id, $map, $fallbackPool, $take = 4)
    {
        if (is_array($map[$id] ?? null) && count($map[$id]) > 0) {
            return $map[$id];
        }
        $seed = (int)abs(crc32((string)$id));
        $shuffled = seeded_shuffle($fallbackPool, $seed);
        return array_slice($shuffled, 0, max(1, (int)$take));
    }
}

/* ===== Ambil deskripsi per ruangan (mapping -> fallback) ===== */
if (!function_exists('get_desc_for_room')) {
    function get_desc_for_room($id, $map, $fallback = "Ruangan nyaman dengan penataan modern, siap digunakan untuk kegiatan internal maupun eksternal.")
    {
        if (trim((string)($map[$id] ?? '')) !== '') return $map[$id];
        return $fallback;
    }
}

/* ===== Ambil tagline per ruangan (mapping -> fallback) ===== */
if (!function_exists('get_tagline_for_room')) {
    function get_tagline_for_room($id, $map, $fallback = "Cocok untuk rapat, presentasi, dan kegiatan resmi.")
    {
        if (trim((string)($map[$id] ?? '')) !== '') return $map[$id];
        return $fallback;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Booking Room Smart Office Tiga Serangkai</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.output.css') ?>">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="min-h-screen text-slate-900 bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200">

    <!-- ================= NAVBAR ================= -->
    <header class="bg-white border-b border-black/5 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-10">
            <div class="flex items-center justify-between h-16">

                <!-- ===== BRAND ===== -->
                <div class="flex items-center gap-7">
                    <div class="h-9 w-9 rounded-lg bg-white border border-black/10 flex items-center justify-center">
                        <img src="<?= base_url('assets/login/LogoTSNew.png'); ?>" class="h-10 w-10 object-contain"
                            alt="Logo">
                    </div>

                    <div class="leading-tight">
                        <div class="text-[10px] font-semibold tracking-[0.25em] uppercase text-slate-500">
                            Smart Office
                        </div>
                        <div class="text-sm font-semibold text-slate-800">
                            E-Booking Room
                        </div>
                    </div>
                </div>

                <!-- ===== MENU ===== -->
                <nav
                    class="hidden md:flex items-center gap-5 mx-5 text-[11px] font-semibold tracking-widest text-slate-700">
                    <a href="<?= site_url('home'); ?>" class="flex items-center gap-2 hover:text-slate-900 transition">
                        <i class="bi bi-house-door text-base"></i> HOME
                    </a>
                    <a href="<?= site_url('home/jadwal'); ?>"
                        class="flex items-center gap-2 hover:text-slate-900 transition">
                        <i class="bi bi-calendar-week text-base"></i> JADWAL
                    </a>
                    <a href="<?= site_url('home/pemesanan'); ?>"
                        class="flex items-center gap-2 hover:text-slate-900 transition">
                        <i class="bi bi-journal-text text-base"></i> PEMESANAN
                    </a>
                    <a href="<?= site_url('home/view-catering'); ?>"
                        class="flex items-center gap-2 hover:text-slate-900 transition">
                        <i class="bi bi-cup-hot text-base"></i> CATERING
                    </a>
                    <a href="<?= site_url('home/pembayaran'); ?>"
                        class="flex items-center gap-2 hover:text-slate-900 transition">
                        <i class="bi bi-credit-card text-base"></i> TRANSAKSI
                    </a>
                </nav>

                <!-- ===== BUTTON LOGIN ===== -->
                <a href="<?= site_url('login'); ?>"
                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 bg-slate-900/40 ring-1 ring-black/10 text-white hover:bg-slate-900/60 transition">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span class="text-sm font-semibold">Login</span>
                </a>

            </div>
        </div>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-8">

            <!-- HERO -->
            <section
                class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 backdrop-blur shadow-sm">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-28 -right-28 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>
                    <div class="absolute -bottom-28 -left-28 h-72 w-72 rounded-full bg-indigo-200/40 blur-3xl"></div>
                </div>

                <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="max-w-2xl">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-semibold">
                            <span class="material-icons text-sm">verified</span>
                            Ruangan fasilitas lengkap
                        </div>

                        <h2 class="mt-4 text-2xl md:text-3xl font-extrabold tracking-tight">
                            Pilih Ruangan dengan Tampilan Modern
                        </h2>
                        <p class="mt-2 text-sm md:text-base text-slate-600">
                            Cocok untuk rapat, presentasi, dan kegiatan resmi. Pilih ruangan sesuai kapasitas dan
                            kebutuhan.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex flex-col items-end">
                            <p class="text-xs text-slate-500">Tersedia</p>
                            <p class="text-xl font-bold"><?= (int)$total; ?> ruangan</p>
                        </div>

                        <a href="<?= site_url('login'); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold
                                  hover:bg-slate-800 active:scale-[0.99] transition shadow-sm
                                  focus:outline-none focus:ring-4 focus:ring-slate-200">
                            Mulai Booking
                            <span class="material-icons text-base">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </section>

            <!-- GRID RUANGAN -->
            <section>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

                    <?php if (!empty($res) && is_array($res)): ?>
                        <?php foreach ($res as $row): ?>
                            <?php
                            $raw_path = (!empty($row['PATH']) && !empty($row['IMG_NAME'])) ? ($row['PATH'] . $row['IMG_NAME']) : '';
                            // Normalisasi: hapus domain lama (http://localhost/bookingsmarts/) lalu tambahkan base_url()
                            $img = '';
                            if ($raw_path !== '') {
                                $clean = preg_replace('#^https?://[^/]+/bookingsmarts/#i', '', $raw_path);
                                $img = base_url($clean);
                            }
                            $nama = $row['NAMA_GEDUNG'] ?? 'Ruangan';
                            $kap  = $row['KAPASITAS'] ?? '-';
                            $id   = (int)($row['ID_GEDUNG'] ?? 0);

                            // ✅ ambil fasilitas, tagline, deskripsi sesuai ID
                            $badges  = get_facilities_for_room($id, $FACILITY_BY_ROOM_ID, $FACILITY_POOL_FALLBACK, 4);
                            $desc    = get_desc_for_room($id, $DESC_BY_ROOM_ID);
                            $tagline = get_tagline_for_room($id, $TAGLINE_BY_ROOM_ID);
                            ?>

                            <article class="group bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm
                                  hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

                                <div class="relative overflow-hidden">
                                    <?php if ($img): ?>
                                        <img src="<?= $img; ?>" alt="<?= e($nama); ?>" loading="lazy"
                                            class="h-56 w-full object-cover transition duration-500 group-hover:scale-110">
                                    <?php else: ?>
                                        <div
                                            class="h-56 w-full bg-gradient-to-br from-slate-200 to-slate-100 flex items-center justify-center">
                                            <span class="material-icons text-5xl text-slate-400">meeting_room</span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent">
                                    </div>

                                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full
                                      text-xs font-semibold text-slate-800 flex items-center gap-1 shadow">
                                        <span class="material-icons text-sm">groups</span>
                                        <?= e($kap); ?> org
                                    </div>

                                    <div class="absolute left-4 right-4 bottom-4">
                                        <h3 class="text-lg font-bold text-white drop-shadow"><?= e($nama); ?></h3>

                                        <!--  TAGLINE PER RUANGAN -->
                                        <p
                                            class="mt-1 text-xs text-white/85 [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] overflow-hidden">
                                            <?= e($tagline); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="p-5">

                                    <!--  FASILITAS PER RUANGAN -->
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($badges as $b): ?>
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">
                                                <span class="material-icons text-sm"><?= e($b['icon']); ?></span>
                                                <?= e($b['label']); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>

                                    <!--  DESKRIPSI PER RUANGAN -->
                                    <p
                                        class="mt-4 text-sm text-slate-600 [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:3] overflow-hidden">
                                        <?= e($desc); ?>
                                    </p>
                                </div>

                                <div class="px-5 pb-5 flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <span class="material-icons text-base">schedule</span>
                                        Respons cepat
                                    </div>

                                    <!-- public: detail diarahkan ke login -->
                                    <a href="<?= site_url('login'); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-sky-600 text-white text-sm font-semibold
                                      hover:bg-sky-700 active:scale-[0.99] transition shadow-sm
                                      focus:outline-none focus:ring-4 focus:ring-sky-200">
                                        Detail
                                        <span
                                            class="material-icons text-base transition group-hover:translate-x-0.5">arrow_forward</span>
                                    </a>
                                </div>

                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full">
                            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                                <div class="mx-auto h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <span class="material-icons text-slate-500">info</span>
                                </div>
                                <h4 class="mt-4 text-lg font-bold">Belum ada data ruangan</h4>
                                <p class="mt-1 text-sm text-slate-600">Silakan tambahkan data ruangan terlebih dahulu.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </section>

        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-black/5 mt-10">
        <div class="max-w-6xl mx-auto px-4 py-6 grid grid-cols-1 md:grid-cols-5 gap-4 text-sm text-slate-900">

            <a href="<?= site_url('login'); ?>" class="hover:text-slate-700 flex items-center gap-2">
                <i class="bi bi-question-circle"></i> How to Order
            </a>
            <a href="<?= site_url('login'); ?>" class="hover:text-slate-700 flex items-center gap-2">
                <i class="bi bi-chat-dots"></i> Ulasan
            </a>
            <a href="<?= site_url('login'); ?>" class="hover:text-slate-700 flex items-center gap-2">
                <i class="bi bi-geo-alt"></i> Location
            </a>
            <a href="<?= site_url('login'); ?>" class="hover:text-slate-700 flex items-center gap-2">
                <i class="bi bi-newspaper"></i> News Feed
            </a>
            <a href="<?= site_url('login'); ?>" class="hover:text-slate-700 flex items-center gap-2">
                <i class="bi bi-building"></i> Tiga Serangkai
            </a>

        </div>

        <div class="text-center text-xs text-slate-700 pb-4">
            © <?= date('Y'); ?> Smart Office – E-Booking Room
        </div>
    </footer>

</body>

</html>