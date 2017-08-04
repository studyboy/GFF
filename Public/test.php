<?php
namespace Double;

define('ROOT',dirname(__DIR__));

require ROOT."/vendor/autoload.php";

//ini_set('display_errors', 'off');
//error_reporting(E_ALL);

$a = explode(' ','CLS55');
print_r($a);exit;

class ab {
	public function tt(){
		echo 'tt';
	}
}
echo ab::class;exit;
call_user_func(array(new ab(),'tt'),'test');

exit;



class Event{
	
	protected $queues = array();
	
	protected $sorted = array();
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $event
	 * @param unknown_type $listener
	 * @param unknown_type $priority
	 * @deprecated
	 */
	public function listen($event, $listener, $priority = 0){
		
		$key = $event.'_event';
		
		$this->queues[$key][$priority][] = is_string($listener) ? $this->createClassListener($listener) : $listener;
		
	}
	
	public function trigger($event, $args){
		
		$event .='_event';
		
		foreach ($this->getListener($event) as $e){
			
//			print_r($e);exit;
			$response = call_user_func_array($e, $args);
		
			 //返回null的時候處理過程
			 
			//返回false終止後面事件
			if( $response === false ) {
				break;
			}
		
			$responses[] = $response;
		}
	
		return $responses;
	}
	
	public function getListener($event){
		
		$listers = $this->sort($event);
	
		return $listers;
	}
	
	public function createClassListener($listener){
		
		return function() use ($listener){
			
			list($class, $method) = explode('@', $listener);
			
			$instance = new $class;
		
			return call_user_func_array(
			
				array($instance, $method), func_get_args()
			);
		};
	}
	
	public function sort($event){
		
		$this->sorted[$event]= array(); 
		
		$listeners = $this->queues[$event];
		
		krsort($listeners);
	
		$this->sorted[$event] = call_user_func_array(
			'array_merge', $listeners
		);
		
		return $this->sorted[$event];
	}
	public function createClassCallable($class){
		
		list($class, $method) = explode('@', $class);
		
		return '';
		
	}
	
}
class Aa{
	
	public function call($name,$age){
		echo "My test info. name:{$name}, age:{$age}";
	}
}

$e = new Event();

$e->listen('test', function($name, $age){

	echo "My info name:{$name}, age:{$age}";
},30);

$e->listen('test','Aa@call', 50);

$e->trigger('test', array('hahha','17') );

//print_r($e);


exit;

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