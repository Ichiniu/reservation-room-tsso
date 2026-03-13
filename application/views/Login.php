<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <style>
        .toastify {
            font-family: ui-sans-serif, system-ui, sans-serif;
            border-radius: 12px !important;
            padding: 12px 20px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25) !important;
        }
    </style>
</head>

<body class="min-h-screen w-full text-white relative overflow-hidden
  bg-[url('<?= base_url('assets/login/gbr_lgn1.png') ?>')]
  bg-cover bg-no-repeat bg-[position:50%_35%]">

    <!-- Dark overlay (biar background lebih gelap & card kebaca) -->
    <div class="pointer-events-none absolute inset-0 bg-black/15"></div>

    <!-- Metallic + glossy overlay -->
    <div class="pointer-events-none absolute -top-40 -left-40 h-[560px] w-[560px] rounded-full bg-white/20 blur-3xl">
    </div>
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-60">
    </div>
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-black/0 to-black/10"></div>

    <main class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <!-- Card glass -->
            <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-2xl p-8">
                <div class="flex flex-col items-center text-center">
                    <img src="<?= base_url('assets/login/Logo-since.png') ?>" alt="Logo" class="h-16 w-auto mb-4" />
                    <h1 class="text-xl sm:text-2xl font-semibold text-[#D7FFF8] tracking-wide">
                        SIGN IN YOUR ACCOUNT
                    </h1>
                    <p class="mt-2 text-sm text-white/75">
                        Masuk untuk mengakses layanan
                        <span class="font-semibold text-[#D7FFF8]">Smart Office Tiga Serangkai</span>.
                    </p>
                </div>

                <!-- Form -->
                <form action="<?= site_url('/validate') ?>" method="post" class="mt-8 space-y-4">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-xs font-semibold tracking-widest text-white/80">
                            USERNAME
                        </label>
                        <input id="username" name="username" type="text" autocomplete="username" class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3
                text-white placeholder:text-white/40
                focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40"
                            placeholder="Masukkan username" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold tracking-widest text-white/80">
                            PASSWORD
                        </label>

                        <div class="relative mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password" class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 pr-12
                  text-white placeholder:text-white/40
                  focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40"
                                placeholder="Masukkan password" />

                            <!-- Toggle -->
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-2 my-auto h-10 w-10 grid place-items-center rounded-lg
                  text-[#D7FFF8] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20"
                                aria-label="Show password">
                                <!-- icon sederhana -->
                                <svg id="eyeOpen" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eyeClosed" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M3 3l18 18"></path>
                                    <path d="M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42"></path>
                                    <path
                                        d="M9.88 5.09A10.94 10.94 0 0 1 12 5c6.5 0 10 7 10 7a18.4 18.4 0 0 1-3.17 4.36">
                                    </path>
                                    <path d="M6.11 6.11A18.4 18.4 0 0 0 2 12s3.5 7 10 7a10.94 10.94 0 0 0 2.12-.09">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Button metallic glossy -->
                    <button type="submit" class="relative w-full overflow-hidden rounded-xl px-4 py-3 font-semibold tracking-wide
              text-[#071A1A]
              bg-gradient-to-br from-[#D7FFF8] via-[#2CC7C0] to-[#0A7F81]
              shadow-lg shadow-black/25
              hover:brightness-105 active:brightness-95
              focus:outline-none focus:ring-2 focus:ring-white/30">
                        <span class="relative z-10">Login</span>

                        <!-- glossy highlight -->
                        <span class="pointer-events-none absolute -top-10 left-0 h-24 w-full rotate-[-10deg]
              bg-white/30 blur-xl"></span>
                    </button>

                    <!-- OR Divider -->
                    <div class="my-6 flex items-center gap-4">
                        <div class="h-px flex-1 bg-white/10"></div>
                        <span class="text-[10px] font-bold text-white/30 uppercase tracking-widest">Atau masuk dengan</span>
                        <div class="h-px flex-1 bg-white/10"></div>
                    </div>

                    <!-- Google Login Button -->
                    <a href="<?= site_url('auth/google_login') ?>" 
                       class="flex h-12 w-full items-center justify-center gap-3 rounded-xl border border-white/15 bg-white/5 font-semibold text-white transition-all hover:bg-white/10 hover:border-white/30 active:scale-[0.98]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z"/>
                        </svg>
                        Google
                    </a>

                    <div class="mt-4 text-center text-xs text-white/60">
                        Pastikan username &amp; password benar.
                    </div>

                </form>

                <?php $unverified_email = $this->session->flashdata('unverified_email'); ?>
                <?php if (!empty($unverified_email)): ?>
                <!-- Resend Verification Email -->
                <div class="mt-4 rounded-xl border border-amber-400/30 bg-amber-500/10 p-4">
                    <div class="flex items-center gap-2 text-amber-300 text-xs font-semibold mb-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Akun belum diverifikasi</span>
                    </div>
                    <p class="text-xs text-white/60 mb-3">
                        Tidak menerima email? Klik tombol di bawah untuk kirim ulang.
                    </p>
                    <form action="<?= site_url('registration/resend_verification') ?>" method="post">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($unverified_email, ENT_QUOTES, 'UTF-8') ?>" />
                        <button type="submit"
                            class="w-full rounded-lg py-2 text-xs font-semibold tracking-wide
                            text-amber-900 bg-gradient-to-r from-amber-300 to-amber-400
                            hover:brightness-110 active:brightness-95
                            focus:outline-none focus:ring-2 focus:ring-amber-300/40">
                            <i class="bi bi-envelope-arrow-up mr-1"></i> Kirim Ulang Email Verifikasi
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <a href="<?= site_url('registration'); ?>" class="mt-4 relative overflow-hidden block w-full text-center rounded-xl px-4 py-3 font-semibold tracking-wide
            text-[#D7FFF8] border border-white/20 bg-white/10
            hover:bg-white/15 hover:border-white/30
            focus:outline-none focus:ring-2 focus:ring-white/20">
                    <span class="relative z-10">Belum punya akun? Daftar</span>
                    <span
                        class="pointer-events-none absolute -top-10 left-0 h-24 w-full rotate-[-10deg] bg-white/10 blur-xl">
                    </span>
                </a>


                <!-- footer small -->
                <p class="mt-6 text-center text-xs text-white/55">
                    © <?= date('Y') ?> Smart Office Tiga Serangkai
                </p>
            </div>
        </div>
    </main>

    <script>
    /* ===== Toggle Password ===== */
    const btn = document.getElementById("togglePassword");
    const pass = document.getElementById("password");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    btn.addEventListener("click", () => {
        const isPassword = pass.type === "password";
        pass.type = isPassword ? "text" : "password";
        eyeOpen.classList.toggle("hidden", !isPassword);
        eyeClosed.classList.toggle("hidden", isPassword);
    });

    /* ===== Toast from Flash Session ===== */
    <?php
        $flash_msg  = $this->session->flashdata('flash_msg');
        $flash_type = $this->session->flashdata('flash_type');
        if ($flash_msg):
    ?>
    (function() {
        const msg  = <?= json_encode($flash_msg) ?>;
        const type = <?= json_encode($flash_type ?? 'info') ?>;

        const colors = {
            success : { bg: 'linear-gradient(135deg,#0A7F81,#2CC7C0)', icon: '✅' },
            error   : { bg: 'linear-gradient(135deg,#7f1d1d,#dc2626)',  icon: '❌' },
            info    : { bg: 'linear-gradient(135deg,#1e3a5f,#3b82f6)',  icon: 'ℹ️' },
        };
        const cfg = colors[type] || colors.info;

        Toastify({
            text      : cfg.icon + '  ' + msg,
            duration  : 4000,
            gravity   : 'top',
            position  : 'right',
            stopOnFocus: true,
            style     : { background: cfg.bg },
        }).showToast();
    })();
    <?php endif; ?>
    </script>

</body>

</html>