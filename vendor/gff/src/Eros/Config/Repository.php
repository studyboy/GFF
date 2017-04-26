<?php namespace Eros\Config;
/**
 * 
 * +------------------------------------------------
 * parse config path with dot notation.
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/3/22
 * +-------------------------------------------------
 */
use Eros\Contract\Config\Repository as RepositoryContract;
use Eros\Support\Arr;

class Repository implements \ArrayAccess,RepositoryContract{
	
	private $items = array();
	
	public function __construct(array $items = array()){
		
		$this->$items = $items;
	}
	/**
	 * (non-PHPdoc)
	 * @see Eros\Contract\Config.Repository::has()
	 */
	public function has($key){
		
		return Arr::has($this->items, $key);
	}
	public function get($key,$default = NULL){
		
		return Arr::get($this->items, $key, $default);
	}
	/**
	 * 設置兩個值
	 * @see Eros\Contract\Config.Repository::set()
	 */
	public function set($key, $value){
		
		if(is_array($key)){
			foreach ($key as $innerKey=>$innerVal){
				Arr::set($this->items, $innerKey, $innerVal);
			}
		}else{
			Arr::set($this->items, $key, $value);
		}
	}
	public function prepend($key, $value ){
		
		$array = $this->get($key);
		
		array_unshift($array, $value);
		
		return $this->set($key, $value);
	}
	public function push($key, $value){
		
		$array = $this->get($key);
		
		array_push($array, $value);
		
		return $this->set($key, $value);
	}
	public function all(){
		
		return $this->items;
	}
	public function offsetExists($offset){
		
		return $this->has($offset);
	}
	public function offsetGet($offset){
		
		return $this->get($offset);
	}
	
	public function offsetSet($offset, $value){
		
		return $this->set($offset, $value);
	}
	public function offsetUnset($offset){
		
		return $this->set($offset, null);
	}
}