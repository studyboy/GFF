<?php namespace Eros\Foundation\Bootstrap;

use Eros\Debug\Exceptions\FatalErrorException;
use Eros\Contracts\Foundation\ApplicationInterface;
use ErrorException;

class HandleException {
	
	protected $app;
	
	public function bootstrap(ApplicationInterface $app){
		
		$this->app = $app;
		
		error_reporting(-1);
		
		set_error_handler(array($this, 'errorHandler'));
		
		set_exception_handler(array($this, 'exceptionHandler'));
		
		register_shutdown_function(array($this, 'shutdownHandler'));
		
		//關閉錯誤
		if( !$this->app['config']['app.debug'] ){
			
			ini_set('display_error', 'off');	
		}
	}
	
	public function errorHandler($level, $message, $file, $line, $context = array()){
		
		if(error_reporting() & $level){
			
			throw new ErrorException($message, 0, $level, $file, $line);
		}
	}
	
	public function isFatal($type){
		
		return in_array($type, array(E_ERROR, E_COMPILE_ERROR, E_CORE_ERROR, E_PARSE));
	}
	
	public function exceptionHandler(\Exception $e){
		
		$this->getExceptionHandler()->report($e);
		
		//向http發送對應狀態碼
		$this->renderHttpResponse($e);
	}
	
	public function shutdownHandler(){
		
		if( !is_null($error = error_get_last()) && $this->isFatal($error['type'])){
			
			$this->exceptionHandler($this->fatalExceptionFromError($error, 0));
		}
		
	}
	/**
	 * 
	 * 異常處理
	 * @param unknown_type $error
	 * @param unknown_type $traceoffset
	 * @throws FatalErrorException
	 */
	protected function fatalExceptionFromError($error, $traceoffset = 0){
		
		throw new FatalErrorException(
			$error['message'], 0, $error['type'], $error['file'], $error['line'], $traceoffset
		);
	}
	
	protected function renderHttpResponse($e){
		
		return $this->getExceptionHandler()->render($this->app['request'], $e);
	}
	
	protected function getExceptionHandler(){
		
		return $this->app->make('Eros\Contracts\Debug\HandleExceptionInterface');
	}
	
	
}