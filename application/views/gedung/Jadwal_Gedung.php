<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Jadwal Gedung</title>

  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen text-black
  bg-slate-200">

<?php $this->load->view('components/navbar'); ?>
<?php $this->load->view('components/header'); ?>

<div class="max-w-6xl mx-auto px-4 py-8">

<section class="rounded-3xl bg-white ring-white/15 shadow-xl p-6">

  <!-- HEADER -->
  <div class="mb-6">
    <h1 class="text-2xl font-semibold text-text-heading">Jadwal Penggunaan Gedung</h1>
    <p class="text-sm text-text-subheading">Data jadwal penggunaan gedung</p>
  </div>

  <!-- FILTER -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <!-- BULAN -->
    <div>
      <label class="block text-xs font-semibold text-text-content mb-1">Bulan</label>
      <select id="filterBulan"
        class="w-full rounded-xl bg-dropdown-bg border border-dropdown-border px-4 py-3">
        <option value="">Semua Bulan</option>
        <option value="01">Januari</option>
        <option value="02">Februari</option>
        <option value="03">Maret</option>
        <option value="04">April</option>
        <option value="05">Mei</option>
        <option value="06">Juni</option>
        <option value="07">Juli</option>
        <option value="08">Agustus</option>
        <option value="09">September</option>
        <option value="10">Oktober</option>
        <option value="11">November</option>
        <option value="12">Desember</option>
      </select>
    </div>

    <!-- TAHUN -->
    <div>
      <label class="block text-xs font-semibold text-text-content mb-1">Tahun</label>
      <select id="filterTahun"
        class="w-full rounded-xl bg-dropdown-bg border border-dropdown-border px-4 py-3">
        <option value="">Semua Tahun</option>
        <?php for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++): ?>
          <option value="<?= $y ?>"><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <!-- RESET -->
    <div class="flex items-end">
      <button onclick="resetFilter()"
        class="w-full rounded-xl px-4 py-3 text-white bg-btn-primary ring-white/15 hover:bg-btn-hover transition">
        Reset
      </button>
    </div>

  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto rounded-2xl ring-1 bg-card-bg  ring-white/15">
    <table class="min-w-full">
      <thead class="bg-white/10 text-xs">
        <tr>
          <th class="px-4 py-3 text-left">NO</th>
          <th class="px-4 py-3 text-left">TANGGAL</th>
          <th class="px-4 py-3 text-left">JAM</th>
          <th class="px-4 py-3 text-left">GEDUNG</th>
          <th class="px-4 py-3 text-left">DESKRIPSI</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-white/10">
      <?php if (!empty($jadwal)): ?>
        <?php $no = 1; foreach ($jadwal as $row): ?>
          <tr data-date="<?= date('Y-m-d', strtotime($row['TANGGAL_FINAL_PEMESANAN'])) ?>">
            <td class="px-4 py-3"><?= $no++ ?></td>
            <td class="px-4 py-3">
              <?= date('d M Y', strtotime($row['TANGGAL_FINAL_PEMESANAN'])) ?>
            </td>
            <td class="px-4 py-3">
              <?= $row['JAM_MULAI'] ?> - <?= $row['JAM_SELESAI'] ?>
            </td>
            <td class="px-4 py-3 font-semibold text-text-subheading">
              <?= $row['NAMA_GEDUNG'] ?>
            </td>
            <td class="px-4 py-3 text-text-subheading">
              <?= $row['DESKRIPSI_ACARA'] ?>
            </td>
          </tr>
        <?php endforeach ?>
      <?php endif ?>
      </tbody>
    </table>
  </div>

  <!-- PAGINATION -->
  <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

    <!-- PREV -->
    <button id="prevBtn"
      class="px-4 py-2 rounded-xl bg-white/10 ring-1 ring-white/15 hover:bg-white/20">
      Prev
    </button>

    <!-- INFO -->
    <span id="pageInfo" class="text-sm text-text-muted text-center"></span>

    <!-- NEXT + ROWS -->
    <div class="flex items-center gap-3 justify-end">
      <select id="rowsPerPage"
        class="rounded-xl bg-white/10 border border-white/15 px-3 py-2 text-sm">
        <option value="5">5 rows</option>
        <option value="10" selected>10 rows</option>
        <option value="25">25 rows</option>
      </select>

      <button id="nextBtn"
        class="px-4 py-2 rounded-xl bg-white/10 ring-1 ring-white/15 hover:bg-white/20">
        Next
      </button>
    </div>

  </div>

</section>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
const rows = Array.from(document.querySelectorAll('tbody tr[data-date]'));

const bulanSelect = document.getElementById('filterBulan');
const tahunSelect = document.getElementById('filterTahun');
const rowsSelect = document.getElementById('rowsPerPage');

const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const pageInfo = document.getElementById('pageInfo');

let rowsPerPage = parseInt(rowsSelect.value);
let currentPage = 1;
let filteredRows = [];

// DEFAULT BULAN & TAHUN SEKARANG
bulanSelect.value = String(new Date().getMonth() + 1).padStart(2, '0');
tahunSelect.value = new Date().getFullYear();

function applyFilter() {
  const bulan = bulanSelect.value;
  const tahun = tahunSelect.value;

  filteredRows = rows.filter(row => {
    const date = row.dataset.date;
    if (!bulan && !tahun) return true;
    if (bulan && !date.includes('-' + bulan)) return false;
    if (tahun && !date.startsWith(tahun)) return false;
    return true;
  });

  currentPage = 1;
  render();
}

function resetFilter() {
  bulanSelect.value = '';
  tahunSelect.value = '';
  filteredRows = [...rows];
  currentPage = 1;
  render();
}

function render() {
  rows.forEach(r => r.style.display = 'none');

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;

  filteredRows.slice(start, end).forEach(r => r.style.display = '');

  const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
  pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;

  prevBtn.disabled = currentPage === 1;
  nextBtn.disabled = currentPage === totalPages;
}

prevBtn.onclick = () => {
  if (currentPage > 1) {
    currentPage--;
    render();
  }
};

nextBtn.onclick = () => {
  const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    render();
  }
};

bulanSelect.addEventListener('change', applyFilter);
tahunSelect.addEventListener('change', applyFilter);

rowsSelect.addEventListener('change', () => {
  rowsPerPage = parseInt(rowsSelect.value);
  currentPage = 1;
  render();
});

// INIT
applyFilter();
</script>

</body>
</html>
