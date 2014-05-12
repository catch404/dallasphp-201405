<?php

require(sprintf(
	'%s/lib/cache.php',
	dirname(__FILE__)
));

var_dump(Cache::Create());

$obj = (object)['Hello'=>'World'];

Cache::Set('test',$obj);
var_dump(Cache::Get('test'));

Cache::Drop('test');
var_dump(Cache::Get('test'));

