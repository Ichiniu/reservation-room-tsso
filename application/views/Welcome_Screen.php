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
</head>
<body class="min-h-screen text-black
  bg-slate-200">

<!-- ================= NAVBAR ================= -->
<header class="bg-white border-b border-black/5 sticky top-0 z-30">
  <div class="max-w-7xl mx-auto px-10">
    <div class="flex items-center justify-between h-16">

      <!-- ===== BRAND ===== -->
      <div class="flex items-center gap-7">
        <div class="h-9 w-9 rounded-lg bg-white border border-black/10 flex items-center justify-center">
          <img src="<?= base_url('assets/login/LogoTSNew.png'); ?>"
               class="h-10 w-10 object-contain"
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

      <!-- ===== MENU (TEXT + ICON SAJA) ===== -->
      <nav class="hidden md:flex items-center gap-5 mx-5 text-[11px] font-semibold tracking-widest text-slate-700">
        <a href="<?= site_url('home'); ?>" class="flex items-center gap-2 hover:text-slate-900 transition">
          <i class="bi bi-house-door text-base"></i>
          HOME
        </a>
        <a href="<?= site_url('home/jadwal'); ?>" class="flex items-center gap-2 hover:text-slate-900 transition">
          <i class="bi bi-calendar-week text-base"></i>
          JADWAL
        </a>
        <a href="<?= site_url('home/pemesanan'); ?>" class="flex items-center gap-2 hover:text-slate-900 transition">
          <i class="bi bi-journal-text text-base"></i>
          PEMESANAN
        </a>
        <a href="<?= site_url('home/view-catering'); ?>" class="flex items-center gap-2 hover:text-slate-900 transition">
          <i class="bi bi-cup-hot text-base"></i>
          CATERING
        </a>
        <a href="<?= site_url('home/pembayaran'); ?>" class="flex items-center gap-2 hover:text-slate-900 transition">
          <i class="bi bi-credit-card text-base"></i>
          TRANSAKSI
        </a>
      </nav>

      <!-- ===== BUTTON LOGIN ===== -->
      <a href="<?= site_url('login'); ?>"
         class="inline-flex items-center gap-2 rounded-full px-4 py-2
                bg-slate-900/40 ring-1 ring-black/10 text-white hover:bg-slate-900/60 transition">
        <i class="bi bi-box-arrow-in-right"></i>
        <span class="text-sm font-semibold">Login</span>
      </a>

    </div>
  </div>
</header>

<!-- ================= MAIN CONTENT ================= -->
<main class="py-8 sm:py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Contoh konten / hero -->
        <section class="mb-10">
            <h2 class="text-xl sm:text-2xl font-semibold text-slate-800 mb-2">Pilih Ruang Meeting</h2>
            <p class="text-sm text-slate-600">Jelajahi ruang meeting yang tersedia di Smart Office Tiga Serangkai.</p>
        </section>

        <!-- Grid kartu gedung -->
        <section class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <?php foreach ($res as $row): 
                $path = $row['PATH']; 
                $img_name = $row['IMG_NAME']; 
            ?>
            <article class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                <div class="relative">
                    <img src="<?= $path . $img_name; ?>" 
                         alt="<?= htmlspecialchars($row['NAMA_GEDUNG']); ?>" 
                         class="h-52 sm:h-60 w-full object-cover">
                    <div class="absolute bottom-2 right-2 inline-flex items-center px-2 py-1 rounded-full bg-black/60">
                        <span class="text-[11px] font-medium text-slate-50"><?= $row['KAPASITAS']; ?> Orang</span>
                    </div>
                </div>
                <div class="flex-1 flex flex-col p-4 space-y-2">
                    <h3 class="text-base font-semibold text-slate-900"><?= $row['NAMA_GEDUNG']; ?></h3>
                    <p class="text-sm text-slate-500">
                        Kapasitas hingga <span class="font-semibold text-slate-800"><?= $row['KAPASITAS']; ?> orang</span>
                        &mdash; cocok untuk meeting tim, presentasi, maupun acara internal.
                    </p>
                    <div class="pt-3 mt-auto">
                        <a href="<?= site_url('login'); ?>" class="inline-flex items-center text-sm font-medium text-sky-600 hover:text-sky-700">
                            Details
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 ml-1">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L13.586 11H4a1 1 0 110-2h9.586l-3.293-3.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </section>

        <?php if (empty($res)): ?>
        <div class="mt-8 text-center text-sm text-slate-500">
            Belum ada data gedung yang tersedia.
        </div>
        <?php endif; ?>
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
