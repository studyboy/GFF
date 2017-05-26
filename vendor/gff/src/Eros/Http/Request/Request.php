<?php namespace Eros\Http\Request;

use Eros\Http\Session\SessionInterface;

class Request{
   
	const HEADER_CLIENT_IP   = 'client_ip';
	const HEADER_CLIENT_HOST = 'client_host';
	const HEADER_CLIENT_PROTO= 'client_proto';
	const HEADER_CLIENT_PORT = 'client_port';
	
	//request methods
	const METHOD_GET  	= 'GET';
	const METHOD_POST 	= 'POST';
	const METHOD_HEAD   = 'HEAD';
	const METHOD_PUT	= 'PUT';
	const METHOD_DELETE = 'DELETE';
	const METHOD_PATCH  = 'PATCH';
	const METHOD_OPTIONS= 'OPTIONs';
	const METHOD_CONNECT= 'CONNECT';
	const METHOD_PURGE  = 'PURGE';
	const METHOD_TRACE  = 'TRACE';
	
	protected static $httpMethodParameterOverride = false;
	
	public $query;
	public $request;
	public $attrs;
	public $cookies;
	public $server;
	protected $content;
	public  $files;
	public $headers;
	protected $charset;
	protected $encoding;
	protected $language;
	protected $acceptableContentTypes;
	protected $pathInfo;
	protected $method;
	protected $baseUrl;
	protected $basePath;
	protected $requestUri;
	protected $session;
	protected $local;
	protected $defaultLcal= 'en';
	protected $format;
	
	protected static $formats;

	protected $trustedProxies;
	
	protected $trustedHeaders = array(
		self::HEADER_CLIENT_IP => 'X_FORWARDED_FOR',
        self::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST',
        self::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO',
        self::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT',
	);
	
	public function __construct(array $query = array(), array $request = array(),array $attrs = array(),array $cookies=array(),array $files=array(), array $server=array(), array $content = null){
		
		$this->init($query, $request, $attrs, $cookies, $files, $server, $content);
	}
	
