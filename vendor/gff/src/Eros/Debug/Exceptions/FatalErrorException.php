<?php namespace Eros\Debug\Exceptions;

class FatalErrorException extends \ErrorException{
	
	public function __construct($message, $code, $severity, $filename, $lineno, $traceoffset=NULL, $traceArgs = array()){
		
		parent::__construct($message, $code, $severity, $filename, $lineno);
		
		
		
	}
}