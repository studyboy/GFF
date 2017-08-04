<?php

class Str {

	public static function contains ($haystack, $needles){
		foreach((array) $needles as $needle){
			if($needle != '' && strpos($haystack, $needle)) return true;
			return false;
		}
	}
	/**
	 * 
	 * 查找以指定字符開頭的字符串
	 * @param unknown_type $haystack
	 * @param unknown_type $needles
	 */
	public static function stratsWith($haystack, $needles){
		
		foreach ((array)$needles as $needle){
			
			if($needle !='' && strpos($haystack, $needle) == 0 ) return true;
			
			return false;
		}
	}
	
}