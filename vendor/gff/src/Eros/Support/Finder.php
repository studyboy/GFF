<?php namespace Eros\Support;
/**
 * 
 * +------------------------------------------------
 * build rules to find file or directory.
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/3/23
 * +-------------------------------------------------
 */
use Eros\Support\Finder\Adapter\AdapterInterface;
use Eros\Support\Finder\Adapter\AbstractAdapter;
use Eros\Support\Finder\Adapter\PhpAdapter;
use Eros\Support\Finder\Iterator;
error_reporting(E_ALL);
class Finder implements \IteratorAggregate, \Countable{
	
	private $mode = 0;
	private $names = array();
	private $notNames = array();
	private $dirs = array();
	private $paths = array();
	private $excludes = array();
	private $iterators = array();
	private $adapters  = array();
	private $filters = array();
	private $ignoreUnreadableDirs = false;
	
	public function __construct(){
		
		$this
			->addAdapter(new PhpAdapter(),'-50')
//			->addAdapter(other)
			->setAdapter('php');
	}
	
	public static function create(){
		return new static();
	}
	public function files(){

		$this->mode = Iterator\FileTypeFilterIterator::ONLY_FILES;
		
		return $this;
	}
	
	public function directories(){
		
		$this->mode = Iterator\FileTypeFilterIterator::ONLY_DIRECTORES;
		
		return $this;
	}
	
	public function exclude($dirs){
		
		$this->excludes = array_merge($this->exclude,(array)$dirs);
		
		return $this;
	}
	public function name($name){
		
		$this->names[] = $name;
		
		return $this;
	}
	public function notName($pattern){
		
		$this->notNames[] = $pattern;
		
		return $this;
	}
	/**
	 * 
	 * 提取目錄信息
	 * @param unknown_type $dirs
	 */
	public function in($dirs){
		
		$resolvedDir = array();
		
		foreach ((array)$dirs as $dir){
			
			if(is_dir($dir)){
				$resolvedDir[] = $dir;
			}elseif($glob = glob($dir, GLOB_BRACE | GLOB_ONLYDIR)){
				$resolvedDir = array_merge($resolvedDir, $glob);
			}else{
				throw new \InvalidArgumentException(sprintf("The %s directory is not exist.",$dir));
			}
		}

		$this->dirs = array_merge($this->dirs, $resolvedDir);
		
		return $this;
	}

	public function append($iterator){
		
		if( $iterator instanceof \IteratorAggregate){
			
			$this->iterators[] = $iterator->getIterator();
			
		}elseif($iterator instanceof \Traversable || is_array($iterator)){
			
			$it = new \ArrayIterator();
			foreach ($iterator as $file){
				$it->append($file instanceof \SplFileInfo ? $file : new \SplFileInfo($file));
			}
			
			$this->iterators[] = $it;
			
		}else{
			throw new \InvalidArgumentException("Finder:append() wrong argument.");
		}
		
		return $this;
	}
	public function searchInDirectory($dir){

		foreach ($this->adapters as $adapter){
			if( $adapter['adapter']->isSupported() ){
				try{
					return $this
							->buildAdapter($adapter['adapter'])
							->searchInDirectory($dir);
				}catch (\Exception $e){
					
				}
			}
		}
	}
	/**
	 * 
	 * 添加迭代器并對其排序
	 * @param AdapterInterface $adapter
	 * @param unknown_type $priority
	 */
	public function addAdapter (AdapterInterface $adapter , $priority = 0){
		
		$this->adapters[$adapter->getName()] = array(
			'adapter' => $adapter,
			'priority'=> $priority,
			'selected' => false
		);
		
		return $this->sortAdapter();
	}
	public function setAdapter($name){
		
		if( !isset($this->adapters[$name])) {
			throw new \InvalidArgumentException(sprintf('Adapter %s is not exist.', $name));
		}
		
		$this->resetAdapterSelected();
		
		$this->adapters[$name]['selected'] = true;
		
		return $this->sortAdapter();
	}
	public function buildAdapter (AdapterInterface $adapter){
		
		return $adapter
					->setExcludes($this->excludes)
					->setNames($this->names)
					->setNotNames($this->notNames)
					->setMode($this->mode);
//					->setFilters($this->filters);	
		
	}
	public function resetAdapterSelected(){
		
		return  array_map(function($adapter){
			
					return $adapter['selected'] = false;
					
				}, $this->adapters);
	}
	public function sortAdapter(){
		
		return $this;
	}
	
	/**
	 * 獲取迭代對象
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator(){
		
		if( 0 == count($this->dirs) && 0 == count($this->iterators)){
			throw new \LogicException("You must call one of in() or append() methods before iterating over a Finder.");
		}

		if( 1 == count($this->dirs) && 0 == count($this->iterators)){ 
			return $this->searchInDirectory($this->dirs[0]);
		}
		
		
		$iterator = new \ArrayIterator();
		foreach($this->dirs as $dir){
			$iterator->append($this->searchInDirectory($dir));
		}

		foreach ($this->iterators as $it){
			$iterator->append($it);
		}
		
		return $iterator;
	}
	
	public function count(){
		
		return iterator_count($this->getIterator());
	}
	
}