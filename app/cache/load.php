<?php

$cachefile = $_SERVER['DOCUMENT_ROOT'] . '/' . sha1('index') . '.log';
$cachetime = 3600 * 12;
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
    readfile($cachefile);
    exit;
}
ob_start();
