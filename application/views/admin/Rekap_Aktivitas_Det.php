<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

$no = 1;

$first_date_period  = !empty($first_period) ? date_create($first_period) : null;
$second_date_period = !empty($last_period) ? date_create($last_period) : null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AKTIVITAS FLO</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
    /* biar scroll bar tidak bikin header/body “geser” di beberapa browser */
    .scroll-stable {
        scrollbar-gutter: stable;
    }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <!-- SIDEBAR (kalau sidebar kamu masih pakai materialize, sidebar tetap bisa dipakai;
       kalau sidebar kamu butuh js materialize, kamu perlu ubah sidebar ke tailwind juga) -->
    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-6 pb-10">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-xl font-semibold mb-4">Rekapitulasi Aktivitas</h1>

            <!-- CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
                <!-- HEADER -->
                <div
                    class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-800">Periode:</span>
                        <?php if ($first_date_period && $second_date_period): ?>
                        <?= date_format($first_date_period, 'd F Y'); ?>
                        <span class="mx-2">—</span>
                        <?= date_format($second_date_period, 'd F Y'); ?>
                        <?php else: ?>
                        -
                        <?php endif; ?>
                    </div>

                    <a class="text-sm font-medium text-teal-700 hover:text-teal-800 underline"
                        href="<?= site_url('admin/kegiatan_download_pdf/' . $first_period . '/' . $last_period); ?>">
                        Ekspor ke PDF
                    </a>
                </div>

                <div class="p-5">
                    <!-- TABLE WRAP: HANYA INI YANG SCROLL -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="max-h-[420px] overflow-auto scroll-stable">
                            <table id="rekapTable" class="min-w-[980px] w-full text-sm">
                                <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200">
                                    <tr class="text-left">
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[70px]">No</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[180px]">Nama Gedung</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[160px]">Tanggal Pemesanan
                                        </th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[160px]">Tanggal Approval
                                        </th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[240px]">Kegiatan</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[140px]">Jam Kegiatan</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[200px]">Nama Pemesan</th>
                                    </tr>
                                </thead>

                                <tbody id="rekapBody" class="divide-y divide-slate-200">
                                    <?php if (!empty($hasil)): ?>
                                    <?php foreach ($hasil as $row): ?>
                                    <?php
                        $date = !empty($row['TANGGAL_FINAL_PEMESANAN']) ? date_create($row['TANGGAL_FINAL_PEMESANAN']) : null;
                        $date_approval = !empty($row['TANGGAL_APPROVAL']) ? date_create($row['TANGGAL_APPROVAL']) : null;

                        $jamMulai = !empty($row['JAM_MULAI']) ? $row['JAM_MULAI'] : (!empty($row['JAM_PEMESANAN']) ? $row['JAM_PEMESANAN'] : null);
                        $jamSelesai = !empty($row['JAM_SELESAI']) ? $row['JAM_SELESAI'] : null;

                                $jamText = '-';
                                if (!empty($jamMulai) && !empty($jamSelesai)) {
                                    $jamText = date('H:i', strtotime($jamMulai)) . ' - ' . date('H:i', strtotime($jamSelesai));
                                } elseif (!empty($jamMulai)) {
                                    $jamText = date('H:i', strtotime($jamMulai));
                                }
                                ?>
                                    <tr>
                                        <td><?php echo $no++?></td>
                                        <td><?php echo $row['NAMA_GEDUNG']?></td>
                                        <td><?php echo date_format($date, 'd M Y')?></td>
                                        <td><?php echo date_format($date_approval, 'd M Y')?></td>
                                        <td><?php echo $row['DESKRIPSI_ACARA']?></td>
                                        <td><?php echo $jamText; ?></td>
                                        <td><?php echo $row['NAMA_LENGKAP']?></td>
                                    </tr>
                                    <?php endforeach;?>
                            </table>
                            <table style="display: inline-block;">
                                <tr>
                                    <td><b>Periode:</b></td>
                                    <td><?php echo date_format($first_date_period, 'd F Y') ?></td>
                                    <td><b>To</b></td>
                                    <td><?php echo date_format($second_date_period, 'd F Y') ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <a
                                            href="<?php echo site_url('admin/kegiatan_download_pdf/'.$first_period.'/'.$last_period.'') ?>">Ekspor
                                            ke PDF</a>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
                <main class="">
                </main>
                <!-- Materialize core JavaScript -->
                <!-- Placed at the end of the document so the pages load faster -->
                <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
                <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
                <script src="<?php echo base_url(); ?>assets/home/index.js"></script>
                <script type="text/javascript">
                var startDate = document.getElementById("start_date");
                var label = document.getElementById("labelDari");
                var labelsampai = document.getElementById("labelSampai");
                var endDate = document.getElementById("end_date");
                var btnProses = document.getElementById("btnProses");
                var btnFilter = document.getElementById("btnFilter");

                function unhideElement() {
                    if (startDate.hidden = true) {
                        startDate.hidden = false;
                        label.hidden = false;
                        labelsampai.hidden = false;
                        endDate.hidden = false;
                        btnProses.hidden = false
                        btnFilter.disabled = true;

                    } else {
                        hideElement();
                    }
                }

                function hideElement() {
                    startDate.hidden = true;
                    label.hidden = true;
                    endDate.hidden = true;
                    btnProses.hidden = true;
                }

                function btnProsesAlert() {
                    if (startDate.value == "") {
                        alert("Harap Isi Form Tanggal!");
                        return false;
                    } else if (endDate.value == "") {
                        alert("Harap Isi Form Tanggal!");
                        return false;
                    }
                }
                </script>
                <script>
                $(document).ready(function() {

                    $(".button-collapse").sideNav({
                        menuWidth: 260,
                        edge: 'left',
                        closeOnClick: false,
                        draggable: true
                    });

                    // OPEN / CLOSE SIDEBAR + SHIFT CONTENT
                    $(".button-collapse").on("click", function() {
                        $("body").toggleClass("nav-open");
                    });

                    // CLOSE JIKA KLIK LUAR SIDEBAR
                    $(document).mouseup(function(e) {
                        let sb = $(".side-nav");
                        if (!sb.is(e.target) && sb.has(e.target).length === 0) {
                            $("body").removeClass("nav-open");
                        }
                    });

                });
                </script>
</body>

</html>