<?php
$this->load->helper('form');

$u = (isset($user) && is_array($user)) ? $user : array();

$username       = isset($u['USERNAME']) ? $u['USERNAME'] : '';
$nama_lengkap   = isset($u['NAMA_LENGKAP']) ? $u['NAMA_LENGKAP'] : '';
$email          = isset($u['EMAIL']) ? $u['EMAIL'] : '';
$alamat         = isset($u['ALAMAT']) ? $u['ALAMAT'] : '';
$no_telepon     = isset($u['NO_TELEPON']) ? $u['NO_TELEPON'] : '';
$tanggal_lahir  = isset($u['TANGGAL_LAHIR']) ? $u['TANGGAL_LAHIR'] : '';

$perusahaan      = isset($u['perusahaan']) ? $u['perusahaan'] : '';
$nama_perusahaan = isset($u['nama_perusahaan']) ? $u['nama_perusahaan'] : '';
$departemen      = isset($u['departemen']) ? $u['departemen'] : '';
?>
<!DOCTYPE html>
<html>

<head>
	<title>Edit Data Diri</title>
	<meta charset="utf-8">
	<style>
		body {
			font-family: Arial, sans-serif;
		}

		.container {
			width: 420px;
			margin: 40px auto;
		}

		.row {
			margin-bottom: 10px;
			text-align: left;
		}

		label {
			display: block;
			margin-bottom: 4px;
			font-size: 14px;
		}

		input,
		textarea {
			width: 100%;
			padding: 8px;
			box-sizing: border-box;
		}

		textarea {
			height: 80px;
		}

		.readonly {
			background: #f3f3f3;
		}

		.btn {
			padding: 10px 14px;
			cursor: pointer;
		}

		.msg {
			padding: 10px;
			margin-bottom: 12px;
			border-radius: 4px;
		}

		.success {
			background: #e7f6e7;
			border: 1px solid #b7e0b7;
		}

		.error {
			background: #fde8e8;
			border: 1px solid #f5b5b5;
		}

		small {
			color: #666;
		}
	</style>
</head>

<body>
	<div class="container">
		<h3>Edit Data Diri</h3>

		<?php if ($this->session->flashdata('error')): ?>
			<div class="msg error"><?= htmlspecialchars($this->session->flashdata('error')); ?></div>
		<?php endif; ?>

		<div class="row">
			<label>Username</label>
			<input class="readonly" type="text" value="<?= htmlspecialchars($username); ?>" readonly>
		</div>

		<div class="row">
			<label>Perusahaan (Internal / Eksternal)</label>
			<input class="readonly" type="text" value="<?= htmlspecialchars($perusahaan); ?>" readonly>
		</div>

		<div class="row">
			<label>Nama Perusahaan</label>
			<input class="readonly" type="text" value="<?= htmlspecialchars($nama_perusahaan); ?>" readonly>
			<small>Hanya tampil, tidak bisa diganti.</small>
		</div>

		<div class="row">
			<label>Departemen</label>
			<input class="readonly" type="text" value="<?= htmlspecialchars($departemen); ?>" readonly>
			<small>Hanya tampil, tidak bisa diganti.</small>
		</div>

		<?php echo form_open('edit_data'); ?>

		<div class="row">
			<label>Nama Lengkap</label>
			<input type="text" name="nama_lengkap" value="<?= htmlspecialchars($nama_lengkap); ?>" required>
		</div>

		<div class="row">
			<label>Email</label>
			<input type="email" name="email" value="<?= htmlspecialchars($email); ?>" required>
		</div>

		<div class="row">
			<label>Alamat</label>
			<textarea name="alamat" required><?= htmlspecialchars($alamat); ?></textarea>
		</div>

		<div class="row">
			<label>No Telepon</label>
			<input type="text" name="no_telepon" value="<?= htmlspecialchars($no_telepon); ?>" required>
		</div>

		<div class="row">
			<label>Tanggal Lahir</label>
			<input type="date" name="dob" value="<?= htmlspecialchars($tanggal_lahir); ?>" required>
		</div>

		<hr>

		<div class="row">
			<label>Password Baru (opsional)</label>
			<input type="password" name="password" placeholder="Kosongkan jika tidak ingin ubah password">
		</div>

		<div class="row">
			<label>Confirm Password Baru</label>
			<input type="password" name="confirm_pass" placeholder="Ulangi password baru">
		</div>

		<div class="row">
			<input class="btn" type="submit" value="Ubah">
		</div>

		<?php echo form_close(); ?>
	</div>
</body>

</html>