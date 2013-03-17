<?php
/**
 * 
 * 解析url路径
 * @author Sowang
 * @version 2013/03/10
 */
namespace GFF\Http;

class Request{
	
	  protected $uri;
	  protected  $config;
	  
      public  function __construct($config=null){
      	 $this->config=$this->getAppHelper();
      	 $this->pareRoute();
      }
	  public function pareRoute(){
	  	 $this->uri=$this->pareParam();
	  	 return $this->uri;
	  }
	  public function getUrlArray(){
	  	return  $this->uri;
	  }
	  public function get($key){
	  	 return (isset($this->uri[$key])?$this->uri[$key]:null);
	  }
	  public function getAppHelper(){
	  	   return \GFF\Application\ApplicationHelper::init();
	  }
	  protected function pareParam(){
	  	 //todo http://localhost/module/controller/action/p1/p2/p3.....;
          $config=$this->config->getOptions();
	  	  $method=$_SERVER['REQUEST_METHOD'];
	  	  switch (strtoupper($method)){
	  	  	  case 'GET':
	  	  	  	 $uri=str_replace('\\','/',$_SERVER['REQUEST_URI']);
			  	 $param=explode('/',$uri);
			  	 $params=array(
			  	    'module'=>$param[1]?$param[1]:$config['route']['default_module'],
			  	    'controller'=>$param[2]?$param[2]:$config['route']['default_controller'],
			  	    'action'    =>$param[3]?$param[3]:$config['route']['default_action'],
		 	  	 );
		 	  	 $params=array_merge($params,array_slice($params?$params:array(),4));
	  	       break;
	  	  	  case 'POST':
	  	  	  	   $params=$_POST;
	  	  	   break;
	  	  	  default:
	  	  	  	break;
	  	  }
	  	  $d = array_map(function($a){ return(addslashes($a));},$params);
	  	  
	  	  return $d;
	  }
}