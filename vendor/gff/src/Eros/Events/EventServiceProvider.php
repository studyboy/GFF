<?php namespace Eros\Events;
/**
 * 事件服務者
 */
use Eros\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider{

	public function register(){
		
		return $this->app->singleton('events',function($app){
			
			return new Dispatcher($app);
		});
	}
}
