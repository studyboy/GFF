<?php

use Eros\Filesystem\Filesystem;
use Eros\Foundation\Bootstrap\LoadConfiguration;
use Eros\Http\Request\AcceptHeader;
define('ROOT',dirname(__DIR__));

require ROOT."/vendor/autoload.php";

//ini_set('display_errors', 'off');
echo error_reporting();

throw new exception('test');
//$request = Eros\Http\Request::run();


//print_r(AcceptHeader::fromString($request->headers->get('accept'))->all());


//$app = require ROOT.'/bootstrap/app.php';
//
//$kernel = $app->make('Eros\Contracts\Http\KernelInterface');
//
//$load = new LoadConfiguration();
//$config = $load->bootstrap($app);

$file = new Filesystem();
var_dump($file->exists('index.php')).'oook';


class ai extends  ArrayIterator{
	
	public function rewind(){
		
//		parent::next();
        
		parent::rewind();
		
//		echo '.';
	}
}

$a1 = new ai(array(1,2,3));
$a2 = new ai(array('a','b','c'));

$ap = new AppendIterator();
$ap->append($a1);
$ap->append($a2);

foreach ($ap as $a){
	echo $a;
}



exit;

//use Eros\Http\Request\FileParameters;


//$file = new FileParameters($_FILES);
//
//$u =current($file->all()) ;
//
//foreach ( current($file->all()) as $k=>$upload){
////	die($upload->getOriginName());
//	echo $upload->move('a',iconv('UTF-8','GBK',$upload->getOriginName()));
//	
//}


class iMaxHeap extends SplHeap{
	
	
	public function compare($value1, $value2){
		$v1 = array_values($value1);
		$v2 = array_values($value2);
		
		if($v1[0] === $v2[0]) return 0;
		
		return $v1[0] > $v2[0] ? 1 : -1;
	}
}


$ih = new iMaxHeap();

$ih->insert(array('a'=>1));
$ih->insert(array('g'=>0));
$ih->insert(array('b'=>3));

$ih->top();

while ($ih->valid()){
	$cur = $ih->current();
	list($index,$v) = each($cur);
	echo $ih->key().':'.$index."::".$v.PHP_EOL;
	$ih->next();
}

$a = new ArrayIterator(array('a'=>1,'b'=>2));
$b = new ArrayIterator(array('d'=>3,'f'=>4));

$it = new \AppendIterator();

$it->append($a);
$it->append($b);

//for ($it->rewind();$it->valid(); $it->next()){
//	echo $it->key()."::".$it->current()."::".$it->getInnerIterator();
//}

//$ao = new ArrayObject(range(0,9));

//while(list($key,$v) = each($ao)){
//	unset($ao[$key]);
//}

//foreach ($ao as $key=>$v){
//	unset($ao[$key]);
//}

//}

//$rdi = new RecursiveDirectoryIterator(dirname(__DIR__).'/config',RecursiveDirectoryIterator::SKIP_DOTS);
//
//$rrdi = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST);
//
//
//foreach ($rrdi as $name => $obj){
//	echo $name.'::'.$obj->getFileName().PHP_EOL;
//}

//print_r(iterator_to_array($rrdi));
/**
 * 
 * +------------------------------------------------
 * Enter description here ...
 * +------------------------------------------------
 * @author gaosongwang <songwanggao@gmail.com>
 * +-------------------------------------------------
 * @version 2017/5/26
 * +-------------------------------------------------
 */
class a {
	
	public $a =12;
	protected $b = 34;
	private $cc ='jk';
	
	public function __construct($name , $sex='boy'){
		
	}
	public function getName(){
		$this->$cc;
	}
	
}



//foreach ($rf->getProperties() as $pro){
//	if($pro->isPublic()) echo $pro->getName().":".$pro->getValue($pro).'ll';
//}



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