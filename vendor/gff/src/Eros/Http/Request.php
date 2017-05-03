<?php namespace Eros\Http;
/**
 * 實現和調用分離
 */
use Eros\Http\Request\Request as HttpRequest;
use ArrayAccess;
use Eros\Contracts\Http\RequestInterface;

class Request extends HttpRequest implements RequestInterface ,ArrayAccess{


	public function __construct(){
		
	}
	public static function run(){
		
		return static::createFromGlobals();
		
	}

	public function offsetExists($offset){
	}
	
	public function offsetGet($offset){
	}
	
	public function offsetSet($offset, $value){
	}
	
	public function offsetUnset($offset){
	}
}