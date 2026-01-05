<?php
// $details ini object dari $this->db->row()
$d = $details;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembayaran</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-200 min-h-screen">

    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-4xl mx-auto mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Detail Pembayaran</h1>
            <p class="text-slate-600 text-sm">
                Kode Transaksi: <b><?= 'PB' . str_pad($d->ID_PEMBAYARAN, 6, '0', STR_PAD_LEFT); ?></b>
            </p>
        </div>

        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-slate-500">Kode Pemesanan</div>
                    <div class="font-semibold text-slate-800">
                        <?= htmlspecialchars($d->KODE_PEMESANAN . $d->ID_PEMESANAN_RAW); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Status Verifikasi</div>
                    <div class="font-semibold">
                        <?= htmlspecialchars($d->STATUS_VERIF); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Atas Nama Pengirim</div>
                    <div class="font-semibold text-slate-800">
                        <?= htmlspecialchars($d->ATAS_NAMA_PENGIRIM); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Bank Pengirim</div>
                    <div class="font-semibold text-slate-800">
                        <?= htmlspecialchars($d->BANK_PENGIRIM); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Tanggal Transfer</div>
                    <div class="font-semibold text-slate-800">
                        <?= htmlspecialchars($d->TANGGAL_TRANSFER); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Nominal Transfer</div>
                    <div class="font-semibold text-green-700">
                        Rp <?= number_format((int)$d->NOMINAL_TRANSFER, 0, ',', '.'); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Total Tagihan</div>
                    <div class="font-semibold text-slate-800">
                        Rp <?= number_format((int)$d->TOTAL_TAGIHAN, 0, ',', '.'); ?>
                    </div>
                </div>

                <div>
                    <div class="text-slate-500">Bukti Pembayaran</div>
                    <?php if (!empty($d->BUKTI_PATH)): ?>
                        <a class="text-blue-600 underline"
                            href="<?= base_url($d->BUKTI_PATH); ?>"
                            target="_blank" rel="noopener">
                            Lihat / Download
                        </a>
                    <?php else: ?>
                        <span class="text-slate-500">-</span>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <form method="post">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Catatan Admin (wajib saat Reject)
                </label>
                <textarea name="catatan_admin"
                    class="w-full border rounded-lg p-3 text-sm"
                    rows="3"
                    placeholder="Tulis catatan admin..."></textarea>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit"
                        formaction="<?= site_url('admin/admin_controls/verify_pembayaran/' . $d->ID_PEMBAYARAN . '/confirm'); ?>"
                        class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                        Confirm
                    </button>

                    <button type="submit"
                        formaction="<?= site_url('admin/admin_controls/verify_pembayaran/' . $d->ID_PEMBAYARAN . '/reject'); ?>"
                        onclick="return confirm('Yakin tolak pembayaran ini? Catatan wajib diisi.');"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                        Reject
                    </button>

                    <a href="<?= site_url('admin/admin_controls/pembayaran'); ?>"
                        class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300">
                        Kembali
                    </a>
                </div>
            </form>

        </div>
    </main>
</body>

</html>