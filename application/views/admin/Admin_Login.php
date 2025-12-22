<?php
$this->load->helper('form');
?>
<html>
<head>
<title>Admin Login</title>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">

<style>
    /* WARNA LOGO TIGA SERANGKAI */
    :root {
        --ts-main: #2A7C80;     /* warna teal logo */
        --ts-dark: #225F62;
        --ts-light: #E1F1F1;
    }

    body {
        display: flex;
        min-height: 100vh;
        flex-direction: column;

        background-image: url("<?= base_url('assets/images/gedung/tsu-front.png') ?>");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        background-attachment: fixed;

        font-family: 'arial', sans-serif;
    }

    main {
        flex: 1 0 auto;
    }

    /* Tambahkan logo */
    .login-logo {
        width: 140px;
        margin-bottom: 20px;
    }

    /* Judul */
    .indigo-text {
        color: var(--ts-main) !important;
        font-weight: 600;
        letter-spacing: 1px;
    }

    /* Input */
    .input-field input[type=text]:focus,
    .input-field input[type=password]:focus {
        border-bottom: 3px solid var(--ts-main) !important;
        box-shadow: 0px 1px 8px rgba(42,124,128,0.5) !important;
    }

    .input-field label {
        color: var(--ts-main) !important;
        font-weight: 600;
    }

    .input-field input:focus + label {
        color: var(--ts-main) !important;
    }

    /* Button Login */
    .btn-login {
        background: var(--ts-main) !important;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-login:hover {
        background: var(--ts-dark) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    }

    /* FIX ICON DOUBLE */
    .material-icons {
        font-family: 'Material Icons' !important;
        font-size: 26px !important;
        font-style: normal !important;
        font-weight: normal !important;
        text-transform: none !important;
        letter-spacing: normal !important;
        line-height: 1 !important;
    }

    /* FIX POSITION ICON SHOW/HIDE */
    #togglePassword {
        position: absolute;
        right: 8px;
        top: 8px;
        cursor: pointer;
        z-index: 99;
        background: white;
        padding-left: 4px;
    }

</style>
</head>

<body>
  <div class="section"></div>
  <main>
    <center> 
      <div class="section"></div>

      <div class="container">

        <div class="z-depth-1 grey lighten-4 row" 
             style="display: inline-block; padding: 32px 70px 20px 70px; border: 1px solid #EEE; border-radius:12px;">

          <!-- LOGO -->
          <img src="<?= base_url('assets/login/LogoTSNew.png') ?>" class="login-logo">

          <?php echo form_open('admin/log_in'); ?>

            <h5 class="indigo-text">ADMIN SMART OFFICE </h5>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='username' id='email' />
                <label for='email'>USERNAME</label>
              </div>
            </div>

            <!-- PASSWORD FIELD -->
            <div class='row'>
              <div class='input-field col s12' style="position:relative;">
                
                <input class='validate' type='password' name='password' id='password' />
                <label for='password'>PASSWORD</label>

                <!-- ICON SHOW/HIDE -->
                <span id="togglePassword">
                    <i class="material-icons" id="eyeIcon" style="color:#2A7C80;">visibility_off</i>
                </span>
                <?php if(isset($error)): ?>
                  <div style="color:#C62828; font-weight:600; margin-top:5px; font-size:13px;">
                   <?= $error; ?>
                   </div>
                <?php endif; ?>
              </div>
            </div>

            <br />

            <center>
              <div class='row'>
                <button type='submit' name='submit' 
                        class='col s12 btn btn-large waves-effect btn-login'>
                  Login
                </button>
              </div>
            </center>

          <?php echo form_close(); ?>

        </div>

      </div>
    </center>

    <div class="section"></div>
    <div class="section"></div>
  </main>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>

  <!-- SCRIPT FIX -->
  <script>
  $(document).ready(function(){

      const toggle = document.getElementById("togglePassword");
      const passField = document.getElementById("password");
      const eye = document.getElementById("eyeIcon");

      toggle.addEventListener("click", function () {
          const type = passField.getAttribute("type") === "password" ? "text" : "password";
          passField.setAttribute("type", type);

          // ubah icon
          eye.textContent = type === "password" ? "visibility_off" : "visibility";
      });

  });
  </script>

</body>

</html>
