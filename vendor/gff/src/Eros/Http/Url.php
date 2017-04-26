<?php namespace Eros\Http;

use Eros\Contracts\Http\UrlInterface;

class Url implements UrlInterface {
	
	protected $url;
	
	protected $scheme;
	protected $host;
	protected $path;
	protected $filename;
	protected $parameters = array();
	protected $port;
	protected $queryString;
	protected $anchor;
	
	
	public function __construce($url){
		
		$this->url = $url;
		
		$this->parseUrl($url);
	}
	
	protected function parseUrl($url){
		
		
	}
	
	public function getAnchor(){
	
	}

}