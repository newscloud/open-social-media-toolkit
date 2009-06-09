<?php
/**
* cssCompress from http://ru.php.net/manual/en/function.ob-start.php#71953
*/

class cssMin {

    function __construct() {
    }
    
    function minify($buffer='') {

	    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); // remove comments
	    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer); // remove tabs, spaces, newlines, etc.
	    $buffer = str_replace('{ ', '{', $buffer); // remove unnecessary spaces.
	    $buffer = str_replace(' }', '}', $buffer);
	    $buffer = str_replace('; ', ';', $buffer);
	    $buffer = str_replace(', ', ',', $buffer);
	    $buffer = str_replace(' {', '{', $buffer);
	    $buffer = str_replace('} ', '}', $buffer);
	    $buffer = str_replace(': ', ':', $buffer);
	    $buffer = str_replace(' ,', ',', $buffer);
	    $buffer = str_replace(' ;', ';', $buffer);
		return $buffer;
    }

}
?>