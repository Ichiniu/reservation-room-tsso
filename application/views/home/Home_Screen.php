<?php
$session_id = $this->session->userdata('username');
$this->load->helper(array('text','url'));
$user  = $this->uri->segment(2);

$total = (isset($res) && is_array($res)) ? count($res) : 0;

function e($v){ return html_escape((string)$v); }

/* ===== Format tanggal Indo: 01 januari 2001 ===== */
if (!function_exists('formatTanggalIndo')) {
    function formatTanggalIndo($tgl)
    {
        $tgl = trim((string)$tgl);
        if ($tgl === '') return '-';

        $bulan = array(
            1=>'januari','februari','maret','april','mei','juni',
            'juli','agustus','september','oktober','november','desember'
        );

        $ts = strtotime($tgl);
        if (!$ts) return $tgl;

        $d = date('d', $ts);
        $m = (int)date('n', $ts);
        $y = date('Y', $ts);

        $namaBulan = isset($bulan[$m]) ? $bulan[$m] : '';
        return $d . ' ' . $namaBulan . ' ' . $y;
    }
}

/* ===== Konversi nama bulan Indo/Eng -> angka ===== */
if (!function_exists('bulanKeAngka')) {
    function bulanKeAngka($nama)
    {
        $nama = strtolower(trim((string)$nama));
        $map = array(
            'januari'=>1,'jan'=>1,
            'februari'=>2,'feb'=>2,
            'maret'=>3,'mar'=>3,
            'april'=>4,'apr'=>4,
            'mei'=>5,'may'=>5,
            'juni'=>6,'jun'=>6,
            'juli'=>7,'jul'=>7,
            'agustus'=>8,'agu'=>8,'aug'=>8,
            'september'=>9,'sep'=>9,
            'oktober'=>10,'okt'=>10,'oct'=>10,
            'november'=>11,'nov'=>11,
            'desember'=>12,'des'=>12,'dec'=>12,
        );
        return isset($map[$nama]) ? (int)$map[$nama] : 0;
    }
}

/* ===== Pecah title jadi 3 baris (TOLERAN) ===== */
if (!function_exists('parse_title_3lines')) {
    function parse_title_3lines($title)
    {
        $t = trim((string)$title);
        if ($t === '') return null;

        $original = $t;

        // date
        $dateRawYmd = '';
        $dateTextInTitle = '';

        if (preg_match('/\b(\d{4}-\d{2}-\d{2})\b/', $t, $m)) {
            $dateRawYmd = $m[1];
            $dateTextInTitle = $m[1];
        } elseif (preg_match('/\b(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})\b/u', $t, $m2)) {
            $d = (int)$m2[1];
            $mon = bulanKeAngka($m2[2]);
            $y = (int)$m2[3];
            if ($mon >= 1 && $mon <= 12 && $d >= 1 && $d <= 31) {
                $dateRawYmd = sprintf('%04d-%02d-%02d', $y, $mon, $d);
                $dateTextInTitle = $m2[0];
            }
        }

        // time
        $timeRaw = '';
        if (preg_match('/\(([^)]*)\)/', $t, $mt)) {
            $timeRaw = trim($mt[1]);
        }
        if ($timeRaw === '') {
            if (preg_match('/\b(\d{1,2}[:.]\d{2})\s*-\s*(\d{1,2}[:.]\d{2})\b/i', $t, $tm)) {
                $timeRaw = trim($tm[1]) . ' - ' . trim($tm[2]);
            }
        }

        if ($dateRawYmd === '' && $timeRaw === '') return null;

        // normalize jam
        $jam = ($timeRaw !== '') ? $timeRaw : '-';
        if ($jam !== '-') {
            $jam = str_replace(':', '.', $jam);
            $jam = preg_replace('/\s*-\s*/', ' - ', $jam);

            if (stripos($jam, 'wib') === false) $jam .= ' wib';
            $jam = preg_replace('/\s*wib\s*/i', ' wib', $jam);
            $jam = trim($jam);
        }

        // nama
        $nama = $t;
        $nama = preg_replace('/\([^)]*\)/', '', $nama);
        if ($dateTextInTitle !== '') $nama = str_replace($dateTextInTitle, '', $nama);

        $nama = preg_replace('/\b\d{1,2}[:.]\d{2}\s*-\s*\d{1,2}[:.]\d{2}\b/i', '', $nama);
        $nama = preg_replace('/\s*wib\b/i', '', $nama);

        $nama = preg_replace('/\s+/', ' ', $nama);
        $nama = trim($nama);
        $nama = rtrim($nama, '-');
        $nama = trim($nama);

        if ($nama === '') $nama = $original;

        $tgl = ($dateRawYmd !== '') ? formatTanggalIndo($dateRawYmd) : '-';

        return array(
            'nama' => $nama,
            'tgl'  => $tgl,
            'jam'  => $jam,
        );
    }
}

