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
    /**
     * 
     * 獲取傳輸內容
     * @param unknown_type $key
     */
    public function input($key){
    	
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