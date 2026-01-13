<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

$id_gedung = $this->uri->segment(3);

/** =============================
 * TOTAL (PAJAK DIHILANGKAN)
 * ============================= */
$total_tagihan = isset($result->TOTAL_KESELURUHUHAN)
    ? (int)$result->TOTAL_KESELURUHUHAN
    : (isset($result->TOTAL_KESELURUHAN) ? (int)$result->TOTAL_KESELURUHAN : 0);

/** =============================
 * INFO USER & PROPOSAL
 * ============================= */
$display_username = !empty($user_username) ? $user_username : $session_id;
$display_email    = !empty($user_email) ? $user_email : (isset($result->EMAIL) ? $result->EMAIL : '-');

$proposal_obj       = isset($proposal_details) ? $proposal_details : null;
$deskripsi_acara    = ($proposal_obj && !empty($proposal_obj->DESKRIPSI_ACARA)) ? $proposal_obj->DESKRIPSI_ACARA : '-';
$proposal_file_name = ($proposal_obj && !empty($proposal_obj->FILE_NAME)) ? $proposal_obj->FILE_NAME : '';
$proposal_file_url  = $proposal_file_name ? base_url('assets/user-proposal/' . $proposal_file_name) : '';

/** =============================
 * STATUS
 * ============================= */
$temp_id = isset($result->ID_PEMESANAN) ? substr($result->ID_PEMESANAN, 7) : '';

$statusText = isset($result->STATUS)
    ? strtoupper(trim(preg_replace('/\s+/', ' ', $result->STATUS)))
    : 'UNKNOWN';

$map = array(
    'PROCESS' => 0,
    'PROPOSAL APPROVE' => 1,
    'APPROVE & PAID' => 2,
    'SUBMITED' => 3,
    'REJECTED' => 4,
);
$statusCode = isset($map[$statusText]) ? $map[$statusText] : 0;

$statusDisplay = isset($result->STATUS) ? $result->STATUS : '-';
$idDisplay     = isset($result->ID_PEMESANAN) ? $result->ID_PEMESANAN : '';

/** =============================
 * REMARKS (CATATAN ADMIN) - FIX URUTAN
 * ============================= */
$remarksDisplay = isset($result->REMARKS) ? $result->REMARKS : '';
$remarksSafe    = trim((string)$remarksDisplay);

/** tampilkan remarks hanya saat SUBMITED/REJECTED */
$isShowRemarks = ($statusText === 'SUBMITED' || $statusText === 'REJECTED');

/** =============================
 * BADGE COLOR
 * ============================= */
$badgeClass = 'bg-gray-100 text-gray-800 border-gray-200';
$dotClass   = 'bg-gray-500';

if ($statusText === 'PROCESS') {
    $badgeClass = 'bg-yellow-50 text-yellow-800 border-yellow-200';
    $dotClass   = 'bg-yellow-500';
} elseif ($statusText === 'PROPOSAL APPROVE') {
    $badgeClass = 'bg-blue-50 text-blue-800 border-blue-200';
    $dotClass   = 'bg-blue-500';
} elseif ($statusText === 'APPROVE & PAID') {
    $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
    $dotClass   = 'bg-emerald-500';
} elseif ($statusText === 'SUBMITED') {
    $badgeClass = 'bg-indigo-50 text-indigo-800 border-indigo-200';
    $dotClass   = 'bg-indigo-500';
} elseif ($statusText === 'REJECTED') {
    $badgeClass = 'bg-rose-50 text-rose-800 border-rose-200';
    $dotClass   = 'bg-rose-500';
}

/** =============================
 * DISPLAY (AMAN)
 * ============================= */
$nama_gedung  = isset($result->NAMA_GEDUNG) ? $result->NAMA_GEDUNG : '-';
$nama_paket   = isset($result->NAMA_PAKET) ? trim((string)$result->NAMA_PAKET) : '';
$jumlah_cat   = isset($result->JUMLAH_CATERING) ? (string)$result->JUMLAH_CATERING : '';

$nama_paket_display = ($nama_paket !== '' && strtoupper($nama_paket) !== '0') ? $nama_paket : 'Tidak Ada';
$jumlah_cat_display = ($jumlah_cat !== '' && $jumlah_cat !== '0') ? $jumlah_cat : 'Tidak Ada';

$harga_sewa  = isset($result->HARGA_SEWA) ? (int)$result->HARGA_SEWA : 0;
$total_harga = isset($result->TOTAL_HARGA) ? (int)$result->TOTAL_HARGA : 0;

