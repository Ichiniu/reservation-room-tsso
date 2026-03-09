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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Foto Profil</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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

<body class="bg-slate-50 min-h-screen sm:bg-slate-200 sm:flex sm:items-center sm:justify-center sm:px-4">

    <!-- Container styling optimized for mobile taking full height vs modal on desktop -->
    <div class="w-full sm:max-w-4xl bg-white sm:rounded-2xl sm:shadow-lg sm:border sm:border-slate-200 min-h-screen sm:min-h-0 flex flex-col">
        
        <!-- HEADER -->
        <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur z-20">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">Edit Foto Profil</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Atur posisi foto hingga pas di lingkaran
                </p>
            </div>
            <!-- Tombol Batal untuk mobile (tampil di kanan atas header) -->
            <a href="<?= site_url('home'); ?>" 
               class="sm:hidden text-slate-500 hover:text-slate-800 p-2 -mr-2">
                <i class="bi bi-x-lg text-xl"></i>
            </a>
        </div>

        <!-- CONTENT -->
        <div class="p-4 sm:p-6 flex-1 flex flex-col space-y-5">

            <?php if ($this->session->flashdata('error')): ?>
                <div class="p-3 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm flex gap-3 items-start">
                    <i class="bi bi-exclamation-circle-fill mt-0.5"></i>
                    <span><?= htmlspecialchars($this->session->flashdata('error')); ?></span>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 items-start flex-1">

                <!-- KIRI: CROP AREA -->
                <div class="flex flex-col h-full space-y-4">
                    
                    <div class="bg-slate-50 rounded-xl p-4 sm:p-5 border border-slate-100">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Foto dari Galeri</label>
                        <div class="relative">
                            <input id="fileInput" type="file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full flex items-center justify-center gap-2 border-2 border-dashed border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-600 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-all font-medium text-sm text-center focus-within:ring-2 focus-within:ring-blue-200">
                                <i class="bi bi-cloud-arrow-up text-lg"></i>
                                <span id="fileNameLabel">Upload Foto Baru</span>
                            </div>
                        </div>
                    </div>

                    <!-- AREA CANVAS CROPPER -->
                    <div class="flex-1 w-full bg-slate-900 rounded-2xl overflow-hidden relative shadow-inner aspect-square sm:aspect-auto sm:min-h-[300px] border border-slate-200 flex items-center justify-center">
                        <img id="image" src="" alt="source" class="hidden max-w-full">
                        <div id="emptyState" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 p-6 text-center">
                            <i class="bi bi-image text-4xl mb-3 opacity-50"></i>
                            <p class="text-sm">Area crop akan muncul di sini <br class="hidden sm:block">setelah Anda memilih foto.</p>
                        </div>
                    </div>

                    <!-- TOOLBAR ZOOM -->
                    <div class="flex items-center justify-center gap-2 bg-slate-50 p-2 rounded-xl border border-slate-100">
                        <button type="button" id="btnZoomOut" class="h-10 w-10 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition focus:outline-none" aria-label="Zoom Out">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                        </button>
                        <div class="h-10 flex items-center px-3 border border-slate-200 bg-white rounded-lg text-xs font-medium text-slate-500">
                            ZOOM
                        </div>
                        <button type="button" id="btnZoomIn" class="h-10 w-10 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition focus:outline-none" aria-label="Zoom In">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        </button>
                        <div class="w-px h-6 bg-slate-200 mx-1"></div>
                        <button type="button" id="btnReset" class="h-10 px-4 flex items-center justify-center gap-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition focus:outline-none text-sm font-medium">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>

                </div>

                <!-- KANAN: PREVIEW BULAT -->
                <div class="flex flex-col justify-between h-full space-y-6">
                    
                    <div class="bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-100 flex flex-col items-center text-center">
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4">Hasil Preview</div>

                        <div class="relative group">
                            <div class="h-32 w-32 sm:h-40 sm:w-40 rounded-full border-4 border-white shadow-xl bg-slate-200 overflow-hidden ring-4 ring-slate-50 transition-all">
                                <img id="previewCircle"
                                    src="<?= (($foto ?? '') !== '') ? base_url($foto) : '' ?>"
                                    class="<?= (($foto ?? '') !== '') ? 'h-full w-full object-cover' : 'hidden' ?>"
                                    alt="Preview Bulat">
                                <div id="previewPlaceholder"
                                    class="<?= (($foto ?? '') !== '') ? 'hidden' : '' ?> h-full w-full flex items-center justify-center text-slate-400">
                                    <i class="bi bi-person-fill text-5xl"></i>
                                </div>
                            </div>
                            
                            <!-- Status Indicator -->
                            <div class="absolute bottom-1 right-1 h-6 w-6 sm:h-8 sm:w-8 rounded-full border-2 border-white flex items-center justify-center <?= (($foto ?? '') !== '') ? 'bg-emerald-500' : 'bg-slate-300' ?>" id="statusIndicator">
                                <i class="bi <?= (($foto ?? '') !== '') ? 'bi-check2' : 'bi-dash' ?> text-white text-sm sm:text-base"></i>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($username); ?></div>
                            <div id="statusText" class="text-sm text-slate-500 mt-1">
                                Avatar saat ini
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="hidden sm:flex bg-blue-50/50 border border-blue-100 rounded-xl p-4 items-start gap-3">
                        <i class="bi bi-info-circle text-blue-500 mt-0.5"></i>
                        <p class="text-xs leading-relaxed text-slate-600">
                            Foto akan dipotong bentuk <strong class="text-slate-700">persegi rata (1:1)</strong>, namun nantinya akan ditampilkan secara melingkar. Pastikan wajah atau fokus utama berada tepat di tengah area lingkaran crop.
                        </p>
                    </div>

                    <!-- FORM SIMPAN (Desktop) -->
                    <div class="hidden sm:block mt-auto pt-4 border-t border-slate-100">
                        <?= form_open('edit_foto/' . $username, ['id' => 'saveFormDesktop']); ?>
                        <input type="hidden" name="cropped_image" id="croppedImageDesktop">
                        <div class="flex gap-3">
                            <a href="<?= site_url('home'); ?>" class="w-1/3 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 rounded-xl transition">
                                Batal
                            </a>
                            <button id="btnSaveDesktop" type="submit" class="w-2/3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3 flex items-center justify-center gap-2 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>
                                <i class="bi bi-check2-circle"></i> Simpan Foto Profil
                            </button>
                        </div>
                        <?= form_close(); ?>
                    </div>

                </div>

            </div>

        </div>

        <!-- FORM SIMPAN (Mobile) - Sticky Bottom -->
        <div class="sm:hidden sticky bottom-0 border-t border-slate-200 bg-white/95 backdrop-blur p-4 z-20 shadow-[0_-4px_20px_-10px_rgba(0,0,0,0.1)]">
            <?= form_open('edit_foto/' . $username, ['id' => 'saveFormMobile']); ?>
            <input type="hidden" name="cropped_image" id="croppedImageMobile">
            <button id="btnSaveMobile" type="submit" class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3.5 flex items-center justify-center gap-2 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed transition shadow-md shadow-blue-600/20" disabled>
                <i class="bi bi-check2-circle"></i> Simpan Foto Profil
            </button>
            <?= form_close(); ?>
        </div>
        
    </div>

    <script>
        (() => {
            const fileInput = document.getElementById('fileInput');
            const image = document.getElementById('image');
            const emptyState = document.getElementById('emptyState');
            const fileNameLabel = document.getElementById('fileNameLabel');

            const previewCircle = document.getElementById('previewCircle');
            const previewPlaceholder = document.getElementById('previewPlaceholder');
            const statusIndicator = document.getElementById('statusIndicator');
            const statusIndicatorIcon = statusIndicator ? statusIndicator.querySelector('i') : null;

            const statusText = document.getElementById('statusText');
            
            // Dual buttons & forms for Responsive Design
            const btnSaveDesktop = document.getElementById('btnSaveDesktop');
            const btnSaveMobile = document.getElementById('btnSaveMobile');
            const croppedImageDesktop = document.getElementById('croppedImageDesktop');
            const croppedImageMobile = document.getElementById('croppedImageMobile');
            const saveFormDesktop = document.getElementById('saveFormDesktop');
            const saveFormMobile = document.getElementById('saveFormMobile');

            const btnZoomIn = document.getElementById('btnZoomIn');
            const btnZoomOut = document.getElementById('btnZoomOut');
            const btnReset = document.getElementById('btnReset');

            let cropper = null;
            let objectUrl = null;

            function disableSave(msg) {
                if (btnSaveDesktop) btnSaveDesktop.disabled = true;
                if (btnSaveMobile) btnSaveMobile.disabled = true;
                if (msg && statusText) statusText.textContent = msg;
                
                if (statusIndicator) {
                    statusIndicator.className = 'absolute bottom-1 right-1 h-6 w-6 sm:h-8 sm:w-8 rounded-full border-2 border-white flex items-center justify-center bg-slate-300';
                    if (statusIndicatorIcon) statusIndicatorIcon.className = 'bi bi-dash text-white text-sm sm:text-base';
                }
            }

            function enableSave(msg) {
                if (btnSaveDesktop) btnSaveDesktop.disabled = false;
                if (btnSaveMobile) btnSaveMobile.disabled = false;
                if (msg && statusText) statusText.textContent = msg;
                
                if (statusIndicator) {
                    statusIndicator.className = 'absolute bottom-1 right-1 h-6 w-6 sm:h-8 sm:w-8 rounded-full border-2 border-white flex items-center justify-center bg-emerald-500';
                    if (statusIndicatorIcon) statusIndicatorIcon.className = 'bi bi-check2 text-white text-sm sm:text-base';
                }
            }

            function initCropper() {
                if (cropper) cropper.destroy();

                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    background: false,
                    autoCropArea: 0.8,
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
                        if (croppedImageDesktop) croppedImageDesktop.value = dataUrl;
                        if (croppedImageMobile) croppedImageMobile.value = dataUrl;

                        enableSave('Foto siap disimpan');
                    }
                });

                enableSave('Atur posisi foto');
            }

            fileInput.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    disableSave('File harus berupa gambar.');
                    return;
                }
                
                if (fileNameLabel) {
                    // Truncate file name if too long
                    let name = file.name;
                    if (name.length > 20) name = name.substring(0, 10) + '...' + name.substring(name.length - 7);
                    fileNameLabel.textContent = name;
                }

                // reset state
                if (croppedImageDesktop) croppedImageDesktop.value = '';
                if (croppedImageMobile) croppedImageMobile.value = '';
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

            if (btnZoomIn) btnZoomIn.addEventListener('click', () => cropper && cropper.zoom(0.1));
            if (btnZoomOut) btnZoomOut.addEventListener('click', () => cropper && cropper.zoom(-0.1));
            if (btnReset) btnReset.addEventListener('click', () => cropper && cropper.reset());

            const handleFormSubmit = (e, hiddenInput) => {
                if (!hiddenInput || !hiddenInput.value) {
                    e.preventDefault();
                    disableSave('Pilih dan atur foto terlebih dahulu.');
                }
            };

            if (saveFormDesktop) {
                saveFormDesktop.addEventListener('submit', (e) => handleFormSubmit(e, croppedImageDesktop));
            }
            if (saveFormMobile) {
                saveFormMobile.addEventListener('submit', (e) => handleFormSubmit(e, croppedImageMobile));
            }

            // UX: scroll mouse untuk zoom
            document.addEventListener('wheel', (e) => {
                if (!cropper) return;
                const cropBox = image.closest('.aspect-square') || image.closest('.aspect-auto');
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