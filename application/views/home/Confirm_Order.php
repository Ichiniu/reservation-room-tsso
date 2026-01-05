<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$this->load->helper('form');
$id_gedung = $this->uri->segment(4);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicons-->
    <link rel="apple-touch-icon-precomposed"
        href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Pemesanan</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Materialize (untuk textarea/file-field helper kamu) -->
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">

    <!-- COMPONENTS -->
    <?php $this->load->view('components/header'); ?>
    <?php $this->load->view('components/navbar'); ?>

    <main class="py-8 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-5">
                <div class="rounded-2xl border border-slate-300 bg-white shadow-sm p-4 sm:p-5">
                    <div class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-800">Pemesanan</span>
                        <span class="mx-2 text-slate-400">/</span>
                        <span class="text-slate-700">Isi Data Pesanan</span>
                        <span class="mx-2 text-slate-400">/</span>
                        <span class="font-semibold text-blue-700">Upload Proposal Acara</span>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div class="mb-5">
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-900">Validasi Data</h1>
                <p class="mt-1 text-sm text-slate-600">Periksa kembali detail pemesanan sebelum submit proposal.</p>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-slate-300 bg-white shadow-sm overflow-hidden ring-1 ring-slate-200">
                <div class="p-5 sm:p-6 border-b border-slate-300 bg-slate-50">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Ringkasan Pemesanan</div>
                            <div class="text-xs text-slate-500">Akun: <?php echo htmlspecialchars($session_id); ?></div>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full border border-slate-300 bg-white px-3 py-1 text-xs font-semibold text-slate-700">
                            Tahap: Upload Proposal
                        </span>
                    </div>
                </div>

                <div class="p-5 sm:p-6">

                    <!-- TABLE VALIDASI -->
                    <!-- TABLE VALIDASI -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border border-slate-300 rounded-xl overflow-hidden">
                            <tbody class="divide-y divide-slate-200">
                                <?php
                                $order = (isset($res[0]) ? $res[0] : null);

                                if (!$order) {
                                    echo '<tr><td class="px-4 py-3">Data pemesanan tidak ditemukan.</td></tr>';
                                } else {
                                    $id = (int)$order['ID_PEMESANAN'];

                                    $mulai   = (isset($order['JAM_PEMESANAN']) ? $order['JAM_PEMESANAN'] : '');
                                    $selesai = (isset($order['JAM_SELESAI']) ? $order['JAM_SELESAI'] : '');
                                    $tipe    = (isset($order['TIPE_JAM']) ? $order['TIPE_JAM'] : 'CUSTOM');

                                    if ($tipe === 'HALF_DAY_PAGI') {
                                        $jam_tampil = "HALF DAY PAGI ($mulai - $selesai)";
                                    } elseif ($tipe === 'HALF_DAY_SIANG') {
                                        $jam_tampil = "HALF DAY SIANG ($mulai - $selesai)";
                                    } elseif ($tipe === 'FULL_DAY') {
                                        $jam_tampil = "FULL DAY ($mulai - $selesai)";
                                    } else {
                                        $jam_tampil = $mulai . " - " . $selesai;
                                    }
                                ?>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Username</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900"><?php echo $order['USERNAME']; ?></td>
                                    </tr>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Tanggal Pemesanan</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900">
                                            <?php
                                            $date = date_create($order['TANGGAL_PEMESANAN']);
                                            echo $date ? date_format($date, 'd F Y') : '';
                                            ?>
                                        </td>
                                    </tr>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Jam Pemesanan</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900"><?php echo $jam_tampil; ?></td>
                                    </tr>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Email</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900"><?php echo $order['EMAIL']; ?></td>
                                    </tr>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Nama Gedung</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900"><?php echo $order['NAMA_GEDUNG']; ?></td>
                                    </tr>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Nama Catering</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900"><?php echo $order['NAMA_PAKET']; ?></td>
                                    </tr>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Jumlah Catering</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900"><?php echo $order['JUMLAH_CATERING']; ?></td>
                                    </tr>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Harga Satuan</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900">Rp. <?php echo number_format((float)$order['HARGA_SATUAN']); ?></td>
                                    </tr>

                                    <tr class="bg-white">
                                        <td class="w-48 px-4 py-3 font-semibold text-slate-700">Total Harga Catering</td>
                                        <td class="px-2 py-3 text-slate-400">:</td>
                                        <td class="px-4 py-3 text-slate-900">Rp. <?php echo number_format((float)$order['TOTAL_HARGA']); ?></td>
                                    </tr>
                                <?php } // end else 
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- FORM UPLOAD -->
                    <div class="mt-6 rounded-xl border border-slate-300 bg-slate-50 p-5 ring-1 ring-slate-200">
                        <?php echo form_open_multipart('home/home/upload_proposal/' . $id, ['id' => 'formUpload']); ?>
                        <input type="hidden" name="request_id" value="<?php echo isset($order['REQUEST_ID']) ? htmlspecialchars($order['REQUEST_ID']) : ''; ?>">
                        <?php if ($this->session->flashdata('upload_error')): ?>
                            <div class="mb-4 p-3 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm">
                                <?php echo $this->session->flashdata('upload_error'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-semibold tracking-widest text-slate-600">KEPERLUAN
                                    ACARA</label>
                                <textarea name="deskripsi-acara" id="textarea1"
                                    class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-700/20 focus:border-blue-700/40"
                                    rows="4" maxlength="200"
                                    placeholder="Tuliskan deskripsi singkat acara..."></textarea>
                                <p class="mt-2 text-xs text-slate-500">Maks 200 karakter.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold tracking-widest text-slate-600">UPLOAD
                                    PROPOSAL</label>

                                <!-- tetap pakai struktur materialize file-field agar tidak merusak logic -->
                                <div class="file-field input-field" style="margin-top:8px;">
                                    <div class="btn"
                                        style="background: #fafafa;border-radius:12px;text-transform:none;">
                                        <span>Pilih File</span>
                                        <input type="file" name="proposal" id="proposal" required accept=".pdf,.doc,.docx">

                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text" placeholder="Upload file PDF/DOCX"
                                            style="border-bottom:1px solid #cbd5e1; box-shadow:none;">
                                    </div>
                                </div>

                                <p class="text-xs text-slate-500">Disarankan PDF/DOCX. Pastikan file bisa dibaca.</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <a href="<?php echo site_url('home/pemesanan'); ?>"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                                Batal
                            </a>

                            <button
                                class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800 active:bg-blue-900"
                                type="submit" name="action">
                                Submit Proposal
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>

                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">© <?php echo date('Y'); ?> Smart Office</p>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formUpload');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                const f = document.getElementById('proposal');
                if (!f || !f.files || f.files.length === 0) {
                    e.preventDefault();
                    alert('Proposal wajib diupload sebelum submit.');
                }
            });
        });
    </script>

</body>

</html>