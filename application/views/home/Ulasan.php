<?php


// ====== Helper bintang (PHP 5 friendly) ======
function star_svg($filled)
{
    $fill = $filled ? '#F59E0B' : 'none';     // amber-500
    $stroke = $filled ? '#F59E0B' : '#94A3B8'; // slate-400
    return '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="1.5" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M11.48 3.499a.75.75 0 0 1 1.04 0l2.84 2.77c.2.195.465.303.744.303h3.93a.75.75 0 0 1 .416 1.374l-3.18 2.11a.75.75 0 0 0-.273.839l1.2 3.73a.75.75 0 0 1-1.154.83l-3.24-2.33a.75.75 0 0 0-.876 0l-3.24 2.33a.75.75 0 0 1-1.154-.83l1.2-3.73a.75.75 0 0 0-.273-.839l-3.18-2.11A.75.75 0 0 1 4.02 6.872h3.93c.279 0 .544-.108.744-.303l2.84-2.77Z" />
  </svg>';
}

function stars_html($rating)
{
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= star_svg($i <= $rating);
    }
    return $out;
}

// ====== Summary (avg & distribusi) ======
$total = count($reviews);
$sum = 0;
$dist = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);

for ($i = 0; $i < $total; $i++) {
    $rating = (int)$reviews[$i]['rating'];
    $sum += $rating;
    if (isset($dist[$rating])) $dist[$rating]++;
}
if (isset($summary) && is_array($summary)) {
    $total = (int)$summary['total'];
    $avg = (float)$summary['avg'];
    $dist = $summary['dist'];
}


$avg = $total ? round($sum / $total, 1) : 0;
$avgRounded = (int)round($avg);

