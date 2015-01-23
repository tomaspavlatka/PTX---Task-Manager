<?php 

define('APP', __DIR__);
define('DS', '/');
define('TPL', APP . DS . 'tpls' . DS);
require APP . DS . 'Config' . DS . 'config.php';

$tpl = file_get_contents(TPL . 'index.tpl');
$replace = array();
$content = strtr($tpl, $replace);

$tpl = file_get_contents(TPL . 'layout.tpl');
$replace = array(
    '[+content+]' => $content);
echo strtr($tpl, $replace);
