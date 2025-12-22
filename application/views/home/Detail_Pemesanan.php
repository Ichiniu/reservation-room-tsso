<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);
$tax = 0.1 * $result->HARGA_SEWA;
$tanggal_pesan = $result->TANGGAL_PEMESANAN;
$min_refund = date('Y-m-d', time());
$perbedaan = date_diff(new DateTime($tanggal_pesan), new DateTime($min_refund));
$temp_id=substr($result->ID_PEMESANAN,7);
$statusText = isset($result->STATUS)
  ? strtoupper(trim(preg_replace('/\s+/', ' ', $result->STATUS)))
  : 'UNKNOWN';
$map = [
  'PROCESS' => 1,
  'PROPOSAL APPROVE' => 2,
  'APPROVE & PAID' => 3,
  'SUBMITED' => 4,
  'REJECTED' => 5,
];
$statusCode = isset($map[$statusText]) ? $map[$statusText] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Pemesanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-200 min-h-screen">

<?php $this->load->view('components/navbar'); ?>
<?php $this->load->view('components/header'); ?>

<div class="max-w-4xl mx-auto mt-10 bg-white rounded-xl shadow-lg p-6">

    <h2 class="text-xl font-bold mb-4 border-b pb-2">Detail Pemesanan</h2>

    <table class="w-full text-sm">
        <tbody class="space-y-2">
            <tr><td class="font-semibold">ID Pemesanan</td><td>: <?= $result->ID_PEMESANAN ?></td></tr>
            <tr><td class="font-semibold">Tanggal Pemesanan</td><td>: <?= date('d F Y', strtotime($result->TANGGAL_PEMESANAN)) ?></td></tr>
            <tr><td class="font-semibold">Gedung</td><td>: <?= $result->NAMA_GEDUNG ?></td></tr>
            <tr><td class="font-semibold">Nama Catering</td><td>: <?= $result->NAMA_PAKET ?></td></tr>
            <tr><td class="font-semibold">Jumlah Catering</td><td>: <?= $result->JUMLAH_CATERING ?></td></tr>
            <tr><td class="font-semibold">Harga Gedung</td><td>: Rp <?= number_format($result->HARGA_SEWA) ?></td></tr>
            <tr><td class="font-semibold">Total Catering</td><td>: Rp <?= number_format($result->TOTAL_HARGA) ?></td></tr>
            <tr><td class="font-semibold">Pajak 10%</td><td>: Rp <?= number_format($tax) ?></td></tr>
            <tr class="font-bold text-red-600">
                <td>Total Keseluruhan</td>
                <td>: Rp <?= number_format($result->TOTAL_KESELURUHAN + $tax) ?></td>
            </tr>
            <tr><td class="font-semibold">Status</td><td>: <?= $result->STATUS ?></td></tr>
        </tbody>
    </table>

    <!-- BUTTON AREA -->
    <div class="flex justify-between mt-6">
        <!-- Batalkan -->
        <a href="<?= site_url('home/cancel-order/'.$temp_id) ?>"
           onclick="return dialog();"
           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Batalkan Pesanan
        </a>

        <!-- Bayar -->
        <?php if ($statusText === 'PROPOSAL APPROVE'): ?>
  <button onclick="openModal()"
          class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
    Bayar
  </button>
<?php endif; ?>

    </div>
</div>

<!-- MODAL PEMBAYARAN -->
<div id="modalBayar" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4">Pembayaran</h3>

        <div class="mb-3 text-sm">
            <p><b>Transfer ke Rekening:</b></p>
            <p>Bank BCA</p>
            <p>No Rekening: <b>1234567890</b></p>
            <p>Atas Nama: <b>Tiga Serangkai Smart Office</b></p>
        </div>

        <form action="<?= site_url('home/upload-bukti') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_pemesanan" value="<?= $result->ID_PEMESANAN ?>">

            <label class="block mb-2 text-sm font-medium">Upload Bukti Pembayaran</label>
            <input type="file" name="bukti"
                   required
                   class="w-full border rounded-lg p-2 mb-4">

            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="closeModal()"
                        class="px-4 py-2 bg-gray-400 rounded-lg text-white">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 rounded-lg text-white">
                    Kirim Bukti
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT -->
<script>
function openModal() {
    document.getElementById('modalBayar').classList.remove('hidden');
    document.getElementById('modalBayar').classList.add('flex');
}
function closeModal() {
    document.getElementById('modalBayar').classList.add('hidden');
}
var perbedaan = <?= $perbedaan->format('%d') ?>;
function dialog() {
    if (perbedaan <= 7) {
        return confirm("Pembatalan kurang dari 7 hari, dana tidak direfund. Lanjutkan?");
    } else {
        return confirm("Pembatalan lebih dari 7 hari, dana akan direfund. Lanjutkan?");
    }
}
</script>

</body>
</html>
