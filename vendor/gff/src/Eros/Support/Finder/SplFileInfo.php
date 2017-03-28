<?php namespace Eros\Support\Finder;

/**
 * 
 * +------------------------------------------------
 * 擴展SplFileInfo 以便支持相對路徑
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/3/24
 * +-------------------------------------------------
 */
class SplFileInfo extends \SplFileInfo{
	
	private $relativePath;
	private $relativePathname;
	
	public function __construct($file, $relativePath, $relativePathname){
		
		$this->relativePath = $relativePath;
		$this->relativePathname = $relativePathname;
		
		parent::__construct($file);
		
	}
	
	public function getRelativePath(){
		return $this->relativePath;
	}
	
	public function getRelativePathname(){
		return $this->relativePathname;
	}
	
	public function getContents(){
		
	}
}