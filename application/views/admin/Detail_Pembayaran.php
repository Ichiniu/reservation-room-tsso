<?php
$session_id = $this->session->userdata('username');
$this->load->helper(array('text','form'));

$catatan_admin = isset($details->CATATAN_ADMIN) ? trim($details->CATATAN_ADMIN) : '';

function row_item($label, $value, $bold=false){
    $font = $bold ? 'font-semibold text-slate-900' : 'text-slate-700';
    echo "
    <div class='flex'>
        <div class='w-48 text-slate-600'>{$label}</div>
        <div class='{$font}'>{$value}</div>
    </div>";
}

/* STATUS */
$status_raw = isset($details->STATUS_VERIF) ? $details->STATUS_VERIF : '';
$status = strtoupper(trim($status_raw));
$is_locked = in_array($status, array('CONFIRMED','REJECTED'));
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

        <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-6">

            <!-- DETAIL -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <?php
$id_pembayaran = isset($details->ID_PEMBAYARAN) ? (int)$details->ID_PEMBAYARAN : 0;
row_item('ID Transaksi', 'PB'.str_pad($id_pembayaran, 6, '0', STR_PAD_LEFT), true);

$kode = isset($details->KODE_PEMESANAN) ? $details->KODE_PEMESANAN : '';
$id_raw = isset($details->ID_PEMESANAN_RAW) ? $details->ID_PEMESANAN_RAW : '';
row_item('ID Pemesanan', htmlspecialchars($kode.$id_raw));

$pengirim = isset($details->ATAS_NAMA_PENGIRIM) ? $details->ATAS_NAMA_PENGIRIM : '-';
row_item('Atas Nama Pengirim', htmlspecialchars($pengirim));

$tgl = isset($details->TANGGAL_TRANSFER) ? date('d F Y', strtotime($details->TANGGAL_TRANSFER)) : '-';
row_item('Tanggal Pembayaran', $tgl);

$nominal = isset($details->NOMINAL_TRANSFER) ? (int)$details->NOMINAL_TRANSFER : 0;
row_item('Nominal Transfer', 'Rp '.number_format($nominal,0,',','.'), true);

$total = isset($details->TOTAL_TAGIHAN) ? (int)$details->TOTAL_TAGIHAN : 0;
row_item('Total Tagihan', 'Rp '.number_format($total,0,',','.'), true);

$sisa = $total - $nominal;
if ($sisa < 0) $sisa = 0;
row_item('Sisa Tagihan', 'Rp '.number_format($sisa,0,',','.'), true);

$bank = isset($details->BANK_PENGIRIM) ? $details->BANK_PENGIRIM : '-';
row_item('Bank Pengirim', htmlspecialchars($bank));

row_item('Status Verifikasi', htmlspecialchars($status), true);
?>
            </div>

            <!-- BUKTI -->
            <?php
$bukti_path = isset($details->BUKTI_PATH) ? $details->BUKTI_PATH : '';
$bukti_name = isset($details->BUKTI_NAME) ? $details->BUKTI_NAME : '';

if ($bukti_name && strpos($bukti_path, $bukti_name) === false) {
    $bukti_path = rtrim($bukti_path,'/').'/'.$bukti_name;
}

$bukti_url = base_url($bukti_path);
$is_pdf = (strpos(strtolower($bukti_path), '.pdf') !== false);
?>

            <div class="mt-6 text-center">
                <?php if ($is_pdf): ?>
                <a href="<?php echo $bukti_url; ?>" target="_blank" class="px-4 py-2 bg-slate-800 text-white rounded">
                    Lihat Bukti (PDF)
                </a>
                <?php else: ?>
                <img src="<?php echo $bukti_url; ?>" class="max-h-96 mx-auto rounded shadow">
                <?php endif; ?>
            </div>

            <!-- VERIFIKASI -->
            <div class="mt-8 border-t pt-6">

                <?php if ($is_locked): ?>

                <div class="bg-gray-50 border rounded p-4 text-sm">
                    <b>Status Final:</b> <?php echo htmlspecialchars($status); ?>
                </div>

                <?php if ($catatan_admin != ''): ?>
                <div class="mt-4">
                    <p class="text-sm font-medium">Catatan Admin</p>
                    <div class="border rounded bg-gray-50 p-3 text-sm">
                        <?php echo nl2br(htmlspecialchars($catatan_admin)); ?>
                    </div>
                </div>
                <?php endif; ?>

                <a href="<?php echo site_url('admin/pembayaran'); ?>"
                    class="inline-block mt-4 px-4 py-2 bg-gray-200 rounded">
                    Kembali
                </a>

                <?php else: ?>

                <form method="post"
                    action="<?php echo site_url('admin/pembayaran/verify/'.$id_pembayaran.'/reject'); ?>">

                    <label class="block text-sm font-medium mb-1">Catatan Admin (wajib)</label>
                    <textarea name="catatan_admin" required class="w-full border rounded px-3 py-2 text-sm"
                        rows="3"><?php echo htmlspecialchars($catatan_admin); ?></textarea>

                    <div class="flex justify-end gap-3 mt-4">

                        <a href="<?php echo site_url('admin/pembayaran'); ?>"
                            class="px-4 py-2 bg-gray-200 rounded">Kembali</a>

                        <button type="submit" onclick="return confirm('Yakin menolak pembayaran?')"
                            class="px-4 py-2 bg-red-600 text-white rounded">
                            Tolak
                        </button>

                        <button type="submit"
                            formaction="<?php echo site_url('admin/pembayaran/verify/'.$id_pembayaran.'/confirm'); ?>"
                            onclick="return confirm('Yakin menerima pembayaran?')"
                            class="px-4 py-2 bg-teal-600 text-white rounded">
                            Terima
                        </button>

                    </div>
                </form>

                <?php endif; ?>

            </div>
        </div>
    </main>
</body>

</html>