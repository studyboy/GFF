<?php namespace Eros\Contracts\Routing;

interface UrlGeneratorInterface {
	
	public function to($path, $headers = array(), $secure = null);
	
	public function secure($path, $parameters = array());
	
	public function asset($path, $secure = null);
	
	public function route($name, $parameters = array(), $absolute = true);
	
	public function action($action, $parameters = array(), $absolute = true);
	
	public function setRootControllerNamespace($rootNamespace);
}