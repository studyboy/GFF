<?php namespace Eros\Contracts\Foundation\Bootstrap;

use Eros\Contracts\Foundation\ApplicationInterface;

interface BootstrapInterface {
	
	public function bootstrap(ApplicationInterface $app);
}