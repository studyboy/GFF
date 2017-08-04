<?php namespace Eros\Foundation;
/**
 * 
 * +------------------------------------------------
 * 解析提供者配置
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/6/2
 * +-------------------------------------------------
 */
use Eros\Contracts\Foundation\ApplicationInterface;
use Eros\Filesystem\Filesystem;

class ProviderRepository {
	
	private $app;
	private $file;
	private $mainfestPath;
	
	public function __construct(ApplicationInterface $app,Filesystem $filesystem, $mainfestPath){
		
		$this->app = $app;
		
		$this->file = $filesystem;
		
		$this->mainfest = $mainfestPath;
	}
	/**
	 * 
	 * 載入解析后的緩存文件
	 * @param unknown_type $mainfest
	 */
	public function load(array $providers){
		
		//載入已編譯過的緩存文件，以便能夠區分提供者的加載順序
		$mainfest = $this->loadMainfest();	
		
		//未編譯的，則進行編譯判斷，即是否寫入緩存
		if( $this->isShoudCompile($mainfest, $providers)) {
			
			$mainfest = $this->compileMainfest($providers);
		}
		
		//為提供者設定觸發的條件
		foreach ($mainfest['when'] as $provider){
			
		}
		
		//將需要優先加載的提供者，直接實例化在application註冊以便調用，
		//將延遲的註冊者保存於延遲管理器中
		foreach ($mainfest['eager'] as $provider){
			
			$this->app->register($this->createProvier($provider));
		}
	
		$this->app->setDeferredProviders($mainfest['defered']);
	}
	/**
	 * 
	 * 載入解析緩存文件
	 */
	protected function loadMainfest(){
		
		//檢測是否有緩存文件，有則加載并返回解析結果
		if( $this->file->exists($this->mainfestPath)){
			
			$mainfest = json_decode($this->file->get($this->mainfestPath));
			
			return array_merge(['when'=>[]], $mainfest);
		}
			
	}
	
	protected function isShoudCompile($mainfest, $providers){
		
		return is_null($mainfest) || $mainfest['providers'] != $providers;
	}
	/**
	 * 
	 * 編譯解析配置文件的提供者，生成分類緩存文件
	 * @param array $providers
	 */
	protected function compileMainfest(array $providers){
		
		$mainfest = ['providers'=>$providers, 'eager'=>[], 'deferred'=> [] ];
		
		//將提供者按屬性進行分類
		foreach ($providers as $provider){
			
			$instance = $this->createProvider($provider);
			
			if( $instance->isdeferred() ){
				
				foreach ($instance->provides() as $service){
					
					$mainfest['deferred'][$service] = $provider;
				}
				$mainfest['when'][$provider] = $instance->when();
				
				//提供者沒有設置為延遲加載的話，則將其直接在請求的時候即時載入	
			}else{
				
				$mainfest['eager'][] = $provider;
			}
			
		}
		
		return $this->writeMainfest($mainfest);
	}
	
	public function createProvider($provider){
		
		return new $provider($this->app);
	}
	
	protected function writeMainfest($mainfest){
		
		return $this->file->put(
			$this->mainfestPath, json_encode($mainfest)
		);
	}
}