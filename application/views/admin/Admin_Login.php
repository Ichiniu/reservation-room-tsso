<?php
$this->load->helper('form');

/* warna brand */
$ts_main = '#2A7C80';
$ts_dark = '#225F62';

$errorText = isset($error) ? (string)$error : '';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons (eye icon) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
    :root {
        --ts-main: <?=$ts_main ?>;
        --ts-dark: <?=$ts_dark ?>;
    }
    </style>
</head>

<body class="min-h-screen text-slate-800 relative overflow-hidden
  bg-[url('<?= base_url('assets/images/gedung/tsu-front.png') ?>')]
  bg-cover bg-center bg-no-repeat bg-fixed">

    <!-- overlay biar background lembut -->
    <div class="absolute inset-0 bg-black/35"></div>
    <div
        class="pointer-events-none absolute -top-40 -left-40 h-[520px] w-[520px] rounded-full bg-emerald-200/25 blur-3xl">
    </div>
    <div
        class="pointer-events-none absolute -bottom-40 -right-40 h-[520px] w-[520px] rounded-full bg-sky-200/25 blur-3xl">
    </div>

    <main class="relative min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-[460px]">

            <!-- CARD -->
            <div
                class="rounded-3xl bg-white/85 backdrop-blur-xl shadow-[0_20px_60px_-20px_rgba(0,0,0,0.45)] border border-white/60 overflow-hidden">

                <!-- TOP STRIP -->
                <div class="h-2 w-full" style="background:linear-gradient(90deg,var(--ts-main),#2fb6a3,#79c6f2)"></div>

                <div class="p-8 sm:p-10">

                    <!-- LOGO + TITLE -->
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="h-16 w-16 rounded-2xl bg-white shadow-sm border border-black/5 flex items-center justify-center">
                            <img src="<?= base_url('assets/login/LogoTSNew.png') ?>" alt="Logo"
                                class="h-12 w-12 object-contain">
                        </div>

                        <h1 class="mt-5 text-xl sm:text-2xl font-extrabold tracking-wide" style="color:var(--ts-main);">
                            ADMIN SMART OFFICE
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Silakan masuk untuk melanjutkan
                        </p>
                    </div>

                    <!-- ERROR -->
                    <?php if (trim($errorText) !== ''): ?>
                    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <div class="font-semibold mb-0.5">Login gagal</div>
                        <div><?= htmlspecialchars($errorText, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <?php endif; ?>

                    <!-- FORM -->
                    <?= form_open('admin/login/is_sign_in', ['class' => 'mt-6 space-y-4']); ?>

                    <!-- USERNAME -->
                    <div>
                        <label for="username" class="block text-[11px] font-bold tracking-widest text-slate-600 mb-2">
                            USERNAME
                        </label>
                        <div class="relative">
                            <input id="username" name="username" type="text" autocomplete="username" required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm
                         outline-none transition
                         focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100"
                                placeholder="Masukkan username" />
                        </div>
                    </div>

                    <!-- PASSWORD -->
                    <div>
                        <label for="password" class="block text-[11px] font-bold tracking-widest text-slate-600 mb-2">
                            PASSWORD
                        </label>

                        <div class="relative">
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 pr-12 text-sm
                         outline-none transition
                         focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100"
                                placeholder="Masukkan password" />

                            <!-- toggle -->
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2
                         inline-flex items-center justify-center
                         h-9 w-9 rounded-xl border border-slate-200 bg-white/90
                         hover:bg-slate-50 active:scale-[0.98] transition" aria-label="Tampilkan/Sembunyikan password">
                                <span id="eyeIcon"
                                    class="material-icons text-slate-600 text-[20px]">visibility_off</span>
                            </button>
                        </div>

                        <div class="mt-2 text-xs text-slate-500">
                            Tips: Pastikan Caps Lock tidak aktif.
                        </div>
                    </div>

                    <!-- BUTTON -->
                    <button type="submit" class="w-full mt-2 rounded-2xl px-4 py-3 font-semibold text-white shadow-lg
                     hover:shadow-xl active:scale-[0.99] transition
                     focus:outline-none focus:ring-4 focus:ring-emerald-200" style="background:var(--ts-main);">
                        LOGIN
                    </button>

                    <?= form_close(); ?>

                    <!-- FOOTER MINI -->
                    <div class="mt-6 text-center text-xs text-slate-500">
                        © <?= date('Y'); ?> Tiga Serangkai Smart Office
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    (function() {
        var toggle = document.getElementById("togglePassword");
        var passField = document.getElementById("password");
        var eye = document.getElementById("eyeIcon");

        if (!toggle || !passField || !eye) return;

        toggle.addEventListener("click", function() {
            var type = passField.getAttribute("type") === "password" ? "text" : "password";
            passField.setAttribute("type", type);
            eye.textContent = (type === "password") ? "visibility_off" : "visibility";
        });
    })();
    </script>

</body>

</html>