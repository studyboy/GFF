<?php namespace Eros\Http\Request;
/**
 * 
 * +------------------------------------------------
 * header元素管理器
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/5/11
 * +-------------------------------------------------
 */

class AcceptHeader {

	private $items = array();
	private $sorted = true;
	
	public function __construct(array $items ){
		
		foreach ($items as $item){
			
			$this->add($item);
		}
	}
	
	public static function fromString($headerValue){
		
		 $index = 0;

        return new self(array_map(function ($itemValue) use (&$index) {
        	
            $item = AcceptHeaderItem::fromString($itemValue);
            $item->setIndex($index++);
            
            return $item;      
        }, preg_split('/\s*(?:,*("[^"]+"),*|,*(\'[^\']+\'),*|,+)\s*/', $headerValue, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)));
	}
	
	public function __toString(){
		
		return implode(';', $this->items);
	}
	
	public function has($key){
		
		return isset($this->items[$key]);
	}
	
	public function get($key){
		
		return isset($this->items[$key]) ? $this->items[$key] : '';
	}
	
	public function add(AcceptHeaderItem $item){
		
		$this->items[$item->getValue()] = $item;
		
		$this->sorted = false;
		
		return $this;
	}
	
	public function all(){
		
		$this->sort();
		
		return $this->items;
	}
	/**
	 * 
	 * 重置數組指針
	 */
	public function first(){
	
		$this->sort();
		
		return !empty($this->items) ? reset($this->items) : null;
	}
	/**
	 * 
	 * 按正則過濾信息
	 * @param unknown_type $patten
	 */
	public function filter($patten){
		
		return new self(array_filter($this->items, function(AcceptHeaderItem $item) use($patten){
			return preg_match($patten, $item->getValue());
		}));
	}

	public function sort(){
		
		if(!$this->sorted) {
		
			uasort($this->items, function($a, $b){
				
				$qA = $a->getQuality();
				$qB = $b->getQuality();
				//升序
				if( $qA === $qB){ 
					return $a->getIndex() > $b->getIndex() ? 1 : -1;
				}
				//降序
				return $qA > $qB ? -1 : 1;
			});
			
			$this->sorted = true;
		}
	}
}