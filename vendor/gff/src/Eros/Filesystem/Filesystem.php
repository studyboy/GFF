<?php namespace Eros\Filesystem;

use Eros\Support\Traits\Macroable;

class Filesystem {

	use Macroable;
	
	public function get($path){
		
		return file_get_contents($path,false, stream_context_create(array(
				'http'=> array(
					'timeout' => 5
				)
		)));
		
	}
	
	public function put($path, $data){
		
		return file_put_contents($path, $data) ;
	}
	/**
	 * 
	 * 在內容前追加內容
	 * @param unknown_type $path
	 * @param unknown_type $data
	 */
	public function prepend($path, $data){
		
		if($this->isFile($path)){
			
			$old = $this->get($path);
			
			return $this->put($path, $data.$old);
		}
		return false;
	}
	/**
	 * 
	 * 向后追加文件內容
	 * @param unknown_type $path
	 * @param unknown_type $data
	 */
	public function append($path, $data){
		
		return file_put_contents($path, $data, FILE_APPEND);
	}
	
	public function remove($path){
		
		return @unlink($path);
	}
	
	public function move($source, $target ){
	
		return rename($source, $target);
	}
	
	public function copy($source, $starget ){
		
		return copy($source, $starget);
	}
	
	public function getRequire($file){
		
		if($this->isFile($file)) require $file;

		return false;
	}
	
	public function getRequireOnly($file){
		
		if($this->isFile($file)) require_once $file;
		
		return false;
	}
	/**
	 * 
	 * 檢測文件是否存在
	 * @param unknown_type $path
	 */
	public function exists($path){
		
		return file_exists($path);
	}
	
	public function isFile($path){
		
		return is_file($path);
	}
	
	public function isDir($path){

		return is_dir($path);
	}
	
	
}