<?php
/**
 * 
 * abstract view class
 * @author Sowang
 *
 */
namespace GFF\View;

abstract class AbstractView{
    protected $var;
    /**
     * 
     * 用于渲染模板
     */
	abstract public function render();
	/**
	 * 
	 * 解析模板
	 */
	abstract public function loadTemplate();
	/**
	 *  解析变量，和自定义标签 
	 */
	abstract public function cache();
	abstract public function writeCache();
	
	
	public function __get($key){
		return isset($this->var[$key])?$this->var[$key]:null;
	}
     public function assign($key,$val=null){
     	if(is_array($key)){
     		foreach ($key as $k=>$v){
     			 if($k !='') $this->var[$k]=$v;
     		}
     	}else {
     	    if($key !='')$this->var[$key]=$val;
     	}
     	
     }
}
