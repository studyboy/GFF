<?php
/**
 * 首页控制器
 */
use GFF\Controllers;
use GFF\View,
    GFF\Http;
    
class IndexController extends Controllers\Controller{
   
	public function __construct(){
	    parent::__construct();
	}
	public function __destruct(){
	}
	
	public function indexAction(){

	    $view=new View\View();
	    $view->assign('name','sowang');
        $view->assign('boy','boy');
	    $view->render();
	}
	
}