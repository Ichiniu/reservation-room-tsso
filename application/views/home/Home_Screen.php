<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$user = $this->uri->segment(2);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<?php if ($this->session->flashdata('success_popup')): ?>
<script>
alert("<?= addslashes($this->session->flashdata('success_popup')); ?>");
</script>
<?php endif; ?>

<body class="min-h-screen bg-slate-200 text-black">

    <!-- NAVBAR -->
    <?php $this->load->view('components/navbar'); ?>

    <main class="py-10">
        <div class="max-w-6xl mx-auto px-4 space-y-8">

            <!-- HEADER -->
            <section>
                <h2 class="text-2xl font-bold text-slate-900">Pilih Ruangan</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Ruangan dengan fasilitas lengkap & tampilan modern
                </p>
            </section>

            <!-- GRID -->
            <section>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                    <?php foreach (array_slice($res, 0, 3) as $row): ?>
                    <article class="group bg-white rounded-2xl overflow-hidden
       border border-slate-200
       shadow-sm
       transition-all duration-300
       hover:-translate-y-2 hover:shadow-xl">

                        <!-- IMAGE -->
                        <div class="relative overflow-hidden">
                            <img src="<?= $row['PATH'] . $row['IMG_NAME']; ?>"
                                class="h-56 w-full object-cover transition duration-500 group-hover:scale-110">

                            <!-- KAPASITAS -->
                            <div
                                class="absolute top-3 right-3 bg-black/70 px-2 py-1 rounded-full text-xs text-white flex items-center gap-1">
                                <span class="material-icons text-sm">groups</span>
                                <?= $row['KAPASITAS']; ?>
                            </div>
                        </div>

                        <!-- BODY -->
                        <div class="p-4 flex flex-col gap-3">

                            <h3 class="text-lg font-semibold text-slate-900 group-hover:text-sky-600 transition">
                                <?= $row['NAMA_GEDUNG']; ?>
                            </h3>

                            <p class="text-sm text-slate-500">
                                Cocok untuk rapat, presentasi, dan kegiatan resmi.
                            </p>

                            <!-- FASILITAS -->
                            <div class="flex gap-4 text-slate-500 pt-2">

                                <div class="flex items-center gap-1 text-xs">
                                    <span class="material-icons text-base">videocam</span> Proyektor
                                </div>

                                <div class="flex items-center gap-1 text-xs">
                                    <span class="material-icons text-base">volume_up</span> Sound
                                </div>

                                <div class="flex items-center gap-1 text-xs">
                                    <span class="material-icons text-base">wifi</span> WiFi
                                </div>

                                <div class="flex items-center gap-1 text-xs">
                                    <span class="material-icons text-base">ac_unit</span> AC
                                </div>

                            </div>
                        </div>

                        <!-- ===== BOTTOM COMPONENT ===== -->
                        <div class="flex items-center justify-end px-4 py-3
       border-t border-slate-100
       bg-slate-50">

                            <!-- LEFT INFO -->
                            <!-- <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="material-icons text-amber-400 text-base">star</span>
                                4.8
                                <span class="text-slate-400">(120)</span>
                            </div> -->

                            <!-- STATUS -->
                            <!-- <div class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 font-medium">
                                Tersedia
                            </div> -->

                            <!-- ACTION -->
                            <a href="<?= site_url('home/details/' . $row['ID_GEDUNG']); ?>"
                                class="flex items-center gap-1 text-sm font-semibold text-sky-600 hover:text-sky-700 transition">
                                Detail
                                <span class="material-icons text-base transition group-hover:translate-x-1">
                                    arrow_forward
                                </span>
                            </a>

                        </div>
                        <!-- ===== END BOTTOM COMPONENT ===== -->

                    </article>
                    <?php endforeach; ?>

                </div>
            </section>

        </div>
    </main>

    <!-- FOOTER -->
    <?php $this->load->view('components/Footer'); ?>

</body>

</html>