<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detail Gedung</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
    /* Biar konsisten nuansa */
    :root {
        --teal: #14B8A6;
        --teal-soft: #D7FFF8;
    }

    /* Optional: kalau deskripsi panjang, scroll hanya di dalam card */
    .content-scroll {
        max-height: 160px;
        /* ubah sesuai kebutuhan */
        overflow: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Scrollbar halus */
    .content-scroll::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .content-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.35);
        border-radius: 999px;
    }

    .content-scroll::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 999px;
    }
    </style>
</head>

<body class="min-h-screen text-slate-900 bg-slate-200">

    <!-- HEADER + NAVBAR -->
    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>
    <!-- /HEADER + NAVBAR -->

    <main class="relative">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-6">

            <?php foreach($result as $row): $harga_gedung = $row['HARGA_SEWA']; ?>

            <!-- HERO + CTA -->
            <section class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                <div class="p-6 sm:p-8 flex flex-col lg:flex-row gap-6 lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div
                            class="inline-flex items-center gap-2 rounded-full bg-teal-50 border border-teal-100 px-3 py-1 text-xs text-teal-800">
                            <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                            Informasi Gedung
                        </div>

                        <h1 class="mt-4 text-2xl sm:text-3xl font-semibold text-slate-900 tracking-wide">
                            <?php echo $row['NAMA_GEDUNG']; ?>
                        </h1>

                        <p class="mt-2 text-sm text-slate-700 max-w-2xl text-justify hyphens-auto">
                            Ruang ini menawarkan set studio yang rapi dan berkelas—perpaduan panel akustik bernuansa
                            hangat, aksen kayu modern,
                            serta dekorasi bunga yang memberi kesan premium di setiap frame. Dengan pencahayaan
                            profesional (LED panel/soft light)
                            dan area pengambilan gambar yang lapang, tempat ini siap untuk produksi konten seperti
                            podcast, interview, video branding,
                            hingga live streaming dengan tampilan visual yang clean dan elegan.
                        </p>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <div class="inline-flex items-center gap-2 rounded-2xl bg-teal-600 px-4 py-3 shadow-sm">
                                <span class="text-xs text-white/90">Harga Sewa</span>
                                <span class="text-sm font-semibold text-white">
                                    <?php echo "Rp. " . number_format($harga_gedung); ?>
                                </span>
                            </div>

                            <div class="inline-flex items-center gap-2 rounded-2xl bg-teal-600 px-4 py-3 shadow-sm">
                                <span class="text-xs text-white/90">Kapasitas</span>
                                <span class="text-sm font-semibold text-white">
                                    <?php echo $row['KAPASITAS']. " Orang"; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="<?php echo site_url('home/order-gedung/'.$id_gedung.''); ?>"
                            class="relative overflow-hidden inline-flex items-center justify-center rounded-2xl px-5 py-3 font-semibold
                      text-white bg-teal-700 hover:bg-teal-800 transition shadow-lg focus:outline-none focus:ring-2 focus:ring-teal-200">
                            <span class="relative z-10">Ajukan Pesanan</span>
                            <span
                                class="pointer-events-none absolute -top-10 left-0 h-24 w-full rotate-[-10deg] bg-white/25 blur-xl"></span>
                        </a>
                    </div>
                </div>

                <!-- divider -->
                <div class="h-px bg-gradient-to-r from-white/0 via-slate-200 to-white/0"></div>

                <!-- DETAILS GRID -->
                <div class="p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-teal-600 p-5 shadow-sm">
                        <h2 class="text-sm font-semibold tracking-widest text-teal-50">ALAMAT</h2>
                        <div class="mt-2 text-sm text-white leading-relaxed content-scroll pr-2">
                            <?php echo $row['ALAMAT']; ?>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-teal-600 p-5 shadow-sm">
                        <h2 class="text-sm font-semibold tracking-widest text-teal-50">FASILITAS</h2>
                        <div class="mt-2 text-sm text-white leading-relaxed content-scroll pr-2">
                            <?php echo $row['DESKRIPSI_GEDUNG']; ?>
                        </div>
                    </div>
                </div>
            </section>

            <?php endforeach; ?>

            <!-- GALLERY -->
            <section class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                <div class="p-6 sm:p-8 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Gallery Gedung</h2>
                        <p class="mt-1 text-sm text-slate-600">Geser foto untuk melihat lebih banyak tampilan
                            ruangan/gedung.</p>
                    </div>

                    <div class="hidden sm:flex items-center gap-2">
                        <button id="prevBtn"
                            class="rounded-xl px-3 py-2 bg-slate-900 text-white hover:bg-slate-800 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Prev
                        </button>
                        <button id="nextBtn"
                            class="rounded-xl px-3 py-2 bg-slate-900 text-white hover:bg-slate-800 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Next
                        </button>
                    </div>
                </div>

                <div class="px-6 sm:px-8 pb-8">
                    <div class="relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-100">
                        <!-- slider -->
                        <div id="sliderTrack" class="flex transition-transform duration-500 ease-out">
                            <?php if(!empty($gallery)): ?>
                            <?php foreach($gallery as $images):
                  $path = $images['PATH']; $img = $images['IMG_NAME']; ?>
                            <div class="min-w-full">
                                <img src="<?php echo $path . $img; ?>" alt="Gallery"
                                    class="h-[260px] sm:h-[360px] w-full object-cover" loading="lazy" />
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <div class="min-w-full flex items-center justify-center h-[260px] sm:h-[360px]">
                                <p class="text-slate-500 text-sm">Belum ada foto untuk gedung ini.</p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- overlay controls (mobile) -->
                        <button id="prevBtnMobile" class="sm:hidden absolute left-3 top-1/2 -translate-y-1/2 rounded-full h-10 w-10
                     bg-black/40 text-white hover:bg-black/55 transition">
                            ‹
                        </button>
                        <button id="nextBtnMobile" class="sm:hidden absolute right-3 top-1/2 -translate-y-1/2 rounded-full h-10 w-10
                     bg-black/40 text-white hover:bg-black/55 transition">
                            ›
                        </button>
                    </div>
                </div>
            </section>

        </div>

        <!-- FOOTER (samakan case folder/view: footer) -->
        <?php $this->load->view('components/footer'); ?>
    </main>

    <script>
    // simple slider (no dependencies)
    (function() {
        const track = document.getElementById('sliderTrack');
        if (!track) return;

        const slides = track.children.length;
        if (slides <= 1) return;

        let index = 0;

        function go(to) {
            index = (to + slides) % slides;
            track.style.transform = `translateX(${-index * 100}%)`;
        }

        const prev = document.getElementById('prevBtn');
        const next = document.getElementById('nextBtn');
        const prevM = document.getElementById('prevBtnMobile');
        const nextM = document.getElementById('nextBtnMobile');

        prev && prev.addEventListener('click', () => go(index - 1));
        next && next.addEventListener('click', () => go(index + 1));
        prevM && prevM.addEventListener('click', () => go(index - 1));
        nextM && nextM.addEventListener('click', () => go(index + 1));

        // swipe support (mobile)
        let startX = null;
        track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, {
            passive: true
        });
        track.addEventListener('touchend', (e) => {
            if (startX === null) return;
            const endX = e.changedTouches[0].clientX;
            const dx = endX - startX;
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
                profileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function() {
                profileMenu.classList.add('hidden');
            });

            profileMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
    );
    </script>

</body>

</html>