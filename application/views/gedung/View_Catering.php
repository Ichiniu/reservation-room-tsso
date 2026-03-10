<?php
$session_id = $this->session->userdata('username');
$this->load->helper('text');
$user = $this->uri->segment(2);

function pick_label($pick)
{
    $pick = (int)$pick;
    if ($pick <= 0) return '';
    return "Bebas memilih {$pick} macam";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Catering</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 text-slate-900 flex flex-col">

    <?php $this->load->view('components/navbar'); ?>
    <?php $this->load->view('components/header'); ?>

    <main class="py-8 flex-1">
        <div class="max-w-6xl mx-auto px-4 space-y-6">

            <!-- HERO -->
            <section
                class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 backdrop-blur shadow-sm">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-28 -right-28 h-72 w-72 rounded-full bg-emerald-200/40 blur-3xl"></div>
                    <div class="absolute -bottom-28 -left-28 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>
                </div>

                <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div>
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-semibold">
                            <span class="material-icons text-sm">restaurant</span>
                            Catering
                        </div>

                        <h1 class="mt-3 text-2xl md:text-3xl font-extrabold tracking-tight">
                            Cerita Rasa Catering
                        </h1>

                        <p class="mt-2 text-sm md:text-base text-slate-600">
                            Pilih paket sesuai kebutuhan acara.
                        </p>
                    </div>

                    <div
                        class="hidden sm:flex items-center gap-2 text-xs text-slate-600 bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
                        <span class="material-icons text-base text-slate-500">tips_and_updates</span>
                        Harga per porsi, cek menu detail.
                    </div>
                </div>
            </section>

            <!-- GRID -->
            <section>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <?php if (!empty($res) && is_array($res)): ?>
                        <?php foreach ($res as $row): ?>
                            <?php
                            $nama = ($row['NAMA_PAKET'] ?? '') !== '' ? $row['NAMA_PAKET'] : 'Paket Catering';
                            $harga = (int)($row['HARGA'] ?? 0);

                            $menu = [];
                            if (!empty($row['MENU_JSON'])) {
                                $tmp = json_decode($row['MENU_JSON'], true);
                                if (is_array($tmp)) $menu = $tmp;
                            }

                            // badge kecil biar keliatan "kaya"
                            $hasCategories = !empty($menu['categories'] ?? null);
                            ?>

                            <article
                                class="group bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">

                                <!-- TOP STRIP -->
                                <div class="px-6 pt-6 pb-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3
                                                class="text-lg font-bold text-slate-900 group-hover:text-emerald-700 transition">
                                                <?php echo htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>
                                            </h3>

                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                                <span
                                                    class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                                                    <span class="material-icons text-[16px]">receipt_long</span>
                                                    Paket
                                                </span>

                                                <?php if ($hasCategories): ?>
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-emerald-800 border border-emerald-200">
                                                        <span class="material-icons text-[16px]">check_circle</span>
                                                        Menu tersedia
                                                    </span>
                                                <?php else: ?>
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-amber-900 border border-amber-200">
                                                        <span class="material-icons text-[16px]">info</span>
                                                        Menu belum diatur
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <div class="text-xs text-slate-500">Harga / Porsi</div>
                                            <div class="mt-1 text-xl font-extrabold text-emerald-700">
                                                Rp <?php echo number_format($harga, 0, ',', '.'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BODY -->
                                <div class="px-6 pb-6">
                                    <?php if ($hasCategories): ?>
                                        <div class="space-y-4 text-sm">

                                            <?php foreach ($menu['categories'] as $cat): ?>
                                                <?php
                                                $label = $cat['label'] ?? '-';
                                                $pick  = (int)($cat['pick'] ?? 0);
                                                $note  = $cat['note'] ?? '';
                                                $items = $cat['items'] ?? [];

                                                $rule = pick_label($pick);
                                                ?>

                                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                    <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                                                        <div class="sm:w-44">
                                                            <div class="text-sm font-bold text-slate-900">
                                                                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                                            </div>

                                                            <?php if ($rule !== ''): ?>
                                                                <div class="mt-1 inline-flex items-center gap-1 text-xs text-slate-600">
                                                                    <span class="material-icons text-[16px] text-slate-500">check</span>
                                                                    <?php echo htmlspecialchars($rule, ENT_QUOTES, 'UTF-8'); ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if ($note !== ''): ?>
                                                                <div class="mt-1 text-xs text-slate-500">
                                                                    <?php echo htmlspecialchars($note, ENT_QUOTES, 'UTF-8'); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <div class="flex-1">
                                                            <?php if (!empty($items)): ?>
                                                                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1 text-slate-700">
                                                                    <?php foreach ($items as $it): ?>
                                                                        <li class="flex items-start gap-2">
                                                                            <span
                                                                                class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                                                            <span class="leading-relaxed break-words">
                                                                                <?php echo htmlspecialchars($it, ENT_QUOTES, 'UTF-8'); ?>
                                                                            </span>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            <?php else: ?>
                                                                <div class="text-slate-400">-</div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php endforeach; ?>

                                        </div>
                                    <?php else: ?>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                                            Menu belum diatur untuk paket ini.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- FOOTER ACTION (opsional kalau kamu mau pilih paket) -->
                                <div class="px-6 py-4 border-t border-slate-100 bg-white flex items-center justify-between">
                                    <div class="text-xs text-slate-500 inline-flex items-center gap-1">
                                        <span class="material-icons text-[16px]">local_dining</span>
                                        Detail menu per kategori
                                    </div>

                                    <!-- kalau nanti ada tombol pilih, tinggal aktifkan -->
                                    <!--
                  <a href="<?php echo site_url('home/catering/pilih/' . $row['ID_PAKET']); ?>"
                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 text-white px-4 py-2 text-sm font-semibold hover:bg-emerald-700 transition">
                    Pilih Paket
                    <span class="material-icons text-base">arrow_forward</span>
                  </a>
                  -->
                                </div>

                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full">
                            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                                <div class="mx-auto h-14 w-14 rounded-2xl bg-amber-50 flex items-center justify-center">
                                    <span class="material-icons text-amber-500 text-2xl">restaurant</span>
                                </div>
                                <h4 class="mt-4 text-lg font-bold text-slate-900">Untuk sementara catering belum tersedia</h4>
                                <p class="mt-2 text-sm text-slate-600">Apabila menghendaki layanan catering, bisa hubungi nomor berikut:</p>
                                <?php $phone = $catering_phone ?? '089649261851'; ?>
                                <a href="https://wa.me/62<?= preg_replace('/^0/', '', $phone) ?>" target="_blank"
                                    class="inline-flex items-center gap-2 mt-4 px-5 py-3 bg-green-50 text-green-700 rounded-2xl border border-green-200 font-semibold text-sm hover:bg-green-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                    <?= htmlspecialchars($phone) ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </section>

        </div>
    </main>

    <?php $this->load->view('components/footer'); ?>
</body>

</html>