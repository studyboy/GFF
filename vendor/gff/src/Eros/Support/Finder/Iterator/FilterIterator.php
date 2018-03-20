<?php namespace Eros\Support\Finder\Iterator;
/**
 * 
 * +------------------------------------------------
 * Enter description here ...
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/3/24
 * +-------------------------------------------------
 */
abstract class FilterIterator extends \FilterIterator{
	/**
	 * filterIterator 內部Filesystemiterator 在一些情況下rewind后狀態錯誤
	 * @see FilterIterator::rewind()
	 */
	public function rewind(){
		$iterator = $this;
		while ($iterator instanceof \OuterIterator){
		
			$innerIterator = $iterator->getInnerIterator();
			
			if($innerIterator instanceof RecursiveDirectoryIterator){
				if($innerIterator->isRewindable()){
					$innerIterator->next();
					$innerIterator->rewind();
				}
			}elseif($innerIterator instanceof  \FilesystemIterator){
				$innerIterator->next();
				$innerIterator->rewind();
			}
			
			$iterator = $iterator->getInnerIterator();
		}
		parent::rewind();
	}
}