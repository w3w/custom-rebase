<?php

require_once "convert.functions.php";

$commit = $argv[1];

$commit = "e758f6395c16b558a0b2927d21095b6823b5e518";

$commandMessage = sprintf('git log --format=%%B -n 1 %s', $commit);
exec($commandMessage, $output);
$commandMessage = $output[0];

echo $commandMessage . PHP_EOL;

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

$commandCherryPick = sprintf('git cherry-pick %s', $commit);
exec($commandCherryPick, $output);
print_r($output);

echo "Pausing command..." . PHP_EOL;
fgetc(STDIN);

foreach ($phpFiles as $phpFile) {
    // exec $phpFile - revert short diffs
}
