<?php
error_reporting(0);
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('APPPATH', $_SERVER['DOCUMENT_ROOT'] . '/app/');
define('VIEWS', APPPATH . 'views/');
define('DIRPATH', $_SERVER['DOCUMENT_ROOT'] . '/public/');

require_once APPPATH . 'web.php';
