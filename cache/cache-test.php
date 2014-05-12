<?php

require(sprintf(
	'%s/lib/cache.php',
	dirname(__FILE__)
));

var_dump(Cache::Create());

Cache::Set('test','test string');
var_dump(Cache::Get('test'));

Cache::Drop('test');
var_dump(Cache::Get('test'));

