<?php

/**
 * PATCH FILE: Insert this code manually after line 579 in Home.php
 * Location: c:\xampp\htdocs\bookingsmarts\application\controllers\home\Home.php
 * 
 * Insert BEFORE line 581: // ===== INSERT PEMESANAN =====
 */

// ✅ FALLBACK: pastikan TOTAL_PESERTA tersimpan jika ada di POST
if (!isset($data_pemesanan['TOTAL_PESERTA']) || $data_pemesanan['TOTAL_PESERTA'] == 0) {
    $peserta_post = $this->input->post('total_peserta');
    if ($peserta_post !== null && $peserta_post !== '' && $peserta_post !== false) {
        $peserta_val = (int)$peserta_post;
        if ($peserta_val > 0 && $this->db->field_exists('TOTAL_PESERTA', 'pemesanan')) {
            $data_pemesanan['TOTAL_PESERTA'] = $peserta_val;
            log_message('debug', 'FALLBACK: Saving TOTAL_PESERTA = ' . $peserta_val . ' from POST');
        }
    }
}
