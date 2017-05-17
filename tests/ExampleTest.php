<?php

class ExempleTest extends TestCase {

	
	public function testEmpty(){
		
		$stack = array();
		$app = $this->createApp();
		$this->assertEmpty($stack);
		array_push($stack, 'age');
		$this->assertEquals(1,count($stack));
//		$this->assertEquals('15', array_pop($stack));

		return $stack;
	}
	/**
	 * 
	 * @depends testEmpty
	 */
	
	public function testPush(array $stack){
//		$stack = array();
		array_push($stack, 'foo');
		$this->assertEquals('foo', $stack[count($stack)-1]);
		$this->assertNotEmpty($stack);
		
		return $stack;
	}
	/**
	 *@dataProvider provider
	 */
	public function testData($a,$b,$sum){
		$this->assertEquals($sum, $a+$b);
	}
	
	public function provider(){
		return array(
			array(1,2,3),
			array(1,5,7)
		);
	}
}