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

//error_reporting(E_ALL);

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
	
	protected $reboundCallbacks = array();
	
	protected $globalResovingCallbacks = array();
	
	protected $globalAfterResovingCallbacks = array();
	
	protected $afterResovingCallbacks = array();
	
	public function call($callback, array $paramters = array() , $defaultMethod = null ){
		
	}
	
	public function isAlias($abstract){
		
		return isset($this->aliases[$abstract]);
	}
	
	public function alias($abstract, $alias){
		
		return $this->aliases[$alias] = $abstract;
	}
	public function resolved($abstract) {
		
		return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
	}
	public function resolving($abstract, Closure $callback = null){
	}
	public function afterResolving($abstract, Closure $callback = null){
	}
	/**
	 * 聲明綁定接口實現實例
	 * @see Eros\Contract\Container.ContainerInterface::bind()
	 */
	public function bind($abstract, $concrete = null, $shared = false){
		
		//$abstract 指定為數組，則將其註冊為有別名類，以便能夠簡單調用
		if( is_array($abstract) ){
			list($abstract, $alias) = $this->extractAlias($abstract);
			$this->alias($abstract, $alias);
		}
		//如果沒有實現類，就默認實現類為抽象類，并刪除舊的綁定類映射
		$this->dropStaleInstance($abstract);
		
		$concrete = is_null($concrete) ? $abstract : $concrete;

		//如果實現類不是匿名函數，將會轉為匿名函數，以便於擴展
		if( ! $concrete instanceof Closure){
			
			$concrete = $this->getClosure($abstract, $concrete);
		}

		$this->bindings[$abstract] = compact('concrete', 'shared');;

		//如果該類已經解析了，將調用rebound監聽，以便已經解析的對象能夠獲取通過監聽callbacks更新對象的拷貝
		if( $this->resolved[$abstract]){
			
			$this->rebound($abstract);
		}

	}
	
	public function instance($abstract, $instance){
		
		//如果是數組，則指定了別名，將為其綁定入別名
		if( is_array($abstract)){
			
			list($abstract, $alias) = $this->extractAlias($abstract);
			
			$this->alias($abstract, $alias);
		}
		
		unset($this->aliases[$abstract]);
		
		//檢測是否之前已經有綁定對象，如果有綁定則調用rebound調用容器的註冊
		$bound = $this->resolvable($abstract);
		
		$this->instances[$abstract] = $instance;
		
		//如果是一個已經綁定的抽象類，將通過rebound調用容器內的回調註冊類，并採用在此實例的對象更新綁定實例
		if( $bound){
			$this->rebound($abstract);
		}
	}
	/**
	 * 聲明單例
	 * @see Eros\Contracts\Container.ContainerInterface::singleton()
	 */
	public function singleton($abstract, $concrete = null){
		
		$this->bind($abstract, $concrete, true);
	}

	public function extractAlias(array $definition){
		
		return array(key($definition), current($definition));
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
		$concrete = $this->getConcrete($abstract);

		if( $this->isBuildable($concrete, $abstract) ){

			$object = $this->build($concrete, $parameters);
			
		}else{
			
			$object = $this->make($concrete, $parameters);
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
	public function build($concrete, $parameters = array() ){

		if($concrete instanceof Closure){
			
			return $concrete($this,$parameters);
		}

		$reflect = new \ReflectionClass($concrete);

		if( !$reflect->isInstantiable()){

			throw new BindingResolutionException("Target [$concrete] is not instantiable.");
		}
		
		$this->buildStack[] = $concrete;
		
		$constructor = $reflect->getConstructor();
		
		if( is_null($constructor) ){

			array_pop($this->buildStack);
			
			return new $concrete();
		}
		
		$dependencies = $constructor->getParameters();
		
		//解析類的構造函數的參數，當構造函數有參數，我們將索引轉為關聯數組
		$parameters = $this->keyParametersByArgument(
			$dependencies, $parameters
		);

		$instance = $this->getDependences($dependencies, $parameters);
		
	 
		array_pop($this->buildStack);
		

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
	protected function getDependences($parameters, $primitives = array() ){
		
		$dependencies = array();
		
		foreach ($parameters as $k=>$parameter){
			
			$dependency = $parameter->getClass();
			
			if( array_key_exists($parameter->name, $primitives)){
				
				$dependencies[] = $primitives[$parameter->name];
				
			}elseif( is_null($dependency)){
				
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
//			echo $parameter->getClass()->name.">>>";
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
	protected function getClosure($abstract, $concrete){
		
		return function($c, $parameters = array()) use($abstract, $concrete){
			
			$method = $abstract == $concrete ? 'build' : 'make';
			
			return $c->$method($concrete, $parameters);
		};
	}
	/**
	 * 
	 * 獲取映射的實現
	 * @param unknown_type $abstract
	 */
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
		
		if( isset($this->contextual[end($this->buildStack)][$abstract]) ){
			
			$this->contextual[end($this->buildStack)][$abstract];
		}
		
	}
	protected function missingLeadingSlash($abstract){
		
		return is_string($abstract) && strpos($abstract, '\\') !== 0;
	}
	
	protected function isBuildable($concrete, $abstract){
		
		return $concrete === $abstract || $concrete instanceof Closure;
	}
	
	protected function isShared($abstract){
		
		$shared = isset($this->bindings[$abstract]['shared']) ? $this->bindings[$abstract]['shared'] : false;
		
		return isset($this->instances[$abstract]) || $shared === true;
	}

	protected function fireResolvingCallbacks($abstract, $object){
		
		$this->fireCallbackArray($object, $this->globalResovingCallbacks);
		
		$this->fireCallbackArray(
			$object, $this->getCallbacksForType(
				$abstract, $object, $this->resolvingCallbacks 
			)
		);
		
	}
	//調用回調函數數組
	protected function fireCallbackArray($object, array $callbacks){
		
		foreach ($callbacks as $callback){
			
			$callback($object, $this);
		}
	}
	protected function getCallbacksForType($abstract, $object, array $callbacksPerType){
		
		$results = array();
		
		foreach ($callbacksPerType as $type => $callbacks){
			
			if( $type === $abstract || $object instanceof $type){
				
				$results = array_merge($results, $callbacks);
			}
		}
		return $results;
	}

	protected function rebound($abstract){
		
		$instance = $this->make($abstract);
		
		foreach ($this->getReboundCallbacks($abstract) as $callback){
			
			call_user_func($callback, $this, $instance);
		}
	}
	
	protected function getReboundCallbacks($abstract){
		
		if( isset($this->reboundCallbacks[$abstract])){
			
			return $this->reboundCallbacks[$abstract];
		}
		
		return array();
	}
	//從新綁定新的實例到$abstract的綁定事件中
	protected function rebindings($abstract, Closure $callback){
		
		$this->reboundCallbacks[$abstract][] = $callback;
		
		if( $this->resolvable($abstract)) return $this->make($abstract);
	}
	/**
	 * 
	 * 從新綁定
	 * @param unknown_type $abstract
	 * @param unknown_type $target
	 * @param unknown_type $method
	 */
	protected function refresh($abstract, $target, $method){
		
		return $this->rebindings($abstract, function ($app, $instance) use ($target, $method){
			return $target->{$method}($instance);
		});
	}
	/**
	 * (non-PHPdoc)
	 * @see Eros\Contracts\Container.ContainerInterface::resovable()
	 */
	public function resolvable($abstract){
		return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || $this->isAlias($abstract);
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
	public function offsetUnset($offset){
		
		unset($this->bindings[$offset], $this->instances[$offset], $this->resolved[$offset]);
	}
	public function __get($key){
		
		return $this[$key];
	}
	
	public function __set($key, $value){
		
		$this[$key] = $value;
	}
	
	
}