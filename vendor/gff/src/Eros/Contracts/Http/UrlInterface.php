<?php namespace Eros\Contracts\Http;

interface UrlInterface{
	
	public function __construce($url);
	
	public function getScheme();
	
	public function getHost();
	
	public function getPort();
	
	public function getPath();
	
	public function getAnchor();
	
	public function getParameters();
	
	public function getFilename();
}