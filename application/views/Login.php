    <!doctype html>
    <html lang="id">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Login User</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="min-h-screen text-white relative overflow-hidden
    bg-[url('<?=base_url('assets/login/login.png')?>')]
    bg-cover bg-center bg-no-repeat bg-fixed">

        <!-- Dark overlay (biar background lebih gelap) -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/10 via-black/50 to-black/70"></div>

        <!-- Metallic + glossy overlay -->
        <div class="pointer-events-none absolute -top-40 -left-40 h-[560px] w-[560px] rounded-full bg-white/20 blur-3xl">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-60">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/15 via-black/0 to-black/5"></div>

        <main class="relative min-h-screen flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <!-- Card glass -->
                <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-2xl p-8">
                    <div class="flex flex-col items-center text-center">
                        <img src="<?= base_url('assets/login/LogoTSNew.png') ?>" alt="Logo" class="h-16 w-auto mb-4" />
                        <h1 class="text-xl sm:text-2xl font-semibold text-[#D7FFF8] tracking-wide">
                            SIGN IN YOUR ACCOUNT
                        </h1>
                        <p class="mt-2 text-sm text-white/75">
                            Masuk untuk mengakses layanan <span class="font-semibold text-[#D7FFF8]">Smart Office Tiga
                                Serangkai</span>.
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
                                <input id="password" name="password" type="password" autocomplete="current-password"
                                    class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 pr-12
                                        text-white placeholder:text-white/40
                                        focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" placeholder="Masukkan password" />

                                <!-- Toggle -->
                                <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-2 my-auto h-10 w-10 grid place-items-center rounded-lg
                                        text-[#D7FFF8] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20" aria-label="Show password">
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

                        <div class="mt-4 text-center text-xs text-white/60">
                            Pastikan username & password benar.
                        </div>
                    </form>

                    <a href="<?php echo site_url('registration'); ?>" class="mt-4 relative overflow-hidden block w-full text-center rounded-xl px-4 py-3 font-semibold tracking-wide
                            text-[#D7FFF8] border border-white/20 bg-white/10
                            hover:bg-white/15 hover:border-white/30
                            focus:outline-none focus:ring-2 focus:ring-white/20">
                        <span class="relative z-10">Belum punya akun? Daftar</span>
                        <span
                            class="pointer-events-none absolute -top-10 left-0 h-24 w-full rotate-[-10deg] bg-white/10 blur-xl"></span>
                    </a>

                    <!-- footer small -->
                    <p class="mt-6 text-center text-xs text-white/55">
                        © <?= date('Y') ?> Smart Office Tiga Serangkai
                    </p>
                </div>
            </div>
        </main>

        <script>
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
        </script>
    </body>

    </html>