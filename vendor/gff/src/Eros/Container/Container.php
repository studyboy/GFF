<?php namespace Eros\Container;
/**
 * 
 * +------------------------------------------------
 * 資源管理器
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/3/29
 * +-------------------------------------------------
 */
use Eros\Contracts\Container\ContainerInterface;
use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;


class Container implements \ArrayAccess, ContainerInterface {
	
	protected static $instance;
	
	/**
	 * 
	 * 用於存儲共享單例對象
	 * @var unknown_type
	 */
	protected $instances = array();
	/**
	 * 
	 * 存儲實例化對象
	 * @var unknown_type
	 */
	protected $bindings = array();
	/**
	 * 
	 * 存儲別名信息
	 * @var unknown_type
	 */
	protected $aliases = array();
	
	protected $resolved = array();
	/**
	 * 
	 * 存儲標籤信息
	 * @var unknown_type
	 */
	protected $tags  = array();
	/**
	 * 
	 * 存儲回調函數
	 * @var unknown_type
	 */
	protected $resolvingCallbacks = array();
	/**
	 * 
	 * 上下文實例存儲器
	 * @var unknown_type
	 */
	protected $contextual = array();
	
	protected $buildStack = array();
	
	/**
	 * 聲明綁定接口實現實例
	 * @see Eros\Contract\Container.ContainerInterface::bind()
	 */
	public function bind($abstract, $concret = null, $shared = false){
		
	}
	/**
	 * 獲取對象實例，從存儲數組找到返回，否則直接創建
	 * @see Eros\Contract\Container.ContainerInterface::make()
	 */
	public function make($abstract, $parameters = array()){
		
		//先從別名中獲取信息
		$abstract = $this->getAliase($abstract);
		
		//獲取單例對象，以免重複實例化
		if( isset($this->instances[$abstract]) ){
			return $this->instances[$abstract];
		}
		//獲取接口實現對象或函數
		$concret = $this->getConcret($abstract);
		
		if( $this->isBuildable($concret, $abstract) ){
			
			$object = $this->build($concret, $parameters);
			
		}else{
			
			$object = $this->make($concret, $parameters);
		}
		
		//如果該實例為單例，直接調用，不再創建新實例
		if( $this->isShared($abstract)){
			
			$this->instances[$abstract] = $object;			
		
		}
		
		$this->fireResolvingCallbacks($abstract, $object);
		
		$this->resolved[$abstract] = true;
		
		return $object;
		
	}
	/**
	 * 
	 * 實例化對象,并將其保存進容器內
	 */
	public function build($concret, $parameters = array() ){
		
		if($concret instanceof Closure){
			
			return $concret($this,$parameters);
		}
		
		$reflect = new \ReflectionClass($concret);
		
		if( !$reflect->isInstantiable()){

			throw new BindingResolutionException("Target [$concret] is not instantiable.");
		}
		
		$constructor = $reflect->getConstructor();
		
		if( is_null($constructor) ){
			
			return new $concret;
		}
		
		$dependences = $constructor->getParameters();
		
		//解析類的構造函數的參數，當構造函數有參數，我們將索引轉為關聯數組
		$parameters = $this->keyParametersByArgument($dependences, $parameters);
		
		$instance = $this->getDependences($dependences, $parameters);
		
		return $reflect->newInstanceArgs($instance);
	}
	/**
	 * 
	 * 將對象參數由索引數組轉化為關聯數組類型
	 * @param unknown_type $dependences
	 * @param unknown_type $parameters
	 */
	protected function keyParametersByArgument(array $dependences, array $parameters){
		
		foreach($parameters as $key=>$val){
			
			if( is_numeric($key)){
				
				unset($parameters[$key]);
				
				$parameters[$dependences[$key]] = $val;
			}
		}
		
		return $parameters;
	}
	/**
	 * 
	 * 匹配參數值，沒有設置取默認值或者實例化”強制對象類型“
	 * @param unknown_type $parameters
	 */
	protected function getDependences($parameters, $primitives = [] ){
		
		$dependencies = array();
		
		foreach ($parameters as $k=>$parameter){
			
			$dependency = $parameter->getClass();
			
			if( array_key_exists($parameter->name, $primitives)){
				
				$dependencies[] = $primitives[$parameter->name];
				
			}elseif( is_null($dependencies)){
				
				$dependencies[] = $this->resolveNonClass($parameter);
				
			}else{
				
				$dependencies[] = $this->resolveClass($parameter);
				
			}
		}
		
		return $dependencies;
	}
	/**
	 * 
	 * 解析非強制參數的默認值
	 * @param unknown_type $parameter
	 */
	protected function resolveNonClass(ReflectionParameter $parameter){
		
		if( $parameter->isDefaultValueAvailable()){
			return $parameter->getDefaultValue();
		}
		
		$message = "Unresolvable parameter {$parameter} in class {$parameter->getDeclaringClass()->name}";
		
		throw new BindingResolutionException($message);
		
	}
	/**
	 * 
	 * 解析參數有強制對象類型
	 * @param unknown_type $parameter
	 */
	protected function resolveClass(ReflectionParameter $parameter){
		try{
			
			return $this->make($parameter->getClass()->name);
			
		}catch (BindingResolutionException $e){
			
			//當類無法被實例化對象的時候，則取它的默認值，否則跑出異常
			if($parameter->isOptional()){
				
				return $parameter->getDefaultValue();
			}
			
			throw $e;
		}
	}
	/**
	 * 
	 * 獲取別名配置
	 * @param unknown_type $abstract
	 */
	protected function getAliase($abstract){
		
		return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
	}
	/**
	 * 
	 * 通過接口，獲取具體的實現
	 * @param unknown_type $abstract
	 */
	protected function getClosure($abstract, $concret){
		
		return function($c, $parameters = []) use($abstract, $concret){
			
			$method = $abstract === $concret ? 'build' : 'make';
			
			$c->$method($concret, $parameters);
		};
	}
	
