<?php

$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
$u = $base_url . $_SERVER["REQUEST_URI"];
$u = str_replace('&fbclid=', '?fbclid=', $u);
$fb = '?fbclid=';
if ($newurl = strstr($u, $fb, true)) {
    header('Location: ' . $newurl);
}

function url_origin($s, $use_forwarded_host = false)
{
    $ssl      = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on');
    $sp       = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port     = $s['SERVER_PORT'];
    $port     = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
    $host     = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host     = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function full_url($s, $use_forwarded_host = false)
{
    return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
}

$absolute_url = full_url($_SERVER);
$parseUrl = parse_url($absolute_url);
$proto = '';
if (!empty($_SERVER['HTTP_CF_VISITOR'])) {
    try {
        $jsonScheme = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
        if ($jsonScheme['scheme'] == 'https') {
            $proto = 'https://';
        } else {
            $proto = $parseUrl['scheme'] . '://';
        }
    } catch (\Throwable $th) {
    }
}

if ($proto == '') {
    $proto = $parseUrl['scheme'] . '://';
}

$origin = $proto . $_SERVER['HTTP_HOST'];

define('web', [
    'url'           => $origin,
    'full_url'      => $origin . $_SERVER['REQUEST_URI'],
    'domain'        => $_SERVER['HTTP_HOST'],
    'title'         => $_ENV['TITLE'],
    'description'   => $_ENV['DESCRIPTION'],
    'keyword'       => $_ENV['KEYWORD'],
    'author'        => $_ENV['AUTHOR'],
    'icon'          => '/' . $_ENV['FAVICON']
]);
