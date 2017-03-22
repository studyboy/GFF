<?php
/**
 * 
 * 应用程序入口
 * 前端控制器
 * @author Sowang
 *
 */
//namespace GFF\Application;
use GFF\Http;

class Application {
	
	 protected $_request;
	 protected $_response;
	 protected $config=array();
	  
	 public function __construct(){}
	 
     public  public  function run(){
        $instance= new self();
        $instance->init();
     	$instance->getRequest();
     	$instance->dispatch();
     }
     public function init(){
    	$config = GFF\Application\ApplicationHelper::init();
    	$this->config=$config->getOptions();
    	$this->config=$this->config['system'];
     }
     public function getRequest(){
     	$this->_request=new Http\Request();
     	return $this->_request;
     }
     public function getResponse(){
        $this->_response=new Http\Response();
        return $this->_response;
     }
     
     public function dispatch(){
    	
     	  $url_arr=$this->_request->getUrlArray();
     	  $controllerName=empty($url_arr['controller'])? 
     	                             ucfirst($this->config['route']['default_controller']):ucfirst($url_arr['controller']);                             
     	  $actionName= empty($url_arr['action'])? 
     	                             lcfirst($this->config['route']['default_action']):lcfirst($url_arr['action']);
     	  $moduleName=empty($url_arr['module']) ?
     	                           lcfirst($this->config['route']['default_module']):lcfirst($url_arr['module']);
     	                           
          $controllerName = lcfirst($controllerName);
     	  $conFile=APP_CONTROLLER_PATH.$moduleName.'/'.$controllerName.'Controller.class.php';

          $controllerName.='Controller';
          $actionName .='Action';
     	  if(!file_exists($conFile)){
     	  	  die('请求异常,不存在该文件！');
     	  };
     	  
     	  require $conFile;
     	  
     	  if(!class_exists(($controllerName))){
     	  	 die('请求的模块不存在！');
     	  }
     	  
     	  $controller=new $controllerName();

     	  if( !method_exists($controller,"{$actionName}")){
     	  	  die('请求的方法不存在！');
     	  }
     	 
     	  $controller-> $actionName();
     }
     
}