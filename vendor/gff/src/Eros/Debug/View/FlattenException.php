<?php namespace Eros\Debug\View;


class FlattenException {
	
	private $statusCode;
	private $message;
	private $code;
	private $file;
	private $line;
	private $previous;
	private $trace;
	private $headers;
	private $traceString;

	
	public static function create($e, $statusCode = null, array $headers = array()){
		
		$ex = new static();
		$ex->setMessage($e->getMessage());
		$ex->setCode($e->getCode());
		
		if($e instanceof  HttpExceptionInterface){
			$statusCode = $e->getStatusCode();
			$headers    = array_merge($headers, $e->getHeaders());
		}
		
		if( null == $statusCode) $statusCode = 500;
		
		
		$ex->setStatusCode($statusCode);
		$ex->setHeaders($headers);
		$ex->setFile($e->getFile());
		$ex->setLine($e->getLine());
		$ex->setTraceFromException($e);
		$ex->setTraceString($e->getTraceAsString());
		
		return $ex;
	}
	
	public function toArray(){
		
	}
	
	public function getStatusCode(){
		
		return $this->statusCode;
	}
	
	public function setStatusCode($statusCode){
	
		$this->statusCode = $statusCode;
	}
	
	public function getCode(){
		
		return $this->code;
	}
	
	public function setCode($code){
	
		$this->code = $code;
	}
	
	public function getFile(){
	
		return $this->file;
	}
	
	public function setFile($file){
	
		$this->file = $file;
	}
	public function getLine(){
		
		return $this->line;
	}
	
	public function setLine($line){
	
		$this->line = $line;
	}
	
	public function getMessage(){
	
		return $this->message;
	}
	
	public function setMessage($message){
	
		$this->message = $message;
	}
	
	public function getHeaders(){
		
		return $this->headers;
	}
	
	public function setHeaders($headers){
	
		$this->headers = $headers;
	}
	
	public function getPrevious(){
		
		return $this->previous;
	}
	
	public function setPrevious($previous){
		
		$this->previous = $previous;
	}
	
	public function getTrace(){
		
		return $this->trace;
	}
	
	public function getTraceAsString(){
		
		return $this->traceString;
	}
	
	public function setTraceString($traceString){
		
		$this->traceString = $traceString;
	}
	
	public function setTraceFromException(\Exception $e){
		
		$this->setTrace($e->getTrace(), $e->getFile(), $e->getLine());
	}
	
	public function setTrace($trace, $file, $line){
		
		
	}
}