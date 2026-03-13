<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lengkapi Profil - Booking Smarts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body class="min-h-screen overflow-y-auto text-white relative bg-slate-900">

    <!-- Background -->
    <div class="fixed inset-0 -z-10 bg-[url('<?= base_url('assets/login/gbr_lgn3.png') ?>')] bg-cover bg-no-repeat bg-[position:50%_35%]"></div>
    <div class="fixed inset-0 -z-10 bg-black/40 backdrop-blur-sm"></div>

    <main class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-lg">
            <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-2xl p-8">

                <div class="text-center mb-8">
                    <img src="<?= base_url('assets/login/logo-since.png') ?>" class="h-14 mx-auto mb-4" />
                    <h1 class="text-2xl font-bold text-[#D7FFF8]">Satu Langkah Lagi!</h1>
                    <p class="text-sm text-white/70">Halo <?= htmlspecialchars($user->NAMA_LENGKAP) ?>, mohon lengkapi data profil Anda untuk melanjutkan.</p>
                </div>

                <form action="<?= site_url('auth/save_profile') ?>" method="POST" class="space-y-4">
                    
                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-1.5 ml-1">Pilih Username</label>
                        <div class="relative group">
                            <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-white/40 group-focus-within:text-[#2CC7C0] transition-colors"></i>
                            <input type="text" name="username" required value="<?= htmlspecialchars($user->USERNAME) ?>"
                                class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-4 text-sm text-white focus:border-[#2CC7C0]/50 focus:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#2CC7C0]/20 transition-all placeholder:text-white/20"
                                placeholder="Username unik Anda">
                        </div>
                    </div>

                    <!-- No Telepon -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-1.5 ml-1">No Telepon / WhatsApp</label>
                        <div class="relative group">
                            <i class="bi bi-whatsapp absolute left-4 top-1/2 -translate-y-1/2 text-white/40 group-focus-within:text-[#2CC7C0] transition-colors"></i>
                            <input type="number" name="no_telepon" required
                                class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-4 text-sm text-white focus:border-[#2CC7C0]/50 focus:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#2CC7C0]/20 transition-all placeholder:text-white/20"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <!-- DOB -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-1.5 ml-1">Tanggal Lahir</label>
                        <div class="relative group">
                            <i class="bi bi-calendar-event absolute left-4 top-1/2 -translate-y-1/2 text-white/40 group-focus-within:text-[#2CC7C0] transition-colors"></i>
                            <input type="date" name="dob" required
                                class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-4 text-sm text-white focus:border-[#2CC7C0]/50 focus:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#2CC7C0]/20 transition-all">
                        </div>
                    </div>

                    <!-- Perusahaan Radio -->
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative group cursor-pointer">
                            <input type="radio" name="perusahaan" value="INTERNAL" class="peer sr-only" required>
                            <div class="flex flex-col items-center justify-center p-4 rounded-xl border border-white/10 bg-white/5 text-white/60 transition-all peer-checked:border-[#2CC7C0]/50 peer-checked:bg-[#2CC7C0]/10 peer-checked:text-[#D7FFF8]">
                                <i class="bi bi-building mb-1 text-xl"></i>
                                <span class="text-xs font-semibold">INTERNAL</span>
                            </div>
                        </label>
                        <label class="relative group cursor-pointer">
                            <input type="radio" name="perusahaan" value="EKSTERNAL" class="peer sr-only">
                            <div class="flex flex-col items-center justify-center p-4 rounded-xl border border-white/10 bg-white/5 text-white/60 transition-all peer-checked:border-[#2CC7C0]/50 peer-checked:bg-[#2CC7C0]/10 peer-checked:text-[#D7FFF8]">
                                <i class="bi bi-globe2 mb-1 text-xl"></i>
                                <span class="text-xs font-semibold">EKSTERNAL</span>
                            </div>
                        </label>
                    </div>

                    <!-- Dynamic Fields -->
                    <div id="internalFields" class="hidden space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-1.5 ml-1">Departemen</label>
                            <select name="departemen" class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white focus:border-[#2CC7C0]/50 focus:bg-white/10 focus:outline-none transition-all">
                                <option value="" class="bg-slate-800">Pilih Departemen</option>
                                <option value="IDE" class="bg-slate-800">IDE</option>
                                <option value="PRODUKSI" class="bg-slate-800">PRODUKSI</option>
                                <option value="KEUANGAN" class="bg-slate-800">KEUANGAN</option>
                            </select>
                        </div>
                    </div>

                    <div id="externalFields" class="hidden space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-1.5 ml-1">Nama Perusahaan / Institusi</label>
                            <input type="text" name="nama_perusahaan" class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white focus:border-[#2CC7C0]/50 focus:bg-white/10 focus:outline-none transition-all" placeholder="Contoh: Universitas Gadjah Mada">
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-1.5 ml-1">Alamat Lengkap</label>
                        <textarea name="alamat" required rows="2"
                            class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white focus:border-[#2CC7C0]/50 focus:bg-white/10 focus:outline-none transition-all"
                            placeholder="Alamat domisili saat ini"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full rounded-xl py-3.5 mt-4 font-bold tracking-wide text-[#071A1A]
                        bg-gradient-to-br from-[#D7FFF8] via-[#2CC7C0] to-[#0A7F81]
                        shadow-lg shadow-black/25 hover:brightness-105 active:scale-[0.98] transition-all">
                        SIMPAN & LANJUTKAN
                    </button>

                </form>

            </div>
            <p class="mt-8 text-center text-xs text-white/40 tracking-widest uppercase">&copy; <?= date('Y') ?> Smart Office Tiga Serangkai</p>
        </div>
    </main>

    <script>
        // Logic show/hide internal/external fields
        const radios = document.querySelectorAll('input[name="perusahaan"]');
        const internal = document.getElementById('internalFields');
        const external = document.getElementById('externalFields');

        radios.forEach(r => {
            r.addEventListener('change', (e) => {
                if(e.target.value === 'INTERNAL') {
                    internal.classList.remove('hidden');
                    external.classList.add('hidden');
                } else {
                    internal.classList.add('hidden');
                    external.classList.remove('hidden');
                }
            });
        });

        /* ===== Toast from Flash Session ===== */
        <?php $flash_msg = $this->session->flashdata('flash_msg'); $flash_type = $this->session->flashdata('flash_type'); if ($flash_msg): ?>
        Toastify({
            text      : "<?= $flash_msg ?>",
            duration  : 4000,
            style     : { background: "<?= ($flash_type == 'success') ? 'linear-gradient(135deg,#0A7F81,#2CC7C0)' : 'linear-gradient(135deg,#7f1d1d,#dc2626)' ?>" },
        }).showToast();
        <?php endif; ?>
    </script>
</body>
</html>
