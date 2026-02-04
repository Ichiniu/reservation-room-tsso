<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

function h($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

$c = (isset($catering) && is_array($catering)) ? $catering : array();
$is_edit = (isset($c['ID_CATERING']) && $c['ID_CATERING'] !== '' && $c['ID_CATERING'] !== null);

// action URL: create -> admin/add_catering | edit -> admin/tambah_catering/{id}
$action_url = $is_edit
    ? site_url('admin/tambah_catering/' . (int)$c['ID_CATERING'])
    : site_url('admin/add_catering');

// nilai form
$val_nama  = $is_edit ? (isset($c['NAMA_PAKET']) ? $c['NAMA_PAKET'] : '') : set_value('nama_paket');
$val_harga = $is_edit ? (isset($c['HARGA']) ? $c['HARGA'] : '') : set_value('harga');
$val_jenis = $is_edit ? (isset($c['JENIS']) ? $c['JENIS'] : 'PAKET_RASA') : set_value('jenis');
$val_minpax = $is_edit ? (isset($c['MIN_PAX']) ? $c['MIN_PAX'] : 1) : set_value('min_pax', 1);

// MENU_JSON awal
$val_menu_json = '';
if ($is_edit) {
    $val_menu_json = isset($c['MENU_JSON']) ? $c['MENU_JSON'] : '';
} else {
    // jika controller ngirim default template, pakai itu. kalau tidak, kosong.
    $default_tpl = isset($menu_json_template) ? $menu_json_template : '';
    $val_menu_json = set_value('menu_json', $default_tpl);
}

// templates list untuk tombol (dari controller)
$tpls = isset($menu_json_templates) && is_array($menu_json_templates) ? $menu_json_templates : array();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $is_edit ? 'Edit Catering' : 'Tambah Catering'; ?></title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .input-field {
            margin-top: 14px;
        }

        .input-field label {
            position: static !important;
            transform: none !important;
        }

        .input-field input,
        .input-field textarea,
        .input-field select {
            margin-top: 8px;
        }

        input.validate,
        textarea.validate {
            border-bottom: 1px solid #e5e7eb !important;
            box-shadow: none !important;
        }

        input.validate:focus,
        textarea.validate:focus {
            border-bottom: 2px solid #1d4ed8 !important;
            box-shadow: 0 1px 0 0 #1d4ed8 !important;
        }

        .hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">

    <?php $this->load->view('admin/components/sidebar'); ?>

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    <header class="fixed top-0 left-0 right-0 z-20 bg-white border-b">
        <div class="h-16 px-4 md:px-6 flex items-center gap-3">
            <button id="sidebarToggle"
                class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
                type="button" aria-label="Toggle sidebar">
                <i class="material-icons text-gray-800">menu</i>
            </button>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white">
                    <i class="material-icons text-gray-800">restaurant</i>
                </span>
                <div class="leading-tight">
                    <div class="font-semibold"><?php echo $is_edit ? 'Edit Catering' : 'Tambah Catering'; ?></div>
                    <div class="text-xs text-gray-500">Admin Panel</div>
                </div>
            </div>

            <div class="ml-auto text-sm text-gray-600">
                <?php echo h($session_id ? $session_id : '-'); ?>
            </div>
        </div>
    </header>

    <main class="pt-20 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-5xl mx-auto">

            <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h5 class="text-xl font-semibold text-gray-900 m-0">
                        <?php echo $is_edit ? 'Edit Paket Catering' : 'Form Paket Catering'; ?>
                    </h5>
                    <p class="text-sm text-gray-500 mt-1">Isi data paket catering di bawah ini.</p>
                </div>

                <a href="<?php echo site_url('admin/catering'); ?>"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                    <i class="material-icons text-base mr-2">arrow_back</i>
                    Kembali
                </a>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <i class="material-icons text-gray-800">edit</i>
                        <div class="font-semibold text-gray-900"><?php echo $is_edit ? 'Edit Data Catering' : 'Tambah Data Catering'; ?></div>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        Tips: isi harga tanpa titik/koma (contoh: <b>125000</b>).
                    </div>
                </div>

                <div class="p-4 md:p-6">
                    <form class="row" method="post" action="<?php echo $action_url; ?>">

                        <?php if ($is_edit): ?>
                            <input type="hidden" name="id_catering" value="<?php echo (int)$c['ID_CATERING']; ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Nama Paket -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">local_offer</i>
                                    Nama Paket
                                </div>
                                <div class="input-field">
                                    <input placeholder="e.g Paket Rasa 1" name="nama_paket" type="text" class="validate"
                                        value="<?php echo h($val_nama); ?>">
                                    <div class="hint">Nama paket tampil di daftar catering.</div>
                                </div>
                            </div>

                            <!-- Harga -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">payments</i>
                                    Harga (Rp) / Pax
                                </div>
                                <div class="input-field">
                                    <input placeholder="e.g 85000" name="harga" type="number" class="validate"
                                        value="<?php echo h($val_harga); ?>">
                                    <div class="hint">Masukkan angka saja.</div>
                                </div>
                            </div>

                            <!-- Jenis -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">category</i>
                                    Jenis Paket
                                </div>
                                <div class="input-field">
                                    <select name="jenis" class="browser-default border border-gray-200 rounded-lg px-3 py-2">
                                        <?php
                                        $opts = array(
                                            'PAKET_RASA' => 'Paket Rasa',
                                            'HALF_DAY' => 'Half Day',
                                            'FULL_DAY' => 'Full Day',
                                            'COFFEE_BREAK' => 'Coffee Break'
                                        );
                                        foreach ($opts as $k => $v):
                                        ?>
                                            <option value="<?php echo h($k); ?>" <?php echo ($val_jenis === $k ? 'selected' : ''); ?>>
                                                <?php echo h($v); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="hint">Dipakai untuk grouping/tampilan.</div>
                                </div>
                            </div>

                            <!-- Min Pax -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="material-icons text-base text-gray-700">groups</i>
                                    Minimum Pax
                                </div>
                                <div class="input-field">
                                    <input placeholder="e.g 50" name="min_pax" type="number" class="validate" min="1"
                                        value="<?php echo h($val_minpax); ?>">
                                    <div class="hint">Isi 1 jika tidak ada minimum.</div>
                                </div>
                            </div>

                            <!-- MENU_JSON -->
                            <div class="md:col-span-2 border border-gray-200 rounded-xl p-4 bg-white">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                            <i class="material-icons text-base text-gray-700">data_object</i>
                                            MENU_JSON
                                        </div>
                                        <div class="hint">Isi JSON kategori + pilihan menu. Akan tampil sebagai bullet list di halaman catering.</div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 justify-end">
                                        <button type="button" data-tpl="PAKET_RASA_1" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">Template Rasa 1</button>
                                        <button type="button" data-tpl="PAKET_RASA_2" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">Template Rasa 2</button>
                                        <button type="button" data-tpl="PAKET_RASA_3" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">Template Rasa 3</button>
                                        <button type="button" data-tpl="HALF_DAY_MEETING" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">Template Half Day</button>
                                        <button type="button" data-tpl="FULL_DAY_MEETING" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">Template Full Day</button>
                                        <button type="button" data-tpl="COFFEE_BREAK_SNACKS" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">Template Coffee</button>
                                    </div>
                                </div>

                                <div class="input-field">
                                    <textarea id="menu_json" placeholder="{...}" name="menu_json" rows="14"
                                        class="validate w-full border border-gray-200 rounded-lg p-3 font-mono text-xs"><?php echo h($val_menu_json); ?></textarea>
                                    <div class="hint">Simpan format JSON valid. Kalau kosong, tampilan menu akan '-'.</div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-6 flex items-center gap-3 justify-start">
                            <button type="submit" name="submit" id="submit" value="1"
                                class="inline-flex h-10 items-center gap-2 rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white hover:bg-blue-800 active:bg-blue-900">
                                <i class="material-icons text-[18px]"><?php echo $is_edit ? 'save' : 'add'; ?></i>
                                <?php echo $is_edit ? 'Simpan Perubahan' : 'Tambah Menu'; ?>
                            </button>

                            <a href="<?php echo site_url('admin/catering'); ?>"
                                class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 active:bg-gray-100">
                                <i class="material-icons text-[18px]">close</i>
                                Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            <div class="text-xs text-gray-500 text-center mt-6">
                © <?php echo date('Y'); ?> Smart Office • Admin Panel
            </div>

        </div>
    </main>


    <script>
        (function() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var btn = document.getElementById('sidebarToggle');
            if (!sidebar) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                if (overlay) overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (btn) {
                btn.addEventListener('click', function() {
                    var isClosed = sidebar.classList.contains('-translate-x-full');
                    if (isClosed) openSidebar();
                    else closeSidebar();
                });
            }

            if (overlay) overlay.addEventListener('click', closeSidebar);
            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeSidebar();
            });
        })();
    </script>

    <script>
        var templates = <?php echo json_encode($tpls, JSON_UNESCAPED_UNICODE); ?>;

        var buttons = document.querySelectorAll('button[data-tpl]');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].addEventListener('click', function() {
                var key = this.getAttribute('data-tpl');
                if (!templates || !templates[key]) return;
                var ta = document.getElementById('menu_json');
                if (!ta) return;
                ta.value = templates[key];
                ta.focus();
            });
        }
    </script>

</body>

</html>