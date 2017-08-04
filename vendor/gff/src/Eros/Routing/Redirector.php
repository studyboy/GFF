<?php namespace Eros\Routing;

class Redirector {
	
	protected $generator;
	
	protected $session;
	
	public function __construct(UrlGenerator $generator){
		
		$this->generator = $generator;
	}
	
	public function home($status = 302){
		
		return $this->to($this->generator->route('home'), $status);
	}
	
	public function back($status = 302, $headers = array()){
		
		$back = $this->generator->previous();
		
		return $this->to($back, $status, $headers);
	}
	
	public function refresh($status = 302, $headers = array()){
		
		return $this->to($this->generator->getReqeust()->Path(), $status, $headers);
	}
	
	public function guest($path, $status = 302, $headers = array(), $secure = null){
	}
	
	public function intended($default = '/', $status = 302, $headers = array(), $secure = null){
	}
	
	public function to($path, $status = 302, $headers = array(), $secure = null){
		
		$path = $this->generator->to($path, array(), $secure);
		
		return $this->createRedirect($path, $status, $headers);
	}
	
	public function away($path, $status = 302, $headers = array()){
	}
	
	public function secure($path, $status = 302, $headers = array()){
	
	}
	
	public function route($route, $parameters = array(), $status = 302, $headers = array()){
	}
	
	public function action($action, $parameters = array(), $status = 302, $headers = array()){
	}
	
	protected function createRedirect($path, $status, $headers){
		
		$redirector = new RedirectorResponse();
		
		
	}
	
	public function getUrlGenerator(){
	}
	
	public function setSession(SessionStore $session){
	}
}