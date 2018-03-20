<?php
namespace  Eros\Contracts\Config;

interface RepositoryInterface{
	/**
	 * 
	 * 查詢是否存在
	 * @param unknown_type $key
	 */
	public function has($key);
	/**
	 * 
	 * 獲取指定配置的值
	 * @param string $key
	 * @param mix $defualt
	 */
	public function get($key, $defualt = NULL);
	/**
	 * 
	 * 設置配置值
	 * @param string $key
	 * @param mix $value
	 */
	public function set($key,$value = NULL);
	/**
	 * 
	 * 在數組前預追加值
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function prepend($key, $value = NULL);
	/**
	 * 
	 * 在數組配置後追加值
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function push($key, $value = NULL);
	
}