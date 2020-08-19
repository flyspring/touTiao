<?php

require '../vendor/autoload.php';

use Spring\TouTiao\Application as MiniProgram;

$config = require '../src/config/toutiao.php';
if (empty($config)) {
    echo 'config is empty';
    exit;
}

/**
 * 
 * @var MiniProgram $miniProgram
 */
$miniProgram = new MiniProgram($config);

// code2session
$ret = $miniProgram->qrcode->create('toutiao');

if ($ret['errcode'] == 0) {
    file_put_contents('a.png', $ret['content']);
}
var_dump($ret);
