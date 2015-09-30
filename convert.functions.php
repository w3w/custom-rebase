<?php

function convertFiles($files) {
	foreach ($files as $file) {
		convertFile($file);
	}
}

function convertFile($file) {
	$fileContents = file_get_contents($file);
	file_put_contents($file, $fileContents);
}

function convertSquareBracketsToArrays($code)
{
	$brackets = [];
	$ignoreBracket = FALSE;
	foreach (token_get_all($code) as $token) {
		if ($token === '[') {
			$brackets[] = !$ignoreBracket;
			$token = $ignoreBracket ? '[' : 'array(';
		} elseif ($token == ']'){
			$token = array_pop($brackets) ? ')' : ']';
		}
		if (!is_array($token) || $token[0] !== T_WHITESPACE) {
			$ignoreBracket = (in_array($token, [')', ']', '}'])
				|| (is_array($token) && in_array($token[0], [T_VARIABLE, T_STRING, T_STRING_VARNAME])));
		}
		return is_array($token) ? $token[1] : $token;
	}
}