	public function init(array $query = array(), array $request = array(),array $attrs = array(),array $cookies=array(),array $files=array(), array $server=array(), array $content = null ){
		
		$this->query   = new Parameters($query);
		$this->request = new Parameters($request);
		$this->attrs   = new Parameters($attrs);
		$this->cookies = new Parameters($cookies);
		$this->files   = new FileParameters($files);
		$this->server  = new ServerParameters($server);
		$this->headers = new HeaderParameters($this->server->getHeaders());
		
		
		$this->content = $content;
        $this->language = null;
        $this->charset = null;
        $this->encoding = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }
    
    
	/**
	 * 
	 * 構建所有變量
	 */	
	public static function createFromGlobals(){
	
		//php 在命令行端有將content_type content_length 保存在http_content_type 和http_content_length中
		//屬於php 變量的bug
		$server = $_SERVER;
		if('cli-server' == php_sapi_name()){
			if(array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)){
				$server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
			}
			if(array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)){
				$server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
			}
		}
		
		$request = self::createRequestFromFactory($_GET, $_POST, array(), $_COOKIE, $_FILES, $server);
		
		
		return $request;		
	}
	
	public static function createRequestFromFactory(array $query = array(), array $request = array(),array $attrs = array(),array $cookies=array(),array $files=array(), array $server=array(), array $content = null){
		
		return new static($query, $request, $attrs, $cookies, $files, $server, $content);
	}
	
    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null){
   	
        $dup = clone $this;
        if ($query !== null) {
            $dup->query = new Parameters($query);
        }
        if ($request !== null) {
            $dup->request = new Parameters($request);
        }
        if ($attributes !== null) {
            $dup->attributes = new Parameters($attributes);
        }
        if ($cookies !== null) {
            $dup->cookies = new Parameters($cookies);
        }
        if ($files !== null) {
            $dup->files = new FileParameters($files);
        }
        if ($server !== null) {
            $dup->server = new ServerParameters($server);
            $dup->headers = new HeaderParameters($dup->server->getHeaders());
        }
        
        $dup->language = null;
        $dup->charset = null;
        $dup->encoding = null;
        $dup->acceptableContentTypes = null;
        $dup->pathInfo = null;
        $dup->requestUri = null;
        $dup->baseUrl = null;
        $dup->basePath = null;
        $dup->method = null;
        $dup->format = null;

        if (!$dup->get('_format') && $this->get('_format')) {
        	
            $dup->attrs->set('_format', $this->get('_format'));
        }

        if (!$dup->getRequestFormat(null)) {
            $dup->setRequestFormat($this->getRequestFormat(null));
        }

        return $dup;
    }

    /**
     * Clones the current request.
     *
     * Note that the session is not cloned as duplicated requests
     * are most of the time sub-requests of the main one.
     */
    public function __clone(){
    	
        $this->query = clone $this->query;
        $this->request = clone $this->request;
        $this->attrs = clone $this->attrs;
        $this->cookies = clone $this->cookies;
        $this->files = clone $this->files;
        $this->server = clone $this->server;
        $this->headers = clone $this->headers;
    }
    /**
     * 
     * 從全局參數中提取變量值
     * @param unknown_type $key
     * @param unknown_type $default
     * @param unknown_type $deep
     */
    public function get($key, $default = null, $deep=false){
    	
    	if($this !== $result = $this->query->get($key, $this, $deep)){
    		return $result;
    	}
    	
    	if($this !== $result = $this->attrs->get($key, $this, $deep)){
    		return $result;
    	}
    	
   		if($this !== $result = $this->request->get($key, $this, $deep)){
    		return $result;
    	}
    	return $default;
    }
    
    public function setSession(SessionInterface $session){
    	$this->session = $session;
    }
    
    public function getSession(){
    	return $this->getSession();
    }
    
    public function getPreviousSession(){
    	
    	return $this->hasSession() && $this->cookies->get($this->session->getName());
    }
	
    public function hasSession(){
    	return null !== $this->session;
    }
    
    public function setMethod($method){
    	
    	$this->method = null;
    	$this->server->set('REQUEST_METHOD', $method);
    }
    public function getMethod(){
    	
    	if( null === $this->method){
    		$this->method = strtoupper($this->server->get('REQUEST_METHOD','GET'));
    	}
    	return $this->method;
    }
    
    public function getRealMethod(){
    	return strtolower($this->server->get('REQUEST_METHOD','GET'));
    }
    
    public function isMethod($method){
    	
    	return $this->getMethod() === strtoupper($method);
    }
    
    public function isMethodSafe(){
    	return in_array($this->getMethod(), array('GET','HEAD'));
    }
    
    public function getMimeType($format){
    	
    	if(null === static::$formats){
    		static::initializeFormats();
    	}
    	
    	return isset(static::$formats[$format]) ? static::$formats[$format][0] : null;
    }
    public function getFormat($mimeType){
    	
    	if( null === static::$formats){
    		static::initializeFormats();
    	}
    	
    	if( false !== $pos = strpos($mimeType, ';')){
    		$mimeType = substr($mimeType, 0, $pos);
    	}
    	
    	foreach (static::$formats as $format => $mimeTypes){
    		if( in_array($mimeType, $mimeTypes)){
    			return $format;
    		}
    	}
    	
    }
    public function setFormat($format, $mimeType){
    	
    	if( null === static::$formats){
    		static::initializeFormats();
    	}
    	
    	return static::$formats[$format] = is_array($mimeType) ? $mimeType : array($mimeType);
    }
    public function getRequestFormat( $default= 'html'){
    	
    	if( null === $this->format){
    		
    		$this->format = $this->get('_format', $default);
    	}
    	
    	return $this->format;
    }
    
    public function setRequestFormat($format){
    	$this->format = $format;
    }
    
    public function getContentType(){
    	
    	return $this->getformat($this->server->get('CONTENT_TYPE'));
    }
    
	protected static function initializeFormats(){
		
        static::$formats = array(
            'html' => array('text/html', 'application/xhtml+xml'),
            'txt' => array('text/plain'),
            'js' => array('application/javascript', 'application/x-javascript', 'text/javascript'),
            'css' => array('text/css'),
            'json' => array('application/json', 'application/x-json'),
            'xml' => array('text/xml', 'application/xml', 'application/x-xml'),
            'rdf' => array('application/rdf+xml'),
            'atom' => array('application/atom+xml'),
            'rss' => array('application/rss+xml'),
            'form' => array('application/x-www-form-urlencoded'),
        );
    }
    
    public function getContent($resource = false){
    	
    	if( false=== $this->content || (true === $resource && null !== $this->content)){
    		throw new \LogicException('getContent() can only be called once when using the resource return type.');
    	}
    	
    	if($resource) {
    		$this->content = false;
    		
    		return fopen('php://input', 'rb');
    	}
		if( null === $this->content)
    		$this->content = file_get_contents('php://input');
    
    	return $this->content;
    }
    
    public function isSecure(){
    	
    	if (self::$trustedProxies && self::$trustedHeaders[self::HEADER_CLIENT_PROTO] && $proto = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PROTO])) {
            return in_array(strtolower(current(explode(',', $proto))), array('https', 'on', 'ssl', '1'));
        }

        $https = $this->server->get('HTTPS');

        return !empty($https) && 'off' !== strtolower($https);
    }
    public function getScheme(){
    	
    	return $this->isSecure() ? 'https' : 'http';
    }
    public function getPort(){
    	
    	 if (self::$trustedProxies) {
            if (self::$trustedHeaders[self::HEADER_CLIENT_PORT] && $port = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PORT])) {
                return $port;
            }

            if (self::$trustedHeaders[self::HEADER_CLIENT_PROTO] && 'https' === $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PROTO], 'http')) {
                return 443;
            }
        }

        if ($host = $this->headers->get('HOST')) {
            if ($host[0] === '[') {
                $pos = strpos($host, ':', strrpos($host, ']'));
            } else {
                $pos = strrpos($host, ':');
            }

            if (false !== $pos) {
                return intval(substr($host, $pos + 1));
            }

            return 'https' === $this->getScheme() ? 443 : 80;
        }

        return $this->server->get('SERVER_PORT');
    }
    
    public function getClientIps(){
    	$ip = $this->server->get('REMOTE_ADDR');

        if (!self::$trustedProxies) {
            return array($ip);
        }

        if (!self::$trustedHeaders[self::HEADER_CLIENT_IP] || !$this->headers->has(self::$trustedHeaders[self::HEADER_CLIENT_IP])) {
            return array($ip);
        }

        $clientIps = array_map('trim', explode(',', $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_IP])));
        $clientIps[] = $ip; // Complete the IP chain with the IP the request actually came from

        $ip = $clientIps[0]; // Fallback to this when the client IP falls into the range of trusted proxies

        // Eliminate all IPs from the forwarded IP chain which are trusted proxies
        foreach ($clientIps as $key => $clientIp) {
            // Remove port on IPv4 address (unfortunately, it does happen)
            if (preg_match('{((?:\d+\.){3}\d+)\:\d+}', $clientIp, $match)) {
                $clientIps[$key] = $clientIp = $match[1];
            }

            if (IpUtils::checkIp($clientIp, self::$trustedProxies)) {
                unset($clientIps[$key]);
            }
        }

        // Now the IP chain contains only untrusted proxies and the client IP
        return $clientIps ? array_reverse($clientIps) : array($ip);
    }
    public function getClientIp(){
    	 $ipAddresses = $this->getClientIps();

        return $ipAddresses[0];
    }
    /**
     * 
     * 獲取接受的格式
     */
    public function getAcceptableContentTypes(){
    	
    	if(null !== $this->acceptableContentTypes){
    		return $this->acceptableContentTypes;
    	}
    	
    	return $this->acceptableContentTypes = array_keys(AcceptHeader::fromString($this->headers->get('Access'))->all());
    }
}