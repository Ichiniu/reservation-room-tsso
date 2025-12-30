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

    <!-- Favicons-->
    <link rel="apple-touch-icon-precomposed"
        href="<?php echo base_url(); ?>assets/home/assets/img/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage"
        content="<?php echo base_url(); ?>assets/home/assets/img/favicon/mstile-144x144.png">
    <link rel="icon" href="<?php echo base_url(); ?>assets/home/assets/img/favicon/favicon-32x32.png" sizes="32x32">

    <title>Pemesanan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Materialize core CSS -->
    <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/home/template.css" rel="stylesheet" type="text/css">

    <style>
    /* Biar yang scroll cuma area data tabel */
    .table-scroll {
        max-height: 420px;
        /* silakan ubah tinggi sesuai kebutuhan */
        overflow-y: auto;
        /* scroll vertikal */
        overflow-x: auto;
        /* kalau tabel melebar, scroll horizontal */
    }

    /* bikin header tabel tetap terlihat saat scroll */
    .table-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #f8fafc;
        /* warna mirip bg-slate-50 */
    }
    </style>
</head>

<body class="min-h-screen text-black bg-slate-200">

    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <section class="rounded-2xl bg-white border border-slate-300 ring-1 ring-slate-200 shadow-sm p-6">

            <!-- WRAPPER SCROLL: yang scroll hanya tabel -->
            <div class="table-scroll rounded-xl border border-slate-300 bg-white">
                <table class="min-w-full bordered">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Tanggal Pemesanan</th>
                            <th class="px-4 py-3 text-left">ID Pemesanan</th>
                            <th class="px-4 py-3 text-left">Jam Pemesanan</th>
                            <th class="px-4 py-3 text-left">Paket Catering</th>
                            <th class="px-4 py-3 text-left">Nama Gedung</th>
                            <th class="px-4 py-3 text-left">Status Pemesanan</th>
                            <th class="px-4 py-3 text-left">Details</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($res as $row):
                            $id_pemesanan = $row['ID_PEMESANAN'];
                        ?>
                        <tr class="table-row">
                            <td class="px-4 py-3">
                                <?php $date = date_create($row['TANGGAL_PEMESANAN']); echo date_format($date, 'd F Y') ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php echo $id_pemesanan; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if (!empty($row['JAM_PEMESANAN']) && !empty($row['JAM_SELESAI'])): ?>
                                <?= date('H:i', strtotime($row['JAM_PEMESANAN'])); ?> -
                                <?= date('H:i', strtotime($row['JAM_SELESAI'])); ?> WIB
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-3">
                                <?php echo $row['NAMA_PAKET']; ?>
                            </td>

                            <td class="px-4 py-3">
                                <?php echo $row['NAMA_GEDUNG']; ?>
                            </td>

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
                                    } else if ($status === "PROCESS") {
                                        echo '<font color="black">'.$row['STATUS'].'</font>';
                                    } else {
                                        echo $row['STATUS'];
                                    }
                                ?>
                            </td>

                            <td class="px-4 py-3">
                                <a href="<?php echo site_url('home/pemesanan/details/'.$id_pemesanan.''); ?>">
                                    <i class="material-icons">open_in_new</i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if($rows < 1): ?>
                <h5>
                    <center>------------- <?php echo $no_data; ?> -----------</center>
                </h5>
                <?php endif; ?>
            </div>

            <!-- PAGINATION -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button id="prevBtn"
                    class="px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    Prev
                </button>

                <span id="pageInfo" class="text-sm text-slate-600 text-center"></span>

                <div class="flex items-center gap-3 justify-end">
                    <select id="rowsPerPage" class="rounded-xl bg-white border border-slate-300 px-3 py-2 text-sm">
                        <option value="5">5 rows</option>
                        <option value="10" selected>10 rows</option>
                        <option value="25">25 rows</option>
                    </select>

                    <button id="nextBtn"
                        class="px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>

        </section>
    </div>

    <?php $this->load->view('components/footer'); ?>

    <!-- JS -->
    <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
    <script src="<?php echo base_url(); ?>assets/home/index.js"></script>

    <script>
    $(document).ready(function() {
        $(".button-collapse").sideNav({
            menuWidth: 260,
            edge: 'left',
            closeOnClick: false,
            draggable: true
        });

        $(".button-collapse").on("click", function() {
            $("body").toggleClass("nav-open");
        });

        $(document).mouseup(function(e) {
            let sb = $(".side-nav");
            if (!sb.is(e.target) && sb.has(e.target).length === 0) {
                $("body").removeClass("nav-open");
            }
        });
    });
    </script>

    <!-- PAGINATION SCRIPT (TANPA FILTER) -->
    <script>
    const rows = Array.from(document.querySelectorAll('tbody .table-row'));
    const rowsSelect = document.getElementById('rowsPerPage');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageInfo = document.getElementById('pageInfo');

    let rowsPerPage = parseInt(rowsSelect.value, 10);
    let currentPage = 1;

    function render() {
        rows.forEach(r => r.style.display = 'none');

        const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.slice(start, end).forEach(r => r.style.display = '');

        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            render();
        }
    });

    nextBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
        if (currentPage < totalPages) {
            currentPage++;
            render();
        }
    });

    rowsSelect.addEventListener('change', () => {
        rowsPerPage = parseInt(rowsSelect.value, 10);
        currentPage = 1;
        render();
    });

    render();
    </script>

</body>

</html>