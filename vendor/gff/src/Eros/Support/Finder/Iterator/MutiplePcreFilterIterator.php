<?php namespace Eros\Support\Finder\Iterator;

use Eros\Support\Finder\Expression\Expression;

abstract class MutiplePcreFilterIterator extends FilterIterator {

	protected  $matchRegexps = array();
	protected  $noMatchRegexps = array();
	
	public function __construct($iterator,array $matchPatterns, array $noMatchPatterns){
		
		foreach ( $matchPatterns as $matchPattern){
			$this->matchRegexps[] = $this->toRegex($matchPattern);
		}
		
		foreach ( $noMatchPatterns as $noMatchPattern){
			$this->noMatchRegexps[] = $this->toRegex($noMatchPattern);
		}
		
		parent::__construct($iterator);
	}
	
	public function isRegex($str){
		return Expression::create($str)->isRegex();
	}
	
	public abstract function toRegex($str);
} 