<?php
/*****************************
 *  入口程序
 *****************************/

$app = new Eros\Foundation\Application(dirname(__DIR__));


$app->singleton(
	'Eros\Contracts\Http\KernelInterface',
	'App\Http\Kernel'
);


return $app;