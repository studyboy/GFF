<?php namespace Eros\Contracts\Events;


interface DispatcherInterface {
	/**
	 * 
	 * 綁定事件
	 * @param unknown_type $event
	 * @param unknown_type $listener
	 * @param unknown_type $priority
	 */
	public function listen($event, $listener, $priority = 0);
	
	public function hasListeners($eventName);
	/**
	 * 
	 * 觸發事件
	 * @param unknown_type $event
	 * @param unknown_type $payload
	 * @param unknown_type $halt
	 */
	public function trigger($event, $payload = array(), $halt = false);
	/**
	 * 
	 * 觸發事件，直到第一個事件返回非空為止
	 * @param unknown_type $event
	 * @param unknown_type $payload
	 */
	public function until($event, $payload = array());
	
	public function forget($event);
	
	public function forgetPushed();
}