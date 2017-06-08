<?php namespace Eros\Support\Traits;

use Closure;
use BadMethodCallException; 

trait Macroable {
	
	protected static $macros = array();
	
	public function macro($name, callable $callable){
		
		static::$macros[$name] = $callable;
	}
	
	public static function hasMacro($name){
		
		return isset(static::$macros[$name]);
	}
	
	/**
	 * 設定調用未聲明的方法時的動作
	 */
	public function __call($method, $args){
		
		if( static::hasMacro($method)){
			
			if( static::$macros[$method] instanceof Closure){
				
				return call_user_func_array( static::$macros[$method]->bindTo($this, get_class($this)), $args);
				
			}else{

				return call_user_func_array( static::$macros[$method], $args);
			}
		}
		
		throw new BadMethodCallException(sprintf("Method %s does not exist.", $method));
	}
	/**
	 * 設定調用未聲明的靜態方法時的動作
	 */
	public static function __callStatic($method, $args){
		
		if( static::hasMacro($method)){
			
			if( static::$macros[$method] instanceof Closure ){
				
				return call_user_func_array( Closure::bind(static::$macros[$method],null, get_called_class()), $args );
				
			}else{

				return call_user_func_array( static::$macros[$method], $args );
			}
		}
		
		throw new BadMethodCallException(sprintf("Method %s does not exist.", $method));
	}
	
}