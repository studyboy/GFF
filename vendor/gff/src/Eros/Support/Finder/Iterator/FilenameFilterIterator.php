<?php namespace Eros\Support\Finder\Iterator;

use Eros\Support\Finder\Expression\Expression;

class FilenameFilterIterator extends MutiplePcreFilterIterator{
	
	
	public function accept(){
		
		$fileName = $this->current()->getFileName();
		//
		foreach ($this->noMatchRegexps as $noMatchRegex){
			if( preg_match( $noMatchRegex, $fileName)) {
				return false;
			}
		}
		
		//match pattern
		$match = true;
		if($this->matchRegexps){
			$match = false;
			foreach ($this->matchRegexps as $matchRegex){
				if(preg_match($matchRegex, $fileName)){
					return true;
				}
			}
		}
		
		return $match;
	}
	
	public function toRegex($str){
		return Expression::create($str)->getRegex()->render();
	}
	
}