/* ===== Bintang rapi (Material Icons) ===== */
if (!function_exists('stars_html')) {
    function stars_html($rating)
    {
        $rating = (int)$rating;
        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;

        $out = '<div class="inline-flex items-center gap-0.5 leading-none">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $out .= '<span class="material-icons text-[18px] text-amber-500 align-middle leading-none">star</span>';
            } else {
                $out .= '<span class="material-icons text-[18px] text-slate-300 align-middle leading-none">star_border</span>';
            }
        }
        $out .= '</div>';
        return $out;
    }
}

/* ===== Data ulasan (dari controller) ===== */
$ul_total = 0;
$ul_avg   = 0;

if (isset($ulasan_summary) && is_array($ulasan_summary)) {
    if (isset($ulasan_summary['total'])) $ul_total = (int)$ulasan_summary['total'];
    if (isset($ulasan_summary['avg']))   $ul_avg   = (float)$ulasan_summary['avg'];
}
$ul_avgRounded = (int)round($ul_avg);

$ulasan_home = (isset($ulasan_home) && is_array($ulasan_home)) ? $ulasan_home : array();
$ulasan_count = is_array($ulasan_home) ? count($ulasan_home) : 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
    /* ===== marquee animation (loop ke kanan terus) ===== */
    @keyframes review-marquee {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }

        /* karena track digandakan */
    }

    .marquee-track {
        animation: review-marquee linear infinite;
        will-change: transform;
    }

    /* pause saat hover (opsional, hapus kalau tidak perlu) */
    .marquee-wrap:hover .marquee-track {
        animation-play-state: paused;
    }
    </style>
</head>

