<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Aktivitas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

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
    <main class="pt-24 md:pl-64 px-6 pb-20">

        <!-- HEADER -->
        <div class="max-w-4xl mx-auto mb-10 text-center">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Rekapitulasi Aktivitas</h1>
            <p class="text-slate-500">Pantau dan filter aktivitas harian dengan presisi</p>
        </div>

        <!-- CARD FILTER -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-slate-100 overflow-hidden">
                <div class="p-8 md:p-12">
                    <form action="<?= site_url('admin/rekap_aktivitas/details') ?>" method="get" class="space-y-8" onsubmit="return validateRekap(this)">

                        <!-- RANGE TANGGAL -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Tanggal Awal</label>
                                <input type="date" name="start_date" id="start_date"
                                    class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-blue-500/20 text-slate-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Tanggal Akhir</label>
                                <input type="date" name="end_date" id="end_date"
                                    class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-blue-500/20 text-slate-700 font-medium">
                            </div>
                        </div>

                        <!-- FILTER BULAN & TAHUN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-2">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Bulan (Opsional)</label>
                                <select name="bulan" id="bulanSelect" onchange="syncFromBT()" class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-blue-500/20 text-slate-700 font-medium outline-none">
                                    <option value="">-- Semua Bulan --</option>
                                    <?php
                                    $bulan_indo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    foreach ($bulan_indo as $m => $n): ?>
                                        <option value="<?= $m ?>"><?= $n ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Tahun (Opsional)</label>
                                <select name="tahun" id="tahunSelect" onchange="syncFromBT()" class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-blue-500/20 text-slate-700 font-medium outline-none">
                                    <option value="">-- Semua Tahun --</option>
                                    <?php for ($y = 2031; $y >= 2020; $y--): ?>
                                        <option value="<?= $y ?>"><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="bg-blue-50/50 rounded-2xl p-5 border border-blue-100/50">
                            <p class="text-xs text-blue-600 font-semibold leading-relaxed">
                                <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-[10px] uppercase font-bold mr-2">Tips</span>
                                Anda dapat mengisi rentang tanggal secara manual atau memilih Bulan & Tahun untuk mengisi otomatis.
                            </p>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-5 bg-slate-900 hover:bg-blue-600 text-white font-black text-lg rounded-2xl shadow-xl shadow-slate-200 hover:shadow-blue-200 transition-all active:scale-[0.98]">
                                Tampilkan Rekapitulasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function syncFromBT() {
            const b = document.getElementById('bulanSelect').value;
            const t = document.getElementById('tahunSelect').value;
            if (b && t) {
                const lastDay = new Date(t, b, 0).getDate();
                document.getElementById('start_date').value = `${t}-${b.padStart(2, '0')}-01`;
                document.getElementById('end_date').value = `${t}-${b.padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
            }
        }

        function validateRekap(form) {
            const s = form.start_date.value;
            const e = form.end_date.value;
            const b = form.bulan.value;
            const t = form.tahun.value;

            if (!s && !e && !b && !t) {
                alert('Pilih rentang tanggal atau bulan/tahun terlebih dahulu!');
                return false;
            }
            if (s && e && s > e) {
                alert('Tanggal awal tidak boleh melebihi tanggal akhir!');
                return false;
            }
            return true;
        }

        // Clear BT if date manual change
        const clearBT = () => {
            document.getElementById('bulanSelect').value = '';
            document.getElementById('tahunSelect').value = '';
        };
        document.getElementById('start_date').addEventListener('change', clearBT);
        document.getElementById('end_date').addEventListener('change', clearBT);
    </script>
</body>

</html>