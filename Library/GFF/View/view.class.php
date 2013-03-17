<?php
/**
 * 
 * 解析html模板
 * @author Sowang
 *
 */

namespace GFF\View;
use GFF\Http;
//error_reporting(E_ALL);
class View extends AbstractView{
	
	private $request;
    public function __construct(){
    	$this->request=new Http\Request();
    	$this->writeCache();
    }
    public function __destruct(){
    	unset($this->request);
    }
	public function render(){
	   $this->loadTemplate();
	}
	public function getRequest(){
		return $this->request;
	}
	public function loadTemplate(){

		$requestParam=$this->request->getUrlArray();
		$filePath=APP_VIEW_CACHE_PATH.$requestParam['module'].'/'.$requestParam['controller'].'/';
		$fileName=$filePath.$requestParam['action'].'.tpl.php';

		if(!file_exists($fileName)){
			throw new \Exception('调用模板不存在！');
		}
		extract($this->var,EXTR_OVERWRITE);
		require $fileName;
	}
	/**
	 * 解析模板自定义变量
	 */
	public function cache(){

		$reParam=$this->request->getUrlArray();
		$dir=ucfirst($reParam['module']).'/'.ucfirst($reParam['controller']).'/';
		$filePath= APP_VIEW_TEMPL_PATH.$dir;
		if(!file_exists($filePath)){
			// r=4 w=2 x=1;
			mkdir($filePath,'0766',true);
		} 
		$fileName =  $filePath.lcfirst($reParam['action']).'.tpl.php';
		$cacheFileName=APP_VIEW_CACHE_PATH.$dir.lcfirst($reParam['action']).'.tpl.php';

		if(file_exists($cacheFileName) && filemtime($fileName) <= filemtime($cacheFileName)) {
			return false;
		}
		
		$content=file_get_contents($fileName,
		                                false,
		                stream_context_create(array('Http'=>array('timeout'=>5))));
		                
		$content =preg_replace('/\{\$(.*)\}/i','<?=$${1};?>',$content);

		return $content;
	}
	public function writeCache(){
		$content=$this->cache(); 
		if(!$content) return false;
		$reParam=$this->request->getUrlArray();
		$filePath=APP_VIEW_CACHE_PATH.ucfirst($reParam['module']).'/'.ucfirst($reParam['controller']).'/';
		$fileName=$filePath.lcfirst($reParam['action']).'.tpl.php';
		if(!file_exists($filePath)){
			mkdir($filePath,'0766',true);
		}
	    return $this->writeFile($fileName,$content);	
	}
	protected function writeFile($fileName,$content){

        $fp=fopen($fileName,'w',false,stream_context_create(array('Http'=>array('timeout'=>5))));
        $st=fwrite($fp,$content,strlen($content));
        fclose($fp);
        return $st;
	} 
	public function __clone(){}
}