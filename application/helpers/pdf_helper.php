<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Warsito PDF Helper
 *
 * Generates a PDF file from HTML content using DOMPDF.
 * Falls back gracefully if DOMPDF is not installed.
 *
 * Usage:
 *   $this->load->helper('pdf_helper');
 *   $html = $this->load->view('my_view', $data, true);
 *   generate_pdf($html, 'namafile.pdf', true); // true = force download
 */

if (!function_exists('generate_pdf')) {
    /**
     * Generate & stream a PDF from HTML content.
     *
     * @param  string  $html          HTML string to render as PDF
     * @param  string  $filename      Output filename (e.g. "Report.pdf")
     * @param  bool    $download      TRUE  = force download (attachment)
     *                                FALSE = display inline in browser
     * @return void
     */
    function generate_pdf($html, $filename = 'document.pdf', $download = true)
    {
        // ── Try DOMPDF (Composer install: composer require dompdf/dompdf) ──
        $autoloads = [
            FCPATH . 'vendor/autoload.php',
            APPPATH . 'vendor/autoload.php',
        ];

        $dompdf_loaded = false;
        foreach ($autoloads as $autoload) {
            if (file_exists($autoload)) {
                require_once $autoload;
                $dompdf_loaded = class_exists('Dompdf\Dompdf');
                break;
            }
        }

        if ($dompdf_loaded) {
            // ── DOMPDF path ──────────────────────────────────────────────
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $disposition = $download ? 'attachment' : 'inline';
            $dompdf->stream($filename, ['Attachment' => $download ? 1 : 0]);
            exit;
        }

        // ── Fallback: output as HTML if DOMPDF unavailable ───────────────
        // Shows a notice so developer knows DOMPDF needs to be installed.
        if (ob_get_length()) ob_end_clean();

        header('Content-Type: text/html; charset=UTF-8');
        echo '<div style="font-family:sans-serif;padding:20px;background:#fff3cd;border:1px solid #ffc107;margin:20px;border-radius:6px;">';
        echo '<strong>⚠️ PDF Library tidak tersedia.</strong><br>';
        echo 'Silakan install DOMPDF dengan perintah:<br>';
        echo '<code style="background:#f8f9fa;padding:4px 8px;border-radius:3px;">composer require dompdf/dompdf</code><br><br>';
        echo 'Berikut adalah konten HTML yang akan dicetak sebagai PDF:';
        echo '</div>';
        echo $html;
        exit;
    }
}
