<?php

class TestCase extends Eros\Foundation\Testing\TestCase{

	public function createApp(){
		
		$app = require __DIR__."/../bootstrap/app.php";
		
		return $app;
		
	}
	
//	public function setUp(){
//		echo "Test start :\r\n";
//	}
}