<?php
/**
 * 定義全局自定義函數
 */

use Eros\Support\Arr;

if(!function_exists('array_get')){
	function array_get($array, $key, $default = null){
		return Arr::get($array, $key, $default);
	}
}


if(!function_exists('str_contains')){

	function str_contains($haystack, $needles){
		return Str::contains($haystack, $needles);
	}
}