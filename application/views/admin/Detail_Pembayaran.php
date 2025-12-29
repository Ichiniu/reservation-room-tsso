<?php
$session_id = $this->session->userdata('username');
$this->load->helper(array('text','form'));

function row_item($label, $value, $bold=false){
  $font = $bold ? 'font-semibold text-slate-900' : 'text-slate-700';
  echo "
  <div class='flex'>
    <div class='w-48 text-slate-600'>".$label."</div>
    <div class='".$font."'>".$value."</div>
  </div>";
}

/* ===== LOCK BUTTON JIKA SUDAH FINAL ===== */
$status_raw = isset($details->STATUS_VERIF) ? $details->STATUS_VERIF : '';
$status = strtoupper(trim($status_raw));
$is_locked = in_array($status, array('CONFIRMED', 'REJECTED'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Detail Pembayaran</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-900">

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
          $id_pembayaran = isset($details->ID_PEMBAYARAN) ? $details->ID_PEMBAYARAN : 0;
          row_item('ID Transaksi', 'PB' . str_pad($id_pembayaran, 6, '0', STR_PAD_LEFT), true);

          // pemesanan
          $kode_pemesanan = isset($details->KODE_PEMESANAN) ? $details->KODE_PEMESANAN : '';
          $id_pemesanan_raw = isset($details->ID_PEMESANAN_RAW) ? $details->ID_PEMESANAN_RAW : '';
          row_item('ID Pemesanan', $kode_pemesanan . $id_pemesanan_raw);

          // pengirim
          $atas_nama = isset($details->ATAS_NAMA_PENGIRIM) ? $details->ATAS_NAMA_PENGIRIM : '-';
          row_item('Atas Nama Pengirim', $atas_nama);

          // tanggal & nominal
          $tgl_transfer_raw = isset($details->TANGGAL_TRANSFER) ? $details->TANGGAL_TRANSFER : '';
          $tgl_transfer = $tgl_transfer_raw ? date('d F Y', strtotime($tgl_transfer_raw)) : '-';
          row_item('Tanggal Pembayaran', $tgl_transfer);

          $nominal_transfer = isset($details->NOMINAL_TRANSFER) ? (int)$details->NOMINAL_TRANSFER : 0;
          row_item('Nominal Transfer', 'Rp '.number_format($nominal_transfer,0,',','.'), true);

          // total tagihan
          $total_tagihan = isset($details->TOTAL_TAGIHAN) ? (int)$details->TOTAL_TAGIHAN : 0;
          row_item('Total Tagihan', 'Rp '.number_format($total_tagihan,0,',','.'), true);

          // sisa
          $sisa = $total_tagihan - $nominal_transfer;
          if ($sisa < 0) { $sisa = 0; }
          row_item('Sisa Tagihan', 'Rp '.number_format($sisa,0,',','.'), true);

          // bank
          $bank_pengirim = isset($details->BANK_PENGIRIM) ? $details->BANK_PENGIRIM : '-';
          row_item('Bank Pengirim', $bank_pengirim);

          // status verif
          row_item('Status Verifikasi', htmlspecialchars($status_raw), true);
        ?>
      </div>

      <!-- BUKTI TRANSFER -->
      <?php
        // BUKTI_PATH disarankan menyimpan relative file, mis: assets/images/client-bukti-pembayaran/xxx.jpg
        $bukti_path = isset($details->BUKTI_PATH) ? $details->BUKTI_PATH : '';

        // kalau BUKTI_PATH hanya folder, gabungkan dengan BUKTI_NAME
        $bukti_name = isset($details->BUKTI_NAME) ? $details->BUKTI_NAME : '';
        if (!empty($bukti_name)) {
          // cek apakah $bukti_path sudah mengandung nama file
          $len = strlen($bukti_name);
          if ($len > 0) {
            $tail = substr($bukti_path, -$len);
            if ($tail !== $bukti_name) {
              $bukti_path = rtrim($bukti_path, '/') . '/' . $bukti_name;
            }
          }
        }

        $bukti_url = base_url($bukti_path);
        $is_pdf = (strpos(strtolower($bukti_path), '.pdf') !== false);
      ?>

      <div class="mt-6">
        <p class="text-sm font-medium text-slate-600 mb-2">Bukti Transfer</p>

        <?php if($is_pdf): ?>
          <div class="flex justify-center">
            <a href="<?php echo $bukti_url; ?>" target="_blank"
               class="px-4 py-2 rounded-lg bg-slate-800 text-white hover:bg-slate-900">
              Lihat Bukti (PDF)
            </a>
          </div>
        <?php else: ?>
          <div class="flex justify-center">
            <img src="<?php echo $bukti_url; ?>" class="max-h-96 rounded-lg shadow border" alt="Bukti Transfer">
          </div>
        <?php endif; ?>
      </div>

      <!-- VERIFIKASI ADMIN -->
      <div class="mt-8 border-t pt-6">
        <p class="text-sm font-medium text-slate-700 mb-3">Verifikasi Pembayaran</p>

        <?php if ($is_locked): ?>
          <div class="rounded-lg bg-slate-50 border px-4 py-3 text-sm text-slate-700">
            Pembayaran sudah diproses dengan status:
            <span class="font-semibold"><?php echo htmlspecialchars($status_raw); ?></span>.
            Tombol verifikasi dinonaktifkan.
          </div>

          <div class="flex justify-end mt-6">
            <a href="<?php echo site_url('admin/pembayaran'); ?>"
               class="px-5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800">
              Kembali
            </a>
          </div>

        <?php else: ?>
          <!-- REJECT (POST, wajib catatan) -->
          <form method="post"
                action="<?php echo site_url('admin/pembayaran/verify/'.$id_pembayaran.'/reject'); ?>"
                class="mt-4">

            <label class="block text-sm font-medium text-slate-600 mb-1">
              Catatan Admin (wajib jika ditolak)
            </label>
            <textarea name="catatan_admin" required
                      class="w-full border rounded-lg px-4 py-2 text-sm" rows="3"
                      placeholder="Contoh: bukti transfer tidak jelas / nominal tidak sesuai / rekening salah"></textarea>

            <div class="flex justify-end gap-3 mt-6">

              <a href="<?php echo site_url('admin/pembayaran'); ?>"
                 class="px-5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800">
                Kembali
              </a>

              <button type="submit"
                      onclick="return confirm('Yakin menolak pembayaran ini? Status pemesanan akan menjadi REJECTED (final).')"
                      class="px-5 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white shadow">
                Tolak Pembayaran
              </button>

              <a href="<?php echo site_url('admin/pembayaran/verify/'.$id_pembayaran.'/confirm'); ?>"
                 onclick="return confirm('Yakin menerima pembayaran ini? Status pemesanan akan menjadi SUBMITED.')"
                 class="px-5 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white shadow">
                Terima Pembayaran
              </a>

            </div>
          </form>
        <?php endif; ?>
      </div>

    </div>
  </main>

</body>
</html>
