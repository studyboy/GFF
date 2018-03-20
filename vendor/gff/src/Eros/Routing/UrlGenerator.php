<?php namespace Eros\Routing;

use Eros\Contracts\Routing\UrlRoutableInterface;

use phpDocumentor\Reflection\Types\Callable_;

use Eros\Http\Request;

use Eros\Contracts\Routing\UrlGeneratorInterface;

class UrlGenerator implements UrlGeneratorInterface {
	
	protected $routes;
	
	protected $request;
	
	protected $sessionResolver;
	
	protected $forceScheme;
	
	public function __construct(RouteCollection $route, Request $request){
		
	    //路由信息管理器
		$this->routes = $route;
		
		$this->setRequest($request);
	}
	
	public function setRequest(Request $request){
	
		$this->request = $request;
	}
	
	public function getRequest(){
		
		return $this->request;
	}
	
	/**
	 * 
	 * 全路徑
	 */
	public function full(){
		
		return $this->request->fullUrl();
	}
	/**
	 * 
	 * 當前地址
	 */
	public function current(){
		
		return $this->to($this->request->getPathInfo());
	}
	/**
	 * 
	 * 來源地址
	 */
	public function previous(){
		
		$referer = $this->request->headers->get('referer');
		
		$url =  $referer ? $this->to($referer) : $this->getPreviousUrlFromSession();
		
		return $url ?: $this->to('/');
	}
	/**
	 * 生成絕對路徑
	 * @see Eros\Contracts\Routing.UrlGeneratorInterface::to()
	 */
	public function to($path, $extra = array(), $secure = null){
		
		//檢驗是否是否合法的Url，是直接返回不再解析
		if($this->isValidUrl($path)) return $path;
		
		$scheme = $this->getScheme($secure);
		
		$extra = $this->formatParameters($extra);
		
		$tail = implode('/', array_map(
			'rawurlencdoe', (array)$extra )
		);
		
		//生成根url
		$root = $this->getRootUrl($scheme);
		
		return $this->trimUrl($root, $path, $tail);
	}
	/**
	 * 組裝安全的絕對url
	 * @see Eros\Contracts\Routing.UrlGeneratorInterface::secure()
	 */
	public function secure($path, $parameters = array()){
		
		return $this->to($path, $parameters, true);
	}
	
	public function asset($path, $secure = null){
		
		if($this->isValidUrl($path)) return $path;
		
		$root = $this->getRootUrl($this->getScheme($secure));
		
		
	}
	
	public function route($name, $parameters = array(), $absolute = true){
		
		if( !is_null($route = $this->routes->getByName($name))){
		
			return $this->toRoute($route, $parameters, $absolute);
		}
		
		throw new \InvalidArgumentException("Route [{$name}] not defined.");
	}
	
	protected function toRoute($route, $parameters , $absolute){
		
		$parameters = $this->formatParameters($parameters);
		
		$domain = $route->domain() ? $this->formatDomain($route, $parameters) : null;
		
	}
	/**
	 * 
	 * 格式化域名
	 * @param unknown_type $route
	 * @param unknown_type $parameters
	 */
	protected function formatDomain($route, &$parameters){
	
		return $this->addPortToDomain($this->getDomainAndScheme($route));
	}
	/**
	 * 
	 * 添加端口
	 * @param unknown_type $domain
	 */
	protected function addPortToDomain($domain){
		
		if(in_array($this->request->getPort(), array(80,443))){
			
			return $domain;
		}
		
		return $domain.":".$this->request->getPort();
	}
	
	protected function getDomainAndScheme($route){
		
		return $this->getRouteScheme($route).$route->domain();
	}
	/**
	 * 
	 * 獲取鏈接協議
	 * @param unknown_type $route
	 */
	protected function getRouteScheme($route){
		
		if($route->httpOnly()){
			
			return $this->getScheme(false);
		}elseif($route->httpsOnly()){
		
			return $this->getScheme(true);
		}
		
		return $this->getScheme(null);
	}
	
	
	public function action($action, $parameters = array(), $absolute = true){
	}
	
	public function setRootControllerNamespace($rootNamespace){
	}
	
	protected function getPreviousUrlSession(){
		
		$session = $this->getSession();
		
		return $session ? $session->getPreviousUrl() : null;
	}
	
	protected function getSession(){
		
		return call_user_func($this->sessionResolver ?: function(){});
	}

	protected function setSessionResolver(callable $session){
		
		$this->sessionResolver = $session;
		
		return $this;
	}
	
	public function isValidUrl($path){
		
		if(starts_with($path, ['#', '//', 'mailto:', 'tel:', 'http://', 'https://'])) return true;
		
		return filter_var($path, FILTER_VALIDATE_URL) !== false;
	}
	/**
	 * 
	 * 獲取協議
	 * @param unknown_type $secure
	 */
	protected function getScheme($secure){
		
		if(is_null($secure)){
			
			return $this->forceSchema ?: $this->request->getScheme().'://';
		}
		
		return $secure ? 'https://' : 'http://';
	}
	public function setForceScheme($forceScheme){
		
		$this->forceScheme = $forceScheme;
	}
	
	protected function formatParameters($parameters){
		
		$parameters = is_array($parameters) ? $parameters : array($parameters);
		
		foreach ($parameters as $key => $parameter){
			
			if($parameter instanceof UrlRoutableInterface){
				
				$parameters[$key] = $parameter->getRouteKey();
			}
		}
		
		return $parameters;
	}
	
	protected function getRootUrl($scheme, $root = null){
		
		if(is_null($root)){
			
			$root = $this->forcedRoot ?: $this->request->root();
		}
		
		$start = starts_with($root, 'http://') ? 'http://' : 'https://';

		return preg_replace('~'.$start.'~', $scheme, $root, 1);
	}
	
	protected function trimUrl($root, $path, $tail = ''){
		
		return trim($root.'/'.trim($path.'/'.$tail, '/'), '/');
	}
}