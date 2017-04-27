<?php
define('ROOT',dirname(__DIR__));

require ROOT."/vendor/autoload.php";

use Eros\Http\Request\FileParameters;


$file = new FileParameters($_FILES);

$u =current($file->all()) ;

foreach ( current($file->all()) as $k=>$upload){
//	die($upload->getOriginName());
	echo $upload->move('a',iconv('UTF-8','GBK',$upload->getOriginName()));
	
}

?>

<html>
<head></head>
<body>
<form name="t1" method="POST" action="/test.php"  enctype="multipart/form-data">

<input type='file' name="s[]"/><br/>
<input type='file' name="s[]"/><br/>
<input type="submit" name="submit"/>

</form>

</body>
</html>