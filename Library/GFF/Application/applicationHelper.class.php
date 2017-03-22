<?php
/**
 * 
 * 配置助手类 全局
 * @author Sowang
 *
 */
namespace GFF\Application;

class ApplicationHelper{
    
	protected $options=array();
	protected static  $instance;
	private function __construct(){}
	
	static function init(){
		if(!isset(self::$instance)) {
			self::$instance=new self();
		}
		self::$instance->options=self::$instance->setOptions();
		return self::$instance;
	}
	public function getOptions(){
		return $this->options;
	}
	public function setOptions(){
		if(!empty($this->options)) return $this->options;
		$optionsPath=PROJECT_ROOT.'public/config/config.class.php';
		require $optionsPath;
		return $config['system'];
	}
	
}