<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['admin_emails'] = [
    getenv('SMTP_USER') ?: 'bookingsmarts@gmail.com'
];
$config['mail_from']      = getenv('SMTP_USER') ?: 'bookingsmarts@gmail.com';
$config['mail_from_name'] = 'Booking Smarts';

$config['smtp'] = [
    'protocol'    => 'smtp',
    'smtp_host'   => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
    'smtp_user'   => getenv('SMTP_USER') ?: 'bookingsmarts@gmail.com',
    'smtp_pass'   => getenv('SMTP_PASS') ?: '',
    'smtp_port'   => (int) getenv('SMTP_PORT') ?: 465,
    'smtp_crypto' => getenv('SMTP_CRYPTO') ?: 'ssl',
    'smtp_auth'   => true,
    'useragent'   => 'CodeIgniter',

    'mailtype'    => 'html',
    'charset'     => 'utf-8',
    'newline'     => "\r\n",
    'crlf'        => "\r\n",
    'smtp_timeout' => 30,
    'smtp_keepalive' => TRUE,

    // Tambahan untuk bypass SSL verification di localhost jika perlu
    'smtp_conn_options' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]
];

$config['payment_bank_name']    = getenv('PAYMENT_BANK_NAME') ?: 'BCA';
$config['payment_bank_account'] = getenv('PAYMENT_BANK_ACCOUNT') ?: '';
$config['payment_bank_holder']  = getenv('PAYMENT_BANK_HOLDER') ?: '';
$config['payment_due_days']     = 7;

$config['auto_review_key']      = getenv('AUTO_REVIEW_KEY') ?: '';
