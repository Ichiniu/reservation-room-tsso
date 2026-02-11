/*
* PATCH FOR: detail_pemesanan() function
* FILE: c:\xampp\htdocs\bookingsmarts\application\controllers\home\Home.php
* LOCATION: Insert AFTER line 755 (setelah $data['result']->TOTAL_KESELURUHAN = ...)
* dan BEFORE line 756 (closing brace })
*/

// ✅ FALLBACK: pastikan TOTAL_PESERTA terambil dari database
if (!isset($data['result']->TOTAL_PESERTA) || $data['result']->TOTAL_PESERTA == 0) {
if ($this->db->field_exists('TOTAL_PESERTA', 'pemesanan')) {
$peserta_row = $this->db->select('TOTAL_PESERTA')
->from('pemesanan')
->where('ID_PEMESANAN', (int)$temp_id)
->get()
->row();

if ($peserta_row && isset($peserta_row->TOTAL_PESERTA) && $peserta_row->TOTAL_PESERTA > 0) {
$data['result']->TOTAL_PESERTA = (int)$peserta_row->TOTAL_PESERTA;
}
}
}