	protected function getConcrete($abstract){
		
		if( !is_null($concrete = $this->getContextualConcrete($abstract))){
			return $concrete;
		}
		
		//如果該類型沒有指定的解析器和實例，者默認給予指定一個，以便能夠解析
		if ( ! isset($this->bindings[$abstract])){
			
			if ($this->missingLeadingSlash($abstract) &&
				isset($this->bindings['\\'.$abstract])){
				
				$abstract = '\\'.$abstract;
			}

			return $abstract;
		}
		
		return $this->bindings[$abstract]['concrete'];
	}
	/**
	 * 
	 * 獲取上下文實例
	 * @param unknown_type $abstract
	 */
	protected function getContextualConcrete($abstract){
		
		if( isset($this->contextual[end($this->buildStack)][$abstract] ){
			$this->contextual[end($this->buildStack)][$abstract]
		}
		
	}
	protected function missingLeadingSlash($abstract){
		
		return is_string($abstract) && strpos($abstract, '\\') !== 0;
	}
	
	protected function isBuildable($concret, $abstract){
		
		return $concret === $abstract || $concret instanceof Closure;
	}
	
	protected function isShared($abstract){
		
		$shared = isset($this->bindings[$abstract]['shared']) ? $this->bindings[$abstract]['shared'] : false;
		
		return isset($this->instances[$abstract]) || $shared === true;
	}

	protected function fireResolvingCallbacks($abstract, $object){
		
	}
	/**
	 * (non-PHPdoc)
	 * @see Eros\Contract\Container.ContainerInterface::bound()
	 */
	public function resovable($abstract){
		return 
	}
	
	public function dropStaleInstance($abstract){
		unset($this->bindings[$abstract], $this->aliases[$abstract]);
	}
	
	public function forgetInstance($abstract){
		unset($this->bindings[$abstract]);
	}
	
	
	public function flush(){
		
		$this->bindings = [];
		$this->aliases  = [];
		$this->instances = [];
		$this->resolved =[];
	}
	public function getBindings(){
		return $this->bindings;
	}
	public static function getInstance(){
		return static::$instance;
	}
	
	public static function setInstantce(ContainerInterface $container){
		
		static::$instance = $container;
	}
	
	public function offsetExists($offset){
		
		return isset($this->bindings[$offset]);
	}
	public function offsetGet($offset){
		
		return $this->make($offset);
	}
	public function offsetSet($offset, $value){
		
		if( ! $value instanceof Closure){
			
			$value = function()use($value){
				return $value;
			};
		}
		
		$this->bind($offset,$value);
	}
	public function __get($key){
		
		return $this[$key];
	}
	
	public function __set($key, $value){
		
		$this[$key] = $value;
	}
	
	
}