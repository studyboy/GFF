<?php namespace Eros\Filesystem\File;
/**
 * >=5.3.6 已增加getExtension()函數
 */
use Eros\Filesystem\File\Exception\FileException;

use Eros\Filesystem\File\Exception\FileNotFoundException;

class File extends \SplFileInfo{
	
	public function __construct($path, $checkPath = true){
		
		parent::__construct($path);
		
		if($checkPath && !$this->isFile()){
			throw new FileNotFoundException($path);
		}
	}
	/**
	 * 
	 * 移動文件
	 * @param unknown_type $dir
	 * @param unknown_type $name
	 * @throws FileException
	 */
	public function move($dir, $name = null){
		
		$target = $this->getTargetFile($dir, $name);
		
		if(false === @rename($this->getPathname(), $target)){
			$error = error_get_last();
			throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
		}
		
		@chmod($this->getPathname(), 0666 & ~umask());
		
		return $target;
	}
	/**
	 * 
	 * 創建
	 * @param unknown_type $dir
	 * @param unknown_type $name
	 * @throws FileException
	 */
	public function getTargetFile($dir, $name = null){
	
		if(!is_dir($dir)){
			if(false === @mkdir($dir, 0777, true)){
				throw new FileException(sprintf("Unable to create directory %s,", $dir));
			}elseif(!is_writeable($dir)){
				throw new FileException(sprintf("Unable to write directory %s,", $dir));
			}
		}
		$target = rtrim($dir,'/\\').DIRECTORY_SEPARATOR.(null === $name ? $this->getBasename() : $this->getName($name));
		
		return new File($target,false); 
	}
	
	public function getMimeType(){
		
	}
	
	public function getName($name){
		
		$oname = str_replace('\\', '/', $name);
		
		$pos = strrpos($name, '/');
		
		$oname = (false === $pos ? $oname : substr($name, $pos+1));
		
		return $oname;
	}
}