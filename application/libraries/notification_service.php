<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification_service
{
  protected $CI;
  protected $initialized = false;

  public function __construct()
  {
    $this->CI = &get_instance();
    $this->CI->load->library('email');
    $this->CI->load->database();
    // tetap pakai group "notification"
    $this->CI->load->config('notification', true);
  }

  private function insertNotif($username, $type, $title, $message, $url)
  {
    $this->CI->db->insert('notifications', [
      'username'   => $username,
      'type'       => $type,
      'title'      => $title,
      'message'    => $message,
      'url'        => $url,
      'created_at' => date('Y-m-d H:i:s'),
      'read_at'    => null,
      'emailed_at' => null,
    ]);
    return (int)$this->CI->db->insert_id();
  }

  private function markEmailed($id)
  {
    if ($id > 0) {
      $this->CI->db->where('id', $id)->update('notifications', [
        'emailed_at' => date('Y-m-d H:i:s')
      ]);
    }
  }

  private function getUserEmail($username)
  {
    $u = $this->CI->db->query(
      "SELECT EMAIL FROM user WHERE LOWER(USERNAME)=? LIMIT 1",
      array(strtolower(trim((string)$username)))
    )->row_array();

    return ($u && !empty($u['EMAIL'])) ? $u['EMAIL'] : null;
  }


  private function getTotalTagihanFromView($id_pemesanan_raw)
  {
    $id_full = 'PMSN000' . (int)$id_pemesanan_raw;

    $row = $this->CI->db->select('TOTAL_KESELURUHAN')
      ->from('v_pemesanan')
      ->where('ID_PEMESANAN', $id_full)
      ->limit(1)
      ->get()
      ->row_array();

    if ($row && isset($row['TOTAL_KESELURUHAN'])) {
      return (float)$row['TOTAL_KESELURUHAN'];
    }
    return null;
  }

  private function sendEmail($to, $subject, $html)
  {
    $smtp     = $this->CI->config->item('smtp', 'notification');
    $from     = $this->CI->config->item('mail_from', 'notification');
    $fromName = $this->CI->config->item('mail_from_name', 'notification');

    // init config SMTP (usahakan sekali saja atau pastikan konsisten)
    if (!$this->initialized && is_array($smtp) && !empty($smtp)) {
      if (!isset($smtp['mailtype'])) $smtp['mailtype'] = 'html';
      if (!isset($smtp['charset']))  $smtp['charset']  = 'utf-8';
      if (!isset($smtp['newline']))  $smtp['newline']  = "\r\n";
      if (!isset($smtp['crlf']))     $smtp['crlf']     = "\r\n";

      $this->CI->email->initialize($smtp);
      $this->CI->email->set_newline($smtp['newline']);
      $this->CI->email->set_crlf($smtp['crlf']);
      $this->initialized = true;
    }

    $this->CI->email->clear(true);
    $this->CI->email->from($from, $fromName);
    $this->CI->email->to($to);
    $this->CI->email->subject($subject);
    $this->CI->email->message($html);

    // Suppress notices (seperti errno 10053 pada Windows) agar tidak merusak UI jika smtp gagal
    $ok = @$this->CI->email->send();

    if (!$ok) {
      log_message('error', 'EMAIL FAIL: ' . $this->CI->email->print_debugger(['headers', 'subject', 'body']));
    } else {
      log_message('debug', 'EMAIL OK to=' . $to . ' subject=' . $subject);
    }

    return $ok;
  }


  private function rupiah($n)
  {
    $n = (float)$n;
    return 'Rp ' . number_format($n, 0, ',', '.');
  }
  private function getRincianPemesananFromView($id_pemesanan_full, $fallbackUsername, $fallbackEmail)
  {
    $row = $this->CI->db->from('v_pemesanan')
      ->where('ID_PEMESANAN', $id_pemesanan_full)
      ->limit(1)
      ->get()
      ->row_array();

    if (!$row) {
      return array(
        'id' => $id_pemesanan_full,
        'username' => $fallbackUsername ? $fallbackUsername : '-',
        'email' => $fallbackEmail ? $fallbackEmail : '-',
        'tanggal' => '-',
        'jam' => '-',
        'ruangan' => '-',
        'jumlah_catering' => 'Tidak Ada',
        'harga_ruangan' => '-',
        'total_catering' => $this->rupiah(0),
        'total_keseluruhan' => '-',
        'status' => '-',
        'keperluan' => '-'
      );
    }

    $username = (isset($row['USERNAME']) && $row['USERNAME'] !== '') ? $row['USERNAME'] : $fallbackUsername;
    $email    = (isset($row['EMAIL']) && $row['EMAIL'] !== '') ? $row['EMAIL'] : $fallbackEmail;

    $tanggalRaw = isset($row['TANGGAL_PEMESANAN']) ? $row['TANGGAL_PEMESANAN'] : '';
    if ($tanggalRaw !== '') {
      $ts = strtotime($tanggalRaw);
      $tanggal = $ts ? date('d F Y', $ts) : $tanggalRaw;
    } else {
      $tanggal = '-';
    }

    $jamMulai = isset($row['JAM_PEMESANAN']) ? $row['JAM_PEMESANAN'] : '';
    $jamAkhir = isset($row['JAM_SELESAI']) ? $row['JAM_SELESAI'] : '';
    $jam = ($jamMulai !== '' && $jamAkhir !== '') ? ($jamMulai . ' - ' . $jamAkhir . ' WIB') : '-';

    $ruangan = (isset($row['NAMA_GEDUNG']) && $row['NAMA_GEDUNG'] !== '') ? $row['NAMA_GEDUNG'] : '-';

    $jumlahC = isset($row['JUMLAH_CATERING']) ? trim((string)$row['JUMLAH_CATERING']) : '';
    $jumlahCateringText = ($jumlahC === '' || strtolower($jumlahC) === 'tidak ada' || (int)$jumlahC === 0) ? 'Tidak Ada' : $jumlahC;

    $namaPaket = isset($row['NAMA_PAKET']) ? trim((string)$row['NAMA_PAKET']) : '';
    $namaCateringText = ($namaPaket === '' || strtolower($namaPaket) === 'tidak ada') ? 'Tidak Ada' : $namaPaket;

    $hargaRuangan = isset($row['HARGA_SEWA']) ? $row['HARGA_SEWA'] : null;
    $hargaRuanganText = ($hargaRuangan === null || $hargaRuangan === '') ? '-' : $this->rupiah($hargaRuangan);

    $totalCatering = isset($row['TOTAL_HARGA']) ? $row['TOTAL_HARGA'] : 0;
    $totalCateringText = $this->rupiah($totalCatering);

    $totalAll = isset($row['TOTAL_KESELURUHAN']) ? $row['TOTAL_KESELURUHAN'] : null;
    $totalAllText = ($totalAll === null || $totalAll === '') ? '-' : $this->rupiah($totalAll);

    $status = (isset($row['STATUS']) && $row['STATUS'] !== '') ? $row['STATUS'] : '-';

    $keperluan = (isset($row['DESKRIPSI_ACARA']) && $row['DESKRIPSI_ACARA'] !== '') ? $row['DESKRIPSI_ACARA'] : '-';

    return array(
      'id' => $id_pemesanan_full,
      'username' => $username ? $username : '-',
      'email' => $email ? $email : '-',
      'tanggal' => $tanggal,
      'jam' => $jam,
      'ruangan' => $ruangan,
      'nama_catering' => $namaCateringText,
      'jumlah_catering' => $jumlahCateringText,
      'harga_ruangan' => $hargaRuanganText,
      'total_catering' => $totalCateringText,
      'total_keseluruhan' => $totalAllText,
      'status' => $status,
      'keperluan' => $keperluan
    );
  }

  private function buildRincianPemesananHtml($d)
  {
    $esc = function ($v) {
      return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    };

    return '
  <tr>
    <td style="padding:0 22px 18px 22px;">
      <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
        <div style="background:#111827;color:#fff;padding:12px 14px;font-weight:bold;">Rincian Pemesanan</div>

        <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;width:42%;">ID Pemesanan</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;font-weight:bold;">' . $esc($d['id']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Username</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['username']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Email</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['email']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Tanggal Pemesanan</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['tanggal']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Jam Pemesanan</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['jam']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Ruangan</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['ruangan']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Jumlah Catering</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['jumlah_catering']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Harga Ruangan</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['harga_ruangan']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Total Catering</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['total_catering']) . '</td>
          </tr>

          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#065f46;background:#ecfdf5;font-weight:bold;">Total Keseluruhan</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;font-weight:bold;color:#065f46;background:#ecfdf5;">' . $esc($d['total_keseluruhan']) . '</td>
          </tr>

          <tr>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#6b7280;">Status</td>
            <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;">' . $esc($d['status']) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 14px;font-size:12px;color:#6b7280;vertical-align:top;">Keperluan Acara</td>
            <td style="padding:12px 14px;font-size:13px;line-height:18px;">' . $esc($d['keperluan']) . '</td>
          </tr>
        </table>
      </div>
    </td>
  </tr>';
  }

  private function buildProposalApprovedEmail($nama_user, $id_pemesanan_full, $total_tagihan, $link_bayar, $rincianHtml)
  {
    $bank_nama = $this->CI->config->item('payment_bank_name', 'notification');
    $bank_rek  = $this->CI->config->item('payment_bank_account', 'notification');
    $bank_an   = $this->CI->config->item('payment_bank_holder', 'notification');
    $due_days  = (int)$this->CI->config->item('payment_due_days', 'notification');
    if ($due_days <= 0) $due_days = 7;

    $invoice_no   = 'INV-' . date('Ymd') . '-' . preg_replace('/\D+/', '', $id_pemesanan_full);
    $invoice_date = date('d M Y');
    $due_date     = date('d M Y', strtotime('+' . $due_days . ' days'));

    if ($total_tagihan === null || $total_tagihan === '' || $total_tagihan === 0) {
      $total_tagihan_text = 'Silakan cek di halaman pembayaran';
    } else {
      $total_tagihan_text = $this->rupiah($total_tagihan);
    }

    $nama_user         = htmlspecialchars((string)$nama_user, ENT_QUOTES, 'UTF-8');
    $id_pemesanan_full = htmlspecialchars((string)$id_pemesanan_full, ENT_QUOTES, 'UTF-8');
    $link_bayar        = htmlspecialchars((string)$link_bayar, ENT_QUOTES, 'UTF-8');

    $bank_nama  = htmlspecialchars((string)$bank_nama, ENT_QUOTES, 'UTF-8');
    $bank_rek   = htmlspecialchars((string)$bank_rek, ENT_QUOTES, 'UTF-8');
    $bank_an    = htmlspecialchars((string)$bank_an, ENT_QUOTES, 'UTF-8');
    $invoice_no = htmlspecialchars((string)$invoice_no, ENT_QUOTES, 'UTF-8');

    return '
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#111827;">
  <table width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
    <tr><td align="center">
      <table width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">
        <tr>
          <td style="padding:18px 22px;border-bottom:1px solid #e5e7eb;">
            <div style="font-size:12px;letter-spacing:3px;color:#6b7280;font-weight:bold;">BOOKING SMARTS</div>
            <div style="font-size:18px;font-weight:bold;margin-top:4px;">Reservasi Disetujui</div>
          </td>
        </tr>

        <tr>
          <td style="padding:18px 22px;">
            <div style="font-size:14px;line-height:20px;">
              Halo <b>' . $nama_user . '</b>,<br>
              Reservasi pada pemesanan <b>' . $id_pemesanan_full . '</b> telah <b>DISETUJUI</b>.
              Silakan lakukan pembayaran melalui rekening di bawah ini.
            </div>

            <div style="margin-top:16px;">
              <a href="' . $link_bayar . '" style="display:inline-block;text-decoration:none;background:#2563eb;color:#fff;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:bold;">
                Buka Halaman Pembayaran
              </a>
            </div>

            <div style="margin-top:10px;font-size:12px;color:#6b7280;line-height:18px;">
              Catatan: Jika link masih <b>localhost</b>, hanya bisa dibuka dari komputer/server yang menjalankan aplikasi.
            </div>
          </td>
        </tr>

        ' . $rincianHtml . '

        <tr>
          <td style="padding:0 22px 18px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
              <div style="background:#7c3aed;color:#fff;padding:12px 14px;font-weight:bold;">Billing Statement</div>
              <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                <tr>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Invoice Number</b><br><span style="font-size:13px;">' . $invoice_no . '</span>
                  </td>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Customer</b><br><span style="font-size:13px;">' . $nama_user . '</span>
                  </td>
                </tr>
                <tr>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Invoice Date</b><br><span style="font-size:13px;">' . $invoice_date . '</span>
                  </td>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Due Date</b><br><span style="font-size:13px;">' . $due_date . '</span>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding:12px 14px;font-size:12px;">
                    <b>Total Charge</b><br>
                    <span style="font-size:18px;font-weight:bold;">' . $total_tagihan_text . '</span>
                  </td>
                </tr>
              </table>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:0 22px 22px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
              <div style="background:#7c3aed;color:#fff;padding:12px 14px;font-weight:bold;">
                Cara Pembayaran <span style="font-weight:normal;font-size:12px;">/ Payment Method</span>
              </div>

              <div style="padding:14px;">
                <div style="border:1px solid #e5e7eb;border-radius:12px;padding:12px;">
                  <div style="font-size:12px;color:#6b7280;font-weight:bold;margin-bottom:6px;">
                    Transfer Bank (' . $bank_nama . ')
                  </div>

                  <div style="font-size:12px;color:#6b7280;">Nomor Rekening</div>
                  <div style="font-size:18px;font-weight:bold;letter-spacing:1px;">' . $bank_rek . '</div>

                  <div style="height:10px;"></div>

                  <div style="font-size:12px;color:#6b7280;">Atas Nama</div>
                  <div style="font-size:14px;font-weight:bold;">' . $bank_an . '</div>

                  <div style="margin-top:10px;font-size:12px;color:#6b7280;line-height:18px;">
                    Berita transfer: <b>' . $id_pemesanan_full . '</b>
                  </div>
                </div>

                <div style="margin-top:12px;font-size:12px;line-height:18px;color:#6b7280;">
                  Setelah transfer, silakan upload bukti pembayaran pada halaman transaksi.
                </div>

                <div style="color:#111827;font-weight:bold;margin-top:12px;margin-bottom:6px;">Terima kasih,</div>
                <div style="margin-bottom:2px;">Admin Booking Smart Office</div>
                <div>085112345548</div> 
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:14px 22px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;">
            Email ini dikirim otomatis oleh sistem Booking Smarts.
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';
  }


  public function notifyProposalApproved($username_user, $id_pemesanan_raw, $sendEmail = true)
  {
    if (empty($username_user)) return;

    $id_pemesanan_raw = (int)$id_pemesanan_raw;
    $id_full = 'PMSN000' . $id_pemesanan_raw;

    list($nama_user, $email_user) = $this->getNamaUser($username_user);
    $total_tagihan = $this->getTotalKeseluruhanByPemesanan($id_pemesanan_raw);

    $title   = 'RESERVASI RUANGAN SMART OFFICE DISETUJUI';
    $message = 'Reservasi pada pesanan ' . $id_full . ' telah disetujui. Silakan lakukan pembayaran.';
    $url     = 'home/pemesanan';

    $notifId = $this->insertNotif($username_user, 'USER_PEMESANAN', $title, $message, $url);

    if (!$sendEmail) return;

    $to = $email_user ? $email_user : $this->getUserEmail($username_user);
    if (!$to) return;

    $link_bayar = site_url($url);

    $r = $this->getRincianPemesananFromView($id_full, $username_user, $to);
    $rincianHtml = $this->buildRincianPemesananHtml($r);

    $emailHtml  = $this->buildProposalApprovedEmail($nama_user, $id_full, $total_tagihan, $link_bayar, $rincianHtml);

    if ($this->sendEmail($to, $title, $emailHtml)) {
      $this->markEmailed($notifId);
    }
  }

  /**
   * Kirim permintaan review ke user setelah reservasi berstatus SUBMITTED (3).
   * - memasukkan notification ke tabel notifications
   * - mengirim email (jika email tersedia)
   */
  public function notifyReviewRequest($username, $id_pemesanan_raw)
  {
    $id = (int)$id_pemesanan_raw;
    if ($id <= 0) return false;

    $full_id = 'PMSN000' . $id;

    // ambil rincian pemesanan untuk email/body
    $r = $this->getRincianPemesananFromView($full_id, $username, null);

    $title = 'Mohon Review Reservasi ' . $full_id;
    $message = 'Halo ' . htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8') . "\n\n";
    $message .= 'Terima kasih telah melakukan reservasi. Mohon luangkan waktu untuk menulis ulasan mengenai pengalaman Anda dengan pemesanan ' . $full_id . ".\n\n";
    $message .= 'Jika Anda tidak mengisi ulasan dalam waktu 3 hari, sistem akan mengisi ulasan otomatis dengan rating 5 tanpa komentar.\n\n';
    $message .= 'Klik untuk menulis ulasan: ' . site_url('home/ulasan') . '\n';

    $notifId = $this->insertNotif($username, 'REVIEW_REQUEST', $title, $message, 'home/ulasan');

    $to = $this->getUserEmail($username);
    if ($to) {
      $html = '<p>Halo ' . htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8') . ',</p>';
      $html .= '<p>Terima kasih telah melakukan reservasi <strong>' . htmlspecialchars($full_id, ENT_QUOTES, 'UTF-8') . '</strong>.</p>';
      $html .= '<p>Mohon luangkan waktu untuk menulis ulasan tentang pengalaman Anda. Jika Anda tidak mengisi ulasan dalam waktu 3 hari, sistem akan mengisi ulasan otomatis dengan rating <strong>5</strong> tanpa komentar.</p>';
      $html .= '<p><a href="' . site_url('home/ulasan') . '">Tulis Ulasan</a></p>';
      $html .= '<hr/>' . $this->buildRincianPemesananHtml($r);

      $sent = $this->sendEmail($to, 'Mohon Tulis Ulasan untuk Reservasi ' . $full_id, $html);
      if ($sent) $this->markEmailed($notifId);
    }

    return true;
  }


  private function getTotalKeseluruhanByPemesanan($id_pemesanan_raw)
  {
    $id_full = 'PMSN000' . (int)$id_pemesanan_raw;

    $row = $this->CI->db->select('TOTAL_KESELURUHAN')
      ->from('v_pemesanan')
      ->where('ID_PEMESANAN', $id_full)
      ->get()
      ->row_array();

    return ($row && isset($row['TOTAL_KESELURUHAN'])) ? (float)$row['TOTAL_KESELURUHAN'] : null;
  }
  private function buildProposalRejectedEmail($nama_user, $id_pemesanan_full, $remarks, $link_detail, $link_upload, $rincianHtml)
  {
    $nama_user         = htmlspecialchars((string)$nama_user, ENT_QUOTES, 'UTF-8');
    $id_pemesanan_full = htmlspecialchars((string)$id_pemesanan_full, ENT_QUOTES, 'UTF-8');
    $link_detail       = htmlspecialchars((string)$link_detail, ENT_QUOTES, 'UTF-8');
    $link_upload       = htmlspecialchars((string)$link_upload, ENT_QUOTES, 'UTF-8');

    if ($remarks === null || trim((string)$remarks) === '') {
      $remarks = 'Kegiatan belum memenuhi ketentuan.';
    }

    $remarks_html = nl2br(htmlspecialchars((string)$remarks, ENT_QUOTES, 'UTF-8'));
    $reject_date = date('d M Y');

    $btn_upload = '';
    if (!empty($link_upload)) {
      $btn_upload = '
      <a href="' . $link_upload . '" style="display:inline-block;text-decoration:none;background:#7c3aed;color:#fff;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:bold;margin-left:8px;">
        Upload Ulang
      </a>';
    }

    return '
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#111827;">
  <table width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
    <tr><td align="center">
      <table width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">
        <tr>
          <td style="padding:18px 22px;border-bottom:1px solid #e5e7eb;">
            <div style="font-size:12px;letter-spacing:3px;color:#6b7280;font-weight:bold;">BOOKING SMARTS</div>
            <div style="font-size:18px;font-weight:bold;margin-top:4px;">Reservasi Ditolak</div>
          </td>
        </tr>

        <tr>
          <td style="padding:18px 22px;">
            <div style="font-size:14px;line-height:20px;">
              Halo <b>' . $nama_user . '</b>,<br>
              Reservasi pada pemesanan <b>' . $id_pemesanan_full . '</b> pada tanggal <b>' . $reject_date . '</b> <b>DITOLAK</b>.
              Silakan lakukan perbaikan sesuai catatan di bawah ini.
            </div>

            <div style="margin-top:16px;">
              <a href="' . $link_detail . '" style="display:inline-block;text-decoration:none;background:#2563eb;color:#fff;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:bold;">
                Buka Detail Pemesanan
              </a>
              ' . $btn_upload . '
            </div>

            <div style="margin-top:10px;font-size:12px;color:#6b7280;line-height:18px;">
              Catatan: Jika link masih <b>localhost</b>, hanya bisa dibuka dari komputer/server yang menjalankan aplikasi.
            </div>
          </td>
        </tr>

        ' . $rincianHtml . '

        <tr>
          <td style="padding:0 22px 18px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
              <div style="background:#ef4444;color:#fff;padding:12px 14px;font-weight:bold;">Catatan Penolakan</div>
              <div style="padding:14px;font-size:13px;line-height:19px;color:#111827;">
                ' . $remarks_html . '
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:0 22px 22px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
              <div style="background:#7c3aed;color:#fff;padding:12px 14px;font-weight:bold;">
                Langkah Selanjutnya <span style="font-weight:normal;font-size:12px;">/ Next Steps</span>
              </div>
              <div style="padding:14px;font-size:12px;line-height:18px;color:#6b7280;">
                <ol style="margin:0;padding-left:18px;">
                  <li>Perbaiki proposal sesuai catatan penolakan.</li>
                  <li>Upload ulang proposal melalui aplikasi.</li>
                  <li>Admin akan meninjau kembali setelah proposal diperbarui.</li>
                </ol>
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:14px 22px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;">
            <div style="color:#111827;font-weight:bold;margin-bottom:6px;">Terima kasih,</div>
            <div style="margin-bottom:10px;">Admin Booking Smart Office</div>
            Email ini dikirim otomatis oleh sistem Booking Smarts.
          </td>
          <td> 085112345548</td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';
  }

  public function notifyProposalRejected($username_user, $id_pemesanan_raw, $remarks, $sendEmail = true)
  {
    if (empty($username_user)) return;

    $id_pemesanan_raw = (int)$id_pemesanan_raw;
    if ($id_pemesanan_raw <= 0) return;

    $id_full = 'PMSN000' . $id_pemesanan_raw;

    list($nama_user, $email_user) = $this->getNamaUser($username_user);

    $url = 'home/pemesanan/details/' . $id_full;

    $title   = 'RESERVASI RUANGAN SMART OFFICE DITOLAK';
    $message = 'Reservasi pada pesanan ' . $id_full . ' ditolak. Catatan: ' . $remarks;

    $notifId = $this->insertNotif($username_user, 'USER_PEMESANAN', $title, $message, $url);

    if (!$sendEmail) return;

    $to = $email_user ? $email_user : $this->getUserEmail($username_user);
    if (!$to) return;

    $link_detail = site_url($url);
    $link_upload = site_url('home/pemesanan');

    $r = $this->getRincianPemesananFromView($id_full, $username_user, $to);
    $rincianHtml = $this->buildRincianPemesananHtml($r);

    $emailHtml = $this->buildProposalRejectedEmail($nama_user, $id_full, $remarks, $link_detail, $link_upload, $rincianHtml);

    if ($this->sendEmail($to, $title, $emailHtml)) {
      $this->markEmailed($notifId);
    }
  }

  private function buildPaymentRejectedEmail($nama_user, $id_pemesanan_full, $total_tagihan, $catatan, $link_detail, $rincianHtml)
  {
    $bank_nama = $this->CI->config->item('payment_bank_name', 'notification');
    $bank_rek  = $this->CI->config->item('payment_bank_account', 'notification');
    $bank_an   = $this->CI->config->item('payment_bank_holder', 'notification');

    $reject_date = date('d M Y');
    $invoice_no  = 'INV-' . date('Ymd') . '-' . preg_replace('/\D+/', '', $id_pemesanan_full);

    if ($total_tagihan === null || $total_tagihan === '' || (int)$total_tagihan === 0) {
      $total_tagihan_text = 'Silakan cek di halaman transaksi/pembayaran';
    } else {
      $total_tagihan_text = $this->rupiah($total_tagihan);
    }

    if ($catatan === null || trim((string)$catatan) === '') {
      $catatan = 'Bukti pembayaran belum sesuai/kurang jelas. Silakan upload ulang bukti pembayaran yang valid.';
    }

    $nama_user         = htmlspecialchars((string)$nama_user, ENT_QUOTES, 'UTF-8');
    $id_pemesanan_full = htmlspecialchars((string)$id_pemesanan_full, ENT_QUOTES, 'UTF-8');
    $link_detail       = htmlspecialchars((string)$link_detail, ENT_QUOTES, 'UTF-8');

    $bank_nama  = htmlspecialchars((string)$bank_nama, ENT_QUOTES, 'UTF-8');
    $bank_rek   = htmlspecialchars((string)$bank_rek, ENT_QUOTES, 'UTF-8');
    $bank_an    = htmlspecialchars((string)$bank_an, ENT_QUOTES, 'UTF-8');
    $invoice_no = htmlspecialchars((string)$invoice_no, ENT_QUOTES, 'UTF-8');

    $catatan_html = nl2br(htmlspecialchars((string)$catatan, ENT_QUOTES, 'UTF-8'));

    return '
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#111827;">
  <table width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
    <tr><td align="center">
      <table width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">

        <tr>
          <td style="padding:18px 22px;border-bottom:1px solid #e5e7eb;">
            <div style="font-size:12px;letter-spacing:3px;color:#6b7280;font-weight:bold;">BOOKING SMARTS</div>
            <div style="font-size:18px;font-weight:bold;margin-top:4px;">Pembayaran Ditolak</div>
          </td>
        </tr>

        <tr>
          <td style="padding:18px 22px;">
            <div style="font-size:14px;line-height:20px;">
              Halo <b>' . $nama_user . '</b>,<br>
              Pembayaran untuk pesanan <b>' . $id_pemesanan_full . '</b> pada tanggal <b>' . $reject_date . '</b> <b>DITOLAK</b>.
              Silakan perbaiki/unggah ulang bukti pembayaran sesuai catatan berikut.
            </div>

            <div style="margin-top:16px;">
              <a href="' . $link_detail . '" style="display:inline-block;text-decoration:none;background:#2563eb;color:#fff;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:bold;">
                Buka Detail Pemesanan
              </a>
            </div>

            <div style="margin-top:10px;font-size:12px;color:#6b7280;line-height:18px;">
              Catatan: Jika link masih <b>localhost</b>, hanya bisa dibuka dari komputer/server yang menjalankan aplikasi.
            </div>
          </td>
        </tr>

        ' . $rincianHtml . '

        <tr>
          <td style="padding:0 22px 18px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
              <div style="background:#ef4444;color:#fff;padding:12px 14px;font-weight:bold;">Catatan Penolakan</div>
              <div style="padding:14px;font-size:13px;line-height:19px;color:#111827;">
                ' . $catatan_html . '
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:0 22px 18px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
              <div style="background:#7c3aed;color:#fff;padding:12px 14px;font-weight:bold;">Ringkasan Tagihan</div>
              <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                <tr>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Invoice Number</b><br><span style="font-size:13px;">' . $invoice_no . '</span>
                  </td>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Customer</b><br><span style="font-size:13px;">' . $nama_user . '</span>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding:12px 14px;font-size:12px;">
                    <b>Total Charge</b><br>
                    <span style="font-size:18px;font-weight:bold;">' . $total_tagihan_text . '</span>
                  </td>
                </tr>
              </table>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:0 22px 22px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
              <div style="background:#7c3aed;color:#fff;padding:12px 14px;font-weight:bold;">
                Cara Pembayaran <span style="font-weight:normal;font-size:12px;">/ Payment Method</span>
              </div>

              <div style="padding:14px;">
                <div style="border:1px solid #e5e7eb;border-radius:12px;padding:12px;">
                  <div style="font-size:12px;color:#6b7280;font-weight:bold;margin-bottom:6px;">
                    Transfer Bank (' . $bank_nama . ')
                  </div>

                  <div style="font-size:12px;color:#6b7280;">Nomor Rekening</div>
                  <div style="font-size:18px;font-weight:bold;letter-spacing:1px;">' . $bank_rek . '</div>

                  <div style="height:10px;"></div>

                  <div style="font-size:12px;color:#6b7280;">Atas Nama</div>
                  <div style="font-size:14px;font-weight:bold;">' . $bank_an . '</div>

                  <div style="margin-top:10px;font-size:12px;color:#6b7280;line-height:18px;">
                    Berita transfer: <b>' . $id_pemesanan_full . '</b>
                  </div>
                </div>

                <div style="margin-top:12px;font-size:12px;line-height:18px;color:#6b7280;">
                  Setelah upload ulang bukti pembayaran, admin akan melakukan verifikasi kembali.
                </div>

                <div style="color:#111827;font-weight:bold;margin-top:12px;margin-bottom:6px;">Terima kasih,</div>
                <div style="margin-bottom:2px;">Admin Booking Smart Office</div>
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:14px 22px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;">
            Email ini dikirim otomatis oleh sistem Booking Smarts.
          </td>
          <td> 085112345548</td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';
  }


  public function notifyPaymentRejected($username_user, $id_pemesanan_raw, $catatan, $sendEmail = true)
  {
    if (empty($username_user)) return;

    $id_pemesanan_raw = (int)$id_pemesanan_raw;
    if ($id_pemesanan_raw <= 0) return;

    $id_full = 'PMSN000' . $id_pemesanan_raw;

    list($nama_user, $email_user) = $this->getNamaUser($username_user);

    $total_tagihan = $this->getTotalKeseluruhanByPemesanan($id_pemesanan_raw);

    $url = 'home/pemesanan/details/' . $id_full;

    $title   = 'PEMBAYARAN RUANGAN SMART OFFICE DITOLAK';
    $message = 'Pembayaran untuk pesanan ' . $id_full . ' ditolak. Catatan: ' . $catatan;

    $notifId = $this->insertNotif($username_user, 'USER_TRANSAKSI', $title, $message, $url);

    if (!$sendEmail) return;

    $to = $email_user ? $email_user : $this->getUserEmail($username_user);
    if (!$to) return;

    $link_detail = site_url($url);

    $r = $this->getRincianPemesananFromView($id_full, $username_user, $to);
    $rincianHtml = $this->buildRincianPemesananHtml($r);

    $emailHtml = $this->buildPaymentRejectedEmail($nama_user, $id_full, $total_tagihan, $catatan, $link_detail, $rincianHtml);

    if ($this->sendEmail($to, $title, $emailHtml)) {
      $this->markEmailed($notifId);
    }
  }

  private function buildPaymentConfirmedEmail($nama_user, $id_pemesanan_full, $total_tagihan, $catatan, $link_detail, $rincianHtml)
  {
    $confirm_date = date('d M Y');
    $invoice_no   = 'INV-' . date('Ymd') . '-' . preg_replace('/\D+/', '', $id_pemesanan_full);

    if ($total_tagihan === null || $total_tagihan === '' || (int)$total_tagihan === 0) {
      $total_tagihan_text = 'Silakan cek di halaman transaksi/pembayaran';
    } else {
      $total_tagihan_text = $this->rupiah($total_tagihan);
    }

    $nama_user         = htmlspecialchars((string)$nama_user, ENT_QUOTES, 'UTF-8');
    $id_pemesanan_full = htmlspecialchars((string)$id_pemesanan_full, ENT_QUOTES, 'UTF-8');
    $link_detail       = htmlspecialchars((string)$link_detail, ENT_QUOTES, 'UTF-8');
    $invoice_no        = htmlspecialchars((string)$invoice_no, ENT_QUOTES, 'UTF-8');

    $catatan_html = '';
    $catatan = trim((string)$catatan);
    if ($catatan !== '') {
      $catatan_html = '
      <tr>
        <td style="padding:0 22px 18px 22px;">
          <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
            <div style="background:#7c3aed;color:#fff;padding:12px 14px;font-weight:bold;">Catatan Admin</div>
            <div style="padding:14px;font-size:13px;line-height:19px;color:#111827;">
              ' . nl2br(htmlspecialchars($catatan, ENT_QUOTES, 'UTF-8')) . '
            </div>
          </div>
        </td>
      </tr>';
    }

    return '
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#111827;">
  <table width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
    <tr><td align="center">
      <table width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">

        <tr>
          <td style="padding:18px 22px;border-bottom:1px solid #e5e7eb;">
            <div style="font-size:12px;letter-spacing:3px;color:#6b7280;font-weight:bold;">BOOKING SMARTS</div>
            <div style="font-size:18px;font-weight:bold;margin-top:4px;">Pembayaran Dikonfirmasi</div>
          </td>
        </tr>

        <tr>
          <td style="padding:18px 22px;">
            <div style="font-size:14px;line-height:20px;">
              Halo <b>' . $nama_user . '</b>,<br>
              Pembayaran untuk pesanan <b>' . $id_pemesanan_full . '</b> telah <b>DIKONFIRMASI</b> pada tanggal <b>' . $confirm_date . '</b>.
              Terima kasih, reservasi Anda telah diproses.
            </div>

            <div style="margin-top:16px;">
              <a href="' . $link_detail . '" style="display:inline-block;text-decoration:none;background:#2563eb;color:#fff;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:bold;">
                Buka Detail Pemesanan
              </a>
            </div>

            <div style="margin-top:10px;font-size:12px;color:#6b7280;line-height:18px;">
              Catatan: Jika link masih <b>localhost</b>, hanya bisa dibuka dari komputer/server yang menjalankan aplikasi.
            </div>
          </td>
        </tr>

        ' . $rincianHtml . '

        <tr>
          <td style="padding:0 22px 18px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
              <div style="background:#16a34a;color:#fff;padding:12px 14px;font-weight:bold;">Payment Receipt</div>
              <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                <tr>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Invoice Number</b><br><span style="font-size:13px;">' . $invoice_no . '</span>
                  </td>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Status</b><br><span style="font-size:13px;font-weight:bold;color:#16a34a;">CONFIRMED</span>
                  </td>
                </tr>
                <tr>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Customer</b><br><span style="font-size:13px;">' . $nama_user . '</span>
                  </td>
                  <td style="padding:12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;">
                    <b>Confirmed Date</b><br><span style="font-size:13px;">' . $confirm_date . '</span>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding:12px 14px;font-size:12px;">
                    <b>Total Paid</b><br>
                    <span style="font-size:18px;font-weight:bold;">' . $total_tagihan_text . '</span>
                  </td>
                </tr>
              </table>
            </div>
          </td>
        </tr>

        ' . $catatan_html . '

        <tr>
          <td style="padding:14px 22px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;">
            <div style="color:#111827;font-weight:bold;margin-bottom:6px;">Terima kasih,</div>
            <div style="margin-bottom:10px;">Admin Booking Smart Office</div>
            Email ini dikirim otomatis oleh sistem Booking Smarts.
          </td>
          <td> 085112345548</td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';
  }

  public function notifyPaymentConfirmed($username_user, $id_pemesanan_raw, $catatan, $sendEmail = true)
  {
    if (empty($username_user)) return;

    $id_pemesanan_raw = (int)$id_pemesanan_raw;
    if ($id_pemesanan_raw <= 0) return;

    $id_full = 'PMSN000' . $id_pemesanan_raw;

    list($nama_user, $email_user) = $this->getNamaUser($username_user);

    $total_tagihan = $this->getTotalKeseluruhanByPemesanan($id_pemesanan_raw);

    $url = 'home/pemesanan/details/' . $id_full;

    $title   = 'PEMBAYARAN RUANGAN SMART OFFICE DIKONFIRMASI';
    $message = 'Pembayaran untuk pesanan ' . $id_full . ' telah dikonfirmasi. Terima kasih.';

    $notifId = $this->insertNotif($username_user, 'USER_TRANSAKSI', $title, $message, $url);

    if (!$sendEmail) return;

    $to = $email_user ? $email_user : $this->getUserEmail($username_user);
    if (!$to) return;

    $link_detail = site_url($url);

    $r = $this->getRincianPemesananFromView($id_full, $username_user, $to);
    $rincianHtml = $this->buildRincianPemesananHtml($r);

    $emailHtml = $this->buildPaymentConfirmedEmail($nama_user, $id_full, $total_tagihan, $catatan, $link_detail, $rincianHtml);

    if ($this->sendEmail($to, $title, $emailHtml)) {
      $this->markEmailed($notifId);
    }
  }


  private function getNamaUser($username)
  {
    // aman untuk kasus username beda kapital (AWKARIN vs awkarin)
    $u = $this->CI->db->query(
      "SELECT NAMA_LENGKAP, EMAIL FROM user WHERE LOWER(USERNAME)=? LIMIT 1",
      array(strtolower(trim((string)$username)))
    )->row_array();

    if (!$u) return array(null, null);

    $nama  = !empty($u['NAMA_LENGKAP']) ? $u['NAMA_LENGKAP'] : $username;
    $email = !empty($u['EMAIL']) ? $u['EMAIL'] : null;

    return array($nama, $email);
  }

  public function notifyUser($username, $type, $title, $message, $url, $sendEmail = true)
  {
    if (empty($username)) return;

    $notifId = $this->insertNotif($username, $type, $title, $message, $url);

    if (!$sendEmail) return;

    $to = $this->getUserEmail($username);
    if (!$to) return;

    $link = site_url($url);
    $html = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) .
      '<br><br>Silakan cek: <a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">' .
      htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</a>';

    if ($this->sendEmail($to, $title, $html)) {
      $this->markEmailed($notifId);
    }
  }

  public function notifyAdmin($type, $title, $message, $url, $sendEmail = true)
  {
    $notifId = $this->insertNotif('admin', $type, $title, $message, $url);

    if (!$sendEmail) return;

    // ✅ ambil dari config group notification
    $emails = $this->CI->config->item('admin_emails', 'notification');
    if (!is_array($emails) || empty($emails)) return;

    $link = site_url($url);
    $html = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) .
      '<br><br>Link: <a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">' .
      htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</a>';

    $sent = false;
    foreach ($emails as $to) {
      if (!empty($to) && $this->sendEmail($to, $title, $html)) {
        $sent = true;
      }
    }

    if ($sent) $this->markEmailed($notifId);
  }

  public function get_unread($username, $types = [], $limit = 10)
  {
    $this->CI->db->from('notifications');
    $this->CI->db->where('username', $username);
    $this->CI->db->where('read_at IS NULL', null, false);
    if (!empty($types)) $this->CI->db->where_in('type', $types);
    $this->CI->db->order_by('id', 'DESC');
    $this->CI->db->limit((int)$limit);
    return $this->CI->db->get()->result_array();
  }

  public function count_unread($username, $types = [])
  {
    $this->CI->db->from('notifications');
    $this->CI->db->where('username', $username);
    $this->CI->db->where('read_at IS NULL', null, false);
    if (!empty($types)) $this->CI->db->where_in('type', $types);
    return (int)$this->CI->db->count_all_results();
  }
}
