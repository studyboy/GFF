<?php namespace Eros\Debug\View;

class DisplayExceptionView {


	protected $debug;
	
	public function __construct($debug = true){
	
		$this->debug = $debug;
	}
	
	public function send(){
		
	}
	
	public function createResponse($e){
		
		if(!$e instanceof FlattenException) {
		
			$e = FlattenException::create($e);
		}
		//返回
		echo $this->decode($this->getContent($e), $this->getStylesheet($e));
		 
		 return $this;
	}
	
	public function getContent($e){
		
	 	switch ($e->getStatusCode()) {
            case 404:
                $title = 'Sorry, the page you are looking for could not be found.';
                break;
            default:
                $title = 'Whoops, looks like something went wrong.';
        }
        
		$content = '';
        if($this->debug){
        	$content = $e;
        }
        
        return  <<<EOF
         <div id="sf-resetcontent" class="sf-reset">
                <h1>$title</h1>
                <div class="sf-content">
                $content
                </div>
            </div>
EOF;
	}
	
	public function getStylesheet($e){
		
		return <<<EOF
		
		#sf-resetcontent h1 {border-bottom: 2px solid #ccc;padding: 5px;font-size: 25px }
		#sf-resetcontent .sf-content {margin:10px 0; padding:0 5px;}
EOF;
	}
	
	public function decode($content, $css){
	
		 return <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="noindex,nofollow" />
        <style>
            html{color:#000;background:#FFF;}body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,textarea,p,blockquote,th,td{margin:0;padding:0;}table{border-collapse:collapse;border-spacing:0;}fieldset,img{border:0;}address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:normal;}li{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:normal;}q:before,q:after{content:'';}abbr,acronym{border:0;font-variant:normal;}sup{vertical-align:text-top;}sub{vertical-align:text-bottom;}input,textarea,select{font-family:inherit;font-size:inherit;font-weight:inherit;}input,textarea,select{*font-size:100%;}legend{color:#000;}

            html { background: #eee; padding: 10px }
            img { border: 0; }
            #sf-resetcontent { width:970px; margin:0 auto; }
            $css
        </style>
    </head>
    <body>
        $content
    </body>
</html>
EOF;
	}

}