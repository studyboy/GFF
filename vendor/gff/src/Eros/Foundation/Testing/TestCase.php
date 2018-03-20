<?php namespace Eros\Foundation\Testing;

use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase{
	
	abstract public function createApp();
}