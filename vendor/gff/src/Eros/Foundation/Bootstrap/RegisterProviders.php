<?php namespace Eros\Foundation\Bootstrap;

use Eros\Contracts\Foundation\ApplicationInterface;

class RegisterProviders {
	/**
	 * 
	 * 內容引導程序
	 * @param ApplicationInterface $app
	 */
	public function bootstrap(ApplicationInterface $app){
		
		$app->registerConfiguredProviders();
	}
}