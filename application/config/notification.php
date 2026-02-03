<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['admin_emails'] = [
    'bookingsmarts@gmail.com'
];

// ⚠️ "from" harus email yang REAL dan sama dengan smtp_user (untuk Gmail SMTP)
$config['mail_from']      = 'ikhsanwahyu04@gmail.com';
$config['mail_from_name'] = 'Booking Smarts';

$config['smtp'] = [
    'protocol'    => 'smtp',
    'smtp_host'   => 'smtp.gmail.com',
    'smtp_user'   => 'bookingsmarts@gmail.com',
    'smtp_pass'   => 'UNKONWNN', // ganti dengan app password Gmail Anda
    'smtp_port'   => 587,
    'smtp_crypto' => 'tls',

    'mailtype'    => 'html',
    'charset'     => 'utf-8',
    'newline'     => "\r\n",
    'crlf'        => "\r\n",
    'smtp_timeout' => 30,
];
$config['payment_bank_name']    = 'BCA';
$config['payment_bank_account'] = '62954742756724';
$config['payment_bank_holder']  = 'PAK EKO TIGA SERANGKAI';
$config['payment_due_days']     = 7; // optional