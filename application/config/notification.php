<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['admin_emails'] = [
    'bookingsmarts@gmail.com'
];
$config['smtp']['smtp_auth'] = true;
$config['smtp']['useragent'] = 'CodeIgniter';
$config['mail_from']      = 'bookingsmarts@gmail.com';
$config['mail_from_name'] = 'Booking Smarts';


$config['smtp'] = [
    'protocol'    => 'smtp',
    'smtp_host'   => 'ssl://smtp.gmail.com',
    'smtp_user'   => 'bookingsmarts@gmail.com',
    'smtp_pass'   => 'lwmkpwviazobsgkc',
    'smtp_port'   => 465,
    'smtp_crypto' => 'ssl',

    'mailtype'    => 'html',
    'charset'     => 'utf-8',
    'newline'     => "\r\n",
    'crlf'        => "\r\n",
    'smtp_timeout' => 30,
    'smtp_keepalive' => TRUE,
];
$config['payment_bank_name']    = 'BCA';
$config['payment_bank_account'] = '62954742756724';
$config['payment_bank_holder']  = 'PAK EKO TIGA SERANGKAI';
$config['payment_due_days']     = 7; // optional
// secret key to allow web-trigger of auto-review cron (optional). Keep empty to allow CLI only.
$config['auto_review_key'] = 'CHANGE_ME_SECRET_KEY';
