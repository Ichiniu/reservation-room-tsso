<?php
$session_id = $this->session->userdata('username');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasi - PT Tiga Serangkai</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<?php $this->load->view('components/navbar'); ?>
<?php $this->load->view('components/header'); ?>

<main class="bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 py-8">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Lokasi PT Tiga Serangkai</h1>
                <p class="mt-1 text-sm text-slate-600">
                    Jl. Dr. Supomo No. 23, Laweyan, Surakarta, Jawa Tengah 57141
                </p>
            </div>

            <a
                href="https://www.google.com/maps/search/?api=1&query=PT%20Tiga%20Serangkai%20Jl.%20Dr.%20Supomo%20No.%2023%20Laweyan%20Surakarta"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
               bg-teal-700 text-white hover:bg-teal-800 transition">
                Buka di Google Maps
            </a>
        </div>

        <div class="mt-6 rounded-2xl overflow-hidden border border-black/10 shadow-sm bg-white">
            <iframe
                src="https://www.google.com/maps?q=Jl.%20Dr%20Supomo%20No.%2023%20Laweyan%20Surakarta%2057141&output=embed"
                width="100%"
                height="500"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-black/10 rounded-2xl p-5">
                <div class="text-sm font-semibold text-slate-900">Alamat</div>
                <div class="mt-2 text-sm text-slate-600">
                    Jl. Dr. Supomo No. 23, Laweyan, Surakarta, Jawa Tengah 57141
                </div>
            </div>

            <div class="bg-white border border-black/10 rounded-2xl p-5">
                <div class="text-sm font-semibold text-slate-900">Catatan</div>
                <div class="mt-2 text-sm text-slate-600">
                    Jika yang dimaksud cabang/gedung lain (mis. Kartasura/Sukoharjo),
                    tinggal ganti alamat/URL map-nya.
                </div>
            </div>

            <div class="bg-white border border-black/10 rounded-2xl p-5">
                <div class="text-sm font-semibold text-slate-900">Bantuan</div>
                <div class="mt-2 text-sm text-slate-600">
                    Klik tombol “Buka di Google Maps” untuk rute & navigasi.
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('components/footer'); ?>
</main>

</body>

</html>