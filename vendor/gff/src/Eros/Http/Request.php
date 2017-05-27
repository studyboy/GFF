<?php namespace Eros\Http;
/**
 * 實現和調用分離
 */
use Eros\Http\Request\Request as HttpRequest;
use ArrayAccess;
use Eros\Contracts\Http\RequestInterface;

class Request extends HttpRequest implements RequestInterface ,ArrayAccess{

	
	public static function run(){
		
		return static::createFromBase(HttpRequest::createFromGlobals());
	}
	
    public static function createFromBase(HttpRequest $request){
    	
    	if( $request instanceof static ) return $request;
    	
    	$content = $request->getContent();
    	
    	$request = (new static)->duplicate(
    		$request->query->all(), $request->request->all(),
    		$request->attrs->all(), $request->cookies->all(),
    		$request->files->all(), $request->server->all()
    	);
    	
    	$request->content = $content;
    	
    	$request->request = $request->getInputSource();
    	
    	return $request;
    }

    public function duplicate(array $query = null,array $request = null, array $attrs = null, array $cookies = null, array $files = null, array $server = null){
    	
    	return parent::duplicate($query, $request, $attrs, $cookies, $files, $server);
    }
    
    public function getInputSource(){
    	
    	return $this->getMethod() === 'GET' ? $this->query : $this->request;
    }
    
    public function has($key){
    	
    	$keys = is_array($key) ? $key : func_get_args();
    	
    	foreach ($keys as $key){
    		
    		if( $this->isEmptyString($key)) return false;
    		
    	}
    	
    	return true;
    }
    
    public function isEmptyString($key){
    	
    	$value = $this->input($key);
    	
    	$boolOrArray = is_bool($value) || is_array($value);
    	
    	return !$boolOrArray && trim((string)$value) === '';
    }
    
    /**
     * 
     * 獲取傳輸內容
     * @param unknown_type $key
     */
    public function input($key = null, $default = null){
    	
    	$input = $this->getInputSource()->all() + $this->query->all();
    	
    	return array_get($input, $key, $default);
    }
    public function all(){
    	
    	return array_replace_recursive($this->input(), $this->files->all());
    }
    public function except($key){
    	
    	$keys = is_array($key) ? $key : func_get_args();
    
    	$input = $this->all();
    	
    	array_forget($input, $keys);
    	
    	
    	return $input;
    }
    
    /**
     * 
     * 檢測值是否存在
     * @param unknown_type $key
     */
    public function exists($key){
    	
    	$keys = is_array($key) ? $key : func_get_args();
    	
    	$values = $this->all();
    	
    	foreach ($keys as $key){
    		if( ! array_key_exists($key, $values) ) return false; 
    	}
    	return true;
    }
    
    /**
     * 
     * 從給定的資源內獲取數據
     * @param unknown_type $source
     * @param unknown_type $key
     * @param unknown_type $default
     */
    public function retrieveItem($source, $key, $default){
    	
    	if(is_null($key)) return $this->$source->all();
    	
    	return $this->$source->get($key, $default);
    }
    public function isJson(){
    	
    	return str_contains($this->header('Content-Type'),'/json');
    }
    
    public function wantsJson(){
    	
    	$acceptTypes = $this->getAcceptableContentTypes();
    	
    	return isset($acceptTypes[0]) && $acceptTypes[0] == 'application/json';
    	
    }
    public function query($key = null, $default = null){
    	
    	return $this->retrieveItem('query', $key, $default);
    }
  	
    public function cookie($key=null, $default=null){
    	
    	return $this->retrieveItem('cookies', $key, $default);;
    }
    public function hasCookie($key){
    	
    	return !is_null($this->cookie($key));
    }
    /**
     * 
     * 
     * @param unknown_type $key
     * @param unknown_type $default
     */
    public function file($key = null, $default = null){
    	
    	return array_get($this->files->all(), $key, $default);
    }
    public function hasFile($key){
    	
    	$files = !is_array($files = $this->file($key)) ? array($files) : $files;
    	
    	foreach ($files as $file){
    		
    		if($file instanceof \SplFileInfo) return true;
    	}
    	return false;
    }
    public function server($key = null, $default = null){
    	return $this->retrieveItem('server', $key, $default);
    }
    public function header($key = null, $default = NULL){
    	
    	return $this->retrieveItem('headers', $key, $default);
    }
    
	public function offsetExists($offset){
	}
	
	public function offsetGet($offset){
	}
	
	public function offsetSet($offset, $value){
	}
	
	public function offsetUnset($offset){
		
	}
}