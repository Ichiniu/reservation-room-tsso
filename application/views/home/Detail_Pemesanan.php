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
 * REMARKS (CATATAN ADMIN)
 * ============================= */
$remarksDisplay = isset($result->REMARKS) ? $result->REMARKS : '';
$remarksSafe    = trim((string)$remarksDisplay);
$isShowRemarks  = ($statusText === 'SUBMITED' || $statusText === 'REJECTED');

/** =============================
 * BADGE COLOR
 * ============================= */
$badgeClass = 'bg-slate-50 text-slate-800 border-slate-200';
$dotClass   = 'bg-slate-500';

if ($statusText === 'PROCESS') {
    $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
    $dotClass   = 'bg-amber-500';
} elseif ($statusText === 'PROPOSAL APPROVE') {
    $badgeClass = 'bg-sky-50 text-sky-800 border-sky-200';
    $dotClass   = 'bg-sky-500';
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

function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Pemesanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="min-h-screen text-slate-900 bg-gradient-to-b from-slate-100 via-slate-50 to-emerald-50">
    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <main class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- CARD -->
            <section
                class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/80 backdrop-blur shadow-xl">
                <!-- soft glow -->
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-sky-200/25 blur-3xl"></div>
                    <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-emerald-200/25 blur-3xl"></div>
                </div>

                <div class="relative p-6 sm:p-8">
                    <!-- HEADER -->
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between pb-5 border-b border-slate-200/70">
                        <div>
                            <div
                                class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                <span class="material-icons text-[16px] text-slate-500">receipt_long</span>
                                Detail Pemesanan
                            </div>
                            <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">
                                <?php echo e($idDisplay); ?>
                            </h2>
                            <p class="mt-1 text-sm text-slate-600">
                                Ruangan: <span
                                    class="font-semibold text-slate-800"><?php echo e($nama_gedung); ?></span>
                            </p>
                        </div>

                        <!-- STATUS BADGE -->
                        <div
                            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 <?php echo $badgeClass; ?>">
                            <span class="h-2.5 w-2.5 rounded-full <?php echo $dotClass; ?>"></span>
                            <span class="text-xs font-semibold">STATUS</span>
                            <span class="text-sm font-extrabold"><?php echo e($statusDisplay); ?></span>
                        </div>
                    </div>

                    <!-- CATATAN ADMIN -->
                    <?php if ($isShowRemarks): ?>
                        <?php
                        $remarkBoxClass = ($statusText === 'REJECTED') ? 'border-rose-200 bg-rose-50/80' : 'border-indigo-200 bg-indigo-50/80';
                        $remarkTitleClass = ($statusText === 'REJECTED') ? 'text-rose-800' : 'text-indigo-800';
                        $remarkIcon = ($statusText === 'REJECTED') ? 'report' : 'info';
                        ?>
                        <div class="mt-5 rounded-2xl border p-4 text-sm <?php echo $remarkBoxClass; ?>">
                            <div class="flex items-start gap-2">
                                <span
                                    class="material-icons text-[18px] <?php echo $remarkTitleClass; ?>"><?php echo $remarkIcon; ?></span>
                                <div>
                                    <div class="font-semibold <?php echo $remarkTitleClass; ?>">Catatan Admin</div>
                                    <?php if ($remarksSafe !== ''): ?>
                                        <div class="mt-1 text-slate-800 whitespace-pre-line"><?php echo e($remarksSafe); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-1 text-slate-500">Tidak ada catatan admin.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- GRID SUMMARY (mini cards) -->
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="text-xs text-slate-500">Tanggal</div>
                            <div class="mt-1 font-semibold text-slate-900 flex items-center gap-2">
                                <span class="material-icons text-[18px] text-sky-600">event</span>
                                <?php echo e($tgl_pemesanan_fmt); ?>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="text-xs text-slate-500">Jam</div>
                            <div class="mt-1 font-semibold text-slate-900 flex items-center gap-2">
                                <span class="material-icons text-[18px] text-emerald-600">schedule</span>
                                <?php echo e($jam_fmt); ?>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="text-xs text-slate-500">Total Tagihan</div>
                            <div class="mt-1 font-extrabold text-slate-900 flex items-center gap-2">
                                <span class="material-icons text-[18px] text-emerald-600">payments</span>
                                Rp <?php echo number_format($total_tagihan, 0, ',', '.'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- DETAIL TABLE -->
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-white overflow-hidden">
                        <div
                            class="px-5 py-3 border-b border-slate-200 bg-slate-50 text-sm font-semibold text-slate-800 flex items-center gap-2">
                            <span class="material-icons text-[18px] text-slate-500">list_alt</span>
                            Rincian
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <tbody class="[&>tr]:border-b [&>tr:last-child]:border-b-0">
                                    <tr>
                                        <td class="px-5 py-3 font-semibold w-52 text-slate-700">ID Pemesanan</td>
                                        <td class="px-5 py-3 text-slate-800"><?php echo e($idDisplay); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700">Username</td>
                                        <td class="px-5 py-3 text-slate-800"><?php echo e($display_username); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700">Email</td>
                                        <td class="px-5 py-3 text-slate-800"><?php echo e($display_email); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700">Tanggal Pemesanan</td>
                                        <td class="px-5 py-3 text-slate-800"><?php echo e($tgl_pemesanan_fmt); ?></td>
                                    </tr>

                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700">Ruangan</td>
                                        <td class="px-5 py-3 text-slate-800"><?php echo e($nama_gedung); ?></td>
                                    </tr>

                                    <?php if (isset($result->PRICING_MODE) && $result->PRICING_MODE === 'PER_PESERTA'): ?>
                                        <tr>
                                            <td class="px-5 py-3 font-semibold text-slate-700">Total Peserta</td>
                                            <td class="px-5 py-3 text-slate-800"><?php echo isset($result->TOTAL_PESERTA) ? (int)$result->TOTAL_PESERTA : 0; ?> orang</td>
                                        </tr>
                                    <?php elseif (isset($result->PRICING_MODE) && $result->PRICING_MODE === 'PODCAST_PER_JAM'): ?>
                                        <tr>
                                            <td class="px-5 py-3 font-semibold text-slate-700">Jenis Podcast</td>
                                            <td class="px-5 py-3 text-slate-800">
                                                <?php
                                                $pt = isset($result->PODCAST_TYPE) ? strtoupper(trim((string)$result->PODCAST_TYPE)) : '';
                                                echo ($pt === 'VIDEO') ? 'Video Streaming' : 'Audio Podcast';
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-5 py-3 font-semibold text-slate-700">Durasi</td>
                                            <td class="px-5 py-3 text-slate-800"><?php echo isset($result->DURASI_JAM) ? (int)$result->DURASI_JAM : 0; ?> jam</td>
                                        </tr>
                                    <?php endif; ?>

                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700">Jumlah Catering</td>
                                        <td class="px-5 py-3 text-slate-800"><?php echo e($jumlah_cat_display); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700">Harga Ruangan</td>
                                        <td class="px-5 py-3 text-slate-800">Rp
                                            <?php echo number_format($harga_sewa, 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700">Total Catering</td>
                                        <td class="px-5 py-3 text-slate-800">Rp
                                            <?php echo number_format($total_harga, 0, ',', '.'); ?></td>
                                    </tr>

                                    <!-- pajak dihilangkan -->

                                    <tr class="bg-emerald-50/60">
                                        <td class="px-5 py-3 font-extrabold text-emerald-900">Total Keseluruhan</td>
                                        <td class="px-5 py-3 font-extrabold text-emerald-900">
                                            Rp <?php echo number_format($total_tagihan, 0, ',', '.'); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700">Status</td>
                                        <td class="px-5 py-3 text-slate-800"><?php echo e($statusDisplay); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-slate-700 align-top">Keperluan Acara
                                        </td>
                                        <td class="px-5 py-3 text-slate-800"><?php echo nl2br(e($deskripsi_acara)); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- BUTTON AREA -->
                    <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                        <a href="javascript:history.back()" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-slate-800
                                  hover:bg-slate-50 active:scale-[0.99] transition shadow-sm">
                            <span class="material-icons text-[18px] text-slate-600">arrow_back</span>
                            Kembali
                        </a>

                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <?php if ($statusCode !== 2 && $statusCode !== 3 && $statusCode !== 4): ?>
                                <a href="<?php echo site_url('home/cancel-order/' . $temp_id); ?>"
                                    onclick="return dialog();" class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-2 text-white
                                          bg-rose-600 hover:bg-rose-700 active:scale-[0.99] transition shadow-md">
                                    <span class="material-icons text-[18px]">cancel</span>
                                    Batalkan Pesanan
                                </a>
                            <?php endif; ?>

                            <?php if ($statusText === 'PROPOSAL APPROVE'): ?>
                                <button type="button" onclick="openModal()"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl px-6 py-2 text-white
                                               bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] transition shadow-md">
                                    <span class="material-icons text-[18px]">payments</span>
                                    Bayar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </section>

            <!-- FOOTNOTE (proposal optional) -->
            <?php if ($proposal_file_url): ?>
                <div
                    class="mt-4 rounded-2xl border border-slate-200 bg-white/70 backdrop-blur p-4 text-sm text-slate-700 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="material-icons text-[18px] text-slate-500">attach_file</span>
                        <span>Proposal: <span class="font-semibold"><?php echo e($proposal_file_name); ?></span></span>
                    </div>
                    <a href="<?php echo e($proposal_file_url); ?>" target="_blank"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 hover:bg-slate-50 transition">
                        <span class="material-icons text-[18px] text-slate-600">open_in_new</span>
                        Buka
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <!-- MODAL PEMBAYARAN -->
    <div id="modalBayar" class="fixed inset-0 z-[999] hidden bg-black/50 items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl overflow-hidden border border-slate-200">
            <div class="flex items-center justify-between border-b px-5 py-4 bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="material-icons text-[20px] text-emerald-600">payments</span>
                    <h3 class="text-lg font-extrabold text-slate-900">Pembayaran</h3>
                </div>
                <button type="button" onclick="closeModal()"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="px-5 py-4 overflow-y-auto max-h-[75vh]">
                <div class="mb-4 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-200 bg-white p-3">
                            <div class="text-xs text-slate-500">ID Pemesanan</div>
                            <div class="font-semibold text-slate-900"><?php echo e($idDisplay); ?></div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-3">
                            <div class="text-xs text-slate-500">Total Tagihan</div>
                            <div class="font-extrabold text-emerald-700">Rp
                                <?php echo number_format($total_tagihan, 0, ',', '.'); ?></div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-3">
                            <div class="text-xs text-slate-500">Tanggal</div>
                            <div class="font-semibold text-slate-900"><?php echo e($tgl_pemesanan_fmt); ?></div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-3">
                            <div class="text-xs text-slate-500">Jam</div>
                            <div class="font-semibold text-slate-900"><?php echo e($jam_fmt); ?></div>
                        </div>
                    </div>
                </div>

                <div class="mb-4 text-sm rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                    <p class="font-semibold mb-1 text-emerald-900">Transfer ke Rekening</p>
                    <div class="text-slate-700">
                        <p>Bank <b>BCA</b></p>
                        <p>No Rekening: <b>1234567890</b></p>
                        <p>Atas Nama: <b>Tiga Serangkai Smart Office</b></p>
                    </div>
                </div>

                <form action="<?php echo site_url('pembayaran/upload_bukti'); ?>" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="id_pemesanan" value="<?php echo e($idDisplay); ?>">
                    <input type="hidden" name="id_pemesanan_raw" value="<?php echo (int)$temp_id; ?>">

                    <label class="block mb-1 text-sm font-semibold text-slate-800">Nama Lengkap</label>
                    <input type="text" name="atas_nama" required readonly value="<?php echo e($nama_lengkap_user); ?>"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 mb-3 outline-none">

                    <label class="block mb-1 text-sm font-semibold text-slate-800">Tanggal Pembayaran</label>
                    <input type="date" name="tanggal_transfer" required value="<?php echo date('Y-m-d'); ?>"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 mb-3 outline-none focus:ring-4 focus:ring-emerald-100">

                    <label class="block mb-1 text-sm font-semibold text-slate-800">Bank Pengirim</label>
                    <input type="text" name="bank_pengirim" required placeholder="Contoh: BCA / BRI / Mandiri"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 mb-3 outline-none focus:ring-4 focus:ring-emerald-100">

                    <label class="block mb-1 text-sm font-semibold text-slate-800">Nominal Transfer</label>
                    <input type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 mb-2"
                        readonly value="Rp <?php echo number_format($total_tagihan, 0, ',', '.'); ?>">
                    <input type="hidden" name="nominal_transfer" value="<?php echo (int)$total_tagihan; ?>">

                    <label class="block mb-1 text-sm font-semibold text-slate-800">Upload Bukti Pembayaran</label>
                    <input type="file" name="bukti" required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 mb-4">

                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <button type="button" onclick="closeModal()"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50 transition">
                            <span class="material-icons text-[18px] text-slate-600">close</span>
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2 text-white hover:bg-sky-700 transition shadow-md">
                            <span class="material-icons text-[18px]">upload</span>
                            Kirim Bukti
                        </button>
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
            var m = document.getElementById('modalBayar');
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            var m = document.getElementById('modalBayar');
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('click', function(e) {
            var m = document.getElementById('modalBayar');
            if (!m.classList.contains('hidden') && e.target === m) closeModal();
        });
    </script>
</body>

</html>