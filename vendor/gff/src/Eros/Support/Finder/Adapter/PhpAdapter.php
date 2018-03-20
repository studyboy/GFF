<?php namespace Eros\Support\Finder\Adapter;

use Eros\Support\Finder\Iterator;

class PhpAdapter extends AbstractAdapter{
	
	
	public function getName(){
		return 'php';
	}
	
	public function canBeused(){
		return true;
	}
	
	public function searchInDirectory($dir){
		
		$flag = \RecursiveDirectoryIterator::SKIP_DOTS;
		
		//遞歸獲取文件和目錄及子目錄內容
		$iterator = new \RecursiveIteratorIterator(
						new Iterator\RecursiveDirectoryIterator($dir, $flag, $this->ignoreUnreadableDirs) ,
					    \RecursiveIteratorIterator::SELF_FIRST);
	
			 
					    
		//過濾模式
		if( $this->mode ){
			$iterator = new Iterator\FileTypeFilterIterator($iterator, $this->mode);
		}
					    
		//過濾掉指定路徑
		if($this->excludes){
			$iterator = new Iterator\ExcludeFilterIterator($iterator, $this->excludes);
		}
							    
	    //過濾文件名稱
		if($this->names || $this->notNames){
			$iterator = new Iterator\FilenameFilterIterator($iterator, $this->names, $this->notNames);
		}

		return $iterator;
	}	
}