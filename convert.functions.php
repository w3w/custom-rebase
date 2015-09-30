<?php

function convertFiles($files)
{
    foreach ($files as $file) {
        convertFile($file);
    }
}

function convertFile($file)
{
    echo 'Converting ' . $file . PHP_EOL;
    $fileContents = file_get_contents($file);
    file_put_contents($file, convertSquareBracketsToArrays($fileContents));
}

function revertFile($file)
{
    echo 'Reverting ' . $file . PHP_EOL;
    $fileContents = file_get_contents($file);
    file_put_contents($file, convertArraysToSquareBrackets($fileContents));
}

function convertSquareBracketsToArrays($code)
{
    $out = '';
    $brackets = [];
    $ignoreBracket = false;
    foreach (token_get_all($code) as $token) {
        if ($token === '[') {
            $brackets[] = !$ignoreBracket;
            $token = $ignoreBracket ? '[' : 'array(';
        } elseif ($token == ']') {
            $token = array_pop($brackets) ? ')' : ']';
        }
        if (!is_array($token) || $token[0] !== T_WHITESPACE) {
            $ignoreBracket = (in_array($token, [')', ']', '}'])
                || (is_array($token) && in_array($token[0], [T_VARIABLE, T_STRING, T_STRING_VARNAME])));
        }
        $out .= is_array($token) ? $token[1] : $token;
    }
    return $out;
}

function convertArraysToSquareBrackets($code)
{
    $out = '';
    $brackets = [];
    $tokens = token_get_all($code);
    for ($i = 0; $i < count($tokens); $i++) {
        $token = $tokens[$i];
        if ($token === '(') {
            $brackets[] = false;
        } elseif ($token === ')') {
            $token = array_pop($brackets) ? ']' : ')';
        } elseif (is_array($token) && $token[0] === T_ARRAY) {
            $a = $i + 1;
            if (isset($tokens[$a]) && $tokens[$a][0] === T_WHITESPACE) {
                $a++;
            }
            if (isset($tokens[$a]) && $tokens[$a] === '(') {
                $i = $a;
                $brackets[] = true;
                $token = '[';
            }
        }
        $out .= is_array($token) ? $token[1] : $token;
    }
    return $out;
}

function cmd($cmd, &$out = null, &$returnCode = null)
{
    echo $cmd . PHP_EOL;
    exec($cmd, $out, $returnCode);
}
