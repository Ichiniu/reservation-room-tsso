<?php
defined('BASEPATH') or exit('No direct script access allowed');
$redirectUrl = site_url('home/pemesanan');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Upload Berhasil</title>

  <!-- AUTO REDIRECT (100% PASTI JALAN) -->
  <meta http-equiv="refresh" content="3;url=<?php echo $redirectUrl; ?>">

  <!-- Icons & Materialize -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/home/materialize/css/materialize.css" rel="stylesheet">

  <style>
    /* ===== BACKGROUND ===== */
    body {
      background: rgb(243, 244, 246);

      height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ===== MODAL ===== */
    .modal {
      border-radius: 18px;
      max-width: 420px;
    }

    .modal-content {
      text-align: center;
      padding: 32px 24px 16px;
    }

    .modal-footer {
      text-align: center;
      padding-bottom: 24px;
      border-top: none;
    }

    /* ===== ICON ===== */
    .success-icon {
      font-size: 90px;
      color: #ffff;
      animation: pop 0.8s ease;
    }

    @keyframes pop {
      from {
        transform: scale(0.3);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    /* ===== TEXT ===== */
    h4 {
      margin-top: 12px;
      font-weight: 600;

    }

    p {
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 24px;

    }

    /* ===== BUTTON ===== */
    .btn-modern {
      background: linear-gradient(135deg, #2196f3, #1e88e5);
      color: #fff;
      padding: 0 36px;
      height: 46px;
      line-height: 46px;
      border-radius: 30px;
      font-weight: 500;
      text-transform: none;
      box-shadow: 0 6px 15px rgba(33, 150, 243, 0.4);
      transition: all 0.3s ease;
      display: inline-block;
    }

    .btn-modern:hover {
      background: linear-gradient(135deg, #1e88e5, #1565c0);
      box-shadow: 0 8px 20px rgba(33, 150, 243, 0.6);
    }

    /* ===== PROGRESS ===== */
    .progress {
      margin-top: 20px;
    }
  </style>
</head>

<div id="successModal" class="modal">
  <div class="modal-content">
    <i class="material-icons success-icon">check_circle</i>
    <h4>Upload Berhasil</h4>
    <p>
      Reservasi Anda berhasil diupload.<br>
      Anda akan diarahkan ke halaman pemesanan dalam
      <b><span id="countdown">3</span></b> detik.
    </p>

    <div class="progress">
      <div class="indeterminate"></div>
    </div>
  </div>

  <div class="modal-footer">
    <a href="<?php echo $redirectUrl; ?>" class="btn-modern">
      Pindah Sekarang
    </a>
  </div>
</div>

<script src="<?php echo base_url(); ?>assets/home/assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/home/materialize/js/materialize.js"></script>

<script>
  $(document).ready(function() {

    $('.modal').modal({
      dismissible: false
    });

    $('#successModal').modal('open');

    // Countdown visual (redirect sudah ditangani META)
    let time = 3;
    setInterval(function() {
      time--;
      $('#countdown').text(time);
    }, 1000);

  });
</script>

</body>

</html>