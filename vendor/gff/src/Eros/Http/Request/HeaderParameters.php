<?php namespace Eros\Http\Request;

class HeaderParameters implements \IteratorAggregate, \Countable{

	protected $headers = array();
	protected $cacheControl = array();
	
	public function __construct(array $headers){
		
		$this->add($headers);
	}
	public function all(){
		return $this->headers;
	}
	
	public function keys(){
		return array_keys($this->headers);
	}
	
	
	
	public function add(array $headers=array()){
		
		foreach ($headers as $k=>$v){
			$this->set($k, $v);
		}
	}
	
	public function get($key, $default= null , $first = true){
		
	}
	public function has($key){
		
		return array_key_exists(strtr(strtolower($key),'_','-'), $key);
	}
	public function replace(array $headers= array()){
		
		$this->headers = array();
		$this->add($headers);
	}

	public function set($key, $value, $replace = true){
		
		$key = strtr(strtolower($key),'_','-');
		
		if(true === $replace || !isset($this->headers[$key])){
			$this->headers[$key] = $value;
		}else{
			$this->headers[$key] = array_merge($this->headers[$key], $value);
		}
		
		if($key == 'cache-control'){
			$this->cacheControl = $this->parseCacheControl();
		}
		
	}
	
	public function parseCacheControl($value){
		
	}
}