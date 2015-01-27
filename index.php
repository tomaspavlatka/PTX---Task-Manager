<?php 
header('content-type: text/html; charset=utf-8');
require realpath(__DIR__). '/Config/bootstrap.php';

$tpl = file_get_contents(TPL . 'index.tpl');
$replace = array();
$content = strtr($tpl, $replace);

$tpl = file_get_contents(TPL . 'layout.tpl');
$replace = array(
    '[+content+]' => $content);
echo strtr($tpl, $replace);
