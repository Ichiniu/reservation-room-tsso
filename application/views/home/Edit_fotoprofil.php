<?php
$this->load->helper('form');

$u = (isset($user) && is_array($user)) ? $user : [];
$username = $u['USERNAME'] ?? '';
$foto     = $u['FOTO_PROFIL'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Foto Profil</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <style>
        /* overlay lingkaran di atas area crop (hanya tampilan) */
        .circle-mask {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .circle-mask::before {
            content: "";
            width: 74%;
            height: 74%;
            border-radius: 9999px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, .45);
            border: 2px solid rgba(255, 255, 255, .8);
        }
    </style>
</head>

<body class="bg-slate-200 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg border border-slate-200">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-slate-800">Edit Foto Profil</h2>
            <p class="text-sm text-slate-500">
                Pilih foto → atur posisi (geser/zoom) sampai pas di lingkaran → klik Simpan
            </p>
        </div>

        <div class="p-6 space-y-5">

            <?php if ($this->session->flashdata('error')): ?>
                <div class="p-3 rounded bg-red-100 text-red-700 border border-red-300">
                    <?= htmlspecialchars($this->session->flashdata('error')); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

                <!-- KIRI: CROP AREA -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-slate-700">Pilih Foto</label>
                    <input id="fileInput" type="file" accept="image/*"
                        class="w-full border rounded px-3 py-2 bg-white">

                    <div class="border rounded-lg bg-slate-50 p-2">
                        <div class="relative w-full aspect-square overflow-hidden rounded flex items-center justify-center">
                            <!-- gambar sumber crop -->
                            <img id="image" src="" alt="source" class="hidden max-w-full">

                            <!-- overlay lingkaran -->


                            <!-- placeholder -->
                            <div id="emptyState" class="text-slate-500 text-sm">
                                Pilih foto untuk mulai crop
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="btnZoomIn"
                            class="px-3 py-2 rounded bg-slate-200 hover:bg-slate-300 text-slate-800 text-sm">
                            Zoom +
                        </button>
                        <button type="button" id="btnZoomOut"
                            class="px-3 py-2 rounded bg-slate-200 hover:bg-slate-300 text-slate-800 text-sm">
                            Zoom -
                        </button>
                        <button type="button" id="btnReset"
                            class="px-3 py-2 rounded bg-slate-200 hover:bg-slate-300 text-slate-800 text-sm">
                            Reset
                        </button>
                    </div>

                    <p class="text-xs text-slate-500">
                        Geser foto dengan drag, zoom pakai tombol/scroll. Yang masuk lingkaran itu yang akan jadi avatar.
                    </p>
                </div>

                <!-- KANAN: PREVIEW BULAT -->
                <div class="space-y-3">
                    <div class="text-sm font-medium text-slate-700">Preview Avatar</div>

                    <div class="flex items-center gap-4">
                        <div class="h-28 w-28 rounded-full border bg-slate-100 overflow-hidden">
                            <img id="previewCircle"
                                src="<?= (($foto ?? '') !== '') ? base_url($foto) : '' ?>"
                                class="<?= (($foto ?? '') !== '') ? 'h-full w-full object-cover' : 'hidden' ?>"
                                alt="Preview Bulat">
                            <div id="previewPlaceholder"
                                class="<?= (($foto ?? '') !== '') ? 'hidden' : '' ?> h-full w-full flex items-center justify-center text-slate-500 text-sm">
                                No Preview
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($username); ?></div>
                            <div id="statusText" class="text-xs text-slate-500">
                                Pilih foto untuk mulai.
                            </div>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500">
                        Preview ini bulat. File yang disimpan adalah hasil crop (square), tapi tampil bulat di navbar pakai CSS.
                    </div>
                </div>

            </div>

            <!-- FORM SIMPAN -->
            <?= form_open('edit_foto/' . $username, ['id' => 'saveForm']); ?>
            <input type="hidden" name="cropped_image" id="croppedImage">

            <div class="flex gap-3">
                <button id="btnSave" type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Simpan Foto
                </button>

                <a href="<?= site_url('home'); ?>"
                    class="flex-1 text-center bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold py-2 rounded-lg">
                    Batal
                </a>
            </div>
            <?= form_close(); ?>

        </div>
    </div>

    <script>
        (() => {
            const fileInput = document.getElementById('fileInput');
            const image = document.getElementById('image');
            const emptyState = document.getElementById('emptyState');
            const circleMask = document.getElementById('circleMask');

            const previewCircle = document.getElementById('previewCircle');
            const previewPlaceholder = document.getElementById('previewPlaceholder');

            const statusText = document.getElementById('statusText');
            const btnSave = document.getElementById('btnSave');
            const croppedImage = document.getElementById('croppedImage');

            const btnZoomIn = document.getElementById('btnZoomIn');
            const btnZoomOut = document.getElementById('btnZoomOut');
            const btnReset = document.getElementById('btnReset');

            let cropper = null;
            let objectUrl = null;

            function disableSave(msg) {
                btnSave.disabled = true;
                if (msg) statusText.textContent = msg;
            }

            function enableSave(msg) {
                btnSave.disabled = false;
                if (msg) statusText.textContent = msg;
            }

            function initCropper() {
                if (cropper) cropper.destroy();

                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    background: false,
                    autoCropArea: 1,
                    movable: true,
                    zoomable: true,
                    rotatable: false,
                    scalable: false,
                    responsive: true,

                    // supaya preview update realtime
                    crop() {
                        // ambil hasil crop square, lalu kita tampilkan bulat dengan CSS
                        const canvas = cropper.getCroppedCanvas({
                            width: 500,
                            height: 500,
                            imageSmoothingEnabled: true,
                            imageSmoothingQuality: 'high'
                        });

                        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

                        // preview bulat
                        previewCircle.src = dataUrl;
                        previewCircle.className = 'h-full w-full object-cover';
                        previewCircle.classList.remove('hidden');
                        previewPlaceholder.classList.add('hidden');

                        // yang dikirim ke server
                        croppedImage.value = dataUrl;

                        enableSave('Atur sampai pas di lingkaran, lalu klik Simpan.');
                    }
                });

                circleMask.classList.remove('hidden');
                enableSave('Geser/zoom foto sampai pas di lingkaran.');
            }

            fileInput.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    disableSave('File harus berupa gambar.');
                    return;
                }

                // reset state
                croppedImage.value = '';
                disableSave('Memuat foto...');

                emptyState.classList.add('hidden');
                image.classList.remove('hidden');

                // set src
                if (objectUrl) URL.revokeObjectURL(objectUrl);
                objectUrl = URL.createObjectURL(file);
                image.src = objectUrl;

                image.onload = () => {
                    initCropper();
                    // jangan revoke terlalu cepat sebelum cropper pakai; aman revoke setelah init
                    setTimeout(() => {
                        if (objectUrl) URL.revokeObjectURL(objectUrl);
                        objectUrl = null;
                    }, 500);
                };
            });

            btnZoomIn.addEventListener('click', () => cropper && cropper.zoom(0.1));
            btnZoomOut.addEventListener('click', () => cropper && cropper.zoom(-0.1));
            btnReset.addEventListener('click', () => cropper && cropper.reset());

            document.getElementById('saveForm').addEventListener('submit', (e) => {
                if (!croppedImage.value) {
                    e.preventDefault();
                    disableSave('Pilih foto dan atur crop dulu sebelum simpan.');
                }
            });

            // UX: scroll mouse untuk zoom
            document.addEventListener('wheel', (e) => {
                if (!cropper) return;
                const cropBox = image.closest('.aspect-square');
                if (!cropBox) return;

                // zoom hanya kalau pointer sedang di area crop
                const rect = cropBox.getBoundingClientRect();
                const inside = e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom;
                if (!inside) return;

                e.preventDefault();
                cropper.zoom(e.deltaY < 0 ? 0.05 : -0.05);
            }, {
                passive: false
            });

        })();
    </script>

</body>

</html>