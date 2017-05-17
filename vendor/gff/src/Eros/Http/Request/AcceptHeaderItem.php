<?php namespace Eros\Http\Request;
/**
 * 
 * +------------------------------------------------
 * 拆分header值
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/5/10
 * +-------------------------------------------------
 */

class AcceptHeaderItem{
	
	protected $attrs = array();
	
	protected $index = 0;
	
	protected $quality = 1.0;
	
	protected $value;
	

	public function __construct( $value, array $attrs = array() ){
		
		$this->value = $value;
		
		foreach ($attrs as $key => $attr){
			
			$this->setAttribute($key, $attr);
		}
	}
	
	public static function fromString($itemValue){

		$items = preg_split('/\s*(?:;*("[^"]+");*|;*(\'[^\']+\');*|;+)\s*/', $itemValue, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$value = array_shift($items); 
		$attrs = array();
		
		$lastNullAttr = null;
		foreach ($items as $item){
			if(($start = substr($item,0,1)) === ($end = substr($item, -1)) && ($start === '"' || $start ==='\'')){
				$attrs[$lastNullAttr] = substr($item, 1,-1);
			}elseif('=' == $end){
				$lastNullAttr = $item = substr($item, 0,-1);
				$attrs[$item] = null;
			}else{
				$parts = explode('=', $item);
				
				$attrs[$parts[0]] = isset($parts[1]) && strlen($parts[1]) > 0 ? $parts[1] : '';
			}
		}
	
		return new self(($start = substr($value, 0,1)) === ($end= substr($value, -1)) && ($start === '"' || $start ==='\'') ? substr($value,1,-1) : $value , $attrs);
	}
	
	public function __toString(){
	
		$string  = $this->value.($this->quality < 1 ? ';q='.$this->quality :'');
		//拆分備用項
		if(count($this->attrs) > 0){
			
			$string = ';'.implode(';', array_map(function ($name, $value){
				
				return sprintf(preg_match('/[,;=]',$value ) ? '%s="%s"' : '%s=%s', $name, $value);
			
			}, array_keys($this->attrs), $this->attrs));
		}
	}
	
	public function setValue($value){
		
		$this->value = $value;
		
		return $this;
	}
	
	public function getValue(){
		
		return $this->value;
	}
	
	public function setQuality($quality){
		
		$this->quality = $quality;
		
		return $this;
	}
	
	public function getQuality(){
		
		return $this->quality;
	}
	
	public function setIndex($index){
		
		$this->index = $index;
		
		return $this;
	}
	public function getIndex(){
		
		return $this->index;
	}
	
	public function setAttribute($name, $value){
		
		if( 'q' === $name) {
			$this->setQuality((float) $value);
		}else{
			$this->attrs[$name] = (string) $value;
		}
	}
	
	public function hasAttribute($key){
		
		return isset($this->attrs[$key]);
	}
	
	public function getAttribute($key, $default = null){
		
		return isset($this->attrs[$key]) ? $this->attrs[$key] : $default;
	}
	
	public function getAttributes(){
		
		return $this->attrs;
	}
	
	

}