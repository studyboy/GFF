<?php
/*****************************
 *  入口程序
 *****************************/

$app = new Eros\Foundation\Application(realpath(ROOT));


/*---------------------------------
 * 設置單例
 *---------------------------------
 */
$app->singleton(
	'Eros\Contracts\Http\KernelInterface',
	'App\Http\Kernel'
);

$app->singleton(
	'Eros\Contracts\Debug\HandleExceptionInterface',
	'App\Exceptions\Handler'
);


return $app;