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

//get access_token
$ret = $miniProgram->access_token->getToken();
var_dump($ret);
