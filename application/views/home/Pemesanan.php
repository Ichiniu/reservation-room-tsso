<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$id_gedung = $this->uri->segment(3);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed"
        href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Pemesanan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Materialize core CSS -->
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet">

    <style>
    /* =============================
           SCROLL HANYA DI AREA TABEL
           ============================= */
    .table-scroll {
        max-height: 420px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #f8fafc;
    }
    </style>
</head>

<!-- =============================
     🔥 DIUBAH:
     body dibuat flex column
     supaya footer selalu di bawah
     ============================= -->

<body class="h-screen flex flex-col bg-slate-200 text-black">

    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <!-- =============================
         🔥 DIUBAH:
         wrapper konten pakai flex-1
         agar mendorong footer ke bawah
         ============================= -->
    <main class="flex-1">

        <div class="max-w-7xl mx-auto px-4 py-8">
            <section class="rounded-2xl bg-white border border-slate-300 ring-1 ring-slate-200 shadow-sm p-6">

                <!-- =============================
                     SCROLL HANYA DI TABEL
                     ============================= -->
                <div class="table-scroll rounded-xl border border-slate-300 bg-white">
                    <table class="min-w-full bordered">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Tanggal Pemesanan</th>
                                <th class="px-4 py-3 text-left">ID Pemesanan</th>
                                <th class="px-4 py-3 text-left">Jam Pemesanan</th>
                                <th class="px-4 py-3 text-left">Paket Catering</th>
                                <th class="px-4 py-3 text-left">Nama Ruangan</th>
                                <th class="px-4 py-3 text-left">Status Pemesanan</th>
                                <th class="px-4 py-3 text-left">Details</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($res as $row):
                                $id_pemesanan = $row['ID_PEMESANAN'];
                            ?>
                            <tr class="table-row">
                                <td class="px-4 py-3">
                                    <?php
                                    $date = date_create($row['TANGGAL_PEMESANAN']);
                                    echo date_format($date, 'd F Y');
                                    ?>
                                </td>
                                <td class="px-4 py-3"><?= $id_pemesanan; ?></td>
                                <td class="px-4 py-3">
                                    <?php if (!empty($row['JAM_PEMESANAN']) && !empty($row['JAM_SELESAI'])): ?>
                                    <?= date('H:i', strtotime($row['JAM_PEMESANAN'])); ?> -
                                    <?= date('H:i', strtotime($row['JAM_SELESAI'])); ?> WIB
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3"><?= $row['NAMA_PAKET']; ?></td>
                                <td class="px-4 py-3"><?= $row['NAMA_GEDUNG']; ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                    $status = strtoupper(trim($row['STATUS']));
                                    if ($status === "REJECTED") {
                                        echo '<font color="red">'.$row['STATUS'].'</font>';
                                    } else if ($status === "PROPOSAL APPROVE") {
                                        echo '<font color="blue">'.$row['STATUS'].'</font>';
                                    } else if ($status === "APPROVE & PAID") {
                                        echo '<font color="green">'.$row['STATUS'].'</font>';
                                    } else if ($status === "SUBMITED") {
                                        echo '<font color="purple">'.$row['STATUS'].'</font>';
                                    } else {
                                        echo $row['STATUS'];
                                    }
                                    ?>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="<?= site_url('home/pemesanan/details/'.$id_pemesanan); ?>">
                                        <i class="material-icons">open_in_new</i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($rows < 1): ?>
                    <h5 class="text-center py-4">
                        ---------- <?= $no_data; ?> ----------
                    </h5>
                    <?php endif; ?>
                </div>

                <!-- PAGINATION -->
                <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button id="prevBtn" class="px-4 py-2 rounded-xl border bg-white">Prev</button>
                    <span id="pageInfo" class="text-sm text-slate-600"></span>
                    <div class="flex gap-3">
                        <select id="rowsPerPage" class="rounded-xl border px-3 py-2">
                            <option value="5">5 rows</option>
                            <option value="10" selected>10 rows</option>
                            <option value="25">25 rows</option>
                        </select>
                        <button id="nextBtn" class="px-4 py-2 rounded-xl border bg-white">Next</button>
                    </div>
                </div>

            </section>
        </div>

    </main>

    <!-- =============================
         FOOTER (AUTO STICKY)
         ============================= -->
    <?php $this->load->view('components/footer'); ?>

    <!-- =============================
         PAGINATION + SORT TERBARU
         ============================= -->
    <script>
    const tbody = document.querySelector('tbody');
    let rows = Array.from(document.querySelectorAll('.table-row'));

    const rowsSelect = document.getElementById('rowsPerPage');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageInfo = document.getElementById('pageInfo');

    let rowsPerPage = parseInt(rowsSelect.value, 10);
    let currentPage = 1;

    // 🔥 DIUBAH:
    // Sort data TERBARU dulu
    rows.sort((a, b) => {
        const dateA = new Date(a.children[0].innerText);
        const dateB = new Date(b.children[0].innerText);
        return dateB - dateA;
    });

    tbody.innerHTML = '';
    rows.forEach(r => tbody.appendChild(r));

    function render() {
        rows.forEach(r => r.style.display = 'none');

        const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.slice(start, end).forEach(r => r.style.display = '');
        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    prevBtn.onclick = () => {
        currentPage--;
        render();
    };
    nextBtn.onclick = () => {
        currentPage++;
        render();
    };
    rowsSelect.onchange = () => {
        currentPage = 1;
        rowsPerPage = rowsSelect.value;
        render();
    };

    render();
    </script>

</body>

</html>