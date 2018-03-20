<?php namespace Eros\Http\Upload;

use Eros\Filesystem\File\File;
use Eros\Filesystem\File\Exception\FileException;

class UploadFile extends File {
	
	protected $originName;
	protected $mimeType;
	protected $size;
	protected $error;
	
	public function __construct($path, $originName, $mimeType = NULL, $size = NULL, $error = NULL ){
		
		$this->originName = $this->getName($originName);
		$this->mimeType =  $mimeType ?: 'application/octet-stream';
		$this->size = $size;
		$this->error= $error ?: UPLOAD_ERR_OK;
		
		parent::__construct($path, UPLOAD_ERR_OK === $this->error);
	}
	
	public function getOriginName(){
		return $this->originName;
	}
	
	public function isValid(){
	
		$isOk = $this->error === UPLOAD_ERR_OK;
		
		return $isOk && is_uploaded_file($this->getPathname());
		
	}
	/**
	 * 文件的上傳功能
	 * @see Eros\Filesystem\File.File::move()
	 */
	public function move($dir, $name = null){
		
		if($this->isValid()){
			
			$target = $this->getTargetFile($dir, $name);

			//在上傳含有中文名稱的文件，需要將其轉為GBK 或GB2312，改函數不支持utf8
			if(!move_uploaded_file($this->getPathname(), $target)){
				
				$error = error_get_last();
				
                throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
			}
			
			@chmod($target, 0666 & ~umask());
			
			return $target;
		}
		
	}
	/**
	 * 
	 * 獲取最大的文件大小
	 * @return format size in bytes
	 */
	public static function getMaxFileSize(){
		
		$iniMax = strtolower(ini_get('upload_max_filesize'));
		
		if ('' === $iniMax) {
            return PHP_INT_MAX;
        }

        $max = ltrim($iniMax, '+');
        if (0 === strpos($max, '0x')) {
            $max = intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = intval($max, 8);
        } else {
            $max = intval($max);
        }

        switch (substr($iniMax, -1)) {
            case 't': $max *= 1024;
            case 'g': $max *= 1024;
            case 'm': $max *= 1024;
            case 'k': $max *= 1024;
        }

        return $max;
		
	}
	
	public function getError(){
		return $this->error;
	}
	public function getErrorMessage(){
		
		static $errors = array(
            UPLOAD_ERR_INI_SIZE => 'The file "%s" exceeds your upload_max_filesize ini directive (limit is %d KiB).',
            UPLOAD_ERR_FORM_SIZE => 'The file "%s" exceeds the upload limit defined in your form.',
            UPLOAD_ERR_PARTIAL => 'The file "%s" was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_CANT_WRITE => 'The file "%s" could not be written on disk.',
            UPLOAD_ERR_NO_TMP_DIR => 'File could not be uploaded: missing temporary directory.',
            UPLOAD_ERR_EXTENSION => 'File upload was stopped by a PHP extension.',
        );

        $errorCode = $this->error;
        $maxFilesize = $errorCode === UPLOAD_ERR_INI_SIZE ? self::getMaxFilesize() / 1024 : 0;
        $message = isset($errors[$errorCode]) ? $errors[$errorCode] : 'The file "%s" was not uploaded due to an unknown error.';

        return sprintf($message, $this->getFilename(), $maxFilesize);
	}
	
}