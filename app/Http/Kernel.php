<?php namespace App\Http;

use Eros\Contracts\Http\KernelInterface;

class Kernel implements KernelInterface {

	public function handle(){
		die('ok');
	}
}