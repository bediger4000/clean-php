<?php

# $Id: SymbolTable.php,v 1.4 2016/03/22 02:02:23 bediger Exp $

class CleanerSymbolTable {

var $symbol_tables;
var $global_symbols;
var $globals_synonyms;

public function __construct() {
	$this->symbol_tables = array();
	$this->global_symbols = array();  // Names are the indexes of $GLOBALS[], too.
	$this->globals_synonyms = array();  // $x = $GLOBALS; makes $x into a synonym for $GLOBALS
}

public function pushScope() {
	$this->symbol_tables[] = array();
}

public function popScope() {
	array_pop($this->symbol_tables);
}

public function addSymbol($name, $value, $set = TRUE) {
	$cnt = count($this->symbol_tables);
	if ($cnt === 0) {
		$this->addGlobalSymbol($name, $value, $set);
		return;
	}
	$this->symbol_tables[$cnt-1][$name] = array('set'=>$set, 'value'=>$value);
}

public function addGLOBALSsynonym($name) {
	$this->globals_synonyms[] = $name;
}

public function checkGLOBALSsynonym($candidate) {
	if (in_array($candidate, $this->globals_synonyms)
		|| strcmp($candidate, 'GLOBALS') == 0)
			return true;
	return false;
}

public function addGlobalSymbol($name, $value, $set = TRUE) {
	$this->global_symbols[$name] = array('set'=>$set, 'value'=>$value);
}

public function symbolValue($name, &$value) {
	$found_value = FALSE;

	$cnt = count($this->symbol_tables);
	if ($cnt === 0) {
		$found_value = $this->globalSymbolValue($name, $value);
	} else {

		$cnt -= 1;

		if (isset($this->symbol_tables[$cnt][$name])) {
			$sym = $this->symbol_tables[$cnt][$name];
			if ($sym['set']) {
				$value = $sym['value'];
				$found_value = TRUE;
			}
		}
	}

	return $found_value;
}

public function updateValue($name, $value) {

	$found_value = FALSE;
	$cnt = count($this->symbol_tables);
	$scope = 0;

	while ($cnt > 0) {
		$scope = $cnt - 1;
		if (isset($this->symbol_tables[$scope][$name])) {
			$sym = $this->symbol_tables[$scope][$name];
			$sym['value'] = $value;
			$sym['set'] = TRUE;
			$found_value = TRUE;
			break;
		}
		$cnt = $scope;
	}

	if (!$found_value) {
		if (isset($this->global_symbols[$name])) {
			$sym = $this->global_symbols[$name];
			$sym['value'] = $value;
			$sym['set'] = TRUE;
			$this->global_symbols[$name] = $sym;
			$found_value = TRUE;
		}
	}

	return $found_value;
}

public function isSymbol($candidate_string) {
	$found_symbol = FALSE;
	$cnt = count($this->symbol_tables);
	if ($cnt === 0) {
		if (isset($this->global_symbols[$candidate_string]))
			$found_symbol = TRUE;
	} else {
		if (isset($this->symbol_tables[$cnt-1][$candidate_string]))
			$found_symbol = TRUE;
	}
	return $found_symbol;
}

public function globalSymbolValue($name, &$value) {
	$found_value = FALSE;
	if (is_object($name)) {
		debug_print_backtrace();
	}
	#fwrite(STDERR, "+++++\nglobalSymbolValue: ".print_r($name, TRUE)."\n-------\n");
	if (array_key_exists($name, $this->global_symbols)) {
		$sym = $this->global_symbols[$name];
		if ($sym['set']) {
			$value = $sym['value'];
			$found_value = TRUE;
		}
	}
	return $found_value;
}

public function __destruct() {
	$this->symbol_table = null;
	$this->global_symbols = null;
}

}

?>
