<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->load->helper('text');

/**
 * EXPECTED from controller:
 * - $result (object) => detail pemesanan
 * OPTIONAL:
 * - $user_username (string)
 * - $user_email (string)
 * - $proposal_details (object) => DESKRIPSI_ACARA, FILE_NAME
 */

// ======= SAFE GETTERS (PHP 5.6 friendly) =======
$harga_sewa = isset($result->HARGA_SEWA) ? (float)$result->HARGA_SEWA : 0;
$total_keseluruhan = isset($result->TOTAL_KESELURUHAN) ? (float)$result->TOTAL_KESELURUHAN : 0;
$total_catering = isset($result->TOTAL_HARGA) ? (float)$result->TOTAL_HARGA : 0;

$tax = 0.1 * $harga_sewa;

// tampilkan user/email
$display_username = !empty($user_username)
    ? $user_username
    : (isset($result->USERNAME) ? $result->USERNAME : '-');

$display_email = !empty($user_email)
    ? $user_email
    : (isset($result->EMAIL) ? $result->EMAIL : '-');

// proposal info
$proposal_obj = isset($proposal_details) ? $proposal_details : null;

$deskripsi_acara = '-';
$proposal_file_name = '';

if ($proposal_obj) {
    if (!empty($proposal_obj->DESKRIPSI_ACARA)) $deskripsi_acara = $proposal_obj->DESKRIPSI_ACARA;
    if (!empty($proposal_obj->FILE_NAME)) $proposal_file_name = $proposal_obj->FILE_NAME;
}

// formatting time
$jam_range = '-';
if (!empty($result->JAM_PEMESANAN) && !empty($result->JAM_SELESAI)) {
    $jam_range = date('H:i', strtotime($result->JAM_PEMESANAN)) . ' - ' .
        date('H:i', strtotime($result->JAM_SELESAI)) . ' WIB';
}

$tanggal_text = '-';
if (!empty($result->TANGGAL_PEMESANAN)) {
    $tanggal_text = date('d F Y', strtotime($result->TANGGAL_PEMESANAN));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Admin • Detail Pemesanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-200 min-h-screen flex flex-col">

    <!-- SIDEBAR ADMIN -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <!-- MAIN -->
    <main class="flex-1 pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">

        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-6">

            <div class="flex items-center justify-between gap-3 mb-4 border-b pb-2">
                <h2 class="text-xl font-bold">Detail Pemesanan</h2>

                <a href="<?= site_url('admin/transaksi'); ?>"
                    class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm">
                    Kembali
                </a>
            </div>

            <table class="w-full text-sm">
                <tbody>
                    <tr>
                        <td class="font-semibold w-48 py-1">ID Pemesanan</td>
                        <td class="py-1">: <?= htmlspecialchars((string)$result->ID_PEMESANAN, ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Username</td>
                        <td class="py-1">: <?= htmlspecialchars((string)$display_username, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Email</td>
                        <td class="py-1">: <?= htmlspecialchars((string)$display_email, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Tanggal Pemesanan</td>
                        <td class="py-1">: <?= htmlspecialchars((string)$tanggal_text, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Jam Pemesanan</td>
                        <td class="py-1">: <?= htmlspecialchars((string)$jam_range, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Ruang</td>
                        <td class="py-1">: <?= htmlspecialchars((string)$result->NAMA_GEDUNG, ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Nama Catering</td>
                        <td class="py-1">: <?= htmlspecialchars((string)$result->NAMA_PAKET, ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Jumlah Catering</td>
                        <td class="py-1">:
                            <?= htmlspecialchars((string)$result->JUMLAH_CATERING, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Harga Ruangan</td>
                        <td class="py-1">: Rp <?= number_format($harga_sewa); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Total Catering</td>
                        <td class="py-1">: Rp <?= number_format($total_catering); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Pajak 10%</td>
                        <td class="py-1">: Rp <?= number_format($tax); ?></td>
                    </tr>
                    <tr class="font-bold text-red-600">
                        <td class="py-2">Total Keseluruhan</td>
                        <td class="py-2">: Rp <?= number_format($total_keseluruhan + $tax); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Status</td>
                        <td class="py-1">: <?= htmlspecialchars((string)$result->STATUS, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">Keperluan Acara</td>
                        <td class="py-1">:
                            <?= nl2br(htmlspecialchars((string)$deskripsi_acara, ENT_QUOTES, 'UTF-8')); ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold py-1">File Proposal</td>
                        <td class="py-1">:
                            <?php if (!empty($proposal_file_name)): ?>
                            <a class="text-blue-600 hover:underline"
                                href="<?= site_url('admin/admin_controls/download_proposal/' . (int)$result->ID_PEMESANAN); ?>">
                                <?= htmlspecialchars((string)$proposal_file_name, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <?php else: ?>
                            <span class="text-slate-500">Belum ada file</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </main>

    <footer class="mt-auto text-xs text-gray-500 text-center py-4">
        © <?= date('Y'); ?> Smart Office • Admin Panel
    </footer>

</body>

</html>