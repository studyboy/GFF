<?php namespace Eros\Foundation\Exceptions;

use Eros\Contracts\Foundation\ApplicationInterface;

use Exception;
use Eros\Debug\view\HttpException;
use Eros\Debug\View\DisplayExceptionView;
use Eros\Contracts\Debug\HandleExceptionInterface;

class Handler implements HandleExceptionInterface{

	
	protected $dontReport = array();
	protected $app;
	
	public function __construct(ApplicationInterface $app){
		//增加日誌記錄流程
		$this->app = $app;
		
	}
	/**
	 * 
	 * 異常記入日誌
	 * @param Exception $e
	 */
	public function report(Exception $e){
		
		if($this->shoudntReport($e)) return ;
	}
	
	public function shoudntReport(Exception $e){
		
		foreach ($this->dontReport as $type){
			
			if( $e instanceof $type) return true;
		}
		
		return false;
	}
	
	public function shoudReport(Exception $e){
		
		return !$this->shoudntReport($e);
	}

	public function render($request, Exception $e){

		if($this->isHttpException($e)){
			
			return $this->renderHttpException($e);
			
		}else{
			//輸出異常的定制格式的報錯頁面
			return (new DisplayExceptionView(true))->createResponse($e);
		}
	}
	
	public function renderHttpException(HttpException $e){
	
		$statusCode = $e->getStatusCode();
		
		//檢測定制頁面，存在則輸出，不存在這輸出統一錯誤頁面
		if(false){
			
		}else{

			return (new DisplayExceptionView(true))->createResponse($e);
		}
	}
	
	public function isHttpException(Exception $e){
		
		return $e instanceof HttpException;
	}
	
	
} 