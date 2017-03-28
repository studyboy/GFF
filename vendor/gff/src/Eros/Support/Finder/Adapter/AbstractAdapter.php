<?php namespace Eros\Support\Finder\Adapter;

/**
 * 
 * +------------------------------------------------
 * 定義過濾函數
 * +------------------------------------------------
 * @author  10313
 * +-------------------------------------------------
 * @version 2017/3/24
 * +-------------------------------------------------
 */
abstract class AbstractAdapter implements AdapterInterface{
	
	protected $mode;
	protected $names = array();
	protected $notNames = array();
	protected $excludes = array();
	protected $ignoreUnreadableDirs = false;
	protected $filters = array();
	
	private  static $areSupported = array();
	
	
	public function setMode($mode){
		
		$this->mode = $mode;
		
		return $this;
	}
	
	public function setExcludes(array $exclude){
		
		$this->excludes = $exclude;
		
		return $this;
	}
	
	public function setNames(array $names){
		
		$this->names = $names;
		
		return $this;
	}
	
	public function setNotNames(array $notNames){
		
		$this->notNames = $notNames;
		
		return $this;
	}
	
	public function setFilters(array $filters){
		
		$this->filters = $filters;
		
		return $this;
	}
	public function isSupported(){
		
		$name = $this->getName();
		
		if( !isset(self::$areSupported[$name])){
			self::$areSupported[$name] = $this->canBeused();
		}
		
		return self::$areSupported[$name];
	}
	
	public abstract function canBeused();

}