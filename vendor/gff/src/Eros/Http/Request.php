<?php namespace Eros\Http;
/**
 * 實現和調用分離
 */
use ArrayAccess;
use Eros\Contracts\Http\RequestInterface;

class Request implements RequestInterface ,ArrayAccess{


	public function __construct(){
		
	}
	public static function run(){
		
		
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