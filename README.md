# PHP anonymizer/cleanup

Renames all variables, method names, functions, etc etc to
a string representing their "part of speech".

Ulimately, I'd like it to rename all methods, classes, functions,
variables, etc, and still have working code.
This will require a symbol table to keep track of what names have
been used, and what new name with with to replace the old name.

| Token type | renamed to|
|------------|-----------|
| function name | `functionname` |
| function arguments | `argumentname` |
| variable name | `variablename` |
| class name | `classname` |
| method invocation | `methodcall` |
| something wrong | `propertyname` |
| instance variable reference | `instancevar` |

## BUGS

* Some confusion about class "properties" and refs of instance variables.
* Doesn't detect method names or their formal arguments
* Should distinguish between argument names, and then use those
individual argument names in the method body.

## Building

	$ composer install

## Running

    $ ./cleanup.php somefile.php

Anonymized/cleaned-up PHP appears on stdout

## Testing

    ./runtests