<body class="min-h-screen text-slate-900 bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200">

    <?php if ($this->session->flashdata('success_popup')): ?>
    <div id="toastSuccess"
        class="fixed z-50 top-5 right-5 w-[92vw] max-w-sm rounded-2xl border border-emerald-200 bg-white/90 backdrop-blur shadow-2xl">
        <div class="p-4">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <span class="material-icons">check_circle</span>
                </div>

                <div class="flex-1">
                    <p class="text-sm font-semibold">Berhasil</p>
                    <p class="mt-0.5 text-sm text-slate-600"><?= e($this->session->flashdata('success_popup')); ?></p>
                </div>

                <button type="button" class="h-9 w-9 rounded-xl hover:bg-slate-100 flex items-center justify-center"
                    onclick="var el=document.getElementById('toastSuccess'); if(el) el.remove();">
                    <span class="material-icons text-slate-500">close</span>
                </button>
            </div>

            <div class="mt-3 h-1 w-full rounded-full bg-slate-100 overflow-hidden">
                <div id="toastBar" class="h-full w-full bg-emerald-500"></div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var bar = document.getElementById('toastBar');
        if (bar && bar.animate) {
            bar.animate([{
                width: '100%'
            }, {
                width: '0%'
            }], {
                duration: 4000,
                easing: 'linear'
            });
        }
        setTimeout(function() {
            var el = document.getElementById('toastSuccess');
            if (el) el.remove();
        }, 4200);
    })();
    </script>
    <?php endif; ?>

    <?php $this->load->view('components/navbar'); ?>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-8">

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
                    </div>
                </div>
            </section>

            <section>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

                    <?php if (!empty($res) && is_array($res)): ?>
                    <?php foreach (array_slice($res, 0, 3) as $row): ?>
                    <?php
                $img  = (!empty($row['PATH']) && !empty($row['IMG_NAME'])) ? ($row['PATH'] . $row['IMG_NAME']) : '';
                $nama = isset($row['NAMA_GEDUNG']) ? $row['NAMA_GEDUNG'] : 'Ruangan';
                $kap  = isset($row['KAPASITAS']) ? $row['KAPASITAS'] : '-';
                $id   = isset($row['ID_GEDUNG']) ? $row['ID_GEDUNG'] : '';
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
                                <p
                                    class="mt-1 text-xs text-white/85
                              [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] overflow-hidden">
                                    Cocok untuk rapat, presentasi, dan kegiatan resmi.
                                </p>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">
                                    <span class="material-icons text-sm">videocam</span> Proyektor
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">
                                    <span class="material-icons text-sm">volume_up</span> Sound
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">
                                    <span class="material-icons text-sm">wifi</span> WiFi
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">
                                    <span class="material-icons text-sm">ac_unit</span> AC
                                </span>
                            </div>

                            <p
                                class="mt-4 text-sm text-slate-600
                            [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:3] overflow-hidden">
                                Ruangan nyaman dengan penataan modern, siap digunakan untuk kegiatan internal maupun
                                eksternal.
                            </p>
                        </div>

                        <div class="px-5 pb-5 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <span class="material-icons text-base">schedule</span>
                                Respons cepat
                            </div>

                            <a href="<?= site_url('home/details/' . $id); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-sky-600 text-white text-sm font-semibold
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
                <section
                    class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 backdrop-blur shadow-sm mt-12">
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute -top-24 -left-24 h-64 w-64 rounded-full bg-amber-200/35 blur-3xl"></div>
                        <div class="absolute -bottom-24 -right-24 h-64 w-64 rounded-full bg-sky-200/35 blur-3xl"></div>
                    </div>

                    <div class="relative p-6 md:p-8">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                            <div>
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-semibold">
                                    <span class="material-icons text-sm">rate_review</span>
                                    Ulasan Terbaru
                                </div>
                                <h3 class="mt-4 text-xl md:text-2xl font-extrabold tracking-tight">
                                    Spill sedikit pengalaman customer
                                </h3>
                                <p class="mt-1 text-sm text-slate-600">Biar kamu makin yakin sebelum booking.</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="hidden sm:flex flex-col items-end">
                                    <p class="text-xs text-slate-500">Rata-rata</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-lg font-extrabold"><?= number_format($ul_avg, 1); ?></p>
                                        <?= stars_html($ul_avgRounded); ?>
                                    </div>
                                    <p class="text-xs text-slate-500"><?= (int)$ul_total; ?> ulasan</p>
                                </div>

                                <a href="<?= site_url('home/ulasan'); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold
                                      hover:bg-slate-800 active:scale-[0.99] transition shadow-sm
                                      focus:outline-none focus:ring-4 focus:ring-slate-200">
                                    Lihat semua
                                    <span class="material-icons text-base">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                        <div class="mt-6">
                            <?php if (!empty($ulasan_home) && $ulasan_count > 0): ?>

                            <!-- wrapper -->
                            <div id="reviewMarquee" class="marquee-wrap overflow-hidden">
                                <!-- track: kita gandakan isi 2x supaya loop mulus -->
                                <div id="reviewTrack" class="marquee-track flex gap-6 py-1"
                                    style="animation-duration: 18s;">
                                    <?php for ($loop = 0; $loop < 2; $loop++): ?>
                                    <?php foreach ($ulasan_home as $r): ?>
                                    <?php
                                                $nm = isset($r['name']) ? $r['name'] : 'Customer';
                                                $dt_raw = isset($r['date']) ? $r['date'] : '';
                                                $dt = formatTanggalIndo($dt_raw);

                                                $rt = isset($r['rating']) ? (int)$r['rating'] : 5;
                                                if ($rt < 1) $rt = 1;
                                                if ($rt > 5) $rt = 5;

                                                $tt = isset($r['title']) ? $r['title'] : '';
                                                $cm = isset($r['comment']) ? $r['comment'] : '';

                                                $inisial = strtoupper(substr((string)$nm, 0, 1));
                                                if ($inisial === '') $inisial = 'U';

                                                $parts = parse_title_3lines($tt);
                                            ?>

                                    <!-- slide item: card kecil, bukan w-full -->
                                    <article
                                        class="w-[320px] md:w-[380px] flex-none bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div
                                                    class="h-11 w-11 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center font-extrabold text-slate-700">
                                                    <?= e($inisial); ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold truncate"><?= e($nm); ?></p>
                                                    <p class="text-xs text-slate-500"><?= e($dt); ?></p>
                                                </div>
                                            </div>

                                            <?= stars_html($rt); ?>
                                        </div>

                                        <?php if (!empty($tt)): ?>
                                        <?php if ($parts): ?>
                                        <div class="mt-3 space-y-0.5 leading-snug">
                                            <p class="text-sm font-semibold text-slate-900 truncate">
                                                <?= e($parts['nama']); ?></p>
                                            <p class="text-xs text-slate-600"><?= e($parts['tgl']); ?></p>
                                            <p class="text-xs text-slate-600"><?= e($parts['jam']); ?></p>
                                        </div>
                                        <?php else: ?>
                                        <div
                                            class="mt-3 text-sm font-semibold text-slate-900 whitespace-normal break-words">
                                            <?= e($tt); ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php endif; ?>

                                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                                            <?= e(word_limiter(strip_tags((string)$cm), 32)); ?>
                                        </p>

                                        <div class="mt-4 flex items-center justify-between">
                                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                                <span class="material-icons text-base">verified</span>
                                                Approved
                                            </div>

                                            <a href="<?= site_url('home/ulasan'); ?>"
                                                class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800">
                                                Baca
                                                <span class="material-icons text-base">arrow_forward</span>
                                            </a>
                                        </div>
                                    </article>

                                    <?php endforeach; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <?php else: ?>
                            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                                <div
                                    class="mx-auto h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <span class="material-icons text-slate-500">rate_review</span>
                                </div>
                                <h4 class="mt-4 text-lg font-bold">Belum ada ulasan</h4>
                                <p class="mt-1 text-sm text-slate-600">Ulasan akan tampil setelah ada ulasan APPROVED.
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </section>

        </div>
    </main>

    <?php $this->load->view('components/Footer'); ?>
    <!-- ===== Opsional: set kecepatan otomatis berdasarkan jumlah data ===== -->
    <script>
    (function() {
        var track = document.getElementById('reviewTrack');
        var wrap = document.getElementById('reviewMarquee');
        if (!track || !wrap) return;

        // kalau item sedikit, pelankan biar enak
        var itemCount = <?= (int)$ulasan_count; ?>;
        // base 12s, tambah 2s per item (max 40s)
        var dur = 12 + (itemCount * 2);
        if (dur > 40) dur = 40;
        track.style.animationDuration = dur + 's';
    })();
    </script>
</body>

</html>