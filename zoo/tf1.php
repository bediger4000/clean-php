<?php
$globalscope_variable = 42;
$globalscope_array = array(42, 12, -1, 100000.001);

function fname($arg1, $arg2, $arg3) {
	return $arg1 + $arg2 % $arg3;
}
class someclass {
	var $varxyz;
	function method1($arg1) {
		return $this->varxyz + $arg1;
	}
}

$o = new someclass();
$o->method1(1, 2, 3);
$o->xyz = 93;
