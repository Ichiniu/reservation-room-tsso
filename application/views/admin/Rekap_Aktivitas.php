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


</head>

<body class="bg-gray-100">

    <!-- ================= SIDEBAR ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- ================= MAIN ================= -->
    <main class="pt-24 md:pl-64 px-6 pb-10">

        <!-- HEADER -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Rekapitulasi Aktivitas</h1>
            <p class="text-sm text-gray-500">Filter berdasarkan tanggal, bulan, atau tahun</p>
        </div>

        <!-- CARD FILTER -->
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6">

            <div class="text-center mb-4">
                <button id="btnFilter" onclick="unhideElement()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">
                    Filter Data
                </button>
            </div>

            <form action="<?= site_url('admin/rekap_aktivitas/details') ?>" method="get">

                <!-- FILTER -->
                <div id="filterBox" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">

                    <!-- DARI -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <!-- SAMPAI -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <!-- BULAN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Bulan</label>
                        <select name="bulan" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Semua Bulan --</option>
                            <?php for($i=1;$i<=12;$i++): ?>
                            <option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- TAHUN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tahun</label>
                        <select name="tahun" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Semua Tahun --</option>
                            <?php for($y=date('Y');$y>=2020;$y--): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>

                <!-- BUTTON -->
                <div id="btnProsesBox" class="text-center mt-6 hidden">
                    <button type="submit" onclick="return validasi()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg shadow">
                        Proses
                    </button>
                </div>

            </form>
        </div>

    </main>

    <!-- SCRIPT -->
    <script>
    function unhideElement() {
        document.getElementById('filterBox').classList.remove('hidden');
        document.getElementById('btnProsesBox').classList.remove('hidden');
        document.getElementById('btnFilter').disabled = true;
    }

    function validasi() {
        const start = document.querySelector('input[name="start_date"]').value;
        const end = document.querySelector('input[name="end_date"]').value;
        const bulan = document.querySelector('select[name="bulan"]').value;
        const tahun = document.querySelector('select[name="tahun"]').value;

        if (!start && !end && !bulan && !tahun) {
            alert('Silakan pilih minimal satu filter!');
            return false;
        }
    }
    </script>

</body>

</html>