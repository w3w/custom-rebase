<?php

require_once "convert.functions.php";

$commit = $argv[1];

$commit = "e758f6395c16b558a0b2927d21095b6823b5e518";

$commandDiff = sprintf('git diff --name-only %s', $commit);

$output = array();
exec($commandDiff, $output);

$phpFiles = array_filter($output, function ($file) {
    $info = new SplFileInfo($file);
    return $info->getExtension() == 'php';
});

foreach ($phpFiles as $phpFile) {
    convertFile($phpFile);
}

//$commandCherryPick = sprintf('git cherry-pick ')