$username = $this->session->userdata('username');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ulasan</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <a href="#tulis-ulasan"
                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
              bg-teal-700 text-white hover:bg-teal-800 transition">
                Tulis Ulasan
            </a>
        </div>

        <!-- Flash message -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <?php echo htmlspecialchars($this->session->flashdata('success'), ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <?php echo htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Summary -->
        <section class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-black/10 shadow-sm p-6">
                <div class="text-sm font-semibold text-slate-900">Rating Rata-rata</div>
                <div class="mt-3 flex items-center gap-3">
                    <div class="text-4xl font-extrabold text-slate-900"><?php echo number_format($avg, 1); ?></div>
                    <div>
                        <div class="flex items-center"><?php echo stars_html($avgRounded); ?></div>
                        <div class="text-sm text-slate-600 mt-1"><?php echo (int)$total; ?> ulasan</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-black/10 shadow-sm p-6 lg:col-span-2">
                <div class="text-sm font-semibold text-slate-900">Distribusi Rating</div>

                <div class="mt-4 space-y-2">
                    <?php for ($s = 5; $s >= 1; $s--): ?>
                        <?php
                        $count = isset($dist[$s]) ? $dist[$s] : 0;
                        $pct = $total ? round(($count / $total) * 100) : 0;
                        ?>
                        <div class="flex items-center gap-3">
                            <div class="w-12 text-sm text-slate-700 font-medium"><?php echo (int)$s; ?>★</div>
                            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-amber-400" style="width: <?php echo (int)$pct; ?>%;"></div>
                            </div>
                            <div class="w-12 text-right text-sm text-slate-600"><?php echo (int)$pct; ?>%</div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </section>

        <!-- List -->
        <section class="mt-6">
            <div class="bg-white rounded-2xl border border-black/10 shadow-sm">
                <div class="px-6 py-4 border-b border-black/5">
                    <div class="text-sm font-semibold text-slate-900">Ulasan Terbaru</div>
                </div>

                <div class="divide-y divide-black/5">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $r): ?>
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">
                                            <?php echo htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            <?php echo htmlspecialchars($r['date'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <?php echo stars_html((int)$r['rating']); ?>
                                    </div>
                                </div>

                                <?php if (!empty($r['title'])): ?>
                                    <div class="mt-3 text-sm font-semibold text-slate-900">
                                        <?php echo htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>

                                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($r['comment'], ENT_QUOTES, 'UTF-8')); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-6 text-sm text-slate-600">Belum ada ulasan.</div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Form -->
        <?php if (!empty($already_reviewed)): ?>
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                Kamu sudah pernah mengirim ulasan, jadi tidak bisa mengisi ulang.
            </div>
        <?php else: ?>
            <!-- section id="tulis-ulasan" (form kamu) taruh di sini -->
        <?php endif; ?>

        <section id="tulis-ulasan" class="mt-8">
            <div class="bg-white rounded-2xl border border-black/10 shadow-sm p-6">
                <div class="text-lg font-bold text-slate-900">Tulis Ulasan</div>
                <p class="mt-1 text-sm text-slate-600">Berikan rating dan pengalamanmu.</p>

                <form method="post" action="<?php echo site_url('home/submit_ulasan'); ?>" class="mt-5 space-y-4">

                    <div>
                        <label class="text-sm font-semibold text-slate-900">Rating</label>
                        <input type="hidden" name="rating" id="ratingInput" value="5">

                        <div class="mt-2 flex items-center gap-1" id="starPicker">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="button" class="star-btn" data-value="<?php echo (int)$i; ?>" aria-label="<?php echo (int)$i; ?> bintang">
                                    <?php echo star_svg(true); ?>
                                </button>
                            <?php endfor; ?>
                            <span class="ml-2 text-sm text-slate-600" id="ratingLabel">5/5</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-900">Nama Gedung</label>
                            <select name="gedung" required
                                class="mt-2 w-full rounded-xl border border-black/10 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-200">
                                <option value="">-- Pilih Gedung --</option>
                                <?php if (isset($gedungs) && is_array($gedungs)): ?>
                                    <?php foreach ($gedungs as $g): ?>
                                        <option value="<?php echo htmlspecialchars($g['NAMA_GEDUNG'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($g['NAMA_GEDUNG'], ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>


                        <div>
                            <label class="text-sm font-semibold text-slate-900">Nama</label>
                            <input type="text" name="name"
                                class="mt-2 w-full rounded-xl border border-black/10 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-200"
                                placeholder="Nama kamu"
                                value="<?php echo htmlspecialchars($username ? $username : '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-900">Komentar</label>
                        <textarea name="comment" rows="4" required
                            class="mt-2 w-full rounded-xl border border-black/10 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-teal-200"
                            placeholder="Ceritakan pengalamanmu..."></textarea>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                       bg-teal-700 text-white hover:bg-teal-800 transition">
                        Kirim Ulasan
                    </button>

                    <p class="text-xs text-slate-500">
                        Catatan: Ulasan dapat ditinjau admin terlebih dahulu sebelum tampil publik.
                    </p>
                </form>
            </div>
        </section>

    </main>

    <?php $this->load->view('components/footer'); ?>

    <script>
        var ratingInput = document.getElementById('ratingInput');
        var ratingLabel = document.getElementById('ratingLabel');
        var buttons = document.getElementsByClassName('star-btn');

        function setRating(val) {
            ratingInput.value = val;
            ratingLabel.innerHTML = val + "/5";

            for (var i = 0; i < buttons.length; i++) {
                var svg = buttons[i].getElementsByTagName('svg')[0];
                var filled = (i + 1) <= val;
                svg.setAttribute('fill', filled ? '#F59E0B' : 'none');
                svg.setAttribute('stroke', filled ? '#F59E0B' : '#94A3B8');
            }
        }

        for (var j = 0; j < buttons.length; j++) {
            (function(idx) {
                buttons[idx].addEventListener('click', function() {
                    var v = parseInt(this.getAttribute('data-value'), 10);
                    setRating(v);
                });
            })(j);
        }

        setRating(5);
    </script>
</body>

</html>