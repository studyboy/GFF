<?php namespace Eros\Http\Request;

class Parameters implements \IteratorAggregate, \Countable {

	protected $parameters = array();
	
	public function __construct(array $parameters){
		
		$this->parameters = $parameters;
	}
	/**
	 * 
	 * 獲取指定路徑下的內容 以a.b獲取
	 * @param unknown_type $path
	 * @param unknown_type $default
	 * @param unknown_type $deep If true,a path like foo.bar will be find.
	 */
	/*public function get($path, $default = null, $deep = false){
		
		if( !$deep || false === $pos = strpos($path, '.')){
			return $this->parameters[$path] ?: $default;
		}
		
		//解析含有深層次的
		$pathArr = explode('.', $path);
		
		if(count($pathArr) !== 2) {
			throw new \InvalidArgumentException(sprintf("Malformed path. expected just %d.",2));
		}
		
		$value = $this->parameters[$pathArr[0]];
		
		if(!is_array($value)) return $default;
		
		return $value[$pathArr[1]] ?: $default;
	}*/
	
	/**
	 * 
	 * 獲取內容
	 * @param unknown_type $path
	 * @param unknown_type $default
	 * @param unknown_type $deep
	 * @throws \InvalidArgumentException
	 */
	public function get($path, $default = null, $deep = false){
		
		if (!$deep || false === $pos = strpos($path, '[')) {
            return array_key_exists($path, $this->parameters) ? $this->parameters[$path] : $default;
        }

        $root = substr($path, 0, $pos);
        if (!array_key_exists($root, $this->parameters)) {
            return $default;
        }

        $value = $this->parameters[$root];
        $currentKey = null;
        for ($i = $pos, $c = strlen($path); $i < $c; $i++) {
            $char = $path[$i];

            if ('[' === $char) {
                if (null !== $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "[" at position %d.', $i));
                }

                $currentKey = '';
            } elseif (']' === $char) {
                if (null === $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "]" at position %d.', $i));
                }

                if (!is_array($value) || !array_key_exists($currentKey, $value)) {
                    return $default;
                }

                $value = $value[$currentKey];
                $currentKey = null;
            } else {
                if (null === $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "%s" at position %d.', $char, $i));
                }

                $currentKey .= $char;
            }
        }

        if (null !== $currentKey) {
            throw new \InvalidArgumentException(sprintf('Malformed path. Path must end with "]".'));
        }

        return $value;
	}

	public function has($key){
		
		return array_key_exists($key, $this->parameters);
	}
	
	public function set($key, $value){
	
		$this->parameters[$key] = $value;
	}
	
	public function add(array $parameters){
		
		$this->parameters = array_replace($this->parameters, $parameters);
	}
	public function remove($key){
		
		unset($this->parameters[$key]);
	}
	
	public function replace(array $parameters){
		
		$this->parameters = $parameters;
	}
	
	public function keys(){
		
		return array_keys($this->parameters);
	}
	
	public function all(){
		
		return $this->parameters;
	}
	
	public function filter($path, $default= null, $deep =false, $filter = FILTER_DEFAULT, $options = array() ){
	
		$value = $this->get($path, $default, $deep);
		
		if( !is_array($options) && $options){
			$options = array('flags' => $options);
		}
		
		if( is_array($options) && !isset($options['flags'])){
			$options['flags'] = FILTER_REQUIRE_ARRAY;
		}
		
		return filter_var($value, $filter, $options);
	}
	
	public function getIterator(){
	
		return new \ArrayIterator($this->parameters);
	}
	
	public function count(){
	
		return count($this->parameters);
	}
}