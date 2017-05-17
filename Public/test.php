<?php
define('ROOT',dirname(__DIR__));

require ROOT."/vendor/autoload.php";

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

$ao = new ArrayObject(range(0,9));

while(list($key,$v) = each($ao)){
	unset($ao[$key]);
}

//foreach ($ao as $key=>$v){
//	unset($ao[$key]);
//}
print_r($ao);

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