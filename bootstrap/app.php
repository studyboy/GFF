<?php
/*****************************
 *  入口程序
 *****************************/

$app = new Eros\Foundation\Application(ROOT);


/*---------------------------------
 * 設置單例
 *---------------------------------
 */

$app->singleton(
	'Eros\Contracts\Http\KernelInterface',
	'App\Http\Kernel'
);



return $app;