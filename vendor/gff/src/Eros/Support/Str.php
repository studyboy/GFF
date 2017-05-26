<?php

class Str {

	public static function contains ($haystack, $needles){
		foreach((array) $needles as $needle){
			if($needle != '' && strpos($haystack, $needle)) return true;
			return false;
		}
	}
}