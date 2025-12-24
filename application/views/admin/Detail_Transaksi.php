<?php
$session_id = $this->session->userdata('username');
$this->load->helper(['text', 'form']);

$tax = 0.1 * $hasil->HARGA_SEWA;
$total_stl_pajak = $hasil->TOTAL_KESELURUHAN + $tax;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-200 min-h-screen">

    <!-- ================= SIDEBAR ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- ================= MAIN ================= -->
    <main class="pt-24 md:pl-64 px-4 md:px-6 pb-10">

        <!-- HEADER -->
        <div class="max-w-6xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Detail Transaksi</h1>
            <p class="text-sm text-slate-500">Informasi lengkap pemesanan</p>
        </div>

        <!-- CARD -->
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow p-6">

            <!-- DETAIL DATA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-3 text-sm">

                <?php
            function item($label, $value, $bold = false) {
                $font = $bold ? 'font-semibold text-slate-900' : 'text-slate-700';
                echo "
                <div class='flex'>
                    <div class='w-56 text-slate-600 font-medium'>$label</div>
                    <div class='$font'>$value</div>
                </div>";
            }

            item('ID Pemesanan', $hasil->ID_PEMESANAN, true);
            item('Username', $hasil->USERNAME);
            item('Tanggal Pemesanan', date('d F Y', strtotime($hasil->TANGGAL_PEMESANAN)));
            item('Email', $hasil->EMAIL);
            item('Gedung', $hasil->NAMA_GEDUNG);
            item('Catering', $hasil->NAMA_PAKET);
            item('Jumlah Porsi', $hasil->JUMLAH_CATERING.' Porsi');
            item('Total Harga Catering', 'Rp '.number_format($hasil->TOTAL_HARGA));
            item('Harga Gedung', 'Rp '.number_format($hasil->HARGA_SEWA));
            item('Pajak 10%', 'Rp '.number_format($tax));
            item('Total Gedung + Catering', 'Rp '.number_format($hasil->TOTAL_KESELURUHAN));
            item(
                'Total Keseluruhan',
                'Rp '.number_format($total_stl_pajak),
                true
            );
            ?>

            </div>

            <!-- DESKRIPSI -->
            <div class="mt-6">
                <p class="text-sm font-medium text-slate-600 mb-1">Deskripsi Acara</p>
                <p class="text-sm text-slate-700 bg-slate-100 p-3 rounded-lg">
                    <?= nl2br($details->DESKRIPSI_ACARA); ?>
                </p>
            </div>

            <!-- PROPOSAL -->
            <div class="mt-4">
                <p class="text-sm font-medium text-slate-600 mb-1">Proposal Acara</p>
                <a href="<?= site_url('admin/admin_controls/download_proposal/'.$hasil->ID_PEMESANAN) ?>"
                    class="text-blue-600 hover:underline text-sm">
                    <?= $details->FILE_NAME; ?>
                </a>
            </div>

            <!-- FORM AKSI -->
            <?= form_open('admin/detail_transaksi/'.$hasil->ID_PEMESANAN); ?>
            <div class="mt-8 border-t pt-6">

                <p class="text-sm font-medium text-slate-700 mb-3">Aksi Proposal</p>

                <div class="flex gap-8">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status-proposal" value="1" onclick="toggleRemarks(false)"
                            class="accent-teal-600">
                        <span>Terima Proposal</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status-proposal" value="5" onclick="toggleRemarks(true)"
                            class="accent-red-600">
                        <span>Tolak Proposal</span>
                    </label>
                </div>

                <!-- REMARKS -->
                <div id="remarksBox" class="mt-4 hidden">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Remarks</label>
                    <input type="text" name="remarks"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:ring focus:ring-teal-200">
                </div>

                <!-- BUTTON -->
                <div class="flex justify-end mt-8">
                    <button type="submit" onclick="return confirm('Lanjutkan proses ini?')"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow">
                        Submit
                    </button>
                </div>

            </div>
            <?= form_close(); ?>

        </div>
    </main>

    <!-- SCRIPT -->
    <script>
    function toggleRemarks(show) {
        document.getElementById('remarksBox').classList.toggle('hidden', !show);
    }
    </script>

</body>

</html>