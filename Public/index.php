<?php

/*****************************
 * 引入自動加載程序
 ********************************/
require __DIR__."vendor/autoload.php";

require __DIR__."/config/config_const.class.php";


$app = require __DIR__.'/bootstrap/app.class.php';

//启动类

$app->run();



?>