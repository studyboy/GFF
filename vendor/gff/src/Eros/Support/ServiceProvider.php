<?php namespace Eros\Support;


abstract class ServiceProvider{
	
	protected $app;
	
	protected $defer = false;
	
	public function __construct($app){
		
		$this->app = $app;
	}
	
	abstract public function register();
}