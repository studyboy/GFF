<?php namespace Eros\Component\HttpFoundation;


use Eros\Http\Request\HeaderParameters;

class ResponseHeaderBag extends HeaderParameters{

	const COOKIES_FLAT = 'flat';
    const COOKIES_ARRAY = 'array';

    const DISPOSITION_ATTACHMENT = 'attachment';
    const DISPOSITION_INLINE = 'inline';

    /**
     * @var array
     */
    protected $computedCacheControl = array();

    /**
     * @var array
     */
    protected $cookies = array();

    /**
     * @var array
     */
    protected $headerNames = array();
    
    
    public function __construct(array $headers = array()){
    	
    	parent::__construct($headers);
    	
    	if(!isset($this->get('cache-control'))){
    		
    		$this->set('Cache-Control', '');
    	}
    }
    
    public function __toString(){
    }
    
    public function replace(array $headers = array()){
    	
    	$this->headerNames = array();
    	
    	parent::replace($headers);
    	
    	if( !isset($this->headers['cache-control']) ){
    	
    		$this->set('cache-control', '');
    	}
    }
    
    public function remove($key){
    	
    	parent::remove($key);
    	
    	$uniqueKey = $this->formatKey($key);
    	unset($this->headerNames[$uniqueKey]);
    
    	if('cache-control' == $uniqueKey) {
    		
    		$this->computedCacheControl = array();
    	}
    	
    }
    
    public function set($key, $value, $replace = true){
    	
    	parent::set($key, $value, $replace);
    	
    	$uniqueKey = $this->formatKey($key);
    	$this->headerNames[$uniqueKey] = $key;
    	
    	if(in_array($key, array('cache-control', 'etag', 'last-modified', 'expires'))){

    		$computed = $this->computeCacheControlValue();
    		$this->headers['cache-control'] = array($computed);
    		$this->headerNames['cache-control'] = 'Cache-Control';
    		$this->computedCacheControl = $this->parseCacheControl($computed);
    	}
    }
    
    public function hasCacheControlDirective($key){
    	
    	return array_key_exists($key, $this->computedCacheControl[$key]);
    }
   
    public function getCacheControlDirective($key){
    	
    	return array_key_exists($key, $this->computedCacheControl[$key]) ? $this->computedCacheControl[$key] : null;
    }
    /**
     * 
     * 重組cache-control的值
     */
    protected function computeCacheControlValue() {
    	
    	if( !$this->cacheControl && !$this->has('etag') && !$this->has('last-modified') && !$this->has('expires')){
    	
    		return 'no-cache';
    	}
    	
    	if( !$this->cacheControl){
    		
    		return 'private, must-revalidate';
    	}
    	
    	$header = $this->getCacheControlHeader();
    	if( isset($this->cacheControl['public']) || isset($this->cacheControl['private'])){
    		return $header;
    	}
    	//有s-maxage為public，其它則為private
    	if( !isset($this->cacheControl['s-maxage']) ){
    		return $header.', private';
    	}
    	
    	return $header;
    }
    
    public function setCooke(Cookie $cookie){
    	$this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
    }
	
    public function clearCookie($name, $path = '/', $domain = null, $secure = false, $httpOnly = true){
    	$this->setCooke(new Cookie($name, null, 1, $path, $domain, $secure, $httpOnly));
    }
}