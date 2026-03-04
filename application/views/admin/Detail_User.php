<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');

function safe_d($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

$u        = $user ?? [];
$username = $u['USERNAME'] ?? '';
$tipe     = strtoupper($u['perusahaan'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail User — <?= safe_d($username) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-slate-100 min-h-screen">

    <?php $this->load->view('admin/components/sidebar'); ?>

    <main class="pt-24 md:pl-64 px-4 md:px-6 pb-10 transition-all duration-300">

        <!-- BREADCRUMB -->
        <nav class="mb-4 flex items-center gap-2 text-sm text-slate-500">
            <a href="<?= site_url('admin/dashboard') ?>" class="hover:text-slate-800">Dashboard</a>
            <span>/</span>
            <a href="<?= site_url('admin/list-user') ?>" class="hover:text-slate-800">Daftar User</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Detail User</span>
        </nav>

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Detail User</h1>
                <p class="text-sm text-slate-500">Informasi lengkap akun pengguna</p>
            </div>
            <div class="flex gap-2">
                <a href="<?= site_url('admin/edit-user/' . urlencode($username)) ?>"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition shadow-sm">
                    <span class="material-icons text-base">edit</span>
                    Edit User
                </a>
                <a href="<?= site_url('admin/list-user') ?>"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                    <span class="material-icons text-base">arrow_back</span>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- ===== KARTU PROFIL ===== -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col items-center text-center">

                    <!-- Avatar -->
                    <?php if (!empty($u['FOTO_PROFIL'])): ?>
                        <img src="<?= safe_d($u['FOTO_PROFIL']) ?>" alt="Foto"
                             class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-md mb-4">
                    <?php else: ?>
                        <div class="h-24 w-24 rounded-full bg-gradient-to-br from-sky-400 to-indigo-500 flex items-center justify-center text-white text-3xl font-bold shadow-md mb-4">
                            <?= strtoupper(substr($username, 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <h2 class="text-lg font-bold text-slate-800"><?= safe_d($u['NAMA_LENGKAP'] ?? $username) ?></h2>
                    <p class="text-sm text-slate-500 mt-0.5">@<?= safe_d($username) ?></p>

                    <!-- Badge tipe -->
                    <div class="mt-3">
                        <?php if ($tipe === 'INTERNAL'): ?>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <span class="material-icons text-sm">domain</span> Internal
                            </span>
                        <?php elseif ($tipe === 'EKSTERNAL'): ?>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-700">
                                <span class="material-icons text-sm">business</span> Eksternal
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">—</span>
                        <?php endif; ?>
                    </div>

                    <div class="mt-5 w-full border-t border-slate-100 pt-4 space-y-2 text-sm text-left">
                        <div class="flex items-center gap-2 text-slate-600">
                            <span class="material-icons text-base text-slate-400">email</span>
                            <span class="break-all"><?= safe_d($u['EMAIL'] ?? '-') ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-600">
                            <span class="material-icons text-base text-slate-400">phone</span>
                            <?= safe_d($u['NO_TELEPON'] ?? '-') ?>
                        </div>
                        <?php if (!empty($u['TANGGAL_LAHIR'])): ?>
                        <div class="flex items-center gap-2 text-slate-600">
                            <span class="material-icons text-base text-slate-400">cake</span>
                            <?= format_tanggal_indo($u['TANGGAL_LAHIR']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ===== INFO DETAIL ===== -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Info Akun -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="material-icons text-indigo-500">manage_accounts</span>
                        Informasi Akun
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div>
                            <dt class="text-xs font-semibold tracking-widest text-slate-400 mb-0.5">USERNAME</dt>
                            <dd class="font-medium text-slate-800"><?= safe_d($username) ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold tracking-widest text-slate-400 mb-0.5">NAMA LENGKAP</dt>
                            <dd class="text-slate-700"><?= safe_d($u['NAMA_LENGKAP'] ?? '-') ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold tracking-widest text-slate-400 mb-0.5">EMAIL</dt>
                            <dd class="text-slate-700 break-all"><?= safe_d($u['EMAIL'] ?? '-') ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold tracking-widest text-slate-400 mb-0.5">NO TELEPON</dt>
                            <dd class="text-slate-700"><?= safe_d($u['NO_TELEPON'] ?? '-') ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold tracking-widest text-slate-400 mb-0.5">TANGGAL LAHIR</dt>
                            <dd class="text-slate-700">
                                <?= !empty($u['TANGGAL_LAHIR']) ? format_tanggal_indo($u['TANGGAL_LAHIR']) : '-' ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold tracking-widest text-slate-400 mb-0.5">TIPE</dt>
                            <dd>
                                <?php if ($tipe === 'INTERNAL'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Internal</span>
                                <?php elseif ($tipe === 'EKSTERNAL'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-700">Eksternal</span>
                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Info Perusahaan -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="material-icons text-sky-500">business</span>
                        Informasi Perusahaan
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <?php if ($tipe === 'INTERNAL'): ?>
                        <div>
                            <dt class="text-xs font-semibold tracking-widest text-slate-400 mb-0.5">DEPARTEMEN</dt>
                            <dd class="text-slate-700"><?= safe_d($u['departemen'] ?? '-') ?></dd>
                        </div>
                        <?php elseif ($tipe === 'EKSTERNAL'): ?>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold tracking-widest text-slate-400 mb-0.5">NAMA PERUSAHAAN / INSTANSI</dt>
                            <dd class="text-slate-700"><?= safe_d($u['nama_perusahaan'] ?? '-') ?></dd>
                        </div>
                        <?php else: ?>
                        <div class="sm:col-span-2 text-slate-400">—</div>
                        <?php endif; ?>
                    </dl>
                </div>

                <!-- Alamat -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="material-icons text-rose-400">location_on</span>
                        Alamat
                    </h3>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        <?= !empty($u['ALAMAT']) ? safe_d($u['ALAMAT']) : '<span class="text-slate-400">—</span>' ?>
                    </p>
                </div>

                <!-- Riwayat Pemesanan -->
                <?php if (!empty($bookings)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-base font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="material-icons text-amber-500">history</span>
                        Riwayat Pemesanan
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 rounded-xl">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">ID</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">Ruangan</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">Tanggal</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">Jam</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($bookings as $b): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 text-slate-500 font-mono text-xs">PMSN<?= str_pad($b['ID_PEMESANAN'] ?? '', 6, '0', STR_PAD_LEFT) ?></td>
                                    <td class="px-3 py-2 text-slate-700"><?= safe_d($b['NAMA_GEDUNG'] ?? '-') ?></td>
                                    <td class="px-3 py-2 text-slate-600"><?= safe_d($b['TANGGAL_PEMESANAN'] ?? '-') ?></td>
                                    <td class="px-3 py-2 text-slate-600"><?= safe_d(substr($b['JAM_PEMESANAN'] ?? '', 0, 5)) ?> – <?= safe_d(substr($b['JAM_SELESAI'] ?? '', 0, 5)) ?></td>
                                    <td class="px-3 py-2">
                                        <?php $st = (int)($b['STATUS'] ?? -1); ?>
                                        <?php if ($st === 3): ?>
                                            <span class="px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700 font-semibold">Confirmed</span>
                                        <?php elseif ($st === 1): ?>
                                            <span class="px-2 py-0.5 rounded-full text-xs bg-sky-100 text-sky-700 font-semibold">Approved</span>
                                        <?php elseif ($st === 4): ?>
                                            <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-600 font-semibold">Rejected</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-700 font-semibold">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

</body>
</html>
