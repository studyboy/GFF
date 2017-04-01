<?php

/*****************************
 * 引入自動加載程序
 ********************************/
namespace AB;
use Eros\Support\Finder;

define('ROOT',dirname(__DIR__));

require ROOT."/vendor/autoload.php";

$dirs = Finder::create()->files()->name('*')->in(ROOT.'/config');


class A{
	public function __construct(\Iterater $a,$b){
		
	}
}

class B extends A {
	
}


$para = new \ReflectionClass("AB\B");

echo ($p = $para->getConstructor()->getParameters()[0]);
echo $p->name;
exit;
echo ($con->getParameters()[0]->getClass()->getName()->name);
exit;


//echo count($dirs).'ok';

//print_r($dirs);

foreach ($dirs as $dir){
	 print_r($dir->getFileName()."\n\r");
}
exit;


//
//require __DIR__."/config/config_const.class.php";
//
//
//$app = require __DIR__.'/bootstrap/app.class.php';
//
////启动类
//
//$app->run();



?>