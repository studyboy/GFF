<?php namespace Eros\Debug\View;
/**
 * 為特定的異常，定制專有頁面
 * 
 */
use Eros\Debug\View\HttpExceptionInterface;

class HttpException extends \RuntimeException implements  HttpExceptionInterface{

	protected $statusCode;
	protected $headers;
	
	public function __construct($statusCode, $message = null, \Exception $previous = null  , array $headers = array(), $code = 0){
		
		$this->statusCode = $statusCode;
		$this->headers = $headers;
		
		parent::__construct($message, $code, $previous);
	}
	
	public function getStatusCode(){
		
		return $this->statusCode;
	}
	
	public function getHeaders(){
		
		return $this->headers;
	}

} 