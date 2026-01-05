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

<body class="min-h-screen overflow-y-auto text-white relative overflow-x-hidden
  bg-[url('<?= base_url('assets/images/gedung/tsu-front.png') ?>')]
  bg-cover bg-center bg-no-repeat bg-fixed">

  <!-- Overlay -->
  <div class="pointer-events-none absolute -top-40 -left-40 h-[560px] w-[560px] rounded-full bg-white/20 blur-3xl">
  </div>
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-60">
  </div>
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/15 via-black/0 to-black/5"></div>

  <main class="relative min-h-screen flex items-start sm:items-center justify-center p-6">
    <div class="w-full max-w-xl py-8">

      <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-2xl p-8">

        <div class="flex flex-col items-center text-center">
          <img src="<?= base_url('assets/login/LogoTSNew.png') ?>" class="h-16 mb-4" />
          <h1 class="text-2xl font-semibold text-[#D7FFF8]">REGISTRASI</h1>
          <p class="text-sm text-white/70">
            E-Booking Room <span class="font-semibold text-[#D7FFF8]">Smart Office</span>
          </p>
        </div>

        <?= form_open('registration/status', ['id' => 'formReg']); ?>
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">

          <!-- Username -->
          <div>
            <label class="text-xs font-semibold text-white/80">USERNAME</label>
            <input type="text" name="username" id="username" required class="form-input"
              placeholder="Username" autocomplete="username">
            <p class="input-msg hidden"></p>
          </div>

          <!-- Nama Lengkap -->
          <div>
            <label class="text-xs font-semibold text-white/80">NAMA LENGKAP</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" required class="form-input"
              placeholder="Nama lengkap">
            <p class="input-msg hidden"></p>
          </div>

          <!-- Perusahaan -->
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-white/80">PERUSAHAAN</label>
            <select id="perusahaan" name="perusahaan" required class="form-input">
              <option value="" class="text-black">-- Pilih Perusahaan --</option>
              <option value="INTERNAL" class="text-black">PT Tiga Serangkai Pustaka Mandiri</option>
              <option value="EKSTERNAL" class="text-black">Perusahaan Eksternal</option>
            </select>
            <p class="input-msg hidden"></p>
          </div>

          <!-- Nama Perusahaan Eksternal -->
          <div id="wrapEksternal" class="sm:col-span-2 hidden">
            <label class="text-xs font-semibold text-white/80">NAMA PERUSAHAAN</label>
            <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-input"
              placeholder="Nama perusahaan eksternal">
            <p class="input-msg hidden"></p>
          </div>

          <!-- Departemen Internal -->
          <div id="wrapDepartemen" class="sm:col-span-2 hidden">
            <label class="text-xs font-semibold text-white/80">DEPARTEMEN</label>
            <select name="departemen" id="departemen" class="form-input">
              <option value="" class="text-black">-- Pilih Departemen --</option>
              <option value="IT" class="text-black">IT</option>
              <option value="HRD" class="text-black">HRD</option>
              <option value="Keuangan" class="text-black">Keuangan</option>
              <option value="Produksi" class="text-black">Produksi</option>
              <option value="Marketing" class="text-black">Marketing</option>
            </select>
            <p class="input-msg hidden"></p>
          </div>

          <!-- Password -->
          <div>
            <label class="text-xs font-semibold text-white/80">PASSWORD</label>
            <div class="relative">
              <input id="password" type="password" name="password" required class="form-input pr-12"
                placeholder="Password" autocomplete="new-password">
              <button type="button" class="pw-toggle" data-target="password"
                aria-label="Tampilkan password">
                <i class="bi bi-eye slash"></i>
              </button>
            </div>
            <p class="input-msg hidden"></p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="text-xs font-semibold text-white/80">CONFIRM PASSWORD</label>
            <div class="relative">
              <input id="confirm_pass" type="password" name="confirm_pass" required
                class="form-input pr-12" placeholder="****" autocomplete="new-password">
              <button type="button" class="pw-toggle" data-target="confirm_pass"
                aria-label="Tampilkan confirm password">
                <i class="bi bi-eye slash"></i>
              </button>
            </div>
            <p class="input-msg hidden"></p>
          </div>

          <!-- Email -->
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-white/80">EMAIL</label>
            <input type="email" name="email" id="email" required class="form-input"
              placeholder="user@gmail.com" autocomplete="email">
            <p class="input-msg hidden"></p>
          </div>

          <!-- Alamat -->
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-white/80">ALAMAT</label>
            <textarea name="alamat" id="alamat" rows="3" class="form-input resize-none"
              placeholder="Isi alamat anda"></textarea>
            <p class="input-msg hidden"></p>
          </div>

          <!-- Telepon -->
          <div>
            <label class="text-xs font-semibold text-white/80">NO TELEPON</label>
            <input type="text" name="no_telepon" id="no_telepon" required class="form-input"
              placeholder="08xxxxxxxxxx" inputmode="numeric">
            <p class="input-msg hidden"></p>
          </div>

          <!-- DOB -->
          <div>
            <label class="text-xs font-semibold text-white/80">TANGGAL LAHIR</label>
            <input type="date" name="dob" id="dob" required class="form-input">
            <p class="input-msg hidden"></p>
          </div>

        </div>

        <!-- Button -->
        <div class="mt-6 space-y-3">
          <button type="submit" class="w-full rounded-xl py-3 font-semibold text-[#071A1A]
            bg-gradient-to-br from-[#D7FFF8] via-[#2CC7C0] to-[#0A7F81]">
            Daftar
          </button>

          <a href="<?= site_url('login'); ?>"
            class="block text-center rounded-xl py-3 border border-white/20 text-[#D7FFF8]">
            Sudah punya akun? Login
          </a>
        </div>
        </form>

      </div>

      <p class="mt-6 text-center text-xs text-white/60">
        © <?= date('Y') ?> Smart Office Tiga Serangkai
      </p>
    </div>
  </main>

  <!-- Style helper -->
  <style>
    .form-input {
      margin-top: .5rem;
      width: 100%;
      border-radius: 0.75rem;
      background: rgba(255, 255, 255, .1);
      border: 1px solid rgba(255, 255, 255, .15);
      padding: .75rem 1rem;
      color: white;
      outline: none;
    }

    .form-input:focus {
      border-color: rgba(215, 255, 248, .6);
      box-shadow: 0 0 0 3px rgba(44, 199, 192, .15);
    }

    .pw-toggle {
      position: absolute;
      right: .75rem;
      top: 50%;
      transform: translateY(-50%);
      width: 2.25rem;
      height: 2.25rem;
      border-radius: .75rem;
      display: grid;
      place-items: center;
      border: 1px solid rgba(255, 255, 255, .18);
      background: rgba(255, 255, 255, .08);
      color: rgba(215, 255, 248, .95);
      cursor: pointer;
    }

    .pw-toggle:hover {
      background: rgba(255, 255, 255, .12);
    }

    .input-msg {
      margin-top: .45rem;
      font-size: .75rem;
      line-height: 1rem;
    }

    .msg-error {
      color: rgba(255, 200, 200, .95);
    }

    .msg-ok {
      color: rgba(200, 255, 230, .95);
    }

    .input-error {
      border-color: rgba(255, 130, 130, .75) !important;
      box-shadow: 0 0 0 3px rgba(255, 130, 130, .12) !important;
    }
  </style>

  <script>
    /* =========================
       Toggle perusahaan fields
    ========================== */
    const perusahaan = document.getElementById('perusahaan');
    const wrapDepartemen = document.getElementById('wrapDepartemen');
    const wrapEksternal = document.getElementById('wrapEksternal');

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

        // icon switch
        icon.classList.toggle('bi-eye', !isPassword);
        icon.classList.toggle('bi-eye slash', isPassword);

        btn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
      });
    });

    /* =========================
       Validation helpers
    ========================== */
    function getMsgEl(input) {
      // <p class="input-msg"> tepat setelah input atau wrapper div
      // jika input di dalam .relative, message ada di sibling berikutnya (p) setelah parent div
      if (input.closest('.relative')) {
        return input.closest('.relative').parentElement.querySelector('.input-msg');
      }
      return input.parentElement.querySelector('.input-msg');
    }

    function setError(input, message) {
      const msg = getMsgEl(input);
      if (!msg) return;

      msg.textContent = message;
      msg.classList.remove('hidden', 'msg-ok');
      msg.classList.add('msg-error');

      input.classList.add('input-error');
    }

    function setOK(input, message = '') {
      const msg = getMsgEl(input);
      if (!msg) return;

      if (message) {
        msg.textContent = message;
        msg.classList.remove('hidden', 'msg-error');
        msg.classList.add('msg-ok');
      } else {
        msg.textContent = '';
        msg.classList.add('hidden');
        msg.classList.remove('msg-ok', 'msg-error');
      }

      input.classList.remove('input-error');
    }

    function isEmailValid(v) {
      // cukup sederhana untuk frontend
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
    }

    function onlyDigits(v) {
      return v.replace(/[^\d]/g, '');
    }

    function validateField(input) {
      if (!input) return true;

      const id = input.id;
      const name = input.getAttribute('name');
      const value = (input.value || '').trim();

      // skip field yang hidden (nama_perusahaan / departemen) sesuai perusahaan
      if (name === 'nama_perusahaan') {
        if (perusahaan.value !== 'EKSTERNAL') {
          setOK(input);
          return true;
        }
        if (!value) return (setError(input, 'Nama perusahaan wajib diisi.'), false);
        return (setOK(input), true);
      }

      if (name === 'departemen') {
        if (perusahaan.value !== 'INTERNAL') {
          setOK(input);
          return true;
        }
        if (!value) return (setError(input, 'Departemen wajib dipilih.'), false);
        return (setOK(input), true);
      }

      // required generic
      if (input.hasAttribute('required')) {
        if (!value) {
          setError(input, 'Field ini wajib diisi.');
          return false;
        }
      }

      // specific rules
      if (id === 'username') {
        if (value.length < 3) return (setError(input, 'Username minimal 3 karakter.'), false);
        return (setOK(input), true);
      }

      if (id === 'nama_lengkap') {
        if (value.length < 3) return (setError(input, 'Nama lengkap minimal 3 karakter.'), false);
        return (setOK(input), true);
      }

      if (id === 'email') {
        if (!isEmailValid(value)) return (setError(input, 'Format email tidak valid.'), false);
        return (setOK(input), true);
      }

      if (id === 'no_telepon') {
        const digits = onlyDigits(value);
        if (digits.length < 8) return (setError(input, 'No telepon minimal 8 digit angka.'), false);
        // rapikan value jadi angka saja (opsional)
        input.value = digits;
        return (setOK(input), true);
      }

      if (id === 'password') {
        if (value.length < 6) return (setError(input, 'Password minimal 6 karakter.'), false);
        // juga validasi confirm kalau sudah diisi
        const cp = document.getElementById('confirm_pass');
        if ((cp.value || '').trim().length > 0) validateField(cp);
        return (setOK(input), true);
      }

      if (id === 'confirm_pass') {
        const p = (document.getElementById('password').value || '').trim();
        if (value !== p) return (setError(input, 'Confirm password harus sama dengan password.'), false);
        return (setOK(input), true);
      }

      if (id === 'perusahaan') {
        if (!value) return (setError(input, 'Perusahaan wajib dipilih.'), false);
        return (setOK(input), true);
      }

      if (id === 'dob') {
        if (!value) return (setError(input, 'Tanggal lahir wajib diisi.'), false);
        return (setOK(input), true);
      }

      // alamat optional
      return (setOK(input), true);
    }

    /* realtime validation */
    const fields = [
      'username', 'nama_lengkap', 'perusahaan', 'nama_perusahaan', 'departemen',
      'password', 'confirm_pass', 'email', 'alamat', 'no_telepon', 'dob'
    ].map(id => document.getElementById(id)).filter(Boolean);

    fields.forEach(el => {
      el.addEventListener('input', () => validateField(el));
      el.addEventListener('blur', () => validateField(el));
    });

    /* submit validation */
    const form = document.getElementById('formReg');
    form.addEventListener('submit', (e) => {
      // pastikan UI perusahaan update dulu
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