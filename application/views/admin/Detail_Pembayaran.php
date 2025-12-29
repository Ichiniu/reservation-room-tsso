<?php
$session_id = $this->session->userdata('username');
$this->load->helper(['text','form']);

function row_item($label, $value, $bold=false){
  $font = $bold ? 'font-semibold text-slate-900' : 'text-slate-700';
  echo "
  <div class='flex'>
    <div class='w-48 text-slate-600'>$label</div>
    <div class='$font'>$value</div>
  </div>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Detail Pembayaran</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen">

  <?php $this->load->view('admin/components/sidebar'); ?>

  <main class="pt-24 md:pl-64 px-4 md:px-6 pb-10">

    <div class="max-w-5xl mx-auto mb-6">
      <h1 class="text-2xl font-bold text-slate-800">Detail Pembayaran</h1>
      <p class="text-sm text-slate-500">Informasi transaksi pembayaran</p>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-6">

      <!-- DETAIL -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <?php
          // kode transaksi buatan
          row_item('ID Transaksi', 'PB' . str_pad($details->ID_PEMBAYARAN, 6, '0', STR_PAD_LEFT), true);

          // pemesanan
          row_item('ID Pemesanan', $details->KODE_PEMESANAN . $details->ID_PEMESANAN_RAW);

          // pengirim
          row_item('Atas Nama Pengirim', $details->ATAS_NAMA_PENGIRIM);

          // tanggal & nominal
          row_item('Tanggal Pembayaran', date('d F Y', strtotime($details->TANGGAL_TRANSFER)));
          row_item('Nominal Transfer', 'Rp '.number_format($details->NOMINAL_TRANSFER,0,',','.'), true);

          // total tagihan
          row_item('Total Tagihan', 'Rp '.number_format($details->TOTAL_TAGIHAN,0,',','.'), true);

          // sisa
          $sisa = (int)$details->TOTAL_TAGIHAN - (int)$details->NOMINAL_TRANSFER;
          row_item('Sisa Tagihan', 'Rp '.number_format($sisa,0,',','.'), true);

          // bank
          row_item('Bank Pengirim', $details->BANK_PENGIRIM);

          // status verif
          row_item('Status Verifikasi', $details->STATUS_VERIF, true);
        ?>
      </div>

      <!-- BUKTI TRANSFER -->
      <?php
        // BUKTI_PATH disarankan menyimpan relative file, mis: assets/images/client-bukti-pembayaran/xxx.jpg
        $bukti_path = $details->BUKTI_PATH;

        // kalau BUKTI_PATH hanya folder, gabungkan dengan BUKTI_NAME
        if (!empty($details->BUKTI_NAME) && substr($bukti_path, -strlen($details->BUKTI_NAME)) !== $details->BUKTI_NAME) {
          $bukti_path = rtrim($bukti_path, '/') . '/' . $details->BUKTI_NAME;
        }

        $bukti_url = base_url($bukti_path);
        $is_pdf = (strpos(strtolower($bukti_path), '.pdf') !== false);
      ?>

      <div class="mt-6">
        <p class="text-sm font-medium text-slate-600 mb-2">Bukti Transfer</p>

        <?php if($is_pdf): ?>
          <div class="flex justify-center">
            <a href="<?= $bukti_url ?>" target="_blank"
               class="px-4 py-2 rounded-lg bg-slate-800 text-white hover:bg-slate-900">
              Lihat Bukti (PDF)
            </a>
          </div>
        <?php else: ?>
          <div class="flex justify-center">
            <img src="<?= $bukti_url ?>" class="max-h-96 rounded-lg shadow border" alt="Bukti Transfer">
          </div>
        <?php endif; ?>
      </div>

      <!-- VERIFIKASI ADMIN -->
      <div class="mt-8 border-t pt-6">
        <p class="text-sm font-medium text-slate-700 mb-3">Verifikasi Pembayaran</p>

        <!-- REJECT (POST, wajib catatan) -->
        <form method="post"
              action="<?= site_url('admin/pembayaran/verify/'.$details->ID_PEMBAYARAN.'/reject') ?>"
              class="mt-4">

          <label class="block text-sm font-medium text-slate-600 mb-1">
            Catatan Admin (wajib jika ditolak)
          </label>
          <textarea name="catatan_admin" required
                    class="w-full border rounded-lg px-4 py-2 text-sm" rows="3"
                    placeholder="Contoh: bukti transfer tidak jelas / nominal tidak sesuai / rekening salah"></textarea>

          <div class="flex justify-end gap-3 mt-6">

            <a href="<?= site_url('admin/pembayaran') ?>"
               class="px-5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800">
              Kembali
            </a>

            <button type="submit"
                    onclick="return confirm('Yakin menolak pembayaran ini? Status pemesanan akan menjadi REJECTED (final).')"
                    class="px-5 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white shadow">
              Tolak Pembayaran
            </button>

            <a href="<?= site_url('admin/pembayaran/verify/'.$details->ID_PEMBAYARAN.'/confirm') ?>"
               onclick="return confirm('Yakin menerima pembayaran ini? Status pemesanan akan menjadi SUBMITED.')"
               class="px-5 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white shadow">
              Terima Pembayaran
            </a>
          </div>
        </form>
      </div>

    </div>
  </main>

</body>
</html>
