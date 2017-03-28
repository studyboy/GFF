<?php namespace Eros\Support\Finder\Iterator;
/**
 * 
 * +------------------------------------------------
 * 目錄排除
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/3/27
 * +-------------------------------------------------
 */
class ExcludeFilterIterator extends FilterIterator{
	
	private $patterns = array();
	
	public function __construct($iterator, array $dirs ){

		foreach ($dirs as $dir){
			$this->patterns[] = '#^(|/)'.preg_quote($dir,'#').'(/|$)#';
		}
		
		parent::__construct($iterator);
	}
	
	public function accept(){
		
		$dir = $this->isDir() ? $this->current()->getRelativePathname() : $this->current()->getRelativePath();
		
		foreach ($this->patterns as $pattern){
			if(preg_match($pattern, $dir)){
				return false;
			}
			return true;
		}
		
	}
}