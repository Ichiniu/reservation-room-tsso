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
            <h1 class="text-2xl font-semibold text-gray-800">Rekapitulasi Transaksi</h1>
            <p class="text-sm text-gray-500">Filter data transaksi berdasarkan tanggal atau bulan</p>
        </div>

        <!-- CARD -->
        <div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6">

            <form action="<?= site_url('admin/rekap_transaksi/details') ?>" method="get" onsubmit="return validasi();">

                <!-- 2 BARIS -->
                <div class="grid grid-cols-1 gap-4">

                    <!-- BARIS 1: TANGGAL -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
                            <input type="date" name="start_date" id="start_date"
                                class="w-full border rounded-lg px-3 py-2" onchange="onTanggalChange()">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="w-full border rounded-lg px-3 py-2"
                                onchange="onTanggalChange()">
                        </div>
                    </div>

                    <!-- BARIS 2: BULAN & TAHUN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select id="bulan" class="w-full border rounded-lg px-3 py-2"
                                onchange="onBulanTahunChange()">
                                <option value="">Pilih Bulan</option>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select id="tahun" class="w-full border rounded-lg px-3 py-2"
                                onchange="onBulanTahunChange()">
                                <option value="">Pilih Tahun</option>
                                <?php
                                $nowY = (int)date('Y');
                                for ($y = $nowY; $y >= $nowY - 5; $y--) {
                                    echo '<option value="'.$y.'">'.$y.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500">
                        *Isi salah satu: (Tanggal Awal & Akhir) <b>atau</b> (Bulan & Tahun).
                        Jika Bulan & Tahun dipilih, tanggal akan terisi otomatis.
                    </p>

                </div>

                <!-- SUBMIT -->
                <div class="text-center mt-6">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-10 py-2 rounded-lg shadow">
                        Proses
                    </button>
                </div>

            </form>
        </div>

    </main>

    <script>
    function pad2(n) {
        n = parseInt(n, 10);
        return (n < 10) ? ('0' + n) : ('' + n);
    }

    function lastDayOfMonth(year, month) {
        return new Date(year, month, 0).getDate(); // month 1-12
    }

    // Kalau user ubah tanggal manual → kosongkan bulan & tahun biar tidak bentrok
    function onTanggalChange() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;

        if (start || end) {
            document.getElementById('bulan').value = '';
            document.getElementById('tahun').value = '';
        }
    }

    // Kalau user pilih bulan+tahun → auto set start_date & end_date
    function onBulanTahunChange() {
        const bulan = document.getElementById('bulan').value;
        const tahun = document.getElementById('tahun').value;

        if (!bulan || !tahun) return;

        const lastDay = lastDayOfMonth(tahun, bulan);

        document.getElementById('start_date').value = `${tahun}-${pad2(bulan)}-01`;
        document.getElementById('end_date').value = `${tahun}-${pad2(bulan)}-${pad2(lastDay)}`;
    }

    function validasi() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        const bulan = document.getElementById('bulan').value;
        const tahun = document.getElementById('tahun').value;

        const tanggalLengkap = (start && end);
        const bulanTahunLengkap = (bulan && tahun);

        if (!tanggalLengkap && !bulanTahunLengkap) {
            alert('Isi Tanggal Awal & Akhir, atau pilih Bulan & Tahun!');
            return false;
        }

        // jika bulan+tahun dipilih tapi tanggal belum terisi (antisipasi)
        if (bulanTahunLengkap) onBulanTahunChange();

        // validasi urutan tanggal
        const s = document.getElementById('start_date').value;
        const e = document.getElementById('end_date').value;
        if (s && e && s > e) {
            alert('Tanggal Awal tidak boleh lebih besar dari Tanggal Akhir!');
            return false;
        }

        return true;
    }
    </script>

</body>

</html>