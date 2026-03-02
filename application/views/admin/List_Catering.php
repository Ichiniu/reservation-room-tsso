<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

$catering_data = isset($result) && is_array($result) ? $result : [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin Smart Office - List Catering</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-900">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main id="content" class="pt-24 md:pl-64 px-4 md:px-6 pb-20 transition-all duration-300">

        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">List Catering</h1>
                    <p class="text-sm text-slate-500">Kelola paket menu catering Anda</p>
                </div>
                <a href="<?= site_url('admin/add_catering') ?>"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                    <span class="material-icons text-sm">add</span>
                    Tambah Catering
                </a>
            </div>

            <!-- Nomor Telepon Catering -->
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form action="<?= site_url('admin/save_catering_phone') ?>" method="post" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <div class="flex items-center gap-2 text-sm text-slate-600 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                        <span class="font-semibold">Nomor WhatsApp Catering</span>
                    </div>
                    <input type="text" name="catering_phone" value="<?= htmlspecialchars(isset($catering_phone) ? $catering_phone : '089649261851') ?>"
                        class="flex-1 w-full sm:w-auto rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"
                        placeholder="Masukkan nomor telepon" required>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition shadow-sm">
                        <span class="material-icons text-sm">save</span>
                        Simpan
                    </button>
                </form>
                <p class="mt-2 text-xs text-slate-400">Nomor ini akan ditampilkan ke user saat semua catering dinonaktifkan.</p>
            </div>

            <!-- Data Rekening Pembayaran -->
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form action="<?= site_url('admin/save_payment_bank') ?>" method="post" class="space-y-3">
                    <div class="flex items-center gap-2 text-sm text-slate-600 mb-2">
                        <span class="material-icons text-[20px] text-emerald-600">account_balance</span>
                        <span class="font-semibold">Rekening Pembayaran</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Bank</label>
                            <input type="text" name="payment_bank_name"
                                value="<?= htmlspecialchars(isset($payment_bank_name) ? $payment_bank_name : 'BCA') ?>"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 focus:border-emerald-600"
                                placeholder="Contoh: BCA" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Nomor Rekening</label>
                            <input type="text" name="payment_bank_account"
                                value="<?= htmlspecialchars(isset($payment_bank_account) ? $payment_bank_account : '') ?>"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 focus:border-emerald-600"
                                placeholder="Contoh: 1234567890" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Pemilik Rekening</label>
                            <input type="text" name="payment_bank_holder"
                                value="<?= htmlspecialchars(isset($payment_bank_holder) ? $payment_bank_holder : '') ?>"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 focus:border-emerald-600"
                                placeholder="Contoh: PT Tiga Serangkai" required>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-xs text-slate-400">Data ini akan ditampilkan di halaman pembayaran user.</p>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                            <span class="material-icons text-sm">save</span>
                            Simpan Rekening
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-[700px] w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50">
                                    <th class="px-4 py-4 font-bold text-slate-700 text-center w-16">No</th>
                                    <th class="px-4 py-4 font-bold text-slate-700">Nama Paket</th>
                                    <th class="px-4 py-4 font-bold text-slate-700">Jenis</th>
                                    <th class="px-4 py-4 font-bold text-slate-700">Min Pax</th>
                                    <th class="px-4 py-4 font-bold text-slate-700">Kategori Menu</th>
                                    <th class="px-4 py-4 font-bold text-slate-700">Harga</th>
                                    <th class="px-4 py-4 font-bold text-slate-700 text-center">Status</th>
                                    <th class="px-4 py-4 font-bold text-slate-700 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php $no = 1;
                                foreach ($catering_data as $row): ?>
                                    <?php
                                    $menu = [];
                                    if (!empty($row['MENU_JSON'])) {
                                        $decoded = json_decode($row['MENU_JSON'], true);
                                        if (is_array($decoded)) $menu = $decoded;
                                    }
                                    $cats = isset($menu['categories']) && is_array($menu['categories']) ? $menu['categories'] : [];
                                    $labels = [];
                                    foreach ($cats as $c) {
                                        if (!empty($c['label'])) $labels[] = $c['label'];
                                    }
                                    $summary = empty($labels) ? '-' : implode(', ', array_slice($labels, 0, 4)) . (count($labels) > 4 ? '…' : '');
                                    ?>
                                    <tr class="table-row hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-4 text-center text-slate-500 font-medium"><?= $no++ ?></td>
                                        <td class="px-4 py-4 font-bold text-slate-900"><?= $row['NAMA_PAKET'] ?></td>
                                        <td class="px-4 py-4">
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                                                <?= isset($row['JENIS']) ? str_replace('_', ' ', $row['JENIS']) : '-' ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-slate-600 font-medium"><?= !empty($row['MIN_PAX']) ? (int)$row['MIN_PAX'] : '-' ?> Pax</td>
                                        <td class="px-4 py-4 text-slate-500 text-xs italic"><?= $summary ?></td>
                                        <td class="px-4 py-4 font-black text-slate-900">
                                            Rp <?= number_format($row['HARGA'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <?php
                                            $is_active = isset($row['IS_ACTIVE']) ? (int)$row['IS_ACTIVE'] : 1;
                                            ?>
                                            <form action="<?= site_url('admin/toggle_catering_status') ?>" method="post"
                                                onsubmit="return confirm('<?= $is_active ? 'Nonaktifkan paket catering ini? Paket tidak akan tampil di halaman order user.' : 'Aktifkan kembali paket catering ini?' ?>')">
                                                <input type="hidden" name="id_catering" value="<?= $row['ID_CATERING'] ?>">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all <?= $is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' ?>">
                                                    <span class="h-2 w-2 rounded-full <?= $is_active ? 'bg-emerald-500' : 'bg-slate-400' ?>"></span>
                                                    <?= $is_active ? 'Aktif' : 'Nonaktif' ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex justify-center gap-2">
                                                <a href="<?= site_url('admin/add_catering/' . $row['ID_CATERING']) ?>"
                                                    class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition shadow-sm">
                                                    <span class="material-icons text-sm block">edit</span>
                                                </a>
                                                <form action="<?= site_url('admin/delete_catering') ?>" method="post"
                                                    onsubmit="return confirm('Yakin ingin menghapus data catering ini?')">
                                                    <input type="hidden" name="id_catering" value="<?= $row['ID_CATERING'] ?>">
                                                    <button type="submit"
                                                        class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition shadow-sm">
                                                        <span class="material-icons text-sm block">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($catering_data)): ?>
                                    <tr>
                                        <td colspan="8" class="px-4 py-12 text-center text-slate-400 font-medium italic">
                                            Data catering belum tersedia
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-8 flex items-center justify-between border-t border-slate-50 pt-6">
                        <button id="prevBtn" class="px-4 py-2 text-sm font-bold bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 disabled:opacity-30 transition">
                            Prev
                        </button>

                        <span id="pageInfo" class="text-xs font-bold text-slate-400 uppercase tracking-widest"></span>

                        <div class="flex items-center gap-4">
                            <select id="rowsPerPage" class="bg-slate-50 border-0 rounded-xl px-3 py-2 text-xs font-bold text-slate-600 focus:ring-0">
                                <option value="5">5 Baris</option>
                                <option value="10" selected>10 Baris</option>
                                <option value="25">25 Baris</option>
                            </select>

                            <button id="nextBtn" class="px-4 py-2 text-sm font-bold bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 disabled:opacity-30 transition">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="mt-12 text-center">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">© <?php echo date('Y'); ?> Smart Office • Admin Panel</p>
            </footer>
        </div>
    </main>

    <script>
        const rows = document.querySelectorAll('.table-row');
        const rowsPerPageSelect = document.getElementById('rowsPerPage');
        const pageInfo = document.getElementById('pageInfo');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        let currentPage = 1;
        let rowsPerPage = parseInt(rowsPerPageSelect.value);

        function renderTable() {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });
            const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
            pageInfo.innerText = `Halaman ${currentPage} dari ${totalPages}`;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }

        rowsPerPageSelect.onchange = () => {
            rowsPerPage = parseInt(rowsPerPageSelect.value);
            currentPage = 1;
            renderTable();
        }
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        }
        nextBtn.onclick = () => {
            if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
                currentPage++;
                renderTable();
            }
        }

        renderTable();
    </script>

</body>

</html>