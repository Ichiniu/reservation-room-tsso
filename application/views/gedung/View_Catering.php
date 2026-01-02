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

  <!-- Material Icons & Materialize -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-slate-200 text-slate-800">

  <?php $this->load->view('components/navbar'); ?>
  <?php $this->load->view('components/header'); ?>

  <div class="max-w-6xl mx-auto px-4 py-8">
    <h5 class="text-xl font-semibold mb-6">Warsito Catering</h5>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <?php foreach ($res as $row): ?>
        <div class="bg-white rounded-2xl shadow-md border border-black/5 p-6 hover:shadow-lg transition">

          <h3 class="text-lg font-semibold text-slate-800 mb-4">
            <?php echo htmlspecialchars($row['NAMA_PAKET'], ENT_QUOTES, 'UTF-8'); ?>
          </h3>

          <?php
          $menu = array();
          if (!empty($row['MENU_JSON'])) {
            $tmp = json_decode($row['MENU_JSON'], true);
            if (is_array($tmp)) $menu = $tmp;
          }
          ?>

          <div class="space-y-3 text-sm">
            <?php if (isset($menu['categories']) && is_array($menu['categories']) && !empty($menu['categories'])): ?>
              <?php foreach ($menu['categories'] as $cat): ?>

                <?php
                $label = isset($cat['label']) ? $cat['label'] : '-';
                $pick  = isset($cat['pick']) ? $cat['pick'] : 0;
                $note  = isset($cat['note']) ? $cat['note'] : '';
                $items = (isset($cat['items']) && is_array($cat['items'])) ? $cat['items'] : array();
                ?>

                <div class="flex gap-3">
                  <div class="w-40 font-semibold text-slate-600">
                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                  </div>

                  <div class="flex-1 text-slate-700">
                    <?php $rule = pick_label($pick); ?>
                    <?php if ($rule !== ''): ?>
                      <div class="text-xs text-slate-500 mb-1"><?php echo $rule; ?></div>
                    <?php endif; ?>

                    <?php if ($note !== ''): ?>
                      <div class="text-xs text-slate-500 mb-1">
                        <?php echo htmlspecialchars($note, ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>

                    <?php if (!empty($items)): ?>
                      <ul class="list-disc ml-5 columns-2 gap-8">
                        <?php foreach ($items as $it): ?>
                          <li class="break-inside-avoid mb-1">
                            <?= htmlspecialchars($it, ENT_QUOTES, 'UTF-8') ?>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php else: ?>
                      <div class="text-slate-400">-</div>
                    <?php endif; ?>

                  </div>
                </div>

              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-slate-400">Menu belum diatur</div>
            <?php endif; ?>
          </div>

          <div class="mt-6 flex items-center justify-between">
            <span class="text-sm text-slate-500">Harga / Porsi</span>
            <span class="text-lg font-semibold text-teal-700">
              Rp <?php echo number_format((int)$row['HARGA'], 0, ',', '.'); ?>
            </span>
          </div>

        </div>
      <?php endforeach; ?>

    </div>
  </div>

  <script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>
  <?php $this->load->view('components/footer'); ?>

</body>

</html>