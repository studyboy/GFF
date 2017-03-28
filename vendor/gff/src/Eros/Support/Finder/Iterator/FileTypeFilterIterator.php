<?php namespace Eros\Support\Finder\Iterator;

class FileTypeFilterIterator extends FilterIterator {
	
	const ONLY_FILES = 1;
	const ONLY_DIRECTORES = 2;
	
	private $mode;
	
	public function __construct($iterator, $mode){
		
		$this->mode = $mode;
		
		parent::__construct($iterator);
	}
	
	public function accept(){
		
		$file = $this->current();
		
		if( self::ONLY_DIRECTORES == $this->mode && !$file->isDir() ){
			
			return false;
		}elseif( self::ONLY_FILES == $this->mode && !$file->isFile()){
			
			return false;
		}

		return true;
	}
}