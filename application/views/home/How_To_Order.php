<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? html_escape($title) : 'How to Order'; ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 text-slate-900">

    <?php $this->load->view('components/navbar'); ?>

    <main class="py-10">
        <div class="max-w-5xl mx-auto px-4 space-y-8">

            <!-- Header -->
            <section
                class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 backdrop-blur shadow-sm">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-28 -right-28 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>
                    <div class="absolute -bottom-28 -left-28 h-72 w-72 rounded-full bg-indigo-200/40 blur-3xl"></div>
                </div>

                <div class="relative p-6 md:p-8">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-semibold">
                        <span class="material-icons text-sm">help</span>
                        Panduan Pemesanan
                    </div>

                    <h1 class="mt-4 text-2xl md:text-3xl font-extrabold tracking-tight">
                        How to Order (Alur Booking Ruangan)
                    </h1>
                    <p class="mt-2 text-sm md:text-base text-slate-600">
                        Ikuti langkah berikut untuk melakukan booking ruangan sampai status akhir <b>Submitted</b> atau
                        <b>Rejected</b>.
                    </p>
                </div>
            </section>

            <!-- Alert Catering -->
            <section class="rounded-3xl border border-amber-200 bg-amber-50 shadow-sm p-6">
                <div class="flex items-start gap-3">
                    <div class="h-11 w-11 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-800">
                        <span class="material-icons">warning</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-base font-bold text-amber-900">Aturan Catering (Wajib Dibaca)</h2>
                        <p class="mt-1 text-sm text-amber-900/90">
                            Jika memilih <b>memakai catering</b>, maka pemesanan wajib dilakukan minimal <b>H-2</b>
                            (dua hari sebelum tanggal acara). Jika kurang dari H-2, pemesanan catering tidak dapat
                            diproses.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Steps -->
            <section class="grid grid-cols-1 gap-5">

                <!-- Step 1 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            1</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Registrasi Akun</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                User melakukan <b>registrasi</b>. Setelah registrasi berhasil, user akan <b>diarahkan ke
                                    halaman login</b>.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Step 2 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            2</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Login</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                User login menggunakan akun yang sudah dibuat. Setelah login berhasil, user masuk ke
                                tampilan awal:
                                <b>melihat daftar ruangan</b>.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Step 3 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            3</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Lihat Ruangan & Detail</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                User memilih ruangan, lalu klik <b>Detail</b> untuk melihat informasi lengkap.
                                Jika ingin memesan, klik tombol <b>Ajukan Pesanan</b>.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Step 4 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            4</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Isi Form Jadwal & Opsi Catering</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                User mengisi jadwal yang dibutuhkan (tanggal & jam).
                                Lalu memilih apakah <b>memakai catering</b> atau <b>tidak</b>.
                            </p>

                            <div
                                class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-base text-slate-600">info</span>
                                    <span>
                                        Jika memilih catering, pastikan tanggal acara masih memenuhi syarat minimal
                                        <b>H-2</b>.
                                    </span>
                                </div>
                            </div>

                            <p class="mt-3 text-sm text-slate-600">
                                Jika tidak memakai catering (atau sudah sesuai aturan), klik tombol <b>Lanjutkan</b>.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Step 5 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            5</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Halaman Konfirmasi + Keperluan Acara</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                User diarahkan ke halaman detail konfirmasi sesuai data yang sudah diisi.
                                Pada tahap ini user <b>wajib mengisi Keperluan Acara</b>, lalu klik <b>Submit</b>.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Step 6 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            6</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Status: Process (Menunggu Verifikasi Admin)</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                Setelah submit, status pesanan menjadi <b>Process</b>. User menunggu admin melakukan
                                verifikasi.
                            </p>

                            <div
                                class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-base text-slate-600">mail</span>
                                    <span>
                                        Jika verifikasi selesai, user akan menerima <b>email dari admin</b>.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Step 7 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            7</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Status: Proposal Approve → Masuk Halaman Pembayaran</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                Jika disetujui, status berubah menjadi <b>Proposal Approve</b> dan user diarahkan ke
                                halaman <b>Pembayaran</b>.
                            </p>
                            <p class="mt-2 text-sm text-slate-600">
                                Di halaman pembayaran, user memilih:
                                <b>Bayar</b> atau <b>Tidak</b>.
                                Jika <b>Tidak</b>, sistem kembali ke proses awal.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Step 8 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            8</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Upload Bukti Transfer</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                Jika user memilih lanjut pembayaran, user mengisi data transfer sesuai bank yang
                                tersedia,
                                lalu <b>upload bukti pembayaran</b>.
                            </p>
                            <p class="mt-2 text-sm text-slate-600">
                                Setelah upload, user menunggu <b>verifikasi pembayaran</b> oleh admin.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Step 9 -->
                <article class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
                            9</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold">Verifikasi Akhir → Submitted / Rejected</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                Setelah verifikasi pembayaran selesai, user akan menerima <b>email lagi</b> dari admin:
                            </p>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                                    <div class="flex items-center gap-2 text-emerald-900 font-semibold">
                                        <span class="material-icons text-base">check_circle</span> Jika disetujui
                                    </div>
                                    <p class="mt-1 text-sm text-emerald-900/90">
                                        Status berubah menjadi <b>Submitted</b>. Acara bisa dilanjutkan.
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                                    <div class="flex items-center gap-2 text-rose-900 font-semibold">
                                        <span class="material-icons text-base">cancel</span> Jika ditolak
                                    </div>
                                    <p class="mt-1 text-sm text-rose-900/90">
                                        Status berubah menjadi <b>Rejected</b>. Admin memberi pesan alasan penolakan,
                                        dan user harus mengulangi proses dari awal.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </article>
            </section>

            <!-- CTA -->
            <section class="flex flex-col sm:flex-row gap-3 justify-end">
                <a href="<?= site_url('register'); ?>"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-900 text-white font-semibold text-sm hover:bg-slate-800 transition">
                    <span class="material-icons text-base">person_add</span>
                    Registrasi
                </a>

                <a href="<?= site_url('login'); ?>"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-sky-600 text-white font-semibold text-sm hover:bg-sky-700 transition">
                    <span class="material-icons text-base">login</span>
                    Login
                </a>

                <a href="<?= site_url('home'); ?>"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white border border-slate-200 text-slate-900 font-semibold text-sm hover:bg-slate-50 transition">
                    <span class="material-icons text-base">meeting_room</span>
                    Lihat Ruangan
                </a>
            </section>

        </div>
    </main>

    <?php $this->load->view('components/Footer'); ?>

</body>

</html>