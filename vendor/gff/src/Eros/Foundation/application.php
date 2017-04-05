<?php namespace Eros\Foundation;

use Eros\Contracts\Foundation\ApplicationInterface;
use Eros\Container\Container;

class Application extends Container implements ApplicationInterface{
	/**
	 * (non-PHPdoc)
	 * @see Eros\Contracts\Foundation.ApplicationInterface::version()
	 */
	public function version(){
		return '';
	}
	
	
}