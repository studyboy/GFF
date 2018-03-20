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
	private $class;
	
	private $exception;

	
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
		$ex->setClass(get_class($e));
		
		if($e->getPrevious()){
			$ex->setPrevious(static::create($e->getPrevious()));
		}
		
		$ex->exception = $e;
		
		return $ex;
	}
	
	public function toArray(){
		$exceptions = array();
        foreach (array_merge(array($this), $this->getAllPrevious()) as $exception) {
            $exceptions[] = array(
                'message' => $exception->getMessage(),
                'class' => $exception->getClass(),
                'trace' => $exception->getTrace(),
            );
        }

        return $exceptions;
	}
	
	public function __toString(){
		
		return $this->exception->__toString();
	}
	
	public function getAllPrevious(){
		
		$exs = array();
		$ex = $this;
		while ($e = $ex->getPrevious()){
			
			$exs[] = $e;
		}
		
		return $exs;
	}
	
	public function getClass(){
		
		return $this->class;
	}
	
	public function setClass($class){
		
		return $this->class = $class;
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
		
		$traces[] = array(
			'namespace' => '',
			'className' => '',
			'function'  => '',
			'type'      => '',
			'file'		=> $file,
			'line'		=> $line,
			'args'		=> array()
		);
		
		foreach ($trace as $t){
			
			$namespace = '';
			$class = '';
			
			if($class = $t['class']){
				$ps = explode('\\', $class);
				$className = array_pop($ps);
				$namespace = str_replace('\\'.$className, '', $class);
			}

			$traces[] = array(
				'namespace' => $namespace,
				'className' => $className,
				'function'  => $t['function'] ?: null,
				'type'      => $t['type'] ?: '',
				'line'		=> $t['line'] ?: null,
				'file'		=> $t['file'] ?: null,
				'args'		=> isset($t['args']) ? $this->flattenArgs($t['args']) : array()
			);
			
		}

		$this->trace = $traces;
		
	}
	public function flattenArgs($args, $level = 0, &$count = 0 ){
		
		$result = array();

		foreach ($args as $key=>$arg){
			
			if(++$count > 1e4){
				return array('array', '*SKIPPED over 10000 entries*');
			}
			
			if(is_object($arg)){
				$result[$key] = array('object' => get_class($arg));
			}elseif(is_array($arg)){
				if( $level > 5){
					$result[$key] = array('array', '*DEEP NESTED ARRAY*');
				}else{
					$result[$key] = array('array',$this->flattenArgs($arg, $level+1, $count));
				}
			}elseif (null === $arg) {
                $result[$key] = array('null', null);
            } elseif (is_bool($arg)) {
                $result[$key] = array('boolean', $arg);
            } elseif (is_resource($arg)) {
                $result[$key] = array('resource', get_resource_type($arg));
            } elseif ($arg instanceof \__PHP_Incomplete_Class) {
                // Special case of object, is_object will return false
                $result[$key] = array('incomplete-object', $this->getClassNameFromIncomplete($arg));
            } else {
                $result[$key] = array('string', (string) $arg);
            }
		}
	}
	
	public function getClassNameFromIncomplete($arg){
		
		$a = new \ArrayObject($arg);
		
		return $a['__PHP_Incomplete_Class_Name'];
	}
}