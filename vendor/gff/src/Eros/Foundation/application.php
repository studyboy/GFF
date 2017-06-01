<?php namespace Eros\Foundation;


use Eros\Contracts\Foundation\ApplicationInterface;
use Eros\Container\Container;
use Eros\Support\ServiceProvider;

class Application extends Container implements ApplicationInterface{
	
	const VERSION = '1.0.0';
	
	protected $basePath;
	
	protected $serviceProviders = array();
	
	protected $loadedProviders = array();
	
	protected $deferredServices = array();
	
	protected $storagePath;
	
	protected $booted = false;
	
	protected $bootingCallbacks = array();
	
	protected $bootedCallbacks = array();
	
	protected $hasBeenBootstraped = false;
	
	/**
	 * (non-PHPdoc)
	 * @see Eros\Contracts\Foundation.ApplicationInterface::version()
	 */
	public function version(){
		
		return static::VERSION;
	}
	
	public function __construct($basePath = null){
		
		$this->registerBaseBindings();
		
		$this->registerBaseServiceProviders();
		
		$this->registerCoreContainerAliases();
		
		if($basePath) $this->setBasePath($basePath);
	}
	
	public function registerBaseBindings(){

		static::setInstantce($this);
		
		$this->instance('app', $this);
		
		$this->instance('Eros\Container\Container', $this);
	}
	public function registerBaseServiceProviders(){
		
	}

	
	public function setBasePath($path){
		
		$this->basePath = $path;
		
		return $this;
	}
	
	public function bindPathsInContainer(){
		
		$this->instance('path', $this->getBasePath());
		
		$paths = array('app', 'config', 'database', 'lang', 'public', 'storage');
		
		foreach ($paths as $path){
			$this->instance('path.'.$path, $this->{'get'.ucfirst($path).'Path'}());
		}
		
	}
	public function getBasePath(){
		
		return $this->basePath;
	}
	public function getAppPath(){
		
		return $this->getBasePath().DIRECTORY_SEPARATOR.'app';
	}
	
	public function getConfigPath(){
	
		return $this->getBasePath().DIRECTORY_SEPARATOR.'config';
	}
	
	public function getDatabasePath(){
		
		return $this->getBasePath().DIRECTORY_SEPARATOR.'database';
	}
	
	public function getLangPath(){
	
		return $this->getBasePath().DIRECTORY_SEPARATOR.'lang';
	}
	
	public function getPublicPath(){
	
		return $this->getBasePath().DIRECTORY_SEPARATOR.'public';
	}
	
	public function getStoragePath(){
		
		return $this->storagePath ?: $this->getBasePath().DIRECTORY_SEPARATOR.'storage';
	}
	
	public function setStoragePath($path){
		
		$this->storagePath = $path;
		
		$this->instances('path.storage', $this->storagePath);
		
		return $this;
	}
	
	/**
	 * 註冊提供者
	 * @see Eros\Contracts\Foundation.ApplicationInterface::register()
	 */
	public function register($provider, $options = array(), $force = false){
		
		if( $registered = $this->getProvider($provider) && !$force ){
			
			return $registered;
		}
		
		if(is_string($provider)){
			
			$provider = $this->resolveProviderClass();
		}
		
		$provider->register();
		
		foreach ($options as $key=>$option){
			
			$this[$key] = $option;
		}
		
		if( $this->booted){
			
			$this->bootPorvider();
		}
	}
	
	public function getProvider($provider){
		
		$name = is_string($provider) ? $provider : get_class($provider);
		
		$myproviders = array_walk($this->serviceProviders, function($value,$key)use($name){
			return $value instanceof $name;
		});
		
		return array_shift($myproviders);
	}
	
	
	public function makeAsProvider($provider){
		
		$this['events']->fire($class = get_class($provider), array($provider));
		
		$this->serviceProviders[] = $provider;
		
		$this->loadedProviders[$class] = true;
		
	}
	
	public function registerConfiguredProviders(){
		
		
	}

