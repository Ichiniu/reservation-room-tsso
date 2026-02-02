<?php
$username = $this->session->userdata('username');

/* ========= helper escape ========= */
if (!function_exists('e')) {
    function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

/* ========= Format tanggal Indo: 07 januari 2026 ========= */
if (!function_exists('formatTanggalIndoLower')) {
    function formatTanggalIndoLower($tgl)
    {
        $tgl = trim((string)$tgl);
        if ($tgl === '') return '-';

        $bulan = array(
            1 => 'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        );

        $ts = strtotime($tgl);
        if (!$ts) return $tgl;

        $d = date('d', $ts);
        $m = (int)date('n', $ts);
        $y = date('Y', $ts);

        return $d . ' ' . (isset($bulan[$m]) ? $bulan[$m] : '') . ' ' . $y;
    }
}

/* ========= Konversi jam 08:00 -> 08.00 ========= */
if (!function_exists('jamTitik')) {
    function jamTitik($t)
    {
        $t = trim((string)$t);
        if ($t === '') return '';
        // ambil HH:MM saja
        if (strlen($t) >= 5) $t = substr($t, 0, 5);
        return str_replace(':', '.', $t);
    }
}

/* ========= Stars pakai Material Icons (rapi) ========= */
if (!function_exists('stars_html')) {
    function stars_html($rating)
    {
        $rating = (int)$rating;
        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;

        $out = '<div class="inline-flex items-center gap-0.5">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $out .= '<span class="material-icons text-base text-amber-500 leading-none">star</span>';
            } else {
                $out .= '<span class="material-icons text-base text-slate-300 leading-none">star_border</span>';
            }
        }
        $out .= '</div>';
        return $out;
    }
}

/* ========= Ubah TITLE jadi 3 baris (nama, tanggal indo, jam wib) =========
   contoh input: "Smart Office Meeting Room - 2026-02-05 (13:00 - 16:00)"
*/
if (!function_exists('title_to_3_lines')) {
    function title_to_3_lines($title)
    {
        $title = trim((string)$title);
        if ($title === '') return '';

        if (preg_match('/^(.*?)\s-\s(\d{4}-\d{2}-\d{2})\s*\((.*?)\)\s*$/', $title, $m)) {
            $nama = trim($m[1]);
            $tgl  = formatTanggalIndoLower($m[2]);

            $jamRaw = trim($m[3]); // "13:00 - 16:00" / "08:00"
            // ubah semua HH:MM jadi HH.MM
            $jamRaw = preg_replace('/\b(\d{2}):(\d{2})\b/', '$1.$2', $jamRaw);

            // tambahin WIB kalau belum ada
            if (stripos($jamRaw, 'wib') === false) $jamRaw .= ' wib';

            return $nama . "\n" . $tgl . "\n" . $jamRaw;
        }

        // fallback: kalau bukan format di atas, tetap coba format tanggal & jam aja
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $title, $d)) {
            $title = str_replace($d[0], formatTanggalIndoLower($d[0]), $title);
        }
        $title = preg_replace('/\b(\d{2}):(\d{2})\b/', '$1.$2', $title);
        return $title;
    }
}

/* ========= Summary (avg & distribusi) ========= */
$total = 0;
$avg = 0;
$dist = array(1=>0,2=>0,3=>0,4=>0,5=>0);

if (isset($summary) && is_array($summary) && isset($summary['total'], $summary['avg'], $summary['dist'])) {
    $total = (int)$summary['total'];
    $avg   = (float)$summary['avg'];
    $dist  = $summary['dist'];
} else {
    $total = !empty($reviews) ? count($reviews) : 0;
    $sum = 0;
    if (!empty($reviews)) {
        foreach ($reviews as $rv) {
            $rating = (int)$rv['rating'];
            $sum += $rating;
            if (isset($dist[$rating])) $dist[$rating]++;
        }
    }
    $avg = $total ? round($sum / $total, 1) : 0;
}
$avgRounded = (int)round($avg);

/* ========= Hak tampil form ========= */
$reservasi_list = (isset($reservasi_list) && is_array($reservasi_list)) ? $reservasi_list : array();
$has_login = !empty($username);
$can_review = $has_login && count($reservasi_list) > 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ulasan</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- penting untuk bintang -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<?php $this->load->view('components/navbar'); ?>
<?php $this->load->view('components/header'); ?>