$tgl_pemesanan_raw = isset($result->TANGGAL_PEMESANAN) ? $result->TANGGAL_PEMESANAN : '';
$tgl_pemesanan_fmt = $tgl_pemesanan_raw ? date('d F Y', strtotime($tgl_pemesanan_raw)) : '-';

$jam_mulai   = !empty($result->JAM_PEMESANAN) ? date('H:i', strtotime($result->JAM_PEMESANAN)) : '';
$jam_selesai = !empty($result->JAM_SELESAI) ? date('H:i', strtotime($result->JAM_SELESAI)) : '';
$jam_fmt     = ($jam_mulai && $jam_selesai) ? ($jam_mulai . ' - ' . $jam_selesai . ' WIB') : '-';
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

        <!-- Header + Status badge -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 border-b pb-3">
            <h2 class="text-xl font-bold">Detail Pemesanan</h2>

            <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 <?php echo $badgeClass; ?>">
                <span class="h-2.5 w-2.5 rounded-full <?php echo $dotClass; ?>"></span>
                <span class="text-sm font-semibold">STATUS:</span>
                <span
                    class="text-sm font-bold"><?php echo htmlspecialchars($statusDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <!-- CATATAN ADMIN (SUBMITED / REJECTED) -->
        <?php if ($isShowRemarks): ?>
            <?php
            $remarkBoxClass = ($statusText === 'REJECTED') ? 'border-rose-200 bg-rose-50' : 'border-indigo-200 bg-indigo-50';
            $remarkTitleClass = ($statusText === 'REJECTED') ? 'text-rose-800' : 'text-indigo-800';
            ?>
            <div class="mb-4 rounded-xl border p-4 text-sm <?php echo $remarkBoxClass; ?>">
                <div class="font-semibold <?php echo $remarkTitleClass; ?>">Catatan Admin:</div>

                <?php if ($remarksSafe !== ''): ?>
                    <div class="mt-1 text-slate-800 whitespace-pre-line">
                        <?php echo htmlspecialchars($remarksSafe, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php else: ?>
                    <div class="mt-1 text-slate-500">Tidak ada catatan admin.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- TABEL DETAIL -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody class="[&>tr]:border-b [&>tr:last-child]:border-b-0">
                    <tr>
                        <td class="py-2 font-semibold w-48">ID Pemesanan</td>
                        <td class="py-2">: <?php echo htmlspecialchars($idDisplay, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Username</td>
                        <td class="py-2">:
                            <?php echo htmlspecialchars((string)$display_username, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Email</td>
                        <td class="py-2">: <?php echo htmlspecialchars((string)$display_email, ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Tanggal Pemesanan</td>
                        <td class="py-2">: <?php echo $tgl_pemesanan_fmt; ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Jam Pemesanan</td>
                        <td class="py-2">: <?php echo $jam_fmt; ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Ruangan</td>
                        <td class="py-2">: <?php echo htmlspecialchars((string)$nama_gedung, ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Nama Catering</td>
                        <td class="py-2">:
                            <?php echo htmlspecialchars((string)$nama_paket_display, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Jumlah Catering</td>
                        <td class="py-2">:
                            <?php echo htmlspecialchars((string)$jumlah_cat_display, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Harga Ruangan</td>
                        <td class="py-2">: Rp <?php echo number_format($harga_sewa); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold">Total Catering</td>
                        <td class="py-2">: Rp <?php echo number_format($total_harga); ?></td>
                    </tr>

                    <!-- PAJAK DIHILANGKAN -->

                    <tr class="font-bold text-red-600">
                        <td class="py-2">Total Keseluruhan</td>
                        <td class="py-2">: Rp <?php echo number_format($total_tagihan); ?></td>
                    </tr>

                    <tr>
                        <td class="py-2 font-semibold">Status</td>
                        <td class="py-2">: <?php echo htmlspecialchars((string)$statusDisplay, ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 font-semibold align-top">Keperluan Acara</td>
                        <td class="py-2">:
                            <?php echo nl2br(htmlspecialchars((string)$deskripsi_acara, ENT_QUOTES, 'UTF-8')); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- BUTTON AREA -->
        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <a href="javascript:history.back()"
                class="inline-flex items-center justify-center rounded-lg bg-slate-600 px-4 py-2 text-white hover:bg-slate-700">
                Kembali
            </a>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                <?php if ($statusCode !== 2 && $statusCode !== 3 && $statusCode !== 4): ?>
                    <a href="<?php echo site_url('home/cancel-order/' . $temp_id); ?>" onclick="return dialog();"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                        Batalkan Pesanan
                    </a>
                <?php endif; ?>

                <?php if ($statusText === 'PROPOSAL APPROVE'): ?>
                    <button type="button" onclick="openModal()"
                        class="inline-flex items-center justify-center rounded-lg bg-green-600 px-6 py-2 text-white hover:bg-green-700">
                        Bayar
                    </button>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- MODAL PEMBAYARAN -->
    <div id="modalBayar" class="fixed inset-0 z-[999] hidden bg-black/50 items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden">
            <div class="flex items-center justify-between border-b px-5 py-4">
                <h3 class="text-lg font-bold">Pembayaran</h3>
                <button type="button" onclick="closeModal()"
                    class="text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>
            </div>

            <div class="px-5 py-4 overflow-y-auto max-h-[75vh]">
                <div class="mb-4 text-sm">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-gray-600">ID Pemesanan</div>
                        <div class="font-semibold"><?php echo htmlspecialchars($idDisplay, ENT_QUOTES, 'UTF-8'); ?>
                        </div>

                        <div class="text-gray-600">Tanggal Pemesanan</div>
                        <div class="font-semibold"><?php echo $tgl_pemesanan_fmt; ?></div>

                        <div class="text-gray-600">Jam Pemesanan</div>
                        <div class="font-semibold"><?php echo $jam_fmt; ?></div>

                        <div class="text-gray-600">Gedung</div>
                        <div class="font-semibold">
                            <?php echo htmlspecialchars((string)$nama_gedung, ENT_QUOTES, 'UTF-8'); ?></div>

                        <div class="text-gray-600">Catering</div>
                        <div class="font-semibold">
                            <?php echo htmlspecialchars((string)$nama_paket_display, ENT_QUOTES, 'UTF-8'); ?></div>

                        <div class="text-gray-600">Total Tagihan</div>
                        <div class="font-semibold">Rp <?php echo number_format($total_tagihan); ?></div>
                    </div>
                </div>

                <div class="mb-4 text-sm bg-slate-50 rounded-xl p-3">
                    <p class="font-semibold mb-1">Transfer ke Rekening:</p>
                    <p>Bank BCA</p>
                    <p>No Rekening: <b>1234567890</b></p>
                    <p>Atas Nama: <b>Tiga Serangkai Smart Office</b></p>
                </div>

                <form action="<?php echo site_url('pembayaran/upload_bukti'); ?>" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="id_pemesanan"
                        value="<?php echo htmlspecialchars($idDisplay, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id_pemesanan_raw" value="<?php echo (int)$temp_id; ?>">

                    <label class="block mb-1 text-sm font-medium">Nama Lengkap</label>
                    <input type="text" name="atas_nama" required readonly
                        value="<?php echo htmlspecialchars((string)$nama_lengkap_user, ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full border rounded-lg p-2 mb-3 bg-slate-100">

                    <label class="block mb-1 text-sm font-medium">Tanggal Pembayaran</label>
                    <input type="date" name="tanggal_transfer" required value="<?php echo date('Y-m-d'); ?>"
                        class="w-full border rounded-lg p-2 mb-3">

                    <label class="block mb-1 text-sm font-medium">Bank Pengirim</label>
                    <input type="text" name="bank_pengirim" required placeholder="Contoh: BCA / BRI / Mandiri"
                        class="w-full border rounded-lg p-2 mb-3">

                    <label class="block mb-1 text-sm font-medium">Nominal Transfer</label>
                    <input type="text" class="w-full border rounded-lg p-2 mb-3" readonly
                        value="Rp <?php echo number_format($total_tagihan, 0, ',', '.'); ?>">
                    <input type="hidden" name="nominal_transfer" value="<?php echo (int)$total_tagihan; ?>">

                    <label class="block mb-1 text-sm font-medium">Upload Bukti Pembayaran</label>
                    <input type="file" name="bukti" required class="w-full border rounded-lg p-2 mb-4">

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-400 rounded-lg text-white">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 rounded-lg text-white">Kirim Bukti</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function dialog() {
            var statusCode = <?php echo (int)$statusCode; ?>;
            if (statusCode === 0) return confirm("YAKIN HAPUS RESERVASI INI?");
            if (statusCode === 2 || statusCode === 3) {
                return confirm("Pesanan sudah dibayar. Jika dibatalkan, dana tidak dapat dikembalikan. Lanjutkan?");
            }
            return confirm("Yakin batalkan pesanan ini?");
        }

        function openModal() {
            const m = document.getElementById('modalBayar');
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            const m = document.getElementById('modalBayar');
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('click', function(e) {
            const m = document.getElementById('modalBayar');
            if (!m.classList.contains('hidden') && e.target === m) closeModal();
        });
    </script>
</body>

</html>