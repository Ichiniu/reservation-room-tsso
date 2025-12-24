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
</head>

<body class="bg-gray-100">

    <!-- SIDEBAR -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="pt-24 md:pl-64 px-6 pb-10">

        <!-- TITLE -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">
                Rekapitulasi Transaksi
            </h1>
            <p class="text-sm text-gray-500">
                Filter data transaksi berdasarkan tanggal
            </p>
        </div>

        <!-- CARD -->
        <div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6">

            <!-- FILTER BUTTON -->
            <div class="text-center mb-6">
                <button id="btnFilter" onclick="showFilter()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">
                    Filter Tanggal
                </button>
            </div>

            <!-- FORM -->
            <form action="<?= site_url('admin/rekap_transaksi/details') ?>" method="get">

                <div id="filterBox" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Dari Tanggal
                        </label>
                        <input type="date" name="start_date" id="start_date" class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sampai Tanggal
                        </label>
                        <input type="date" name="end_date" id="end_date" class="w-full border rounded-lg px-3 py-2">
                    </div>

                </div>

                <!-- SUBMIT -->
                <div id="btnProsesBox" class="text-center mt-6 hidden">
                    <button type="submit" onclick="return validasi()"
                        class="bg-green-600 hover:bg-green-700 text-white px-10 py-2 rounded-lg shadow">
                        Proses
                    </button>
                </div>

            </form>
        </div>

    </main>

    <!-- SCRIPT -->
    <script>
    function showFilter() {
        document.getElementById('filterBox').classList.remove('hidden');
        document.getElementById('btnProsesBox').classList.remove('hidden');
        document.getElementById('btnFilter').disabled = true;
    }

    function validasi() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;

        if (!start || !end) {
            alert('Harap isi tanggal awal dan akhir!');
            return false;
        }
        return true;
    }
    </script>

</body>

</html>