	public function loadDeferredProvider($service){
		
		if( !isset($this->deferredServices[$service])) return;
		
		$provider = $this->deferredServices[$service];
		
		if( !isset($this->loadedProviders[$provider]) ){
			
			$this->registerDeferredProvider($provider, $service);
		}
		
	}
	
	public function loadDeferredProviders(){
		
		foreach ($this->deferredServices as $service=>$provider){
			
			$this->loadDeferredProvider($service);
		}
		
		$this->deferredServices = array();
	}
	
	
	public function registerDeferredProvider($provider, $service = NULL){
		
		//延遲綁定中存在，則先清除
		if ($service) unset($this->deferredServices[$service]);

		$this->register($instance = new $provider($this));

		if ( ! $this->booted){
			
			$this->booting(function() use ($instance){
				
				$this->bootProvider($instance);
			});
		}
	}
	
	public function resolveProviderClass($provider){
		
		return new $provider($this);
	}

	public function bootProvider(ServiceProvider $provider){
		
		if( method_exists($provider, 'boot')){
			
			$this->call( array($provider, 'boot'));
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see Eros\Container.Container::make($abstract, $parameters)
	 */
	public function make($abstract, $parameters = array()){

		$abstract = $this->getAliase($abstract);
//		echo $abstract."::".$abstract."333<br/>";
//		if( isset($this->deferredServices[$abstract])){
//			
//			$this->loadDeferredProvider($abstract);
//		}

		return parent::make($abstract, $parameters);;
	}
	
	public function boot(){
		
		if( $this->booted) return ;
		
		$this->fireAppCallbacks($this->bootingCallbacks);
		
		array_walk($this->serviceProviders, function($p) {
			$this->bootProvider($p);
		});

		$this->booted = true;

		$this->fireAppCallbacks($this->bootedCallbacks);
		
	}
	public function booted($callback){
		
		$this->bootedCallbacks[] = $callback;
		
		if($this->booted) $this->fireAppCallbacks($callback);
	}

	
    public function booting($callback){
    	
		$this->bootingCallbacks[] = $callback;
		
	}
	
	public function isBooted(){
		return $this->booted;
	}
	
	public function handle(){
		
	}
	/**
	 * 
	 * 將配置等引導代碼引入app,
	 * @param array $bootstrapers
	 */
	public function bootstrapWith(array $bootstrapers){
		
		foreach ($bootstrapers as $bootstraper){
			
			$this->make($bootstraper)->bootstrap($this);
		}
		
		$this->hasBeenBootstraped = true;
	}
	
	protected function fireAppCallbacks(array $callbacks){
		
		foreach ($callbacks as $callback){
			
			call_user_func($callback, $this);
		}
	}
	
	public function registerCoreContainerAliases(){
		
		$aliases = array(
			'app'                  => ['Eros\Foundation\Application', 'Eros\Contracts\Container\ContainerInterface', 'Eros\Contracts\Foundation\ApplicationInterface'],
		    'config'			   => ['Eros\Config\Repository', 'Eros\Contracts\Config\RepositoryInterface'],
//			'events'               => ['Illuminate\Events\Dispatcher', 'Illuminate\Contracts\Events\Dispatcher'],
//			'log'                  => ['Illuminate\Log\Writer', 'Illuminate\Contracts\Logging\Log', 'Psr\Log\LoggerInterface'],
//			'request'              => 'Illuminate\Http\Request',
//			'router'               => ['Illuminate\Routing\Router', 'Illuminate\Contracts\Routing\Registrar'],
//			'url'                  => ['Illuminate\Routing\UrlGenerator', 'Illuminate\Contracts\Routing\UrlGenerator'],
//			'view'                 => ['Illuminate\View\Factory', 'Illuminate\Contracts\View\Factory'],
		);
		
		foreach ($aliases as $key=>$aliases){
			
			foreach ((array)$aliases as $alias){
				
				$this->alias($key, $alias);
			}
		}

	}
	
	public function flush(){
		
		parent::flush();
		
		$this->loadedProviders = [];
	}
	
	public function getHasBeenBootstraped(){
		return $this->hasBeenBootstraped;
	}
}