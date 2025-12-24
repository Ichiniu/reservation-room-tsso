<?php
$session_id = $this->session->userdata('username');
$this->load->helper(['text', 'form']);

$id_pemesanan = substr($hasil->ID_PEMESANAN, 7);
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
    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <!-- HEADER -->
        <div class="max-w-5xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Detail Transaksi</h1>
            <p class="text-sm text-slate-500">Informasi lengkap pemesanan</p>
        </div>

        <!-- CARD -->
        <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-md p-6">

            <!-- DATA LIST -->
            <div class="space-y-3 text-sm">

                <?php
            function row($label, $value, $bold = false) {
                $font = $bold ? 'font-semibold' : '';
                echo "
                <div class='grid grid-cols-12 gap-4'>
                    <div class='col-span-4 text-slate-600 font-medium'>$label</div>
                    <div class='col-span-8 $font'>$value</div>
                </div>";
            }

            row('ID Pemesanan', $hasil->ID_PEMESANAN, true);
            row('Username', $hasil->USERNAME);
            row('Tanggal Pemesanan', date_format(date_create($hasil->TANGGAL_PEMESANAN), 'd F Y'));
            row('Jam Pemesanan', $hasil->JAM_PEMESANAN.' - '.$hasil->JAM_SELESAI);
            row('Email', $hasil->EMAIL);
            row('Gedung', $hasil->NAMA_GEDUNG);
            row('Catering', $hasil->NAMA_PAKET);
            row('Jumlah Porsi', $hasil->JUMLAH_CATERING.' Porsi');
            row('Total Harga Catering', 'Rp '.number_format($hasil->TOTAL_HARGA));
            row('Harga Gedung', 'Rp '.number_format($hasil->HARGA_SEWA));
            row('Pajak 10%', 'Rp '.number_format($tax));
            row('Total Gedung + Catering', 'Rp '.number_format($hasil->TOTAL_KESELURUHAN));
            row(
                'Total Keseluruhan',
                'Rp '.number_format($total_stl_pajak),
                true
            );
            row('Deskripsi Acara', nl2br($hasil->DESKRIPSI_ACARA));
            ?>

            </div>

            <!-- ACTION -->
            <div class="flex justify-end mt-8">
                <a href="<?= site_url('admin/admin_controls/delete_jadwal/'.$id_pemesanan) ?>"
                    onclick="return confirm('Yakin hapus jadwal ini?')"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow">
                    Hapus Acara
                </a>
            </div>

        </div>
    </main>
    <!-- ================= END MAIN ================= -->

</body>

</html>