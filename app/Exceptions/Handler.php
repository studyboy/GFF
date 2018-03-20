<?php namespace App\Exceptions;

use Eros\Foundation\Exceptions\Handler as HandlerException;

class Handler extends HandlerException {
	
	public function report(\Exception $e){
		
		parent::report($e);
	}
}