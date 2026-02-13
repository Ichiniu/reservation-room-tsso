<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Transaksi</title>

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
    <main class="pt-24 md:pl-64 px-4 md:px-6 pb-20">

        <!-- HEADER -->
        <div class="max-w-4xl mx-auto mb-10 text-center">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Rekapitulasi Transaksi</h1>
            <p class="text-slate-500">Analisis data keuangan dan transaksi Anda secara mendalam</p>
        </div>

        <!-- CARD FILTER -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-green-900/5 border border-slate-100 overflow-hidden">
                <div class="p-8 md:p-12">
                    <form action="<?= site_url('admin/rekap_transaksi/details') ?>" method="get" class="space-y-8" onsubmit="return validateRekap(this)">

                        <!-- RANGE TANGGAL -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Tanggal Awal</label>
                                <input type="date" name="start_date" id="start_date"
                                    class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-green-500/20 text-slate-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Tanggal Akhir</label>
                                <input type="date" name="end_date" id="end_date"
                                    class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-green-500/20 text-slate-700 font-medium">
                            </div>
                        </div>

                        <!-- FILTER BULAN & TAHUN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-2">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Bulan (Opsional)</label>
                                <select name="bulan" id="bulanSelect" onchange="syncFromBT()" class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-green-500/20 text-slate-700 font-medium outline-none">
                                    <option value="">-- Semua Bulan --</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Tahun (Opsional)</label>
                                <select name="tahun" id="tahunSelect" onchange="syncFromBT()" class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-green-500/20 text-slate-700 font-medium outline-none">
                                    <option value="">-- Semua Tahun --</option>
                                    <?php for ($y = 2031; $y >= 2020; $y--): ?>
                                        <option value="<?= $y ?>"><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="bg-green-50/50 rounded-2xl p-5 border border-green-100/50">
                            <p class="text-xs text-green-600 font-semibold leading-relaxed">
                                <span class="bg-green-600 text-white px-2 py-0.5 rounded text-[10px] uppercase font-bold mr-2">Tips</span>
                                Anda dapat mengisi rentang tanggal secara manual atau memilih Bulan & Tahun untuk mengisi otomatis.
                            </p>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-5 bg-green-600 hover:bg-green-700 text-white font-black text-lg rounded-2xl shadow-xl shadow-green-200 hover:shadow-green-300 transition-all active:scale-[0.98]">
                                Tampilkan Rekapitulasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        // Init Flatpickr
        const fpConfig = {
            dateFormat: "Y-m-d",     // format value (dikirim ke server)
            altInput: true,          // aktifkan input alternatif untuk display
            altFormat: "j F Y",      // format display (CONTOH: 14 Januari 2026)
            locale: "id",            // bahasa indonesia
            allowInput: true         // boleh ketik manual
        };

        const fpStart = flatpickr("#start_date", fpConfig);
        const fpEnd = flatpickr("#end_date", fpConfig);

        function syncFromBT() {
            const b = document.getElementById('bulanSelect').value;
            const t = document.getElementById('tahunSelect').value;
            if (b && t) {
                const lastDay = new Date(t, b, 0).getDate();
                const strStart = `${t}-${b.padStart(2, '0')}-01`;
                const strEnd = `${t}-${b.padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
                
                // Update flatpickr
                fpStart.setDate(strStart);
                fpEnd.setDate(strEnd);
            }
        }

        function validateRekap(form) {
            // Flatpickr handles the hidden input value update
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
        fpStart.set('onChange', function(){
            document.getElementById('bulanSelect').value = '';
            document.getElementById('tahunSelect').value = '';
        });
        fpEnd.set('onChange', function(){
            document.getElementById('bulanSelect').value = '';
            document.getElementById('tahunSelect').value = '';
        });
    </script>
</body>

</html>