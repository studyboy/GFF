<?php namespace Eros\Support\Finder\Iterator;

/**
 * 
 * +------------------------------------------------
 * 指定文件路徑信息獲取
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/3/24
 * +-------------------------------------------------
 */
class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator{
	
	public function __construct($path, $flag, $ignoreUreadable = false){

		parent::__construct($path, $flag);
		
	}
}