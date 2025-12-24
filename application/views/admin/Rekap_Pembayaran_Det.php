<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Aktivitas</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="pt-24 pl-0 md:pl-64 px-6">

        <h1 class="text-2xl font-semibold text-center mb-6">
            Rekapitulasi Aktivitas
        </h1>

        <!-- FILTER CARD -->
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6">

            <form id="filterForm">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" required class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" required class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Bulan</label>
                        <select name="bulan" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Semua Bulan</option>
                            <?php for ($i=1;$i<=12;$i++): ?>
                            <option value="<?= $i ?>">
                                <?= date('F', mktime(0,0,0,$i,1)) ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Tahun</label>
                        <select name="tahun" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Semua Tahun</option>
                            <?php for ($y=date('Y');$y>=2020;$y--): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>

                <div class="text-center mt-8">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-2 rounded-lg">
                        Proses
                    </button>
                </div>

            </form>
        </div>
    </main>

    <!-- ================= MODAL ================= -->
    <div id="rekapModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">

        <div class="bg-white w-[95%] md:w-[90%] h-[90%] rounded-xl shadow relative">

            <!-- HEADER -->
            <div class="flex justify-between items-center px-4 py-3 border-b">
                <h2 class="font-semibold text-lg">Hasil Rekapitulasi</h2>
                <button onclick="closeModal()" class="text-red-600 text-xl font-bold">&times;</button>
            </div>

            <!-- CONTENT -->
            <iframe id="rekapFrame" class="w-full h-full rounded-b-xl" frameborder="0"></iframe>

        </div>
    </div>

    <!-- SCRIPT -->
    <script>
    const form = document.getElementById('filterForm');
    const modal = document.getElementById('rekapModal');
    const frame = document.getElementById('rekapFrame');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const params = new URLSearchParams(new FormData(form)).toString();
        const url = "<?= site_url('admin/rekap_aktivitas/details') ?>?" + params;

        frame.src = url;
        modal.classList.remove('hidden');
    });

    function closeModal() {
        modal.classList.add('hidden');
        frame.src = '';
    }
    </script>

</body>

</html>