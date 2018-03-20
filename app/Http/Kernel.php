<?php namespace App\Http;

use Eros\Http\Kernel AS HttpKernel;

class Kernel extends HttpKernel {
	/**
	 * (non-PHPdoc)
	 * @see Eros\Contracts\Http.KernelInterface::handle()
	 */
	
	public function __toString(){
		
		return get_class($this);
	}
}