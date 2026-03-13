<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Verifikasi Email</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="min-h-screen overflow-y-auto text-white relative overflow-x-hidden bg-slate-900">

    <!-- Background -->
    <div class="fixed inset-0 -z-10
        bg-[url('<?= base_url('assets/login/gbr_lgn3.png') ?>')]
        bg-cover bg-no-repeat bg-[position:50%_35%]">
    </div>
    <div class="pointer-events-none fixed inset-0 -z-10
        bg-gradient-to-b from-black/15 via-black/15 to-black/15">
    </div>
    <div class="pointer-events-none fixed -top-40 -left-40 -z-10 h-[560px] w-[560px] rounded-full bg-white/20 blur-3xl">
    </div>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-60">
    </div>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-gradient-to-t from-black/20 via-black/0 to-black/10"></div>

    <main class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-2xl p-8">

                <div class="flex flex-col items-center text-center">
                    <img src="<?= base_url('assets/login/logo-since.png') ?>" class="h-16 mb-6" />

                    <?php
                    $success     = isset($success) ? $success : false;
                    $message     = isset($message) ? $message : '';
                    $show_resend = isset($show_resend) ? $show_resend : false;
                    $email       = isset($email) ? $email : '';
                    ?>

                    <?php if ($success): ?>
                        <!-- SUCCESS STATE -->
                        <div class="w-20 h-20 rounded-full bg-emerald-500/20 border-2 border-emerald-400/40 flex items-center justify-center mb-4
                            animate-[pulse_2s_ease-in-out_infinite]">
                            <i class="bi bi-check-circle-fill text-4xl text-emerald-400"></i>
                        </div>
                        <h1 class="text-xl font-bold text-[#D7FFF8] mb-2">Verifikasi Berhasil!</h1>
                        <p class="text-sm text-white/70 mb-6"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>

                        <a href="<?= site_url('login'); ?>"
                            class="w-full block text-center rounded-xl py-3 font-semibold tracking-wide text-[#071A1A]
                            bg-gradient-to-br from-[#D7FFF8] via-[#2CC7C0] to-[#0A7F81]
                            shadow-lg shadow-black/25 hover:brightness-105 active:brightness-95
                            focus:outline-none focus:ring-2 focus:ring-white/30">
                            <i class="bi bi-box-arrow-in-right mr-1"></i> Login Sekarang
                        </a>

                    <?php else: ?>
                        <!-- FAILED STATE -->
                        <div class="w-20 h-20 rounded-full bg-red-500/20 border-2 border-red-400/40 flex items-center justify-center mb-4">
                            <i class="bi bi-x-circle-fill text-4xl text-red-400"></i>
                        </div>
                        <h1 class="text-xl font-bold text-red-300 mb-2">Verifikasi Gagal</h1>
                        <p class="text-sm text-white/70 mb-6"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>

                        <?php if ($show_resend && !empty($email)): ?>
                            <!-- RESEND FORM -->
                            <form action="<?= site_url('registration/resend_verification') ?>" method="post" class="w-full mb-4">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" />
                                <button type="submit"
                                    class="w-full rounded-xl py-3 font-semibold tracking-wide text-[#071A1A]
                                    bg-gradient-to-br from-[#D7FFF8] via-[#2CC7C0] to-[#0A7F81]
                                    shadow-lg shadow-black/25 hover:brightness-105 active:brightness-95
                                    focus:outline-none focus:ring-2 focus:ring-white/30">
                                    <i class="bi bi-envelope-arrow-up mr-1"></i> Kirim Ulang Email Verifikasi
                                </button>
                            </form>
                        <?php endif; ?>

                        <a href="<?= site_url('login'); ?>"
                            class="w-full block text-center rounded-xl py-3 font-semibold tracking-wide
                            border border-white/20 text-[#D7FFF8] bg-white/10 hover:bg-white/15 hover:border-white/30
                            focus:outline-none focus:ring-2 focus:ring-white/20">
                            <i class="bi bi-arrow-left mr-1"></i> Kembali ke Login
                        </a>
                    <?php endif; ?>
                </div>

            </div>

            <p class="mt-6 text-center text-xs text-white/60">
                &copy; <?= date('Y') ?> Smart Office Tiga Serangkai
            </p>
        </div>
    </main>

</body>

</html>
