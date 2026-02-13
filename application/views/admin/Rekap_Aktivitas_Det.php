<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

$no = 1;

// aman kalau belum dikirim controller
$first_period = isset($first_period) ? $first_period : '';
$last_period  = isset($last_period) ? $last_period : '';
$hasil        = isset($hasil) ? $hasil : array();
$id_gedung    = isset($id_gedung) ? $id_gedung : '';
$nama_gedung_filter = isset($nama_gedung_filter) ? $nama_gedung_filter : '';

// fallback: ambil dari GET / URI kalau periode belum kebawa
$CI = &get_instance();
if (empty($first_period)) $first_period = $CI->input->get('first_period', true);
if (empty($last_period))  $last_period  = $CI->input->get('last_period', true);
if (empty($first_period)) $first_period = $CI->uri->segment(3);
if (empty($last_period))  $last_period  = $CI->uri->segment(4);

/* ===== Format tanggal Indonesia ===== */
function formatTanggalIndo($tgl)
{
    if (empty($tgl)) return '-';

    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    $ts = strtotime($tgl);
    if (!$ts) return '-';

    $d = date('d', $ts);
    $m = (int) date('n', $ts);
    $y = date('Y', $ts);

    return $d . ' ' . $bulan[$m] . ' ' . $y;
}

