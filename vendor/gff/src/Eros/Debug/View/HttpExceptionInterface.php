<?php namespace Eros\Debug\View;

interface HttpExceptionInterface {
	
	public function getStatusCode();
	
	public function getHeaders();
}