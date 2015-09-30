<?php

$path = getcwd();
$from = $argv[1];
$to = $argv[2];
$scriptPath = dirname($argv[0]);

$commandShowTree = sprintf('git log --oneline %s..%s', $from, $to);

$output = array();
exec($commandShowTree, $output);

$revisions = array_map(function ($item) {
    return substr($item, 0, strpos($item, ' '));
}, $output);

foreach ($revisions as $revision) {
    $processRevisionCommand = sprintf('php %s/touch-commit.php %s', $scriptPath, $revision);
    $processRevisionResult = null;
    exec($processRevisionCommand, $processRevisionResult);
    var_dump($processRevisionResult);
}


