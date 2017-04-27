<?php namespace Eros\Http\Request;

use Eros\Http\Upload\UploadFile;

class FileParameters extends Parameters{
	
	private static $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
	
	public function __construct(array $parameters){
		
		$this->replace($parameters);
	}
	
	public function add(array $paramters){
	
		foreach ($paramters as $key=>$value){
			
			$this->set($key, $value);
		}
	}
	
	public function replace(array $parameters){
		
		$this->parameters = array();
		
		$this->add($parameters);
	}
	
	public function set($key, $value){
		
		if (!is_array($value) && !$value instanceof UploadFile) {
            throw new \InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
        }
		
        parent::set($key, array_filter($this->convertFileInformation($value)));
	}
	
	public function convertFileInformation($file){
		
		if( $file instanceof UploadFile ) return $file;
		
		//修正當上傳對象為數組時，返回的數組形式不統一的問題
		$file = $this->fixPhpFileArray($file);

        if (is_array($file)) {
            $keys = array_keys($file);
            sort($keys);
            if ($keys == self::$fileKeys) {
            	
                if (UPLOAD_ERR_NO_FILE == $file['error']) {
                    $file = null;
                } else {
                    $file = new UploadFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
                }
            } else {
                $file = array_map(array($this, 'convertFileInformation'), $file);
            }
        }

        return $file;
	}
	
	public function fixPhpFileArray($file){

		if( !is_array($file) ) return $file;
		
		$keys = array_keys($file);
		sort($keys);
		
		if(static::$fileKeys != $keys || !isset($file['name']) || !is_array($file['name'])){
			return $file;
		}

		$files = $file;
		foreach (static::$fileKeys as $k){
			unset($files[$k]);
		}
		
		$files = array();
		foreach (array_keys($file['name']) as $key){
			$files[$key] = $this->fixPhpFileArray(array(
				'error'=> $file['error'][$key],
				'name' => $file['name'][$key],
				'size' => $file['size'][$key],
				'tmp_name' => $file['tmp_name'][$key],
				'type'	=> $file['type'][$key],
			));
		}
		
		return $files;
	}

}