<?php

require_once 'convert.functions.php';

$commit = $argv[1];

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
    // exec $phpFile - revert short diffs
}

$commandCherryPick = sprintf('git cherry-pick %s', $commit);
exec($commandCherryPick, $output);
//print_r($output);

echo "Pausing command..." . PHP_EOL;
fgetc(STDIN);

foreach ($phpFiles as $phpFile) {
    revertFile($phpFile);
    // exec $phpFile - revert short diffs
}
