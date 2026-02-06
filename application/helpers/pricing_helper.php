<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper perhitungan harga ruangan (khusus eksternal)
 *
 * Mode:
 * - FLAT            : pakai HARGA_SEWA (konsep lama)
 * - PER_PESERTA     : Meeting Room / Amphitheater (halfday/fullday per peserta)
 * - PODCAST_PER_JAM : Studio Podcast (audio/video per jam)
 *
 * Catatan:
 * - INTERNAL tidak diubah (tetap seperti sebelumnya): harga ruangan = 0
 * - Per jam dibulatkan ke atas (ceil).
 * - Jika kolom harga baru belum ada di DB, helper ini memakai default:
 *   halfday_pp=30000, fullday_pp=60000, audio_per_jam=150000, video_per_jam=200000.
 */

if (!function_exists('bs_detect_pricing_mode')) {
    function bs_detect_pricing_mode($nama_gedung, $pricing_mode_db = '')
    {
        $pm = strtoupper(trim((string)$pricing_mode_db));
        if ($pm !== '') return $pm;

        $namaU = strtoupper(trim((string)$nama_gedung));
        if ($namaU === '') return 'FLAT';

        if (strpos($namaU, 'PODCAST') !== false) return 'PODCAST_PER_JAM';

        if (
            strpos($namaU, 'MEETING') !== false ||
            strpos($namaU, 'AMPHI') !== false ||
            strpos($namaU, 'AMPHITHEATER') !== false ||
            strpos($namaU, 'AMPITHEATER') !== false ||
            strpos($namaU, 'AMPHITEATER') !== false
        ) return 'PER_PESERTA';

        return 'FLAT';
    }
}

if (!function_exists('bs_minutes_from_hhmm')) {
    function bs_minutes_from_hhmm($hhmm)
    {
        $s = trim((string)$hhmm);
        if ($s === '') return null;
        $s = substr($s, 0, 5); // HH:MM
        if (!preg_match('/^([0-2]\\d):([0-5]\\d)$/', $s, $m)) return null;
        return ((int)$m[1]) * 60 + (int)$m[2];
    }
}

if (!function_exists('bs_duration_hours_ceil')) {
    function bs_duration_hours_ceil($jam_mulai, $jam_selesai)
    {
        $m1 = bs_minutes_from_hhmm($jam_mulai);
        $m2 = bs_minutes_from_hhmm($jam_selesai);
        if ($m1 === null || $m2 === null) return 0;
        $diff = $m2 - $m1;
        if ($diff <= 0) return 0;
        return (int)ceil($diff / 60);
    }
}

if (!function_exists('bs_get_val')) {
    function bs_get_val($row, $key, $default = null)
    {
        if (is_array($row) && array_key_exists($key, $row)) return $row[$key];
        if (is_object($row) && isset($row->$key)) return $row->$key;
        return $default;
    }
}

if (!function_exists('bs_calc_room_sewa')) {
    function bs_calc_room_sewa($row, $is_internal = false)
    {
        if ($is_internal) return 0;

        $nama_gedung = bs_get_val($row, 'NAMA_GEDUNG', '');
        $pricing_mode_db = bs_get_val($row, 'PRICING_MODE', '');
        $mode = bs_detect_pricing_mode($nama_gedung, $pricing_mode_db);

        $tipe_jam = strtoupper(trim((string)bs_get_val($row, 'TIPE_JAM', '')));
        if ($tipe_jam === '') $tipe_jam = 'CUSTOM';

        $harga_sewa_flat = (int)bs_get_val($row, 'HARGA_SEWA', 0);

        // defaults
        $halfday_pp = (int)bs_get_val($row, 'HARGA_HALF_DAY_PP', 30000);
        $fullday_pp = (int)bs_get_val($row, 'HARGA_FULL_DAY_PP', 60000);
        $audio_per_jam = (int)bs_get_val($row, 'HARGA_AUDIO_PER_JAM', 150000);
        $video_per_jam = (int)bs_get_val($row, 'HARGA_VIDEO_PER_JAM', 200000);

        if ($mode === 'PER_PESERTA') {
            $total_peserta = (int)bs_get_val($row, 'TOTAL_PESERTA', 1);
            if ($total_peserta < 1) $total_peserta = 1;

            $rate = $halfday_pp;
            if ($tipe_jam === 'FULL_DAY') $rate = $fullday_pp;
            if ($rate < 0) $rate = 0;

            return (int)$rate * (int)$total_peserta;
        }

        if ($mode === 'PODCAST_PER_JAM') {
            $podcast_type = strtoupper(trim((string)bs_get_val($row, 'PODCAST_TYPE', 'AUDIO')));
            $rate = ($podcast_type === 'VIDEO') ? $video_per_jam : $audio_per_jam;
            if ($rate < 0) $rate = 0;

            $durasi_jam = (int)bs_get_val($row, 'DURASI_JAM', 0);
            if ($durasi_jam <= 0) {
                $durasi_jam = bs_duration_hours_ceil(
                    bs_get_val($row, 'JAM_PEMESANAN', ''),
                    bs_get_val($row, 'JAM_SELESAI', '')
                );
            }
            if ($durasi_jam < 1) $durasi_jam = 1;

            return (int)$rate * (int)$durasi_jam;
        }

        // FLAT (konsep lama)
        return max(0, (int)$harga_sewa_flat);
    }
}
