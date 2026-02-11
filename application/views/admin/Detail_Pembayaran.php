<?php
$d = $details;

// Normalisasi status
$statusText = strtoupper(trim((string)$d->STATUS_VERIF));
$isPending  = ($statusText === 'PENDING');
$catatanAdmin = trim((string)$d->CATATAN_ADMIN);

// ===== Warna Catatan Admin =====
$badgeClass = 'bg-slate-50 text-slate-700 border-slate-200';
$dotClass   = 'bg-slate-400';

if ($statusText === 'SUBMITED') {
    $badgeClass = 'bg-indigo-50 text-indigo-800 border-indigo-200';
    $dotClass   = 'bg-indigo-500';
} elseif ($statusText === 'REJECTED') {
    $badgeClass = 'bg-rose-50 text-rose-800 border-rose-200';
    $dotClass   = 'bg-rose-500';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-200 min-h-screen">

    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <div class="max-w-4xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Detail Pembayaran</h1>
            <p class="text-sm text-slate-600">
                Kode Transaksi:
                <b><?= 'PB' . str_pad((int)$d->ID_PEMBAYARAN, 6, '0', STR_PAD_LEFT); ?></b>
            </p>
        </div>

        <div class="max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">

            <!-- INFO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-slate-500">Kode Pemesanan</div>
                    <div class="font-semibold">
                        <?= htmlspecialchars($d->KODE_PEMESANAN . $d->ID_PEMESANAN_RAW); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Status</div>
                    <div class="font-semibold"><?= htmlspecialchars($d->STATUS_VERIF); ?></div>
                </div>

                <div>
                    <div class="text-slate-500">Atas Nama</div>
                    <div class="font-semibold"><?= htmlspecialchars($d->ATAS_NAMA_PENGIRIM); ?></div>
                </div>

                <div>
                    <div class="text-slate-500">Bank</div>
                    <div class="font-semibold"><?= htmlspecialchars($d->BANK_PENGIRIM); ?></div>
                </div>

                <div>
                    <div class="text-slate-500">Tanggal Transfer</div>
                    <div class="font-semibold">
                        <?= !empty($d->TANGGAL_TRANSFER) ? format_tanggal_indo($d->TANGGAL_TRANSFER) : '-'; ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Nominal</div>
                    <div class="font-semibold text-green-700">
                        Rp <?= number_format((int)$d->NOMINAL_TRANSFER,0,',','.'); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Total Tagihan</div>
                    <div class="font-semibold">
                        Rp <?= number_format((int)$d->TOTAL_TAGIHAN,0,',','.'); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Bukti</div>
                    <?php if (!empty($d->BUKTI_PATH)): ?>
                    <a href="<?= base_url($d->BUKTI_PATH); ?>" target="_blank" class="text-blue-600 underline">Lihat /
                        Download</a>
                    <?php else: ?>
                    <span class="text-slate-500">-</span>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <!-- CATATAN ADMIN (BERWARNA SESUAI STATUS) -->
            <?php if ($catatanAdmin !== ''): ?>
            <div class="border rounded-xl p-4 <?= $badgeClass; ?>">
                <div class="flex items-start gap-3">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full <?= $dotClass; ?>"></span>
                    <div>
                        <div class="text-sm font-semibold">
                            Catatan Admin
                        </div>
                        <div class="mt-1 text-sm whitespace-pre-line">
                            <?= htmlspecialchars($catatanAdmin); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- FORM -->
            <?php if ($isPending): ?>
            <form method="post" class="space-y-3">
                <label class="text-sm font-semibold">Catatan Admin (wajib saat Reject)</label>
                <textarea name="catatan_admin" class="w-full border rounded-xl p-3 text-sm" rows="3"
                    placeholder="Tulis catatan..."></textarea>

                <div class="flex gap-3 mt-3">
                    <button type="submit"
                        formaction="<?= site_url('admin/admin_controls/verify_pembayaran/'.$d->ID_PEMBAYARAN.'/confirm'); ?>"
                        class="px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700">
                        Confirm
                    </button>

                    <button type="submit"
                        formaction="<?= site_url('admin/admin_controls/verify_pembayaran/'.$d->ID_PEMBAYARAN.'/reject'); ?>"
                        onclick="return confirm('Yakin tolak pembayaran ini? Catatan wajib diisi.');"
                        class="px-5 py-2.5 bg-rose-600 text-white rounded-xl hover:bg-rose-700">
                        Reject
                    </button>

                    <a href="<?= site_url('admin/admin_controls/pembayaran'); ?>"
                        class="px-5 py-2.5 bg-slate-200 rounded-xl hover:bg-slate-300">
                        Kembali
                    </a>
                </div>
            </form>

            <?php else: ?>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm">
                Pembayaran ini sudah diproses dengan status:
                <b><?= htmlspecialchars($d->STATUS_VERIF); ?></b>.
            </div>

            <a href="<?= site_url('admin/admin_controls/pembayaran'); ?>"
                class="inline-block mt-4 px-5 py-2.5 bg-slate-200 rounded-xl hover:bg-slate-300">
                Kembali
            </a>
            <?php endif; ?>

        </div>
    </main>
</body>

</html>