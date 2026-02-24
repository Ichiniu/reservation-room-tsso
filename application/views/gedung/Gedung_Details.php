<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);

function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detail Ruangan</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="min-h-screen text-slate-900 bg-gradient-to-b from-emerald-50 via-slate-50 to-sky-50">
    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <main class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <?php if (!empty($result) && is_array($result)): ?>
                <?php foreach ($result as $row): ?>
                    <?php
                    $harga_gedung = isset($row['HARGA_SEWA']) ? (int)$row['HARGA_SEWA'] : 0;
                    $nama = isset($row['NAMA_GEDUNG']) ? $row['NAMA_GEDUNG'] : 'Ruangan';
                    $kapasitas = isset($row['KAPASITAS']) ? $row['KAPASITAS'] : '-';
                    $alamat = isset($row['ALAMAT']) ? $row['ALAMAT'] : '-';
                    $deskripsi = isset($row['DESKRIPSI_GEDUNG']) ? $row['DESKRIPSI_GEDUNG'] : '';
                    $fasilitas = isset($row['fasilitas']) ? $row['fasilitas'] : '';
                    ?>

                    <!-- HERO -->
                    <section
                        class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 backdrop-blur shadow-xl">

                        <!-- soft glow -->
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="absolute -top-28 -right-28 h-72 w-72 rounded-full bg-emerald-200/35 blur-3xl"></div>
                            <div class="absolute -bottom-28 -left-28 h-72 w-72 rounded-full bg-sky-200/35 blur-3xl"></div>
                        </div>

                        <!-- TOP CONTENT -->
                        <div class="relative p-6 sm:p-8">
                            <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold
                                        bg-emerald-50 text-emerald-800 border border-emerald-100">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Informasi Ruangan
                            </div>

                            <h1 class="mt-4 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                                <?php echo e($nama); ?>
                            </h1>

                            <div class="mt-3 text-sm text-slate-700 leading-relaxed">
                                <?php if (trim($deskripsi) !== ''): ?>
                                    <div class="max-h-40 overflow-auto pr-2 rounded-2xl border border-slate-200 bg-white/70 p-4">
                                        <?php echo nl2br(e($deskripsi)); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-slate-600">
                                        Deskripsi belum tersedia.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                <!-- harga -->
                                <div
                                    class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                                    <span class="material-icons text-emerald-600 text-base">payments</span>
                                    <div class="leading-tight">
                                        <div class="text-xs text-slate-500">Harga Sewa</div>
                                        <div class="text-sm font-extrabold text-slate-900">
                                            Disesuaikan dengan jumlah peserta
                                        </div>
                                    </div>
                                </div>

                                <!-- kapasitas -->
                                <div
                                    class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                                    <span class="material-icons text-sky-600 text-base">groups</span>
                                    <div class="leading-tight">
                                        <div class="text-xs text-slate-500">Kapasitas</div>
                                        <div class="text-sm font-extrabold text-slate-900">
                                            <?php echo e($kapasitas); ?> Orang
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="h-px bg-gradient-to-r from-white/0 via-slate-200 to-white/0"></div>

                        <!-- DETAILS GRID -->
                        <div class="relative p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- alamat -->
                            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-emerald-600">location_on</span>
                                    <h2 class="text-sm font-semibold tracking-widest text-slate-800">ALAMAT</h2>
                                </div>
                                <div class="mt-3 text-sm text-slate-700 leading-relaxed max-h-40 overflow-auto pr-2">
                                    <?php echo nl2br(e($alamat)); ?>
                                </div>
                            </div>

                            <!-- fasilitas -->
                            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-sky-600">checklist</span>
                                    <h2 class="text-sm font-semibold tracking-widest text-slate-800">FASILITAS</h2>
                                </div>
                                <div class="mt-3 text-sm text-slate-700 leading-relaxed max-h-40 overflow-auto pr-2">
                                    <?php
                                    if (trim($fasilitas) !== '') echo nl2br(e($fasilitas));
                                    else echo '<span class="text-slate-500">Fasilitas belum diinput.</span>';
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER BUTTON (KANAN BAWAH CARD, TANPA TABRAKAN) -->
                        <div class="relative px-6 sm:px-8 pb-6 sm:pb-8 flex justify-end">
                            <a href="<?php echo site_url('home/order-gedung/' . $id_gedung . ''); ?>" class="group inline-flex w-full sm:w-auto items-center justify-center gap-2
                                      rounded-2xl px-5 py-3 font-semibold text-white
                                      bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] transition shadow-lg
                                      focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                <span class="material-icons text-base opacity-95">event_available</span>
                                <span>Ajukan Pesanan</span>
                                <span
                                    class="material-icons text-base transition group-hover:translate-x-0.5">arrow_forward</span>
                            </a>
                        </div>

                    </section>
                <?php endforeach; ?>
            <?php else: ?>
                <section class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <div class="mx-auto h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <span class="material-icons text-slate-500">info</span>
                    </div>
                    <h2 class="mt-4 text-lg font-bold">Data ruangan tidak ditemukan</h2>
                    <p class="mt-1 text-sm text-slate-600">Silakan kembali dan pilih ruangan lain.</p>
                </section>
            <?php endif; ?>

            <!-- GALLERY -->
            <section class="rounded-3xl border border-slate-200 bg-white/70 backdrop-blur shadow-xl overflow-hidden">
                <div class="p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Gallery Ruangan</h2>
                        <p class="mt-1 text-sm text-slate-600">Geser foto untuk melihat lebih banyak tampilan
                            ruangan/gedung.</p>
                    </div>

                    <div class="hidden sm:flex items-center gap-2">
                        <button id="prevBtn"
                            class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 bg-white text-slate-800 border border-slate-200
                                   hover:bg-slate-50 active:scale-[0.99] transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <span class="material-icons text-base text-slate-700">chevron_left</span>
                            Prev
                        </button>
                        <button id="nextBtn"
                            class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 bg-emerald-600 text-white
                                   hover:bg-emerald-700 active:scale-[0.99] transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Next
                            <span class="material-icons text-base">chevron_right</span>
                        </button>
                    </div>
                </div>

                <div class="px-6 sm:px-8 pb-8">
                    <div class="relative rounded-3xl overflow-hidden border border-slate-200 bg-slate-100">
                        <div id="sliderTrack" class="flex transition-transform duration-500 ease-out">
                            <?php if (!empty($gallery)): ?>
                                <?php foreach ($gallery as $images): ?>
                                    <?php
                                    $path = isset($images['PATH']) ? $images['PATH'] : '';
                                    $img  = isset($images['IMG_NAME']) ? $images['IMG_NAME'] : '';
                                    $raw_src = $path . $img;
                                    // Normalisasi: hapus domain lama lalu tambahkan base_url()
                                    $clean_src = preg_replace('#^https?://[^/]+/bookingsmarts/#i', '', $raw_src);
                                    $src = base_url($clean_src);
                                    ?>
                                    <div class="min-w-full">
                                        <img src="<?php echo e($src); ?>" alt="Gallery"
                                            class="h-[260px] sm:h-[380px] w-full object-cover" loading="lazy" />
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="min-w-full flex items-center justify-center h-[260px] sm:h-[380px]">
                                    <p class="text-slate-500 text-sm">Belum ada foto untuk Ruangan ini.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- mobile controls -->
                        <button id="prevBtnMobile"
                            class="sm:hidden absolute left-3 top-1/2 -translate-y-1/2 rounded-full h-10 w-10
                                   bg-white/70 text-slate-900 border border-slate-200 hover:bg-white transition">‹</button>
                        <button id="nextBtnMobile"
                            class="sm:hidden absolute right-3 top-1/2 -translate-y-1/2 rounded-full h-10 w-10
                                   bg-white/70 text-slate-900 border border-slate-200 hover:bg-white transition">›</button>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <?php $this->load->view('components/footer'); ?>

    <script>
        (function() {
            var track = document.getElementById('sliderTrack');
            if (!track) return;

            var slides = track.children.length;
            if (slides <= 1) return;

            var index = 0;

            function go(to) {
                index = (to + slides) % slides;
                track.style.transform = 'translateX(' + (-index * 100) + '%)';
            }

            var prev = document.getElementById('prevBtn');
            var next = document.getElementById('nextBtn');
            var prevM = document.getElementById('prevBtnMobile');
            var nextM = document.getElementById('nextBtnMobile');

            if (prev) prev.addEventListener('click', function() {
                go(index - 1);
            });
            if (next) next.addEventListener('click', function() {
                go(index + 1);
            });
            if (prevM) prevM.addEventListener('click', function() {
                go(index - 1);
            });
            if (nextM) nextM.addEventListener('click', function() {
                go(index + 1);
            });

            var startX = null;
            track.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
            }, {
                passive: true
            });

            track.addEventListener('touchend', function(e) {
                if (startX === null) return;
                var endX = e.changedTouches[0].clientX;
                var dx = endX - startX;
                if (Math.abs(dx) > 40) go(index + (dx < 0 ? 1 : -1));
                startX = null;
            });
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var profileToggle = document.getElementById('profileToggle');
            var profileMenu = document.getElementById('profileMenu');

            if (profileToggle && profileMenu) {
                profileToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (profileMenu.classList.contains('hidden')) profileMenu.classList.remove('hidden');
                    else profileMenu.classList.add('hidden');
                });

                document.addEventListener('click', function() {
                    if (!profileMenu.classList.contains('hidden')) profileMenu.classList.add('hidden');
                });

                profileMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    </script>

</body>

</html>