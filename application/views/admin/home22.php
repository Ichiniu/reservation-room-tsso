<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#EEF2F7]">

    <div class="flex min-h-screen">

        <!-- ================= SIDEBAR ================= -->
        <?php $this->load->view('admin/partials/sidebar'); ?>

        <!-- ================= MAIN CONTENT ================= -->
        <main class="flex-1 p-6 overflow-y-auto">

            <!-- ===== HEADER ===== -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Welcome, admin!</h1>
                <p class="text-gray-500">Here is an overview of the facility bookings and user activities.</p>
            </div>

            <!-- ===== STAT CARD ===== -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">

                <!-- Total Users -->
                <div class="bg-blue-50 rounded-xl p-5 shadow">
                    <p class="text-gray-600 font-semibold">Total Users</p>
                    <h2 class="text-3xl font-bold text-blue-600 mt-2">
                        <?= $total_user ?>
                    </h2>
                    <a href="<?= site_url('admin/list_user') ?>"
                        class="block mt-4 text-center bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        View Users
                    </a>
                </div>

                <!-- Total Gedung -->
                <div class="bg-green-50 rounded-xl p-5 shadow">
                    <p class="text-gray-600 font-semibold">Total Ruang</p>
                    <h2 class="text-3xl font-bold text-green-600 mt-2">
                        <?= $total_gedung ?>
                    </h2>
                    <a href="<?= site_url('admin/list_gedung') ?>"
                        class="block mt-4 text-center bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">
                        View Ruang
                    </a>
                </div>

                <!-- Pending Booking -->
                <div class="bg-orange-50 rounded-xl p-5 shadow relative">
                    <p class="text-gray-600 font-semibold">Pending Bookings</p>
                    <span class="absolute top-4 right-4 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        <?= count($result) ?>
                    </span>
                    <h2 class="text-3xl font-bold text-orange-600 mt-2">
                        <?= count($result) ?>
                    </h2>
                    <a href="<?= site_url('admin/transaksi') ?>"
                        class="block mt-4 text-center bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600">
                        Review Bookings
                    </a>
                </div>

                <!-- Total Revenue -->
                <div class="bg-red-50 rounded-xl p-5 shadow">
                    <p class="text-gray-600 font-semibold">Total Revenue</p>
                    <h2 class="text-2xl font-bold text-red-600 mt-2">
                        Rp <?= number_format($total_transaksi, 0, ',', '.') ?>
                    </h2>
                    <a href="<?= site_url('admin/rekap_transaksi') ?>"
                        class="block mt-4 text-center bg-red-500 text-white py-2 rounded-lg hover:bg-red-600">
                        View Transaksi
                    </a>
                </div>

            </div>

            <!-- ===== RECENT & ACTIVITY ===== -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">

                <!-- Recent Bookings -->
                <div class="bg-white rounded-xl shadow p-5">
                    <h2 class="font-bold text-gray-700 mb-4">Recent Bookings</h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">ID</th>
                                    <th class="px-3 py-2 text-left">Ruang</th>
                                    <th class="px-3 py-2 text-left">Tanggal</th>
                                    <th class="px-3 py-2 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($recent_pemesanan as $row): ?>
                                    <tr class="border-b">
                                        <td class="px-3 py-2"><?= $no++ ?></td>
                                        <td class="px-3 py-2 font-semibold"><?= $row->ID_PEMESANAN ?></td>
                                        <td class="px-3 py-2"><?= $row->NAMA_GEDUNG ?></td>
                                        <td class="px-3 py-2"><?= $row->TANGGAL_PEMESANAN ?></td>
                                        <td class="px-3 py-2">
                                            <a href="<?= site_url('admin/detail_pemesanan/' . $row->ID_PEMESANAN) ?>"
                                                class="bg-blue-500 text-white px-3 py-1 rounded text-xs">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- User Activities -->
                <div class="bg-white rounded-xl shadow p-5">
                    <h2 class="font-bold text-gray-700 mb-4">User Activities</h2>

                    <ul class="space-y-4">
                        <?php foreach ($recent_pemesanan as $row): ?>
                            <li class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gray-300 rounded-full"></div>
                                    <div>
                                        <p class="text-sm">
                                            <span class="font-semibold"><?= $row->USERNAME ?></span>
                                            booked <?= $row->NAMA_GEDUNG ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?= $row->TANGGAL_PEMESANAN ?> <?= $row->JAM ?>
                                        </p>
                                    </div>
                                </div>
                                <span class="text-gray-400">›</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="text-right mt-4">
                        <a href="#" class="text-blue-600 text-sm font-semibold">View All</a>
                    </div>
                </div>

            </div>

            <!-- ===== BOOKING SCHEDULE TABLE ===== -->
            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="font-bold text-gray-700 mb-4">Booking Schedule</h2>

                <!-- === FIX SCROLL BUG === -->
                <div class="max-h-[420px] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-100">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">ID</th>
                                <th class="px-3 py-2">Ruang</th>
                                <th class="px-3 py-2">User</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Jam</th>
                                <th class="px-3 py-2">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($front_data)): ?>
                                <?php $no = 1;
                                foreach ($front_data as $row): ?>
                                    <tr class="border-b">
                                        <td class="px-3 py-2"><?= $no++ ?></td>
                                        <td class="px-3 py-2 font-semibold"><?= $row->ID_PEMESANAN ?></td>
                                        <td class="px-3 py-2"><?= $row->NAMA_GEDUNG ?></td>
                                        <td class="px-3 py-2"><?= $row->USERNAME ?></td>
                                        <td class="px-3 py-2"><?= $row->TANGGAL_PEMESANAN ?></td>
                                        <td class="px-3 py-2"><?= !empty($row->JAM) ? $row->JAM : '-' ?></td>
                                        <td class="px-3 py-2">
                                            <a href="<?= site_url('admin/detail_pemesanan/' . $row->ID_PEMESANAN) ?>"
                                                class="bg-blue-500 text-white px-3 py-1 rounded text-xs">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                                        Belum ada jadwal SUBMITED.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>

            <footer class="text-xs text-gray-500 text-center mt-6 stacked">
                © <?php echo date('Y'); ?> Smart Office • Admin Panel

            </footer>
        </main>
    </div>

</body>

</html>