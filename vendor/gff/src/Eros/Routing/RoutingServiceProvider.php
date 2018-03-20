<?php namespace Eros\Routing;


use Eros\Support\ServiceProvider;

class RoutingServiceProvider extends ServiceProvider{
	/**
	 * 註冊組件的相關資源
	 * @see Eros\Support.ServiceProvider::register()
	 */
	public function register(){
		
		$this->registerRouter();
		
		$this->registerUrlGenerator();
		
		$this->registerRedirector();
		
		$this->registerResponseFactory();
	}
	
	protected function registerRouter(){
		
		$this->app['router'] = $this->app->share(function($app){
			
			return new Router($app['events'], $app);
		});
	}
	protected function registerUrlGenerator(){
		
		$this->app['url'] = $this->app->share(function($app){
			
			$routes = $app['router']->getRoutes();
			
			$app->instance('routes', $routes);
			//為請求重綁定最新的對象
			$url = new UrlGenerator(
				$routes, $app->rebinding(
					'request', $this->requestRebinder()
				)
			);
			
			
			return $url;
		});
	}
	
	protected function requestRebinder(){
		
		return function($app, $request){
			
			$app['url']->setRequest($request);
		};
	}
	/**
	 * 
	 * 註冊重定向信息
	 */
	protected function registerRedirector(){
		
		$this->app['redirector'] = $this->app->share(function($app){
			
			$redirector = new Redirector($app['url']);
			
			
			return $redirector;
		});
	}
	protected function registerResponseFactory(){
	}
}