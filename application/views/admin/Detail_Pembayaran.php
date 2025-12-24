<?php
$session_id = $this->session->userdata('username');
$this->load->helper(['text','form']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Pembayaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen">

    <!-- ================= SIDEBAR ================= -->
    <?php $this->load->view('admin/components/sidebar'); ?>
    <!-- =========================================== -->

    <!-- ================= MAIN ================= -->
    <main class="pt-24 md:pl-64 px-4 md:px-6 pb-10">

        <!-- HEADER -->
        <div class="max-w-5xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Detail Pembayaran</h1>
            <p class="text-sm text-slate-500">Informasi transaksi pembayaran</p>
        </div>

        <!-- CARD -->
        <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-6">

            <!-- DETAIL DATA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <?php
            function row($label, $value, $bold=false){
                $font = $bold ? 'font-semibold text-slate-900' : 'text-slate-700';
                echo "
                <div class='flex'>
                    <div class='w-48 text-slate-600'>$label</div>
                    <div class='$font'>$value</div>
                </div>";
            }

            row('ID Transaksi', $details->KODE_PEMBAYARAN.$details->ID_PEMBAYARAN, true);
            row('ID Pemesanan', $details->KODE_PEMESANAN.$details->ID_PEMESANAN);
            row('Atas Nama', $details->ATAS_NAMA);
            row('Tanggal Pembayaran', date('d F Y', strtotime($details->TANGGAL_TRANSFER)));
            row('Nominal Transfer', 'Rp '.number_format($details->NOMINAL_TRANSFER), true);
            row('Total Keseluruhan', 'Rp '.number_format($details->TOTAL));
            row(
                'Sisa Tagihan',
                'Rp '.number_format($details->TOTAL - $details->NOMINAL_TRANSFER),
                true
            );
            row('Bank Pengirim', $details->BANK_PENGIRIM);
            ?>

            </div>

            <!-- BUKTI TRANSFER -->
            <div class="mt-6">
                <p class="text-sm font-medium text-slate-600 mb-2">Bukti Transfer</p>
                <div class="flex justify-center">
                    <img src="<?= $details->PATH.$details->IMG_NAME ?>" class="max-h-96 rounded-lg shadow border"
                        alt="Bukti Transfer">
                </div>
            </div>

            <!-- FORM STATUS -->
            <?= form_open('admin/update_status_pembayaran/'.$details->ID_PEMBAYARAN); ?>
            <div class="mt-8 border-t pt-6">

                <p class="text-sm font-medium text-slate-700 mb-3">Status Pembayaran</p>

                <div class="flex gap-8">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="1" onclick="toggleRemark(false)"
                            class="accent-teal-600">
                        <span>Terima Pembayaran</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="2" onclick="toggleRemark(true)" class="accent-red-600">
                        <span>Tolak Pembayaran</span>
                    </label>
                </div>

                <!-- REMARK -->
                <div id="remarkBox" class="mt-4 hidden">
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Alasan Penolakan
                    </label>
                    <textarea name="remark" class="w-full border rounded-lg px-4 py-2 text-sm" rows="3"></textarea>
                </div>

                <!-- BUTTON -->
                <div class="flex justify-end mt-8">
                    <button type="submit" onclick="return confirm('Yakin menyimpan perubahan?')"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow">
                        Simpan Status
                    </button>
                </div>

            </div>
            <?= form_close(); ?>

        </div>
    </main>

    <!-- SCRIPT -->
    <script>
    function toggleRemark(show) {
        document.getElementById('remarkBox')
            .classList.toggle('hidden', !show);
    }
    </script>

</body>

</html>