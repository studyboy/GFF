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
	/**
	 * 
	 * 返回文件內容
	 */
	public function getContents(){
		
		$level = error_reporting(0);
		
		$content = file_get_contents($this->relativePathname);
		
		if( false === $content){
			$error = error_get_last();
			throw new \RuntimeException($error['message']);
		}
		return $content;
	}
}