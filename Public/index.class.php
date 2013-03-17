<?php
//use GFF\Application as App;
require __DIR__."/Config/config_const.class.php";
require PROJECT_ROOT.'Library/GFF/Application/application.class.php';

//启动类
Application::run();

function __autoLoad($class_name){
//	echo $class_name."<br/>";
	$name=explode('\\',$class_name);
	$fileName=array_pop($name);
	$namespace=join('/',$name);
	$name=substr($class_name,strrpos($class_name,'\\')+1);
	if(!class_exists($class_name) && strpos($class_name,'GFF') !==false){
//		echo APP_LIBRARY_PATH.$namespace.'/'.lcfirst($fileName).'.class.php'."<br/>";
		require APP_LIBRARY_PATH.$namespace.'/'.lcfirst($fileName).'.class.php';
	}
}

?>