<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Format tanggal ke format Indonesia: "01 Januari 2026"
 * 
 * @param string|null $tgl  Tanggal dalam format apapun yang bisa di-parse oleh strtotime()
 * @return string
 */
if (!function_exists('format_tanggal_indo')) {
    function format_tanggal_indo($tgl)
    {
        if (empty($tgl)) return '-';

        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $ts = strtotime($tgl);
        if (!$ts) return '-';

        $d = date('d', $ts);
        $m = (int) date('n', $ts);
        $y = date('Y', $ts);

        return $d . ' ' . $bulan[$m] . ' ' . $y;
    }
}
