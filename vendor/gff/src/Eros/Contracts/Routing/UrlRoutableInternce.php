<?php namespace Eros\Contracts\Routing;

interface UrlRoutableInterface {
	
	public function getRouteKey();
	
	public function getRouteKeyName();
}