<body class="bg-slate-50">
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 py-8">

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ulasan Customer</h1>
                <p class="mt-1 text-sm text-slate-600">Lihat pengalaman customer lain dan tulis ulasanmu.</p>
            </div>

            <?php if ($can_review): ?>
            <a href="#tulis-ulasan" class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
               bg-teal-700 text-white hover:bg-teal-800 transition">
                Tulis Ulasan
            </a>
            <?php else: ?>
            <button type="button" disabled class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                    bg-slate-300 text-slate-600 cursor-not-allowed">
                Tulis Ulasan
            </button>
            <?php endif; ?>
        </div>

        <!-- Flash message -->
        <?php if ($this->session->flashdata('success')): ?>
        <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            <?= e($this->session->flashdata('success')); ?>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <?= e($this->session->flashdata('error')); ?>
        </div>
        <?php endif; ?>

        <!-- Summary -->
        <section class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-black/10 shadow-sm p-6">
                <div class="text-sm font-semibold text-slate-900">Rating Rata-rata</div>
                <div class="mt-3 flex items-center gap-3">
                    <div class="text-4xl font-extrabold text-slate-900"><?= number_format($avg, 1); ?></div>
                    <div>
                        <div class="flex items-center"><?= stars_html($avgRounded); ?></div>
                        <div class="text-sm text-slate-600 mt-1"><?= (int)$total; ?> ulasan</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-black/10 shadow-sm p-6 lg:col-span-2">
                <div class="text-sm font-semibold text-slate-900">Rating</div>

                <div class="mt-4 space-y-2">
                    <?php for ($s = 5; $s >= 1; $s--): ?>
                    <?php
                        $count = isset($dist[$s]) ? (int)$dist[$s] : 0;
                        $pct = $total ? round(($count / $total) * 100) : 0;
                    ?>
                    <div class="flex items-center gap-3">
                        <div class="w-12 text-sm text-slate-700 font-medium"><?= (int)$s; ?>★</div>
                        <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-amber-400" style="width: <?= (int)$pct; ?>%;"></div>
                        </div>
                        <div class="w-12 text-right text-sm text-slate-600"><?= (int)$pct; ?>%</div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </section>

        <!-- List (PER CARD) -->
        <section class="mt-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Ulasan Terbaru</h2>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $r): ?>
                <?php
                        $name = isset($r['name']) ? $r['name'] : '';
                        $rating = isset($r['rating']) ? (int)$r['rating'] : 0;

                        // ✅ paksa date tampil indo, walau dari controller masih Y-m-d
                        $date_raw = isset($r['date']) ? $r['date'] : '';
                        $date_disp = $date_raw ? formatTanggalIndoLower($date_raw) : '-';

                        $title = isset($r['title']) ? $r['title'] : '';
                        $title_3 = title_to_3_lines($title);

                        $comment = isset($r['comment']) ? $r['comment'] : '';
                        $inisial = strtoupper(substr((string)$name, 0, 1));
                        if ($inisial === '') $inisial = 'U';
                    ?>
                <article class="bg-white rounded-2xl border border-black/10 shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="h-10 w-10 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center font-extrabold text-slate-700">
                                <?= e($inisial); ?>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-900 truncate"><?= e($name); ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?= e($date_disp); ?></div>
                            </div>
                        </div>

                        <?= stars_html($rating); ?>
                    </div>

                    <?php if ($title !== ''): ?>
                    <div class="mt-3 text-sm font-semibold text-slate-900 whitespace-pre-line">
                        <?= e($title_3); ?>
                    </div>
                    <?php endif; ?>

                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        <?= nl2br(e($comment)); ?>
                    </p>
                </article>
                <?php endforeach; ?>
                <?php else: ?>
                <div
                    class="col-span-full bg-white rounded-2xl border border-black/10 shadow-sm p-6 text-sm text-slate-600">
                    Belum ada ulasan.
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Info jika tidak bisa mengulas -->
        <?php if (!$has_login): ?>
        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            Silakan login untuk menulis ulasan.
        </div>
        <?php elseif (!$can_review): ?>
        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            Tidak ada pemesanan dengan status <b>submitted</b> yang bisa diulas (atau sudah diulas semua).
        </div>
        <?php endif; ?>

        <!-- Form -->
        <?php if ($can_review): ?>
        <section id="tulis-ulasan" class="mt-8">
            <div class="bg-white rounded-2xl border border-black/10 shadow-sm p-6">
                <div class="text-lg font-bold text-slate-900">Tulis Ulasan</div>
                <p class="mt-1 text-sm text-slate-600">Pilih pemesananmu, lalu beri rating dan komentar.</p>

                <form method="post" action="<?= site_url('home/submit_ulasan'); ?>" class="mt-5 space-y-4">

                    <div>
                        <label class="text-sm font-semibold text-slate-900">Rating</label>
                        <input type="hidden" name="rating" id="ratingInput" value="5">

                        <div class="mt-2 flex items-center gap-1" id="starPicker">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button"
                                class="star-btn h-9 w-9 rounded-lg hover:bg-slate-100 flex items-center justify-center"
                                data-value="<?= (int)$i; ?>" aria-label="<?= (int)$i; ?> bintang">
                                <span
                                    class="material-icons text-xl leading-none <?= ($i <= 5 ? 'text-amber-500' : 'text-slate-300'); ?>">star</span>
                            </button>
                            <?php endfor; ?>
                            <span class="ml-2 text-sm text-slate-600" id="ratingLabel">5/5</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-900">Pemesanan</label>

                        <select id="selectPemesanan" name="id_pemesanan" required
                            class="mt-2 w-full rounded-xl border border-black/10 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-200">
                            <option value="">-- Pilih Pemesanan --</option>

                            <?php foreach ($reservasi_list as $row): ?>
                            <?php
                                    // option: 1 baris
                                    $label_one = isset($row['label']) ? $row['label'] : '';

                                    // preview: 3 baris (controller sebaiknya kirim "label_3_lines")
                                    $label_3 = isset($row['label_3_lines']) ? $row['label_3_lines'] : $label_one;
                                ?>
                            <option value="<?= (int)$row['ID_PEMESANAN']; ?>" data-preview="<?= e($label_3); ?>">
                                <?= e($label_one); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Preview 3 baris -->
                        <div id="pemesananPreview"
                            class="mt-3 rounded-xl border border-black/10 bg-slate-50 px-4 py-3 text-sm text-slate-700 whitespace-pre-line">
                            Pilih pemesanan untuk melihat detail.
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-900">Komentar</label>
                        <textarea name="comment" rows="4" required
                            class="mt-2 w-full rounded-xl border border-black/10 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-200"
                            placeholder="Ceritakan pengalamanmu..."></textarea>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                            bg-teal-700 text-white hover:bg-teal-800 transition">
                        Kirim Ulasan
                    </button>

                    <p class="text-xs text-slate-500">
                        Catatan: Ulasan dapat ditinjau terlebih dahulu sebelum submit
                    </p>
                </form>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <?php $this->load->view('components/footer'); ?>

    <script>
    /* ===== Rating Picker (Material Icons) ===== */
    (function() {
        var ratingInput = document.getElementById('ratingInput');
        var ratingLabel = document.getElementById('ratingLabel');
        var buttons = document.getElementsByClassName('star-btn');

        function setRating(val) {
            if (!ratingInput || !ratingLabel || !buttons) return;

            ratingInput.value = val;
            ratingLabel.textContent = val + "/5";

            for (var i = 0; i < buttons.length; i++) {
                var icon = buttons[i].getElementsByTagName('span')[0];
                if (!icon) continue;

                var filled = (i + 1) <= val;
                icon.textContent = filled ? 'star' : 'star_border';

                // reset class
                icon.className = 'material-icons text-xl leading-none ' + (filled ? 'text-amber-500' :
                    'text-slate-300');
            }
        }

        if (buttons && buttons.length) {
            for (var j = 0; j < buttons.length; j++) {
                (function(idx) {
                    buttons[idx].addEventListener('click', function() {
                        var v = parseInt(this.getAttribute('data-value'), 10);
                        setRating(v);
                    });
                })(j);
            }
            setRating(5);
        }
    })();

    /* ===== Preview 3 baris dropdown pemesanan ===== */
    (function() {
        var sel = document.getElementById('selectPemesanan');
        var box = document.getElementById('pemesananPreview');
        if (!sel || !box) return;

        function update() {
            var opt = sel.options[sel.selectedIndex];
            var pv = opt ? (opt.getAttribute('data-preview') || '') : '';
            box.textContent = pv ? pv : 'Pilih pemesanan untuk melihat detail.';
        }

        sel.addEventListener('change', update);
        update();
    })();
    </script>

</body>

</html>