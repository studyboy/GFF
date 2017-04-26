<?php namespace Eros\Http;

use Eros\Foundation\Application;
use Eros\Routing\Router;
use Eros\Contracts\Http\KernelInterface;

class Kernel implements KernelInterface{
	
	protected $app;
	
	protected $router;
	
	
	protected $bootStrap = array();

	public function __construct(Application $app,Router $router){
		
		$this->app = $app;
		$this->router = $router;
	}
	
	public function handle($request){
	
	}

}