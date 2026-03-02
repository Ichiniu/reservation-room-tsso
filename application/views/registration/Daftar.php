<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrasi</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="min-h-screen overflow-y-auto text-white relative overflow-x-hidden bg-slate-900">

    <!-- ===== BACKGROUND LAYERS (FIXED) ===== -->
    <div class="fixed inset-0 -z-10
    bg-[url('<?= base_url('assets/login/gbr_lgn3.png') ?>')]
    bg-cover bg-no-repeat bg-[position:50%_35%]">
    </div>

    <!-- Dark overlay (kontras) -->
    <div class="pointer-events-none fixed inset-0 -z-10
    bg-gradient-to-b from-black/15 via-black/15 to-black/15">
    </div>

    <!-- Metallic + glossy overlay -->
    <div class="pointer-events-none fixed -top-40 -left-40 -z-10 h-[560px] w-[560px] rounded-full bg-white/20 blur-3xl">
    </div>
    <div
        class="pointer-events-none fixed inset-0 -z-10 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-60">
    </div>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-gradient-to-t from-black/20 via-black/0 to-black/10"></div>

    <main class="relative min-h-screen flex items-start sm:items-center justify-center p-6">
        <div class="w-full max-w-xl py-8">

            <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-2xl p-8">
                <div class="flex flex-col items-center text-center">
                    <img src="<?= base_url('assets/login/logo-since.png') ?>" class="h-16 mb-4" />
                    <h1 class="text-2xl font-semibold text-[#D7FFF8]">REGISTRASI</h1>
                    <p class="text-sm text-white/70">
                        E-Booking Room <span class="font-semibold text-[#D7FFF8]">Smart Office</span>
                    </p>
                </div>

                <?= form_open('registration/status', ['id' => 'formReg']); ?>

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Username -->
                    <div data-field>
                        <label for="username"
                            class="block text-xs font-semibold tracking-widest text-white/80">USERNAME</label>
                        <input type="text" name="username" id="username" required autocomplete="username"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
              placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" placeholder="Username">
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Nama Lengkap -->
                    <div data-field>
                        <label for="nama_lengkap" class="block text-xs font-semibold tracking-widest text-white/80">NAMA
                            LENGKAP</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" required
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
              placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" placeholder="Nama lengkap">
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Perusahaan -->
                    <div class="sm:col-span-2" data-field>
                        <label for="perusahaan"
                            class="block text-xs font-semibold tracking-widest text-white/80">PERUSAHAAN</label>

                        <div class="relative mt-2">
                            <select id="perusahaan" name="perusahaan" required class="w-full appearance-none rounded-xl bg-white/10 border border-white/15 px-4 py-3 pr-10
                text-white/60 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40">
                                <option value="" selected class="bg-[#071A1A] text-white/60">-- Pilih Perusahaan --
                                </option>

                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">PT Tiga Serangkai Inti
                                    Corpora</option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">PT Tiga Serangkai Pustaka
                                    Mandiri</option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">PT Assalaam Niaga Utama
                                </option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">PT Wangsa Jatra Lestari
                                </option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">PT K33 Distribusi</option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">Al Firdaus</option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">Tiga Serangkai University
                                </option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">Puspa Holistic Integrative
                                    Care</option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">Montecarlo</option>
                                <option value="INTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">Cerita Rasa Catering
                                </option>

                                <option value="EKSTERNAL" class="bg-[#071A1A] text-[#D7FFF8]">
                                    Perusahaan/Instansi/Kalangan Umum (Eksternal)
                                </option>
                            </select>

                            <span
                                class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#D7FFF8]/80">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </div>

                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Nama Perusahaan Eksternal -->
                    <div id="wrapEksternal" class="sm:col-span-2 hidden" data-field>
                        <label for="nama_perusahaan" class="block text-xs font-semibold tracking-widest text-white/80">
                            NAMA PERUSAHAAN / INSTANSI / KALANGAN UMUM
                        </label>
                        <input type="text" name="nama_perusahaan" id="nama_perusahaan"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
              placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" placeholder="Nama Non TS Group">
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Departemen Internal -->
                    <div id="wrapDepartemen" class="sm:col-span-2 hidden" data-field>
                        <label for="departemen"
                            class="block text-xs font-semibold tracking-widest text-white/80">DEPARTEMEN</label>
                        <input type="text" name="departemen" id="departemen"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
              placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" placeholder="Masukkan Departemen">
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Password -->
                    <div data-field>
                        <label for="password"
                            class="block text-xs font-semibold tracking-widest text-white/80">PASSWORD</label>
                        <div class="relative mt-2">
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 pr-12 text-white
                placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40"
                                placeholder="Password">

                            <button type="button" class="pw-toggle absolute inset-y-0 right-2 my-auto h-10 w-10 grid place-items-center rounded-lg
                text-[#D7FFF8] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20"
                                data-target="password" aria-label="Tampilkan password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Confirm Password -->
                    <div data-field>
                        <label for="confirm_pass"
                            class="block text-xs font-semibold tracking-widest text-white/80">CONFIRM PASSWORD</label>
                        <div class="relative mt-2">
                            <input id="confirm_pass" type="password" name="confirm_pass" required
                                autocomplete="new-password"
                                class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 pr-12 text-white
                placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" placeholder="****">

                            <button type="button" class="pw-toggle absolute inset-y-0 right-2 my-auto h-10 w-10 grid place-items-center rounded-lg
                text-[#D7FFF8] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20"
                                data-target="confirm_pass" aria-label="Tampilkan confirm password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-2" data-field>
                        <label for="email"
                            class="block text-xs font-semibold tracking-widest text-white/80">EMAIL</label>
                        <input type="email" name="email" id="email" required autocomplete="email"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
              placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" placeholder="user@gmail.com">
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Alamat -->
                    <div class="sm:col-span-2" data-field>
                        <label for="alamat"
                            class="block text-xs font-semibold tracking-widest text-white/80">ALAMAT</label>
                        <textarea name="alamat" id="alamat" rows="3"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white resize-none
              placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40"
                            placeholder="Isi alamat anda"></textarea>
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- Telepon -->
                    <div data-field>
                        <label for="no_telepon" class="block text-xs font-semibold tracking-widest text-white/80">NO
                            TELEPON</label>
                        <input type="text" name="no_telepon" id="no_telepon" required inputmode="numeric"
                            class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
              placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40" placeholder="08xxxxxxxxxx">
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                    <!-- DOB -->
                    <div data-field>
                        <label for="dob" class="block text-xs font-semibold tracking-widest text-white/80">TANGGAL
                            LAHIR</label>
                        <input type="date" name="dob" id="dob" required class="mt-2 w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white
              focus:outline-none focus:ring-2 focus:ring-[#D7FFF8]/40 focus:border-[#D7FFF8]/40">
                        <p class="input-msg mt-2 text-xs hidden"></p>
                    </div>

                </div>

                <!-- Button -->
                <div class="mt-6 space-y-3">
                    <button type="submit" class="w-full rounded-xl py-3 font-semibold tracking-wide text-[#071A1A]
            bg-gradient-to-br from-[#D7FFF8] via-[#2CC7C0] to-[#0A7F81]
            shadow-lg shadow-black/25 hover:brightness-105 active:brightness-95
            focus:outline-none focus:ring-2 focus:ring-white/30">
                        Daftar
                    </button>

                    <a href="<?= site_url('login'); ?>" class="block text-center rounded-xl py-3 font-semibold tracking-wide
            border border-white/20 text-[#D7FFF8] bg-white/10 hover:bg-white/15 hover:border-white/30
            focus:outline-none focus:ring-2 focus:ring-white/20">
                        Sudah punya akun? Login
                    </a>
                </div>

                <?= form_close(); ?>
            </div>

            <p class="mt-6 text-center text-xs text-white/60">
                © <?= date('Y') ?> Smart Office Tiga Serangkai
            </p>
        </div>
    </main>

    <script>
    /* =========================
       Helper message (error/ok)
    ========================== */
    function getMsgEl(input) {
        const wrap = input.closest('[data-field]');
        return wrap ? wrap.querySelector('.input-msg') : null;
    }

    function setError(input, message) {
        const msg = getMsgEl(input);
        if (msg) {
            msg.textContent = message;
            msg.classList.remove('hidden', 'text-emerald-200');
            msg.classList.add('text-red-200');
        }

        input.classList.add(
            'border-red-300', 'ring-2', 'ring-red-300/20',
            'focus:border-red-300', 'focus:ring-red-300/30'
        );
    }

    function setOK(input, message = '') {
        const msg = getMsgEl(input);
        if (msg) {
            if (message) {
                msg.textContent = message;
                msg.classList.remove('hidden', 'text-red-200');
                msg.classList.add('text-emerald-200');
            } else {
                msg.textContent = '';
                msg.classList.add('hidden');
                msg.classList.remove('text-red-200', 'text-emerald-200');
            }
        }

        input.classList.remove(
            'border-red-300', 'ring-2', 'ring-red-300/20',
            'focus:border-red-300', 'focus:ring-red-300/30'
        );
    }

    function isEmailValid(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
    }

    function isGmail(v) {
        return /^[^\s@]+@gmail(\.[a-z]{2,})?(\.[a-z]{2,})?$/i.test(v) || /^[^\s@]+@gmail\.[a-z]{2,}$/i.test(v);
    }

    function calculateAge(dateString) {
        if (!dateString) return 0;
        const dob = new Date(dateString);
        if (isNaN(dob.getTime())) return 0;
        const now = new Date();
        let age = now.getFullYear() - dob.getFullYear();
        const m = now.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) age--;
        return age;
    }

    function onlyDigits(v) {
        return v.replace(/[^\d]/g, '');
    }

    /* =========================
       Toggle perusahaan fields
    ========================== */
    const perusahaan = document.getElementById('perusahaan');
    const wrapDepartemen = document.getElementById('wrapDepartemen');
    const wrapEksternal = document.getElementById('wrapEksternal');

    function syncSelectText() {
        if (!perusahaan.value) {
            perusahaan.classList.add('text-white/60');
            perusahaan.classList.remove('text-white');
        } else {
            perusahaan.classList.add('text-white');
            perusahaan.classList.remove('text-white/60');
        }
    }

    function syncPerusahaanUI() {
        if (perusahaan.value === 'INTERNAL') {
            wrapDepartemen.classList.remove('hidden');
            wrapEksternal.classList.add('hidden');
        } else if (perusahaan.value === 'EKSTERNAL') {
            wrapEksternal.classList.remove('hidden');
            wrapDepartemen.classList.add('hidden');
        } else {
            wrapDepartemen.classList.add('hidden');
            wrapEksternal.classList.add('hidden');
        }
        syncSelectText();
    }

    perusahaan.addEventListener('change', () => {
        syncPerusahaanUI();
        validateField(perusahaan);
        validateField(document.getElementById('departemen'));
        validateField(document.getElementById('nama_perusahaan'));
    });

    syncPerusahaanUI();

    /* =========================
       Toggle password visibility
    ========================== */
    document.querySelectorAll('.pw-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-target');
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            icon.classList.toggle('bi-eye', isPassword);
            icon.classList.toggle('bi-eye-slash', !isPassword);

            btn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    /* =========================
       Validation
    ========================== */
    function validateField(input) {
        if (!input) return true;

        const id = input.id;
        const name = input.getAttribute('name');
        const value = (input.value || '').trim();

        // skip field sesuai perusahaan
        if (name === 'nama_perusahaan') {
            if (perusahaan.value !== 'EKSTERNAL') return (setOK(input), true);
            if (!value) return (setError(input, 'Nama perusahaan wajib diisi.'), false);
            return (setOK(input), true);
        }

        if (name === 'departemen') {
            if (perusahaan.value !== 'INTERNAL') return (setOK(input), true);
            if (!value) return (setError(input, 'Departemen wajib diisi.'), false);
            return (setOK(input), true);
        }

        // required
        if (input.hasAttribute('required') && !value) {
            setError(input, 'Field ini wajib diisi.');
            return false;
        }

        // rules
        if (id === 'username' && value.length < 3) return (setError(input, 'Username minimal 3 karakter.'), false);
        if (id === 'nama_lengkap' && value.length < 3) return (setError(input, 'Nama lengkap minimal 3 karakter.'),
            false);

        if (id === 'email' && value) {
            if (!isEmailValid(value)) return (setError(input, 'Format email tidak valid.'), false);
            if (!isGmail(value)) return (setError(input, 'Email harus menggunakan domain @gmail.'), false);
        }

        if (id === 'no_telepon') {
            const digits = onlyDigits(value);
            if (value && /[A-Za-z]/.test(value)) return (setError(input, 'No telepon hanya boleh berisi angka.'),
                false);
            if (digits.length < 11 || digits.length > 13) return (setError(input,
                'No telepon harus 11 sampai 13 digit angka.'), false);
            input.value = digits;
            return (setOK(input), true);
        }

        if (id === 'password') {
            if (value.length < 6) return (setError(input, 'Password minimal 6 karakter.'), false);
            const cp = document.getElementById('confirm_pass');
            if ((cp.value || '').trim().length > 0) validateField(cp);
            return (setOK(input), true);
        }

        if (id === 'confirm_pass') {
            const p = (document.getElementById('password').value || '').trim();
            if (value !== p) return (setError(input, 'Confirm password harus sama dengan password.'), false);
            return (setOK(input), true);
        }

        if (id === 'perusahaan' && !value) return (setError(input, 'Perusahaan wajib dipilih.'), false);

        if (id === 'dob') {
            if (!value) return (setError(input, 'Tanggal lahir wajib diisi.'), false);
            const age = calculateAge(value);
            if (age < 18) return (setError(input, 'Usia minimal 18 tahun.'), false);
        }

        return (setOK(input), true);
    }

    const fields = [
        'username', 'nama_lengkap', 'perusahaan', 'nama_perusahaan', 'departemen',
        'password', 'confirm_pass', 'email', 'alamat', 'no_telepon', 'dob'
    ].map(id => document.getElementById(id)).filter(Boolean);

    fields.forEach(el => {
        el.addEventListener('input', () => validateField(el));
        el.addEventListener('blur', () => validateField(el));
    });

    const form = document.getElementById('formReg');
    form.addEventListener('submit', (e) => {
        syncPerusahaanUI();

        let firstInvalid = null;
        let ok = true;

        fields.forEach(el => {
            const valid = validateField(el);
            if (!valid && !firstInvalid) firstInvalid = el;
            ok = ok && valid;
        });

        if (!ok) {
            e.preventDefault();
            if (firstInvalid) {
                firstInvalid.focus({
                    preventScroll: false
                });
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }
    });
    </script>

</body>

</html>