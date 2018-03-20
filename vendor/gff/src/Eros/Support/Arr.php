<?php namespace Eros\Support;

use SebastianBergmann\CodeCoverage\Report\Html\Facade;

class Arr {
	
	/**
	 * 
	 * 指定索引沒有值的時候向位置添加新值
	 * @param unknown_type $array
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function add($array, $key, $value){
		if(is_null(static::has($array, $key))) {
			static::set($array, $key, $value);
		} 
		return $array;
	}
	
	public static function build($array, callable $callBack){
		
	}
	public static function divide($array){
	}
	public static function dot($array, $prepend = ''){
	}
	public static function except($array, $keys){
	}
	public static function fetch($array, $key){
	}
	public static function last($array, callable $callback, $default = null){
	}
	public static function first($array, callable $callback, $default = null){
		
	}
	public static function flatten($array){
	}
	/**
	 * 
	 * 以點式確定鍵值深度
	 * @param unknown_type $array
	 * @param unknown_type $keys
	 */
	public static function forget(&$array, $keys){
		
		$original = &$array;
		
		foreach((array)$keys as $key){
			
			$parts = explode('.', $key);
			
			while(count($parts) > 1){
				
				$part = array_shift($parts);
				
				if(isset($array[$part]) && is_array($array[$part]) ){
				
					$array = & $array[$part];
				}
			}
			
			unset($array[array_shift($parts)]);
			
			$array = &$original;
		}
		
	}
	/**
	 * 
	 * 逐級獲取數組的值
	 * @param unknown_type $array
	 * @param unknown_type $key
	 * @param unknown_type $default
	 */
	public static function get($array, $key, $default = NULL){
		
		if(is_null($key)) return $array;
		
		if(array_key_exists($key, $array)) return $array[$key];
		
		$segments = explode('.', $key);
		
		foreach ($segments as $segment){
			
			if( !(is_array($array) || array_key_exists($key, $array)) ){
				return ($default instanceof \Closure ? $default() : $default);
			}
			
			$array = $array[$segment];
		}
		
		return $array;
	}
	/**
	 * 
	 * 給定數組設定新值
	 * @param unknown_type $array
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function set(&$array, $key, $value){
		
		if( is_null($key)) return $array = $value;
		
		$segments = explode('.', $key);
		
		while (count($segments) > 1){
			
			$segment = array_shift($segments);
			if( !isset($array[$segment]) || !is_array($array[$segment])){
				$array[$segment] = array();
			}
			$array =& $array[$segment];
		}
		
		$array[array_shift($segments)] = $value;
		
		return $array;
	}
	/**
	 * 
	 * 檢查數組中是否有值
	 * @param unknown_type $array
	 * @param unknown_type $key
	 */
	public static function has($array, $key){
		if(empty($array) || is_null($key)) return false;
		
		if( array_key_exists($key, $array)) return true;
		
		$segments = explode('.', $key);
		
		foreach ($segments as $segment){
			if( !(is_array($array) || array_key_exists($key, $array))){
				return false;
			}
			$array = $array[$key];
		}
	}
	/**
	 * 
	 * 獲取給定keys的子項
	 * @param unknown_type $array
	 * @param unknown_type $keys
	 */
	public static function only($array, $keys){
		return array_intersect_key($array, array_flip((array)$keys));
	}
	public static function pluck($array, $value, $key = null){
	}
	public static function pull(&$array, $key, $default = null){
	}
	public static function sort($array, callable $callBack){
		
	}
	public static function where($array, callable $callBack){
	}
}