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

use Eros\Support\Finder\Exception\AcceptDeniedException;

use Eros\Support\Finder\SplFileInfo ;

class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator{
	
	private $ignoreUreadable;
	
	private $rewindable;
	
	public function __construct($path, $flag, $ignoreUreadable = false){
		
		if( $flag & (self::CURRENT_AS_PATHNAME | self::CURRENT_AS_SELF)){
			  throw new \RuntimeException('This iterator only support returning current as fileinfo.');
		}
	
		parent::__construct($path, $flag);
		
		$this->ignoreUreadable = $ignoreUreadable;
	}
	
	public function current(){
		
		return new SplFileInfo( parent::current()->getPathname(),$this->getSubPath(), $this->getSubPathname());
		
	}
	
	public function getChildren(){
		try{
			$children = parent::getChildren();
			
			if( $children instanceof self){
			   $children->ignoreUreadable = $this->ignoreUreadable;
			}
			
			return $children;
			
		}catch (\UnexpectedValueException $e){
			if( $this->ignoreUreadable ){
				return new \RecursiveArrayIterator(array());
			}else{
				throw new AcceptDeniedException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}
	
	public function rewind(){
		
		if( false === $this->isRewindable()) return ;

		parent::next();
		
		parent::rewind();
	}
	public function isRewindable(){
		
		if( null !== $this->rewindable){
			return $this->rewindable;
		}
		
		if( false === $stream = @opendir($this->getPath())){
			
			$i = stream_get_meta_data($stream);
			closedir($stream);
			
			if($i['seekable']) return $this->rewindable = true;
		}
		
		return $this->rewindable = false;
	}
}