<?php namespace Eros\Http;

use Eros\Contracts\Foundation\ApplicationInterface as Application;
use Eros\Routing\Router;
use Eros\Contracts\Http\KernelInterface;

class Kernel implements KernelInterface{
	
	protected $app;
	
	protected $router;
	
	
	protected $bootstrapers = array(
		'Eros\Foundation\Bootstrap\LoadConfiguration',
	);
	
//	protected $middleWare = array();
//	
//	protected $routeMiddleWare = array();

	public function __construct(Application $app,Router $router){
		
		$this->app = $app;
		
		$this->router = $router;
	}
	
	public function handle($request){
	
		$this->bootstrap();
	}
	/**
	 * 
	 * 將配置以單例的方式傳入app全局調用
	 */
	public function bootstrap(){
		
		if( !$this->app->getHasBeenBootstraped() ){
		   
			$this->app->bootstrapWith($this->bootstrapers);
		}
	}
	
	public function getBootstrapers(){
		return $this->bootstrapers;
	}

}