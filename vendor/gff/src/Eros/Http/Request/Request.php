<?php namespace Eros\Http\Request;


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
	
	protected $query;
	protected $request;
	protected $attrs;
	protected $cookies;
	protected $server;
	protected $content;
	protected $files;
	protected $headers;
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
		
		
	
	}
}