/*
* FIX: Catering Dropdown Tidak Tampil Data
*
* FILE: c:\xampp\htdocs\bookingsmarts\application\controllers\home\Home.php
* FUNCTION: order_gedung()
* LINE: 421
*
* MASALAH:
* Data catering di $data['res'] (line 367) tertimpa oleh data gedung di line 421.
* Akibatnya, dropdown catering menjadi kosong.
*
* SOLUSI:
* Comment/hapus line 421 yang menimpa $data['res']
*/

// CARI LINE INI (sekitar line 420-421):
// ✅ Pass gedung data as 'res' for view compatibility (jadwal preview needs $res[0]['ID_GEDUNG'])
$data['res'] = $gedung['hasil'];

// GANTI MENJADI (comment line 421):
// ✅ Pass gedung data as 'res' for view compatibility (jadwal preview needs $res[0]['ID_GEDUNG'])
// ❌ DISABLED: This overwrites catering data! Gedung data already available via $gedung['hasil']
// $data['res'] = $gedung['hasil'];

/*
* PENJELASAN:
* - Data catering akan tetap ada di $data['res'] (dari line 367)
* - Data gedung tetap tersedia via $gedung['hasil'] dan akan di-merge ke $hasil
* - Dropdown catering akan menampilkan pilihan paket dengan benar
*/