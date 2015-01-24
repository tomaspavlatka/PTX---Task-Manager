<?php 

define('APP', __DIR__);
define('DS', '/');
require APP . DS . 'Config' . DS . 'bootstrap.php';

$tpl = file_get_contents(TPL . 'tasks.tpl');
$replace = array();
$content = strtr($tpl, $replace);

$tpl = file_get_contents(TPL . 'layout.tpl');
$replace = array(
    '[+content+]' => $content);
echo strtr($tpl, $replace);
 