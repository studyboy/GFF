<?php namespace Eros\Foundation\Bootstrap;

use Eros\Contracts\Foundation\ApplicationInterface;

use Eros\Contracts\Foundation\Bootstrap\BootstrapInterface;

use Eros\Config\Repository;

use Eros\Support\Finder\SplFileInfo;

use Eros\Support\Finder;

use Eros\Contracts\Config\RepositoryInterface;


class LoadConfiguration implements BootstrapInterface {


	public function bootstrap(ApplicationInterface $app){
		
		$items = array();

		$app->instance('config',  $config = new Repository($items));
		
		$this->LoadConfigurationFiles($app, $config);
		
		
		return $config;
	}
	
	protected function LoadConfigurationFiles(ApplicationInterface $app, RepositoryInterface $config){
		
		foreach($this->getConfiguarationFiles($app) as $key=>$path){
			
			$config->set($key, require $path);
		}
		
	}
	
	protected function getConfiguarationFiles(ApplicationInterface $app){
		
		$files = array();
		
		$fs = Finder::create()->files()->name('*.php')->in( $app->getConfigPath());
		
		foreach( $fs as $file){
			
			$nesting = $this->getConfigFileNesting($file, $app);

			$files[$nesting.basename($file->getRealPath(),'.php')] = $file->getRealPath();
			
		}

		return $files;
		
	}
	/**
	 * 
	 * 獲取嵌套路徑
	 * @param SplFileInfo $file
	 * @param Application $app
	 */
	protected function getConfigFileNesting(SplFileInfo $file, ApplicationInterface $app){
		
		$dir = dirname($file->getRealPath());

		if( $tree = trim(str_replace($app->getConfigPath(),'', $dir), DIRECTORY_SEPARATOR) ){
			
			$tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree);
		}

		return $tree;
	}
	
}