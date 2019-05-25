<?php

class CleanupVisitor extends PhpParser\NodeVisitorAbstract
{
	var $symtbl;

	public function __construct() {
		$this->symtbl = new CleanerSymbolTable();
	}

	public function afterTraverse(array $nodes) {
		$this->symtbl = null;
	}

	public function enterNode(PhpParser\Node $node) {
		if ($node instanceof PhpParser\Node\Stmt\Function_) {
			$node->name = 'functionname';
			$this->symtbl->pushScope();
			foreach ($node->params as $formal_argument) {
				$this->symtbl->addSymbol($formal_argument->name, NULL, FALSE);
				$formal_argument->name = 'argumentname';
			}
		} else
		if ($node instanceof PhpParser\Node\Stmt\Property) {
			$node->props[0]->name = 'propertyname';
		} else
		if ($node instanceof PhpParser\Node\Stmt\Class_) {
			// What about built-in class' names?
			$node->name = 'classname';
		} else
		if ($node instanceof PhpParser\Node\Expr\Variable) {
			if ($node->name !== 'this')
				$node->name = 'variablename';
		} else
		if ($node instanceof PhpParser\Node\Expr\FuncCall) {
			if (!function_exists($node->name->parts[0])) {
				// I think this will leave built-in functions alone.
				// This program isn't executing functions in the parsed PHP,
				// it's just visting their AST nodes.
				// It will replace functions defined in extensions,
				// even if they're configured for the installation running this.
				$node->name->parts[0] = 'functioncall';
			}
		} else
		if ($node instanceof PhpParser\Node\Expr\MethodCall) {
			$node->name = 'methodcall';
		} else
		if ($node instanceof PhpParser\Node\Expr\PropertyFetch) {
			$node->name = 'instancevar';
		} else
		if ($node instanceof PhpParser\Node\Stmt\InlineHTML) {
			$node->value = 'inline text';
		}
	}

    public function leaveNode(PhpParser\Node $node) {
		if ($node instanceof PhpParser\Node\Stmt\Function_) {
			$this->symtbl->popscope();
		}
    }
}
