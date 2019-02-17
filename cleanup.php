#!/usr/bin/env php
<?php
require 'vendor/autoload.php';
include('CleanupSymbolTable.php');
include('CleanupVisitor.php');

use PhpParser\NodeTraverser;
use PhpParser\ParserFactor;
use PhpParser\PrettyPrinter;

$input_filename = $argv[$argc - 1];

$code = file_get_contents($input_filename);

$parser        = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7);
$traverser     = new PhpParser\NodeTraverser;
$prettyPrinter = new PhpParser\PrettyPrinter\Standard;

$visitor = new CleanupVisitor();

$traverser->addVisitor($visitor);

try {
   	$stmts = $parser->parse($code);
}
catch (PhpParser\Error $e) {
	fwrite(STDERR, 'Parse Error: '.$e->getMessage()." in {$input_filename}\n");
	exit;
}

$stmts = $traverser->traverse($stmts);

$code = $prettyPrinter->prettyPrint($stmts);
echo $code."\n";

exit;