/* ===== PDF link aktif kalau periode lengkap ===== */
$hasPeriod = (!empty($first_period) && !empty($last_period));
$pdfUrl = '#';
if ($hasPeriod) {
    $pdfSegment = site_url('admin/kegiatan_download_pdf/' . rawurlencode($first_period) . '/' . rawurlencode($last_period));
    if (!empty($id_gedung)) {
        $pdfSegment .= '/' . rawurlencode($id_gedung);
    }
    $pdfUrl = $pdfSegment;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Office</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-slate-800">

    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 pl-0 md:pl-64 px-4 md:px-6 pb-10">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-xl font-semibold mb-4">Rekapitulasi Aktivitas</h1>

            <!-- CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
                <!-- HEADER -->
                <div
                    class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-800">Periode:</span>
                        <?php if ($hasPeriod): ?>
                            <?= formatTanggalIndo($first_period); ?>
                            <span class="mx-2">—</span>
                            <?= formatTanggalIndo($last_period); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                        <?php if (!empty($nama_gedung_filter)): ?>
                            <span class="mx-2">|</span>
                            <span class="font-semibold text-slate-800">Gedung:</span>
                            <?= htmlspecialchars($nama_gedung_filter, ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </div>

                    <a class="text-sm font-medium underline <?= $hasPeriod ? 'text-teal-700 hover:text-teal-800' : 'text-slate-400 pointer-events-none' ?>"
                        href="<?= $pdfUrl; ?>" <?= $hasPeriod ? 'target="_blank" rel="noopener"' : ''; ?>>
                        Ekspor ke PDF
                    </a>
                </div>

                <div class="p-5">
                    <!-- ✅ TABLE WRAP: TANPA overflow-y (tidak ada scroll vertikal di box) -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <!-- hanya overflow-x untuk layar kecil -->
                        <div id="tableScroll" class="overflow-x-auto">
                            <table id="rekapTable" class="min-w-[900px] w-full text-sm table-auto">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr class="text-left">
                                        <th class="px-4 py-3 font-semibold text-slate-700 w-[70px] text-center">No</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700">Nama Gedung</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700">Tanggal Pemesanan</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700">Tanggal Approval</th>

                                        <!-- ✅ kolom baru -->
                                        <th class="px-4 py-3 font-semibold text-slate-700">Total Peserta</th>

                                        <th class="px-4 py-3 font-semibold text-slate-700">Kegiatan</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 whitespace-nowrap">Jam
                                            Kegiatan</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700">Nama Pemesan</th>
                                    </tr>
                                </thead>

                                <tbody id="rekapBody" class="divide-y divide-slate-200">
                                    <?php if (!empty($hasil) && is_array($hasil)): ?>
                                        <?php
                                        $totalSumParticipants = 0;
                                        foreach ($hasil as $row):
                                            $tp = !empty($row['TOTAL_PESERTA']) ? (int)$row['TOTAL_PESERTA'] : 0;
                                        ?>
                                            <?php
                                            // Tanggal Pemesanan (final kalau ada)
                                            $date_pemesanan = null;
                                            if (!empty($row['TANGGAL_FINAL_PEMESANAN'])) {
                                                $date_pemesanan = date_create($row['TANGGAL_FINAL_PEMESANAN']);
                                            } elseif (!empty($row['TANGGAL_PEMESANAN'])) {
                                                $date_pemesanan = date_create($row['TANGGAL_PEMESANAN']);
                                            }

                                            // Tanggal Approval
                                            $date_approval = !empty($row['TANGGAL_APPROVAL']) ? date_create($row['TANGGAL_APPROVAL']) : null;

                                            // Tanggal Kegiatan (ambil dari TANGGAL_PEMESANAN; fallback ke final)
                                            $date_kegiatan = !empty($row['TANGGAL_PEMESANAN']) ? date_create($row['TANGGAL_PEMESANAN']) : null;
                                            if (!$date_kegiatan && $date_pemesanan) $date_kegiatan = $date_pemesanan;

                                            // Jam
                                            $jamMulai   = !empty($row['JAM_MULAI']) ? $row['JAM_MULAI'] : (!empty($row['JAM_PEMESANAN']) ? $row['JAM_PEMESANAN'] : null);
                                            $jamSelesai = !empty($row['JAM_SELESAI']) ? $row['JAM_SELESAI'] : null;

                                            $jamText = '-';
                                            if (!empty($jamMulai) && !empty($jamSelesai)) {
                                                $jamText = date('H:i', strtotime($jamMulai)) . ' - ' . date('H:i', strtotime($jamSelesai));
                                            } elseif (!empty($jamMulai)) {
                                                $jamText = date('H:i', strtotime($jamMulai));
                                            }
                                            ?>
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-3 text-center"><?= $no++; ?></td>

                                                <td class="px-4 py-3">
                                                    <?= !empty($row['NAMA_GEDUNG']) ? htmlspecialchars($row['NAMA_GEDUNG'], ENT_QUOTES, 'UTF-8') : '-'; ?>
                                                </td>

                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <?= !empty($row['TANGGAL_FINAL_PEMESANAN']) ? format_tanggal_indo($row['TANGGAL_FINAL_PEMESANAN']) : '-'; ?>
                                                </td>

                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <?= !empty($row['TANGGAL_APPROVAL']) ? format_tanggal_indo($row['TANGGAL_APPROVAL']) : '-'; ?>
                                                </td>

                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <?= $tp; ?>
                                                </td>

                                                <td class="px-4 py-3 whitespace-normal break-words">
                                                    <?= !empty($row['DESKRIPSI_ACARA']) ? htmlspecialchars($row['DESKRIPSI_ACARA'], ENT_QUOTES, 'UTF-8') : '-'; ?>
                                                </td>

                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <?= htmlspecialchars($jamText, ENT_QUOTES, 'UTF-8'); ?>
                                                </td>

                                                <td class="px-4 py-3">
                                                    <?= !empty($row['NAMA_LENGKAP']) ? htmlspecialchars($row['NAMA_LENGKAP'], ENT_QUOTES, 'UTF-8') : '-'; ?>
                                                </td>
                                            </tr>
                                        <?php
                                            $totalSumParticipants += $tp;
                                        endforeach;
                                        ?>
                                        <tr class="bg-slate-50 font-bold border-t-2 border-slate-300">
                                            <td colspan="4" class="px-4 py-3 text-right">GRAND TOTAL PESERTA</td>
                                            <td class="px-4 py-3 text-center text-blue-700"><?= $totalSumParticipants; ?></td>
                                            <td colspan="3"></td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="px-4 py-6 text-center text-slate-500">Tidak ada data.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <button id="prevBtn"
                            class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40 disabled:cursor-not-allowed">
                            Prev
                        </button>

                        <span id="pageInfo" class="text-sm text-slate-600 text-center"></span>

                        <div class="flex items-center gap-3">
                            <select id="rowsPerPage" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                <option value="5">5 rows</option>
                                <option value="10" selected>10 rows</option>
                                <option value="25">25 rows</option>
                            </select>

                            <button id="nextBtn"
                                class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40 disabled:cursor-not-allowed">
                                Next
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /CARD -->
        </div>
    </main>

    <script>
        (function() {
            const tbody = document.getElementById('rekapBody');
            if (!tbody) return;

            let rows = Array.from(tbody.querySelectorAll('tr'));
            const onlyEmptyRow = rows.length === 1 && rows[0].innerText.toLowerCase().includes('tidak ada data');
            if (onlyEmptyRow) return;

            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const pageInfo = document.getElementById('pageInfo');
            const rowsPerPageSelect = document.getElementById('rowsPerPage');

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;

            function totalPages() {
                return Math.max(1, Math.ceil(rows.length / rowsPerPage));
            }

            function render() {
                const tp = totalPages();
                if (currentPage > tp) currentPage = tp;

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, idx) => {
                    row.style.display = (idx >= start && idx < end) ? '' : 'none';
                });

                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= tp;

                const totalRows = rows.length;
                const showingFrom = totalRows === 0 ? 0 : start + 1;
                const showingTo = Math.min(end, totalRows);

                pageInfo.textContent =
                    `Page ${currentPage} of ${tp} • Showing ${showingFrom}-${showingTo} of ${totalRows}`;

                // renumber
                let visibleNo = start + 1;
                rows.forEach((row, idx) => {
                    if (idx >= start && idx < end) {
                        const firstCell = row.querySelector('td');
                        if (firstCell) firstCell.textContent = visibleNo++;
                    }
                });

                // scroll halaman ke atas tabel saat pindah halaman (biar nyaman)
                const tableTop = document.getElementById('rekapTable');
                if (tableTop) tableTop.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    render();
                }
            });

            nextBtn.addEventListener('click', function() {
                if (currentPage < totalPages()) {
                    currentPage++;
                    render();
                }
            });

            rowsPerPageSelect.addEventListener('change', function() {
                rowsPerPage = parseInt(this.value, 10) || 10;
                currentPage = 1;
                render();
            });

            render();
        })();
    </script>

</body>

</html>