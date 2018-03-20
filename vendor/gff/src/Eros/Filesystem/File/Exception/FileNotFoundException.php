<?php namespace Eros\Filesystem\File\Exception;

class FileNotFoundException extends FileException{

	public function __construct($path){
		parent::__construct(sprintf('The file "%s" is not found.', $path));
	}

}