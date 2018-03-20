<?php namespace Eros\Events;

use Eros\Container\Container;

use Eros\Contracts\Container\ContainerInterface;

use Eros\Contracts\Foundation\ApplicationInterface;

use Eros\Contracts\Events\DispatcherInterface;

class Dispatcher implements DispatcherInterface {
	
	protected $container;
	
	protected $listners = array();
	
	protected $sorted = array();
	
	protected $firing = array();
	
	public function __construct(ContainerInterface $container){
		
		$this->container = $container ?: new Container();
	}
	
	/**
	 * 
	 * 綁定事件
	 * @see Eros\Contracts\Events.DispatcherInterface::listen()
	 */
	public function listen($events, $listner, $priority = 0){
		
		foreach ( (array)$events as $event ){
			
			$this->listners[$event][$priority][] = is_string($event) ? $this->createClassListener($listner) : $listner;
			
			unset($this->sorted[$event]);
		}
		
	}
	/**
	 * 觸發事件
	 * @see Eros\Contracts\Events.DispatcherInterface::trigger()
	 */
	public function trigger($event, $payload = array(), $halt = false){
		
		if( is_object($event)){
			
			list($payload, $event) = array(array($event), get_class($event));
		}
		
		$responses = array();
		
		$payload =  !is_array($payload) ? array($payload) : $payload;
		
		//調用事件的存儲堆棧
		$this->firing[] = $event;
		
		//獲取事件進行調用
		foreach( $this->getlisteners($event) as $listener){
			
			$response = call_user_func_array($listener, $payload);
			
			//如果事件返回非null，並且啟動halt為真的時候，則直接終止後面事件的執行
			if( !is_null($response) && $halt){
				
				array_pop($this->firing);
				
				return $response;
			}
			
			//如果響應的是假，直接終止後面的事件執行
			if( $response === false ) break;
			
			$responses[] = $response;
		}
		
		array_pop($this->firing);
		
		return $halt ? null : $responses;
	}
	/**
	 * 執行事件一直到事件返回非null為止而終止事件
	 * @see Eros\Contracts\Events.DispatcherInterface::until()
	 */
	public function until($event, $payload = array()){
		
		return $this->trigger($event, $payload, true);
	}
	
	public function flush($event){
	
		unset($this->listners[$event], $this->sorted[$event]);
	}
	
	public function firing(){
		
		return last($this->firing);
	}
	
	public function forget($event){
		
		unset($this->listners[$event]);
	}
	
	public function forgetPushed(){
		
	}
	/**
	 * 檢測是否存在對應事件
	 * @see Eros\Contracts\Events.DispatcherInterface::hasListeners()
	 */
	public function hasListeners($eventName){
		
		return isset($this->listners[$eventName]);
	}
	
	protected function getListers($eventName){
		
		//增加通配符事件
		
		if( !isset($this->sorted[$eventName]) ){
			
			$this->sortListeners($eventName);
		}
				
		return $this->sorted[$eventName];
	}
	/**
	 * 
	 * 將類對象進行實例化
	 * @param unknown_type $listner
	 */
	protected function createClassListener($listener){
		
		$container = $this->container;
		
		return function () use($listener, $container) {
			
			return call_user_func_array(
				$this->createClassCallable($listener, $container), func_get_args()
			);
		};
	}
	
	protected function createClassCallable($listener, $container){
		
		$cm = explode('@', $listener);
		
		list($class, $method) = array($cm[0], count($cm) == 2 ? $cm[0] : 'handle');
		
		//增加是否歸入隊列處理
		
		return array($container->make($class), $method);
	}
	
	protected function sortListeners($event){
		
		$this->sorted[$event] = array();
		
		$listeners = $this->listners[$event];
		
		if( isset($listeners) ){
	
			krsort($listeners);
			
			$this->sorted[$event] = call_user_func_array(
				'array_merge', $listeners
			);
		}
		
		return $this->sorted;
	}
}