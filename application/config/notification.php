<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['admin_emails'] = [
    '21530065.ikhsan@sinus.ac.id',
    'ikhsanwahyu04 @gmail . com'
];

// ⚠️ "from" harus email yang REAL dan sama dengan smtp_user (untuk Gmail SMTP)
$config['mail_from']      = 'ikhsanwahyu04@gmail.com';
$config['mail_from_name'] = 'Booking Smarts';

$config['smtp'] = [
    'protocol'    => 'smtp',
    'smtp_host'   => 'smtp.gmail.com',
    'smtp_user'   => 'ikhsanwahyu04@gmail.com',
    'smtp_pass'   => 'blyebtsyrveyngib',
    'smtp_port'   => 587,
    'smtp_crypto' => 'tls',

    'mailtype'    => 'html',
    'charset'     => 'utf-8',
    'newline'     => "\r\n",
    'crlf'        => "\r\n",
    'smtp_timeout' => 30,
];
