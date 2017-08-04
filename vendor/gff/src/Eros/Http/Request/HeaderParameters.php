<?php namespace Eros\Http\Request;

class HeaderParameters implements \IteratorAggregate, \Countable{

	protected $headers = array();
	protected $cacheControl = array();
	
	public function __construct(array $headers){
		foreach ($headers as $name=>$header){
			$this->set($name,$header);
		}
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
		
		$key = $this->formatKey($key);

		if(!array_key_exists($key,$this->headers)){
			
			if(null === $default){
				
				return $first ? null : array();
			}
			return $first ? $default : array($default);
		}
		
		if($first){
			return count($this->headers[$key])? $this->headers[$key][0] : $default;
		}
		
		return $this->headers[$key];
	}
	public function has($key){
		
		return array_key_exists(strtr(strtolower($key),'_','-'), $key);
	}
	
	public function replace(array $headers= array()){
		
		$this->headers = array();
		
		$this->add($headers);
	}
	
	public function remove($key){
		
		$key = $this->formatKey($key);
		
		unset($this->headers[$key]);
		
		if($key =='cache-control') {
			$this->cacheControl = array();
		}
	}
	
	public function contains($key, $value){
		
		$key = $this->formatKey($key);
		
		return in_array($value, $this->get($key,null, false));
	}
	public function set($key, $value, $replace = true){
		
		$key = strtr(strtolower($key),'_','-');
		
		$value = array_values((array) $value);
	
		if( true === $replace || !isset($this->headers[$key]) ){
			
			$this->headers[$key] = $value;
		}else{
			$this->headers[$key] = array_merge($this->headers[$key], $value);
		}
		
		if($key == 'cache-control'){
			$this->cacheControl = $this->parseCacheControl($value[0]);
		}
		
	}
	
    /* Adds a custom Cache-Control directive.
     *
     * @param string $key   The Cache-Control directive name
     * @param mixed  $value The Cache-Control directive value
     */
    public function addCacheControlDirective($key, $value = true)
    {
        $this->cacheControl[$key] = $value;

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * Returns true if the Cache-Control directive is defined.
     *
     * @param string $key The Cache-Control directive
     *
     * @return bool true if the directive exists, false otherwise
     */
    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl);
    }

    /**
     * Returns a Cache-Control directive value by name.
     *
     * @param string $key The directive name
     *
     * @return mixed|null The directive value if defined, null otherwise
     */
    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
    }

    /**
     * Removes a Cache-Control directive.
     *
     * @param string $key The Cache-Control directive
     */
    public function removeCacheControlDirective($key)
    {
        unset($this->cacheControl[$key]);

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * Returns an iterator for headers.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->headers);
    }

    /**
     * Returns the number of headers.
     *
     * @return int The number of headers
     */
    public function count()
    {
        return count($this->headers);
    }
	
 	protected function getCacheControlHeader(){
 		
        $parts = array();
        ksort($this->cacheControl);
        foreach ($this->cacheControl as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
                    $value = '"'.$value.'"';
                }

                $parts[] = "$key=$value";
            }
        }

        return implode(', ', $parts);
    }

    /**
     * Parses a Cache-Control HTTP header.
     *
     * @param string $header The value of the Cache-Control HTTP header
     *
     * @return array An array representing the attribute values
     */
    protected function parseCacheControl($header)
    {
        $cacheControl = array();
        preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $cacheControl[strtolower($match[1])] = isset($match[3]) ? $match[3] : (isset($match[2]) ? $match[2] : true);
        }

        return $cacheControl;
    }
	
	protected function formatKey($key){
		return strtr(strtolower($key), '_', '-');
	}
}