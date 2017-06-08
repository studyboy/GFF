<?php
/*--------------------------------
 * 定義根目錄
 *--------------------------------
 * 
 */
//error_reporting(E_ALL);

use Eros\Http\Request\AcceptHeader;
use Eros\Http\Request\Request;

define('ROOT',dirname(__DIR__));

/*------------------------------------------------
 * 引入compoer自動加載文件，以便自動加載涉及到的類文件
 * ------------------------------------------------
 * 
 *
 */
require ROOT."/vendor/autoload.php";

/*--------------------------------------------
 * 引入引導程序
 *--------------------------------------------
 * 
 * 引入application以便我們能夠啟動框架，并返回
 * 視圖給瀏覽器
 * 
 */

$app = require ROOT.'/bootstrap/app.php';

$kernel = $app->make('Eros\Contracts\Http\KernelInterface');

$reponse = $kernel->handle(
	$request = Eros\Http\Request::run()
);
sdfwer
//輸出模板
//$reponse->send();

?>